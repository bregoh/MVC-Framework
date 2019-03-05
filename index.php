<?php
session_start();

/////////////////////////////////////////////////////////////////
///															////
///															///
///															///
///////////////////////////////////////////////////////////////
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_DIR', dirname(__FILE__)); // ROOT DIRECTORY
define('BASE_URL', 'https://www.example.com/'); // https://www.example.com/

/*
** Load configuration and helper functions
*/
require_once(ROOT_DIR . DS . 'config' . DS . 'config.php');
require_once(ROOT_DIR . DS . 'application' . DS . 'lib' . DS . 'helpers' . DS . 'functions.php');

/*
** Autoload classes by using the autoload library in PHP
*/
function autoloader($classname)
{
	if(file_exists(ROOT_DIR . DS . 'core' . DS . $classname.'.php'))
	{
		require_once(ROOT_DIR . DS . 'core' . DS . $classname.'.php');
	}
	else if(file_exists(ROOT_DIR . DS . 'application'  . DS . 'controller' . DS . $classname.'.php'))
	{
		require_once(ROOT_DIR . DS . 'application'  . DS . 'controller' . DS . $classname.'.php');
	}
	else if(file_exists(ROOT_DIR . DS . 'application'  . DS . 'model' . DS . $classname.'.php'))
	{
		require_once(ROOT_DIR . DS . 'application'  . DS . 'model' . DS . $classname.'.php');
	}
}

spl_autoload_register('autoloader');

/*
** Router
*/
Router::$_URLBit = isset($_SERVER['PATH_INFO']) ? explode('/', ltrim($_SERVER['PATH_INFO'], '/')) : [];

Router::route();
?>