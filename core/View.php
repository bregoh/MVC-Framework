<?php

namespace Core;
use Core\Security;
use Core\Sessions;
use Core\Router;

class View
{	
	public $path = ROOT_DIR . DS . 'application'  . DS . 'view' . DS;
	/*
	** Directory and File names
	*/
	public $directory = '';
	public $pageName = 'index';
	
	public $pcontent = ''; // under review. See pages controller
	public $pageLinks = ''; // under review. See pages controller
	
	/*
	** Page output
	*/
	public $pageTitle = '';
	
	/*
	** Page default layout
	*/
	protected $_template = DEFAULT_TEMPLATE;
	
	public $db;
	public $security;
	public $u_session;
	
	public $email;
	
	public $geoloc;
	
	public $handler;

	public $acl;

	public $param;
	
	// a constructor
	public function __construct()
	{
		// $this->db = DB::getInstance();
		// $this->security = new Security;
		// $this->u_session = new Sessions;
		// $this->file_handler = new File_Manager;
		// $this->email = new Email;
		// $this->geoloc = new Geolocation;
		// $this->handler = new Handler;
		// $this->acl = new ACL;
	}
	
	/*
	** Function to render page based on the url
	*/
	public function pageView($pageName, $path = ROOT_DIR . DS . 'application'  . DS . 'view' . DS)
	{
		$object = explode('/', $pageName);
		$file = array_shift($object);
		
		if(is_dir($path.$file))
		{			
			$this->directory .= $file."/";
			if(count($object) > 0)
			{
				$str = implode("/", $object);
				$this->pageView($str, $path.$file.DS);
			}
			else
			{
				$this->redirect_404("404");
			}
		}
		else
		{
			if(file_exists($path.$file.".php"))
			{
				$this->pageName = $this->directory.$file;
				
				if($this->_template != '')
				{
					include($this->path . 'template' . DS . $this->_template.'.php');
				}
				else
				{
					include($this->path . $this->pageName.'.php');
				}
			}
			else
			{
				$this->redirect_404("404");
			}

		}		
	}
	
	/*
	** Function to set page template
	*/
	public function setTemplate($templateName)
	{
		$this->_template = $templateName;
	}
	
	
	/*
	** Function to set page template
	*/
	public function redirect_404($url)
	{
        Router::redirect($url);
	}
	
	
	/*
	** Function to set page Title
	*/
	
	public function setTitle($title = '')
	{
		$PN = \explode(DS, $title);
		$PN = end($PN);
		
		if(strpos($PN, '_') !== false )
		{
			$val = explode('_', $PN);
			$this->pageTitle = SITE_TITLE." | ".ucfirst($val[0]).' '.ucfirst($val[1]);
		}
		else if(strpos($PN, '-') !== false)
		{
			$val = explode('-', $PN);
			$this->pageTitle = SITE_TITLE." | ".ucfirst($val[0]).' '.ucfirst($val[1]);
		}
		else
		{
			$this->pageTitle = SITE_TITLE." | ".strtoupper($PN);
		}
	}

	public static function getToken()
	{
		//return Security::csrfInput();

		$token = Security::generateToken();
		return '<input type="hidden" name="csrf_token" id="csrf_token" value="'.$token.'" />';
	}

	public static function checkSession()
	{
		if(!Sessions::exists('admin') && !Sessions::exists("login"))
		{
			Router::redirect("login");
		}
	}

	public static function isLoggedIn()
	{
		if(Sessions::exists("login") && Sessions::get("login") == true)
		{
			Router::redirect();
		}
	}

}
?>