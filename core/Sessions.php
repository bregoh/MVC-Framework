<?php

namespace Core;


class Sessions
{
	
	public static function exists($name)
	{
		if(isset($_SESSION[$name]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public static function get($name)
	{
		return $_SESSION[$name];
	}
	
	public static function set($dataArray)
	{
		foreach($dataArray as $key => $value)
		{
			$_SESSION[$key] = $value;
		}
	}
	
	public static function unset($data)
	{
		if(is_array($data))
		{
			foreach($data as $key)
			{
				if(self::exists($key))
				{
                    unset($_SESSION[$key]);
				}
            }
            
            return true;
		}
		else
		{
			unset($_SESSION[$data]);	
		}
		return true;
	}
}
?>
