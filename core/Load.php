<?php
class Load
{
	public function model($model)
	{
		$path = ROOT_DIR . DS . 'application'  . DS . 'model' . DS . $model.'.php';
		if(file_exists($path))
		{
			require_once($path);
			
			if(class_exists($model))
			{
				$registry = Registry::getInstance();
				$registry->$model = new $model;
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}
?>