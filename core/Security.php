<?php

class Security
{
	private $encrypt_method = "AES-256-CBC";
	private	$secret_key = '';
	private	$secret_iv = '';
	
	
	/*********Encryption of data***************/
	function encrypt($string)
	{
		$output = false;
		// hash
		$key = hash('sha256', $this->secret_key);

		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $this->secret_iv), 0, 16);
		$output = openssl_encrypt($string, $this->encrypt_method, $key, 0, $iv);
		$output = base64_encode($output);
		
		return $output;
	}
	
	/**********Decryption of data*************/
	function decrypt($string)
	{
		$output = false;
		// hash
		$key = hash('sha256', $this->secret_key);

		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $this->secret_iv), 0, 16);
		$output = openssl_decrypt(base64_decode($string), $this->encrypt_method, $key, 0, $iv);
		
		return $output;
	}
	// Encrypt password
	function passwordEncrypt($data)
	{
		$options = [ 'cost' => 10, ];
		$password = password_hash($data, PASSWORD_BCRYPT, $options);
		return $password;
	}
	function VerifyPassword($password, $DBPwd)
	{
		if(password_verify($password, $DBPwd))
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