<?php

namespace Core;
use Core\ErrorHandler;

class Controller extends ErrorHandler
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
	** Controller instance
	*/	
	/**
	 * __construct
	 *
	 * @param  mixed $controller
	 * @param  mixed $method
	 * @return void
	 */
	public function __construct($controller = null, $method = null)
	{
		parent::__construct();
		
		$this->_controller = $controller;
		
		$this->_method = $method;

	}
		
	/**
	 * __get
	 *
	 * @param  mixed $key
	 * @return void
	 */
	public function __get($key)
	{
		$registry = Registry::getInstance();
		$obj = $registry->$key;
		return $obj;
	}
	
}
?>