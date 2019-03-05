<?php
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
	protected $_template = DEFAULT_LAYOUT;
	
	public $db;
	public $security;
	public $u_session;
	
	public $email;
	
	public function __construct()
	{
		$this->db = DB::getInstance();
		$this->security = new Security;
		$this->u_session = new Sessions;
		$this->file_handler = new File_Manager;
		$this->email = new Email;
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
				header("location: ".BASE_URL."404");
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
				header("location: ".BASE_URL."404");
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
	** Function to set page Title
	*/
	
	public function setTitle($title = '')
	{		
		$PN = '';
		
		if($title == '' || $title == null)
		{
			$this->pageTitle = SITE_TITLE;
		}
		else
		{
			$name = explode('/', $title);
			$no = count($name) - 1;
			$PN = $name[$no];
		}
		
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
}
?>