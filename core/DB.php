<?php

class DB
{
	/*
	** DB Object
	*/
	private $m_dbObj = null;
	
	/*
	** Singleton Object for external connection
	*/
	private static $m_dbInstance = null;
	
	/*
	** Assign the query m_result
	*/
	private $m_result = '';
	
	/*
	** Define DB driver
	*/
	private $m_driver;
	
	private $_dbName;
	
	public $lastInsertID;
	
	/*
	** It is important to maintain a singleton
	** state, that is why we should create a 
	** constructor for a single DB object
	*/
	private function __construct()
	{
		$this->m_driver = DB_DRIVER;
		// Connect to MySQLi 
		include(ROOT_DIR . DS . 'config'  . DS . 'dbconfig.php');
		$this->m_dbObj = new $this->m_driver($db['host'], $db['username'], $db['password'], $db['dbname']);
		if($this->m_dbObj->connect_error)
		{
			die("Error in connecting to DataBase");
		}
		self::$m_dbInstance = $this->m_dbObj;
		
		$this->_dbName = $db['dbname'];
	}
	
	/*
	** Create an Object of the Connection
	*/
	public static function getInstance()
	{
		// if the instance of the connection is empty
		if(self::$m_dbInstance == null || !isset(self::$m_dbInstance))
		{
			self::$m_dbInstance = new DB;
		}
		
		return self::$m_dbInstance;
	}
	
	/////////////////////////////////////////////////
	// Now for some database queries				/
	// INSERT, DELETE, UPDATE and SELECT functions	/
	/////////////////////////////////////////////////
	
	/*
	** Execute Query function
	*/
	private function execute($data)
	{
		$sql = $this->m_dbObj->query($this->escapeString($data));
		if(!$sql)
		{
			return false;
		}
		else
		{
			$this->m_result = $sql;
			return true;
		}
	}
	
	/*
	** Select data from DB
	*/
	public function select($query)
	{
		if($this->execute("SELECT * FROM ".$query))
		{
			return $this->m_result;
		}
	}
	
	/*
	** Custom Queries from th database
	*/
	public function cusQuery($query)
	{
		//var_dump($query);
		if($this->execute($query))
		{
			return $this->m_result;
		}
	}
	
	/*
	** Insert function from DB
	*/
	public function insert($table, $data)
	{
		$fields = "";
		$values = "";
		
		foreach($data as $f => $v)
		{
			$fields .= "$f,";
			$values .= (is_numeric($v) && (intval($v) == $v)) ? $v."," : "'$v',";
			// if values are numeric, don't put them in apostrophe, else put them in apostrophe
		}
		// as usual don't forget to remove trailing comma
		$fields = substr($fields, 0, -1);
		$values = $this->escapeString(substr($values, 0, -1));
		$insert = "INSERT INTO $table ({$fields}) VALUES ({$values})"; //var_dump($insert);
		return $this->execute($insert);
	}
	
	/*
	** Delete function from DB
	*/
	public function delete($table, $condition, $limit = '')
	{
		//using ternary if condition, if limit is empty, 
		//then do not add limit to query else add limit to query
		$limit = ($limit == '') ? '' : 'LIMIT '.$limit;
		$delete = "DELETE FROM {$table} WHERE {$condition} {$limit}";
		return $this->execute($delete);
		// using {} for variable makes easy concatenation
	}
	
	/*
	** Update function from DB
	*/
	public function update($table, $data, $condition)
	{
		$update = "UPDATE {$table} SET ";
		foreach($data as $field => $value)
		{
			$update .= $field."= '{$value}',";
		}
		$update = substr($update, 0, -1);
		
		if($condition != '')
		{
			$update .= "WHERE ".$condition;
		}
		//var_dump($update);
		return $this->execute($update);
	}
	
	/*
	** Get last insert ID of the recent 
	** insert function
	*/
	public function lastInsertID()
	{
		$this->lastInsertID = $this->m_dbObj->insert_id;
		return $this->m_dbObj->insert_id;
	}
	
	/*
	** get the num rows of the recent query
	*/
	public function getNumRows()
	{
		return $this->m_result->num_rows;
	}
	
	/*
	** get the Affected rows of the recent query
	*/
	public function affectedRows()
	{
		return $this->m_dbObj->affected_rows;
	}
	/*
	** get table fields
	*/
	public function getTblFields($table)
	{
		$query = "SHOW COLUMNS FROM $table FROM $this->_dbName";
		$this->execute($query);
		$rows = [];
		while($row = $this->m_result->fetch_array())
		{
			$rows[] = $row["Field"];
		}
		
		return $rows;
	}
	
	/*
	** get the m_results of the recent query
	*/
	public function result()
	{
		if(!empty($this->m_result))
		{
			while($row = $this->m_result->fetch_array())
			{
				$rows[] = $row;
			}
			return $rows;
		}
		else
		{
			return null;
		}	
	}
	
	/*
	** Verify data before sending to DB
	*/
	private function escapeString($value)
	{
		return htmlentities($value);
	}
	
	public function validateText($text)
	{
		return preg_replace('#[^A-Za-z]#i', '', $text);
	}
	
	public function validateNumText($text)
	{
		return preg_replace('#[^A-Za-z0-9]#i', '', $text);
	}
	
	public function validateNum($text)
	{
		return preg_replace('#[^0-9]#i', '', $text);
	}
	
	public function validateEmail($email)
	{
		return filter_var($email, FILTER_SANITIZE_EMAIL);
	}
	
	public function __deconstruct()
	{
		$this->m_dbObj->close();
	}
}
?>