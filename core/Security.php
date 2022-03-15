<?php

namespace Core;

use Core\Sessions;

class Security
{
	private static $encrypt_method = "AES-256-CBC";
	private static	$secret_key = '%jkdkgfvkX372&@kgfs1nmf*';
	private static	$secret_iv = 'my-very-secured-key';

	private $_db = null;

	
	/*********Encryption of data***************/
	public static function encrypt($string)
	{
		$output = false;
		// hash
		$key = hash('sha256', self::$secret_key);

		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', self::$secret_iv), 0, 16);
		$output = openssl_encrypt($string, self::$encrypt_method, $key, 0, $iv);
		$output = base64_encode($output);
		
		return $output;
	}
	
	/**********Decryption of data*************/
	public static function decrypt($string)
	{
		$output = false;
		// hash
		$key = hash('sha256', self::$secret_key);

		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', self::$secret_iv), 0, 16);
		$output = openssl_decrypt(base64_decode($string), self::$encrypt_method, $key, 0, $iv);
		
		return $output;
	}
	// Encrypt password
	public static function passwordEncrypt($data)
	{
		$options = [ 'cost' => 10, ];
		$password = password_hash($data, PASSWORD_BCRYPT, $options);
		return $password;
	}

	// Verify Password
	public static function VerifyPassword($password, $DBPassword)
	{
		if(!password_verify($password, $DBPassword))
		{
			return false;
		}
		
		return true;
	}
	
	public static function generateToken()
	{
		$token = base64_encode(openssl_random_pseudo_bytes(32));
		Sessions::set(['csrf_token' => $token]);
		return $token;
	}

	public static function checkToken($token)
	{
		if(Sessions::exists('csrf_token') && Sessions::get('csrf_token') == $token)
		{
			return true;
		}

		return false;
	}

	// public static function csrfInput()
	// {
	// 	self::generateToken();
	// 	return '<input type="text" name="csrf_token" id="csrf_token" value="'.Sessions::get("csrf_token").'" />';
	// }

	public static function sanitize($dirty) 
	{
		return htmlentities($dirty, ENT_QUOTES, 'UTF-8');
	}

	public static function posted_values($post) 
	{
		$clean_ary = [];

		foreach($post as $key => $value) 
		{
			$clean_ary[$key] = self::sanitize($value);
		}

		return $clean_ary;
	}
	
	public static function getUserAgent()
	{
		$uagent = $_SERVER['HTTP_USER_AGENT'];
		return $uagent;
	}
}
?>