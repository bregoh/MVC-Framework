<?php
class Router
{
	/*
	** Error Object
	*/
	private $_errorObj;
	
	public static $_URLBit = 	array();
	
	protected $newURL;
	
	private $_routes =			array();
	
	private $_class;
	
	private $_method;
	
	public function __construct()
	{
		//require_once(ROOT_DIR . DS . 'core' . DS . 'ErrorHandler.php');
		
		$this->_errorObj = new ErrorHandler;
		
		$this->_errorObj->__construct();
	}
	
	/**
	** Redirect
	**/
	
	public static function redirect($page = '')
	{
		header("location: ".BASE_URL.$page);
	}
	
	
	/*
	** Get the URL strings, Cleans the Strings
	** Get the static route mapping
	*/
	
	public static function route()
	{
		$obj = new Router();
		
		$obj->_setURL();
		
		if(file_exists(ROOT_DIR . DS . 'config' . DS . 'routes.php'))
		{
			include_once(ROOT_DIR . DS . 'config' . DS . 'routes.php');
		}
		
		if(isset($routes) && is_array($routes))
		{
			$obj->_routes = $routes;
		}
		
		if($obj->newURL !== "")
		{
			$obj->_parseRoute();
		}
		else
		{
			$this->_set_default_controller();
		}
	}
	
	public function _setURL()
	{
		$url = array_filter(Router::$_URLBit);
		//var_dump($url); //die();
		
		$pageURL = '';
		
		if(empty($url))
		{
			$url = array('index');
		}
		
		foreach($url as $value)
		{
			$this->_cleanURL($value);
			
			$pageURL .= $this->_remove_invisible_character($value.'/', FALSE);
		}
		
		
		$this->newURL = substr($pageURL, 0, -1);
	}
	
	
	private function _parseRoute()
	{
		$url = $this->newURL;
		
		foreach($this->_routes as $key => $value)
		{
			$key = str_replace(array(':any', ':num'), array('[^/]+', '[0-9]+'), $key);
			
			if (preg_match('#^'.$key.'$#', $url, $matches))
			{
				if (strpos($value, '$') !== FALSE && strpos($key, '(') !== FALSE)
				{
					$value = preg_replace('#^'.$key.'$#', $value, $url);
				}
				
				
				//echo "Valid request<br/>";
				$this->_runRequest($value);
				return;
			}
		}
		
		$this->_runRequest($url);
	}
	
	private function _runRequest($value)
	{
		$value = explode("/", $value); //var_dump($value);
		
		$params = array();
		
		$controller = ucfirst(array_shift($value));
		
		
		
		if(file_exists(ROOT_DIR . DS . 'application' . DS . 'controller' . DS . $controller . '.php'))
		{
		
			$method = array_shift($value); //echo $controller." ".$method; //die();

			//$method = isset($method) ? $method : 'index';

			if(isset($this->newURL) || $this->newURL !== "")
			{
				$params = array($this->newURL);
			}
			else
			{
				$params = isset($value) ? $value : array();
			}
			
			//var_dump($params); //die();

			$route = new $controller($controller, $method);


			if(method_exists($controller, $method))
			{
				call_user_func_array([$route, $method], $params);
			}
			else
			{
				die("Method doesn't exist in the controller ".$controller);
			}
		}
		else
		{
			$this->_set_default_controller();//die("500 bad request");
		}
	}
	
	
	private function _cleanURL($str)
	{
		if ( ! empty($str) && ! empty($this->_permitted_uri_chars) && ! preg_match('/^['.$this->_permitted_uri_chars.']+$/i'.(UTF8_ENABLED ? 'u' : ''), $str))
		{
			$this->_errorObj->_getError('The URI you submitted has disallowed characters.');
		}
	}
	
	
	private function _set_default_controller()
	{
		$class = DEFAULT_CONTROLLER;
		$method = DEFAULT_METHOD;
		
		if(file_exists(ROOT_DIR . DS . 'application' . DS . 'controller' . DS . $class .'.php'))
		{
			$this->_class = 	ucfirst($class);
			$this->_method = 	$method;
			
			$value = $this->_class."/".$this->_method;
			
			$this->_runRequest($value);
		}
		
	}
	
	
	/**
	 * Remove Invisible Characters
	 *
	 * This prevents sandwiching null characters
	 * between ascii characters, like Java\0script.
	 *
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	private function _remove_invisible_character($string, $url_encoded = TRUE)
	{
		$non_displayables = array();

		// every control character except newline (dec 10),
		// carriage return (dec 13) and horizontal tab (dec 09)
		if ($url_encoded)
		{
			$non_displayables[] = '/%0[0-8bcef]/i';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/i';	// url encoded 16-31
			$non_displayables[] = '/%7f/i';	// url encoded 127
		}

		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do
		{
			$string = preg_replace($non_displayables, '', $string, -1, $count);
		}
		while ($count);

		return $string;
	}
	
}

?>