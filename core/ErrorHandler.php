<?php

namespace Core;

class ErrorHandler
{
	public $errorLog = null;
	
	public function __construct()
	{
		$this->_set_error_reporting();
		$this->_unregister_globals();
	}
	
	/*
	** Error constructor to get the error number, error file
	** Error position in file
	** Error message to be printed
	*/
	function printError($errno, $errstr, $errfile, $errline)
	{
		// $error = "
		// 	<b>Date: </b> ".date("Y-m-d H:i:s")." <br/>
		// 	<b>Custom error: </b> [$errno] <br/>
		// 	<b>Message: </b> $errstr <br/>
		// 	<b>Line: </b> $errline <br/>
		// 	<b>File: </b> $errfile <br/>
		// ";

		$error = [];
		$error["Date"] = date("Y-m-d H:i:s");
		$error["Custom error"] = $errno;
		$error["Message"] = $errstr;
		$error["Line"] = $errline;
		$error["File"] = $errfile;

		if(DEBUG)
		{
			echo "<pre>";
            var_dump($error);
        	echo "</pre>";
		}
		else
		{
			$errorFile = ROOT_DIR . DS . 'debug' . DS . 'logs' . DS . 'errors.log';
			$fp = fopen($errorFile, 'a');//opens file in append mode  
			fwrite($fp, implode("\n", $error));
			fwrite($fp, "\n".'====================================='."\n");  
			fclose($fp);  
		}
	}

	function setError($e)
	{
		$this->errorLog = $e;
	}
	
	/*
	** Print errors
	*/
	public function _getError($errMsg = "")
	{
		// Set user-defined error handler function
		set_error_handler(array($this,"printError"));
		
		if($errMsg != "")
			trigger_error($errMsg); 
		/* It's not necessary to store the trigger error in a variable
		** but necessary to send to database
		** It makes code neat  
		*/
	}
	
	/*
	** Set errors for debugging
	*/
	private function _set_error_reporting()
	{
		if(DEBUG)
		{
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
		}
		else
		{
			error_reporting(0);
			ini_set('display_errors', 0);
			ini_set('log_errors', 1);
		}
		
		$this->_getError();
	}
	
	/*
	** Make sure unregistered global is used
	** For security
	*/
	private function _unregister_globals()
	{
		if(ini_get('register_globals'))
		{
			$globalArray = ['_SESSION', '_COOKIE', '_POST', '_GET', 'REQUEST', '_SERVER', '_ENV', '_FILES'];
			
			foreach($globalArray as $global)
			{
				foreach($GLOBALS[$global] as $key => $value)
				{
					if($GLOBALS[$key] === $value)
					{
						unset($GLOBALS[$key]);
					}
				}
			}
		}
	}
	
}
?>