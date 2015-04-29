<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class order extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *      http://example.com/index.php/order
     *  - or -
     *      http://example.com/index.php/order/index
     *  - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/order/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function __construct()
    {
        parent::__construct();

        $this->general_mdl->setTable('order');

        $this->load->model('dx_auth/users', 'users');
        $this->load->model('dx_auth/user_profile', 'profile');

        $this->data['controller_url'] = "admin/order/";
    }

    public function index()
    {        
        $this->config->load('wine_erp');
        $this->data['operators'] = $this->config->item('operators');
        $this->data['order_type'] = $this->config->item('order_type');

        $order_data = array();

        $this->data['q'] = $q = $this->input->get_post('q');
        $this->data['start'] = $start = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $this->data['pageSize'] = $pageSize = $this->input->get_post('pageSize') ? $this->input->get_post('pageSize') : 20;

        $like = array();

        if($q){
            $like['order_sn'] = $q;
        }

        //查询数据的总量,计算出页数
        $query = $this->general_mdl->get_query_or_like();
        $this->data['total'] = $query->num_rows();
        $page = ceil($query->num_rows()/$pageSize);
        $this->data['page'] = $page;

        //取出当前面数据
        $query = $this->general_mdl->get_query_or_like($like, array(), $start-1, $pageSize);
        $order_data = $query->result_array();
        $this->data['current_page'] = $start;

        $prev_link = $this->data['controller_url'].'?page='.($start == 1 ? $start : $start-1);
        $prev_link .= $q ? '&q='.$q : '';

        $next_link = $this->data['controller_url'].'?page='.($start == $page ? $start : $start+1);
        $next_link .= $q ? '&q='.$q : '';

        $this->data['prev_link'] = $prev_link;
        $this->data['next_link'] = $next_link;

        $page_link = array();
        for ($i=1; $i <= $page; $i++){
            $page_link[$i] = $this->data['controller_url'].'?page='.$i;
            $page_link[$i] .= $q ? '&q='.$q : '';
        }
        $this->data['page_links'] = $page_link;

        $this->data['title'] = '订单管理';
        $this->data['result'] = $order_data;

        $this->load->view('admin_order/list', $this->data);
    }

    public function product_select()
    {
        $this->data['products'] = $this->db->get('products')->result_array();
        $this->load->view('admin_order/product_select', $this->data);
    }

    //添加
    public function add()
    {
        $this->config->load('wine_erp');
        $this->data['operators'] = $this->config->item('operators');
        $this->data['order_type'] = $this->config->item('order_type');

        $pids = $this->input->get_post('pid');

        $this->data['products'] = $this->db->where_in('id', $pids)->get('products')->result_array();

        $this->load->view('admin_order/add', $this->data);
    }

    //添加保存
    public function add_save()
    {
        $d['pid'] = $this->input->post('pid');
        $d['qty'] = $this->input->post('qty');
        $d['pname'] = $this->input->post('pname');
        $d['price'] = $this->input->post('price');

        $order['biller'] = $this->input->post('biller');
        $order['remarks'] = $this->input->post('remarks');
        $order['type'] = $this->input->post('type');
        $order['datetime'] = date('Y-m-d H:i:s');
        $order['total_price'] = $this->input->post('total_price');
        $order['order_sn'] = $this->input->post('order_sn');

        //订单内产品处理
        foreach ($d['pid'] as $key => $value) {
            $product['pname'] = $d['pname'][$key];
            $product['price'] = $d['price'][$key];
            $product['qty'] = $d['qty'][$key];
            $product['pid'] = $value;
            $order_items[] = $product;
        }

        if($order_id = $this->general_mdl->create($order))
        {
            foreach ($order_items as $key => $item) {
                $item['oid'] = $order_id;
                $this->db->insert('order_items', $item);
            }

            $response['status'] = "y";
            $response['info'] = "添加成功";
        }else{
            $response['status'] = "n";
            $response['info'] = "添加失败";
        }

        echo json_encode($response);
    }

    //修改
    public function edit()
    {
        $this->data['id'] = $this->input->post('id');

        $query = $this->general_mdl->get_query_by_where(array('id' => $this->data['id']));
        $row = $query->row_array();

        $this->data['row'] = $row;

        $this->load->view('admin_order/edit', $this->data);
    }

    //修改保存
    public function edit_save()
    {
        $data = $this->input->post(NULL, TRUE);
        $id = $data['id'];
        unset($data['id']);
        $isUpdated = $this->general_mdl->update(array('id'=>$id),$data);

        if($isUpdated){
            $response['status'] = "y";
            $response['info'] = "修改成功";
        }else{
            $response['status'] = "n";
            $response['info'] = "修改完成";
        }

        echo json_encode($response);
    }

    //删除
    public function del()
    {
        $id = $this->input->post('id');

        $response['success'] = false;
 
        $this->general_mdl->delete_by_id($id);
        $response['success'] = true;

        echo json_encode($response);
    }
}

/* End of file order.php */
/* Location: ./application/controllers/order.php */
