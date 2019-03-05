<?php

class Model
{
	/*
	** Singleton Object for external connection
	*/
	private $db = null;
	private static $m_dbInstance = null;
	
	/*
	** Assign the query m_result
	*/
	private $m_result = '';
	
	
	public function __construct()
	{
		$this->db = DB::getInstance();
		self::$m_dbInstance = $this->db;
	}
	
	public static function getInstance()
	{
		return self::$m_dbInstance;
	}
	
	public function select($query)
	{
		return $this->db->select($query);
	}
	
	public function cusQuery($query)
	{
		return $this->db->cusQuery($query);
	}
	
	/*
	** Insert function from DB
	*/
	public function insert($table, $data)
	{
		return $this->db->insert($table, $data);
	}
	
	/*
	** Delete function from DB
	*/
	public function delete($table, $condition, $limit = '')
	{
		return $this->db->delete($table, $condition, $limit);
	}
	
	/*
	** Update function from DB
	*/
	public function update($table, $data, $condition)
	{
		return $this->db->update($table, $data, $condition);
	}
	
	/*
	** Get last insert ID of the recent 
	** insert function
	*/
	public function lastInsertID()
	{
		return $this->db->lastInsertID();
	}
	
	/*
	** get the num rows of the recent query
	*/
	public function getNumRows()
	{
		return $this->db->getNumRows();
	}
	
	/*
	** get the Affected rows of the recent query
	*/
	public function affectedRows()
	{
		return $this->db->affectedRows();
	}
	
	/*
	** get the m_results of the recent query
	*/
	public function result()
	{
		return $this->db->result();	
	}
}
?>