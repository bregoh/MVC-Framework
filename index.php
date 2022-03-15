<?php

use Core\Router;

session_start();

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_DIR', dirname(__FILE__)); // ROOT DIRECTORY
define('env', "sandbox"); // sandbox or live

require_once(ROOT_DIR . DS . 'config' . DS . 'config.php');
require_once(ROOT_DIR . DS . 'application' . DS . 'lib' . DS . 'helpers' . DS . 'helpers.php');
/*
** Autoload classes by using the autoload library in PHP
*/
/**
 * autoloader
 *
 * @param  mixed $classname
 * @return void
 */
function autoloader($classname)
{
    $classArray = explode("\\", $classname);
    $class = array_pop($classArray);
    $subPath = strtolower(implode(DS, $classArray));
    $path = ROOT_DIR . DS . $subPath . DS . $class . ".php";

    if (file_exists($path)) {
        require_once($path);
    }
}

spl_autoload_register('autoloader');

Router::run();
