<?php

class Cookies
{
	public static function set_cookies($name, $value, $duration)
	{
		if(setcookie($name, $value, time() + $duration, '/'))
		{
			return true;
		}
		return false;
	}
	
	public static function cookie_exists($name)
	{
		return isset($_COOKIE[$name]);
	}
	
	public static function unset_cookie($name)
	{
		unset($_COOKIE[$name]);
	}
	
	public static function cookie_enabled()
	{
		if(count($_COOKIE) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>
