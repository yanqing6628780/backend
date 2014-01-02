<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Logs
{
	/**
    * CI���
    * 
    * @access private
    * @var object
    */
	private $_CI;
    /**
    * logs_mdlģ��
    * 
    * @access private
    * @var object
    */
	private $_LogsMdl;
    
	private $log_time;//����ʱ��
	private $user_id;//������ID
	private $log_info;//��������
	private $ip_address;//������IP��ַ
	private $id;//������¼��ID��
    
    private $Fields = array();
    
    public function __construct()
    {
        /** ��ȡCI��� */
		$this->_CI = & get_instance();
        
        $this->_CI->load->model('general_mdl');
        $this->_CI->load->model('logs_mdl');
        $this->_LogsMdl = $this->_CI->logs_mdl;
    }
    
    //���ò�����id
    public function setUserId($userid){
        $this->user_id = $userid;
        $this->Fields['user_id'] = $userid;
    }
    
    //���ò�������
    public function setLogInfo($loginfo){
        $this->log_info = $loginfo;
        $this->Fields['log_info'] = $loginfo;
    }
    
    //���ò�����¼��ID��
    public function setId($id){
        $this->id = $id;
        $this->Fields['id'] = $id;
    }
    
    //���ò�����IP��ַ
    public function setIp($ipaddress){
        $this->ip_address = $ipaddress;
        $this->Fields['ip_address'] = $ipaddress;
    }
    
    //����������¼
    public function create_log()
    {
        $this->_LogsMdl->setData($this->Fields);
        $this->_LogsMdl->create();
    }
}
?>