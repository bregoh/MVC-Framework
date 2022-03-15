<?php

    namespace Core;

    class Router 
    {
        public static $URLBit = array();
        private $_routes = array();
        private $_class;
        private $_method;
        protected $newURL;

        protected $params = array();
                   
        /**
         * redirect
         *
         * @param  mixed $url
         * @return void
         */
        public static function redirect($url = "")
        {
            header("location: ".BASE_URL.$url);
        }

        public static function run()
        {
            $url = array();

            if(isset($_SERVER["PATH_INFO"]))
            {
                $u = $_SERVER["PATH_INFO"];
            }
            else if(isset($_SERVER["REQUEST_URI"]))
            {
                $u = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                
            }
            else
            {
                $u = [];
            }

            if(!empty($u))
            {
                $url = explode('/', ltrim(htmlentities($u), "/"));
            }
            else
            {
                $url = $u;
            }
            
            Router::$URLBit = $url;
            Router::route();
        }
        
        /**
         * route
         *
         * @return void
         */
        public static function route()
        {
            $obj = new Router(); //Router obj
		
            $obj->_setURL(); // clean the url
            
            // get all routes in the route config
            if(file_exists(ROOT_DIR . DS . 'config' . DS . 'routes.php'))
            {
                include_once(ROOT_DIR . DS . 'config' . DS . 'routes.php');
            }
            else
            {
                dnd("routes file not found");
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
                (new self)->_set_default_controller();
            }
        }

        public function _setURL()
        {
            $url = array_filter(Router::$URLBit);
            
            $pageURL = '';
            
            if(empty($url))
            {
                $url = array('index');
            }
            
            foreach($url as $value)
            {          
                $a = $this->_cleanURL($value);      
                $pageURL .= $this->_remove_invisible_character($a.'/', FALSE);
            }

            $this->newURL = substr($pageURL, 0, -1);
        }

        private function _cleanURL($str)
        {
            $urlString = \urlencode($str);
            $urlStringCleaned = \preg_replace('/[^0-9A-Za-z]-_/', "", $urlString);
            return htmlentities($urlStringCleaned);
        }

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

                $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127
            }

            do
            {
                $string = preg_replace($non_displayables, '', $string, -1, $count);
            }
            while ($count);

            return $string;
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
                    
                    $this->getParams($matches);
                    $this->_runRequest($value);
                    return;
                }
            }
            
            $this->_runRequest($url);
        }

        
        private function _runRequest($value)
        {
            $value = explode("/", $value);
            
            $params = $this->params;
            
            $controller = ucfirst(array_shift($value));
            
            if(file_exists(ROOT_DIR . DS . 'application' . DS . 'controller' . DS . $controller . '.php'))
            {
            
                $method = array_shift($value); 
                
                $controller = 'Application\Controller\\' . $controller;

                $route = new $controller($controller, $method);

                if(empty($this->params))
                {
                    $params = array($this->newURL);
                }


                if(method_exists($controller, $method))
                {
                    //call_user_func_array([$route, $method], $params);
                    call_user_func([$route, $method], $params);
                }
                else
                {
                    die("Method doesn't exist in the controller ".$controller);
                }
            }
            else
            {
                $this->_set_default_controller();
            }
        }

        private function _set_default_controller()
        {
            $class = DEFAULT_CONTROLLER."_Controller";
            $method = DEFAULT_METHOD;
            
            if(file_exists(ROOT_DIR . DS . 'application' . DS . 'controller' . DS . $class .'.php'))
            {
                $this->_class = 	ucfirst($class);
                $this->_method = 	$method;
                
                $value = $this->_class."/".$this->_method;
                
                $this->_runRequest($value);
            }
            
        }

        public function getParams($params)
        {
            $paramString = $params[0];

            $paramStringToArray = \explode("/", $paramString);

            $urlBits = [];

            if(in_array("api", $paramStringToArray) && "api" === $paramStringToArray[0])
            {
                \array_shift($paramStringToArray);

                do{
                    $urlBits[array_shift($paramStringToArray)] = array_shift($paramStringToArray);
                }
                while(!empty($paramStringToArray));

                $this->params = $urlBits;
            }
            else
            {
                $this->params = $paramStringToArray;
            } 
        }

    }
?>