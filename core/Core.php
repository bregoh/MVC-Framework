<?php
class Core
{
	/*
	** Error Handler object
	*
	private $errObj;*/
	
	function __construct()
	{
		require_once(ROOT_DIR . DS . 'core' . DS . 'ErrorHandler.php');
		$errObj = new ErrorHandler;
		$errObj->__construct();
	}	
}
?>