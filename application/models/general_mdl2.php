<?php

class General_mdl2 extends CI_Model
{

    private $_table;
    private $_from;
    private $_data;
    
	function __construct()
	{
		parent::__construct();
		$this->db = $this->load->database('db_2', TRUE);
        $this->setTable('');
        $this->setData(array());
	}
	
	// General function
	public function setTable($table = '')
    {
        $this->_table = $table;
    }
    
    public function getTable()
    {
        return $this->_table;
    }
    
    
    public function setData($data = array())
    {
        $this->_data = $data;
    }
    
    public function getData()
    {
        return $this->_data;
    }
    
    
    //��������
    /*
    ** 
    ** return INT or false INT�ǲ������ݿ��ID
    */
    public function create()
    {
        if($this->_data){
            $this->db->insert($this->_table, $this->_data);
            return $this->db->insert_id();
        }else{
            return false;
        }
    }
    
    //ɾ������
    /*
    ** 
    ** 
    */
    public function delete()
    {
        $this->db->delete($this->_table, $this->_data);
        return $this->db->affected_rows();
    }
    
    //ɾ������
    /*
    ** ����idɾ������
    ** 
    */
    public function delete_by_id($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->_table);
    }
    
    //��������
    /*
    ** 
    ** return INT or false INT�ǲ������ݿ��ID
    */
    public function update($where = array())
    {
        
        if($where){
            foreach($where as $key=>$row)
            {
                $this->db->where($key, $row);
            }
            if($this->_data){
                return $this->db->update($this->_table, $this->_data);
            }else{
                return false;
            }
        }
        return false;
    }
    
    public function update_where_in($field, $array = array())
    {
        
        if($array){
            $this->db->where_in($field, $array);
            if($this->_data){
                return $this->db->update($this->_table, $this->_data);
            }else{
                return false;
            }
        }
        return false;
    }
    
    public function get_query($start = 0, $pageSize = '', $orderby = '')
    {
    
        if($orderby)
        {
            $this->db->order_by($orderby); 
        }

        if($pageSize)
        {
            $query = $this->db->get($this->_table, $pageSize, $start);
        }else{
            $query = $this->db->get($this->_table);
        }
        
        return $query;
    }
    
    public function get_fields($where = array(), $fields = '*',$start = 0, $pageSize = '')
    {
        if($where){
            foreach($where as $key=>$row)
            {
                $this->db->where($key, $row);
            }
        }
        
        $this->db->select($fields);
        
        return $this->get_query($start, $pageSize);
    }
    
    public function get_query_by_where_in($where_field = '',$where = array(), $start = 0, $pageSize = '')
    {
        if($where_field){
            $this->db->where_in($where_field, $where);
        }

        return $this->get_query($start, $pageSize);
    }
    
    
    public function get_query_by_where($where = array(), $start = 0, $pageSize = '', $orderby = '')
    {
        if($where){
            foreach($where as $key=>$row)
            {
                $this->db->where($key, $row);
            }
        }
        
        if($orderby)
        {
            $this->db->order_by($orderby); 
        }

        return $this->get_query($start, $pageSize);
    }


    public function get_query_by_or_where($where=array(), $or_where=array(), $start = 0, $pageSize = '', $orderby = '')
    {
        if($where){
            foreach($where as $key=>$row)
            {
                $this->db->where($key, $row);
			}
        }

        if($or_where){
            foreach($or_where as $key=>$row)
            {
                $this->db->or_where($key, $row);
			}
        }
        
        if($orderby)
        {
            $this->db->order_by($orderby); 
        }

        return $this->get_query($start, $pageSize);
    }
    
    public function get_query_like($like = array(), $where = array(), $start = 0, $pageSize = '')
    {
        if($like){
            $this->db->or_like($like);
        }
        
        if($where){
            foreach($where as $key => $row)
            {
                $this->db->where($key, $row);
            }
        }
        
        return $this->get_query($start, $pageSize);
    }
}

?>