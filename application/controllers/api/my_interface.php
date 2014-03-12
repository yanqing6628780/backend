<?php defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class my_interface extends REST_Controller
{
    
    //获取门店(用户)数据
    function users_get(){
        $this->general_mdl->setTable('admin_user_profile');
        $query = $this->general_mdl->get_query();

        $result['content'] = $query->result_array();
        $result['status'] = 1;
        $this->response($result, 200); // 200 being the HTTP response code
    }

    //接受门店上传的菜单数据
    function receive_goods_post(){
        $username = $this->post('username');
        $password = $this->post('password');

        $resp['status'] = 0;
        $is_matche = $this->dx_auth->login($username, $password, FALSE);

        if($is_matche){
            $resp['status'] = 1;
            $user_id = $this->dx_auth->get_user_id();

            //将接受到的菜单写入数据库
            // code...
        }else{
            $resp['error'] = "请填写正确的用户名和密码";
        }
        $this->response($resp, 200);
    }

    //获取门店的菜单数据
    function get_user_goods_post(){
        $result['status'] = 0;
        $user_id = $this->post('user_id');

        $this->general_mdl->setTable('goods');
        $query = $this->general_mdl->get_query_by_where( array('user_id' => $user_id) );

        if($query->num_rows() > 0){        
            $result['content'] = $query->result_array();
            $result['status'] = 1;
            $this->response($result, 200);
        }else{
            $result['error'] = 'no data';
            $result['status'] = 0;
            $this->response($result, 200);
        }
    }

    //下单
    function orders_post(){
        
    }

    //获取订单表数据
    function orders_get(){

        $order_sn = $this->get('sn');
        $wechat_openid = $this->get('openid');

        $this->general_mdl->setTable('order');
        if($order_sn){
            $where = array('order_sn' => $order_sn);
        }else if($wechat_openid){
            $where = array('wechat_openid' => $wechat_openid);
        }else{
            $where = array();
        }
        $query = $this->general_mdl->get_query_by_where($where);

        if ($order_sn) {
            $row = $query->row_array();

            $this->general_mdl->setTable('order_goods');
            $order_goods = $this->general_mdl->get_query_by_where(array('order_id' => $row['id']))->result_array();
            $result['goods'] = $order_goods;
            
            $result['status'] = 1;
            $this->response($result, 200); // 200 being the HTTP response code
        } else if ($query->num_rows() > 0) {
            $result = $query->result_array();

            foreach ($result as $key => $row) {
                $this->general_mdl->setTable('admin_user_profile');
                $profile = $this->general_mdl->get_query_by_where(array('user_id' => $row['user_id']))->row_array();
                $result[$key]['profile'] = $profile;
                
                $this->response($result, 200); // 200 being the HTTP response code
            }

        } else {
            $result['status'] = 0;
            $result['error'] = 'no data';
            $this->response($result, 200);
        }
    }

    //优惠卷发放
    function coupon_use_post()
    {
        $this->load->model('general_mdl');

        if($this->post('id') && $this->post('wx_num')){

            $this->general_mdl->setTable('coupon_kind');
            //优惠劵发行量和限取数量
            $query = $this->general_mdl->get_query_by_where( array('id' => $this->post('id')) ); 
            if($query->num_rows() > 0){
                $d[] = $coupon_circulation =  $query->row()->coupon_circulation;
                $d[] = $coupon_limit =  $query->row()->get_limit;
            }else{
                $data['status'] = 0;
                $data['error'] = "没有该优惠卷";
                $this->response($data, 200);
            }

            //获取优惠劵已经发放条数
            $this->general_mdl->setTable('coupon_use');
            $query = $this->general_mdl->get_query_by_where( array('coupon_id' => $this->post('id')) ); 
            $coupon_used_amount = $query->num_rows();

            $query = $this->general_mdl->get_query_by_where( array('coupon_id' => $this->post('id'), 'wx_num' => $this->post('wx_num')) );
            $user_own_coupon_num = $query->num_rows();

            if ($coupon_used_amount < $coupon_circulation && $user_own_coupon_num < $coupon_limit) {
                //检查优惠卷号是否重复
                do {
                    $coupon_num = generate_password(6, 1);
                } while ($this->general_mdl->get_query_by_where( array('coupon_num' => $coupon_num) )->num_rows() > 0);

                $data['coupon_num'] = $coupon_num;
                $data['wx_num'] = $this->post('wx_num');
                $data['coupon_id'] = $this->post('id');
                $this->general_mdl->setData($data);
                
                if ($this->general_mdl->create()) {
                    $data['status'] = 1;
                    $this->response($data, 200);
                } else {
                    $data['status'] = 0;
                    $this->response($data, 200);
                }
            }

            if ($user_own_coupon_num >= $coupon_limit) {
                $data['status'] = 0;
                $data['error'] = "你领取优惠劵的名额以用完";
                $this->response($data, 200);
            }

            if ($coupon_used_amount >= $coupon_circulation) {
                $data['status'] = 0;
                $data['error'] = "优惠劵已被领完";
                $this->response($data, 200);
            } 
        }else{
            $data['status'] = 0;
            $data['error'] = "请求失败";
            $this->response($data, 200);            
        }

    }

    //优惠卷打印
    function coupon_print_post()
    {
        $coupon_num = $this->post('coupon_num');
        if($coupon_num){

            $this->general_mdl->setTable('coupon_use');
            $query = $this->general_mdl->get_query_by_where( array('coupon_num' => $coupon_num) );
            if($query->num_rows() > 0){
                $row = $query->row_array();
                if($row['coupon_status'] == 1){
                    $data['status'] = 0;
                    $data['error'] = "该 优惠卷/奖品卷 已打印过";
                    $this->response($data, 200); 
                }

                $this->general_mdl->setTable('coupon_kind');
                $query = $this->general_mdl->get_query_by_where( array('id' => $row['coupon_id']) );

                if($query->num_rows() > 0){

                    $row['coupon_status'] = 1;
                    $update_data = $row;
                    $this->general_mdl->setTable('coupon_use');
                    $this->general_mdl->setData($update_data);
                    $this->general_mdl->update(array('id' => $row['id']));

                    $data['content'] = $query->row_array();
                    $data['status'] = 1;
                    $this->response($data, 200);
                }else{
                    $data['status'] = 0;
                    $data['error'] = "没有該 优惠卷/奖品卷 信息";
                    $this->response($data, 200); 
                }
            }else{
                $data['status'] = 0;
                $data['error'] = "没有此 优惠卷/奖品卷 号码";
                $this->response($data, 200); 
            }
        }else{
            $data['status'] = 0;
            $data['error'] = "请求失败";
            $this->response($data, 200);            
        }
    }
}