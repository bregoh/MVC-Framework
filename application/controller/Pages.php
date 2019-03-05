<?php
class Pages extends Controller
{
	public $params;
	public function __construct($controller, $method)
	{
		parent::__construct($controller, $method);
		
	}
	
	/**
	*** $params are page links
	**/
	public function index($params)
	{
		$this->view->setTemplate('');
		$this->view->setTitle($params);
		$this->view->pageView($params);
	}
}
?>