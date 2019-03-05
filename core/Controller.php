<?php
class Controller extends Core
{
	/*
	** Controller object
	*/
	protected $_controller;
	
	/*
	** Method object
	*/
	protected $_method;
	
	/*
	** view/page object
	*/
	public $view;
	
	/*
	** Assign the query m_result
	*/
	private $m_result = '';
	
	/*
	** Store Load class object
	*/
	public $load;
	
	/*
	** Store Registry class object
	*/
	public $registry;
	
	public $security;
	
	public $file_handler;
	
	public $db = null;
	
	public $u_session;
	
	public $email;
	
	/*
	** Controller instance
	*/
	public function __construct($controller = null, $method = null)
	{
		$this->load = new Load;
		
		parent::__construct();
		
		$this->_controller = $controller;
		
		$this->_method = $method;
		
		$this->view = new View;
		
		$this->registry = Registry::getInstance();
		
		$this->security = new Security;
		
		$this->file_handler = new File_Manager;
		
		$this->db = DB::getInstance();
		
		$this->u_session = new Sessions;
		
		$this->email = new Email;
	}
	
	public function __get($key)
	{
		$obj = $this->registry->$key;
		return $obj;
	}
}
?>