<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class wechat_handle extends CI_Controller
{

    var $FromUserName = null;
    var $ToUserName = null;
    var $wechat_config = array();

    function __construct()
    {
        parent::__construct();
        $this->load->library("logger");
        
        $this->general_mdl->setTable('sys_config');
        $result = $this->general_mdl->get_query_by_where(array('cat' => 'wechat'))->result_array();

        foreach($result as $item){
            $this->wechat_config[$item['name']] = $item['value'];
        }
    }

    public function index()
    {
        $this->message();
    }

    public function member_bind()
    {
        $this->load->library("curl");

        $code = $this->input->get('code');

        $this->general_mdl->setTable('sys_config');
        $config = $this->general_mdl->get_query_by_where(array('cat' => 'wechat'))->result_array();
        $appid = $config[0]['value'];
        $secret = $config[1]['value'];

        if($code){        
            //获取用户
            $url_template = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
            $url = sprintf($url_template, $appid, $secret, $code);
            $this->curl->create($url);
            $this->curl->ssl(FALSE,FALSE);
            $response = json_decode($this->curl->execute());

            if( isset($response->openid) ){
                $redirect_url = base_url('wechat/bind.html?openid='.$response->openid);
                redirect($redirect_url);
            }else{
                echo $response->errmsg;
            }
        }else{
            show_404();
        }
    }

    // 在微信平台上设置的对外 URL
    public function message()
    {
        if ($this->_valid())
        {
            // 判读是不是只是验证
            $echostr = $this->input->get('echostr');
            if (!empty($echostr))
            {
                $this->load->view('valid_view', array('output' => $echostr));
            }
            else
            {
                // 实际处理用户消息
                $this->_responseMsg();
            }
        }
        else
        {
            $this->load->view('valid_view', array('output' => 'Error!'));
        }
    }

    // 用于接入验证
    private function _valid()
    {
        $token = isset($this->wechat_config['token']) ? $this->wechat_config['token'] : null;

        $signature = $this->input->get('signature');
        $timestamp = $this->input->get('timestamp');
        $nonce = $this->input->get('nonce');

        $tmp_arr = array($token, $timestamp, $nonce);
        sort($tmp_arr);
        $tmp_str = implode($tmp_arr);
        $tmp_str = sha1($tmp_str);

        return ($tmp_str == $signature);
    }

    // 这里是处理消息的地方，在这里拿到用户发送的字符串
    private function _responseMsg()
    {
        $post_str = $GLOBALS["HTTP_RAW_POST_DATA"];

        $this->logger->conf['log_file'] = "wechat_receive_logs.txt";
        $this->logger->log(array($post_str));

        if (!empty($post_str))
        {
            // 解析微信传过来的 XML 内容
            $post_obj = simplexml_load_string($post_str, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->FromUserName = $post_obj->FromUserName;
            $this->ToUserName = $post_obj->ToUserName;
            // $keyword 就是用户输入的内容
            $keyword = trim($post_obj->Content);

            // 分析消息类型，并分发给对应的函数
            switch ($post_obj->MsgType)
            {
              case 'event':
                switch ($post_obj->Event)
                {
                  case 'subscribe':
                    $this->onSubscribe();
                    break;

                  case 'unsubscribe':
                    $this->onUnsubscribe();
                    break;

                  case 'SCAN':
                    $this->onScan();
                    break;

                  case 'LOCATION':
                    $this->onEventLocation();
                    break;

                  case 'CLICK':
                    $this->onClick($post_obj->EventKey);
                    break;
                }

                break;

              case 'text':
                $this->onText($keyword);
                break;

              case 'image':
                $this->onImage();
                break;

              case 'location':
                $this->onLocation();
                break;

              case 'link':
                $this->onLink();
                break;

              case 'voice':
                $this->onVoice();
                break;

              default:
                $this->onUnknown();
                break;

            }

        }
        else
        {
            $this->load->view('valid_view', array('output' => 'Error!'));
        }
    }

    // 用户关注时触发
    private function onSubscribe()
    {
      $content = isset($this->wechat_config['subscribe']) ? $this->wechat_config['subscribe'] : "欢迎关注";
      $this->responseText($content);
    }

    // 收到文本消息时触发
    private function onText($keyword)
    {
      $this->_parseMessage($keyword);
    }

    // 收到菜单点击事件时触发
    private function onClick($eKey)
    {
        switch ($eKey) {
            case 'value':                
                break;
            default:
                echo "";
                break;
        }
    }

    //发送被动响应文本消息
    private function responseText($content)
    {
      $tpl = "<xml>
              <ToUserName><![CDATA[%s]]></ToUserName>
              <FromUserName><![CDATA[%s]]></FromUserName>
              <CreateTime>%s</CreateTime>
              <MsgType><![CDATA[%s]]></MsgType>
              <Content><![CDATA[%s]]></Content>
              <FuncFlag>0</FuncFlag>
              </xml>";
      $resultStr = sprintf($tpl, $this->FromUserName, $this->ToUserName, time(), "text", $content);
      $resultXML = preg_replace('/[\r|\t]/', '', $resultStr);
      echo $resultXML;
    }

    //发送被动响应图文消息
    private function responseNews($data)
    {
      $tpl = "<xml>
                  <ToUserName><![CDATA[%s]]></ToUserName>
                  <FromUserName><![CDATA[%s]]></FromUserName>
                  <CreateTime>%s</CreateTime>
                  <MsgType><![CDATA[%s]]></MsgType>
                  <ArticleCount>1</ArticleCount>
                  <Articles>
                  <item>
                  <Title><![CDATA[%s]]></Title>
                  <Description><![CDATA[%s]]></Description>
                  <PicUrl><![CDATA[%s]]></PicUrl>
                  <Url><![CDATA[%s]]></Url>
                  </item>
                  </Articles>
                  <FuncFlag>0</FuncFlag>
                  </xml>";
        $resultStr = sprintf(
          $tpl,
          $this->FromUserName, $this->ToUserName, time(), "news",
          $data['title'], $data['description'], $data['picurl'], $data['url']
        );
      $resultXML = preg_replace('/[\r|\t]/', '', $resultStr);
      echo $resultXML;
    }


    // 解析用户输入的字符串
    private function _parseMessage($message)
    {
        $this->logger->conf['log_file'] = "wechat_logs.txt";
        // $this->general_mdl->setTable('wechat_autoreply');
        // $reply_row = $this->general_mdl->get_query_by_where(array("keyword" => $message));
        $reply_row = array();

        // 记录发送日志
        $logData = array(
            date("Y-m-d H:i:s"),
            $message,
          );

        if(!empty($reply_row)){
            switch ($reply_row['msgtype']) {
                case 'text':
                    $reply_data = json_decode($reply_row['reply_data']);

                    $logData[] = $message;
                    $logData[] = $reply_data->content;
                    $this->logger->log($logData);

                    $this->responseText($reply_data->content);
                    break;
                case 'news':
                    $reply_data = (array)json_decode($reply_row['reply_data']);
                    $this->responseNews($reply_data);
                    break;
            }
        }else{
            $this->responseText("收到的信息：".$message);
        }
        // TODO: 在这里做一些字符串解析，比如分析某关键字，返回什么信息等等
    }
}


/* End of file wechat.php */
/* Location: ./application/controllers/wechat.php */