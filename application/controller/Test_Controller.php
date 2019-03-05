<?php

class Test_Controller extends Controller
{
	
	public function __construct($controller, $method)
	{
		parent::__construct($controller, $method);
		$this->load->model('Test_Model'); // is your model
	}
	
	public function test()
	{
		// $this->Test_Model->functionName(); to use the model
	}
}

?>