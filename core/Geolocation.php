<?php
/*
Class : Geoplugin library
Author : Geoplugin
Accessed : 21/06/2018
URL : https://www.geoplugin.com/webservices/php


-- This class was customed to suit the need of JShopon Services
-- The Geoplugin library is intended to provide the best services,...
per user geographical location

author - Bright O. Eguavoen

The default language is English 'en'
supported languages:
de (German)
en (English - default)
es (Spanish)
fr (French)
ja (Japanese)
pt-BR (Portugese, Brazil)
ru (Russian)
zh-CN (Chinese, Zn)

To change the language to e.g. Japanese, use:
$geoplugin->lang = 'ja'

*/

class Geolocation
{
	public $ip = null;
	public $currency = BASE_CURRENCY;
	public $lang = "en";
	
	public $city = null;
	public $countryName = null;
	public $countryCode = null;
	public $region = null;
	public $regionCode = null;
	public $regionName = null;
	public $continentCode = null;
	public $continentName = null;
	public $longitude = null;
	public $latitude = null;
	public $origin = null;
	public $postal_code = null;
	public $subdivision = null;
	public $timezone = null;
	public $currencyCode = null;
	public $currencySymbol = null;
	public $currencyConverter = null;
	public $locationAccuracyRadius = null;
	
	
	/*public function __construct()
	{
		
	}*/
	
	/* 
	@@ Get user information using IP
	*/
	public function getLocation($ip = null)
	{
		if(is_null($ip))
		{
			$this->ip = $_SERVER['REMOTE_ADDR'];
		}
		else
		{
			$this->ip = $ip;
		}
		
		//$host = 'http://www.geoplugin.net/php.gp?ip='.$this->ip.'&base_currency='.$this->currency.'&lang='.$this->lang;
		$host = 'http://www.geoplugin.net/php.gp?ip='.$this->ip.'&base_currency='.$this->currency.'&lang='.$this->lang;
		
		$data = array();
		
		$response = $this->_fetchData($host);
		
		$data = unserialize($response);
		
		$this->city = $data["geoplugin_city"];
		$this->countryName = $data["geoplugin_countryName"];
		$this->countryCode = $data["geoplugin_countryCode"];
		$this->region = $data["geoplugin_region"];
		$this->regionCode = $data["geoplugin_regionCode"];
		$this->regionName = $data["geoplugin_regionName"];
		$this->continentCode = $data["geoplugin_continentCode"];
		$this->continentName = $data["geoplugin_continentName"];
		$this->longitude = $data["geoplugin_longitude"];
		$this->latitude = $data["geoplugin_latitude"];
		$this->timezone = $data["geoplugin_timezone"];
		$this->currencyCode = $data["geoplugin_currencyCode"];
		$this->currencySymbol = $data["geoplugin_currencySymbol"];
		$this->currencyConverter = $data["geoplugin_currencyConverter"];
		$this->locationAccuracyRadius = $data["geoplugin_locationAccuracyRadius"];
		
	}
	
	/*
	** Fetch data from geolocation api using...
	** Curl or file get contents
	** $host is the url
	*/
	private function _fetchData($host)
	{
		$response = "";
		
		if (function_exists('curl_init')) 
		{						
			//use cURL to fetch data
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $host);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'geoPlugin PHP Class v1.1');
			$response = curl_exec($ch);
			curl_close ($ch);
		} 
		else if(ini_get('allow_url_fopen')) 
		{			
			//fall back to fopen()
			$response = file_get_contents($host, 'r');
		} 
		else 
		{
			trigger_error ('geoPlugin class Error: Cannot retrieve data. Either compile PHP with cURL support or enable allow_url_fopen in php.ini ', E_USER_ERROR);
			return;
		}
		
		return $response;
	}
	
	public function currencyConverter($amount, $float=2, $symbol=true, $num_format = true)
	{
		//easily convert amounts to geolocated currency.
		if (!is_numeric($this->currencyConverter) || $this->currencyConverter == 0) 
		{
			trigger_error('geoPlugin class Notice: currencyConverter has no value.', E_USER_NOTICE);
			//return $amount;
		}
		
		if ( !is_numeric($amount) ) 
		{
			trigger_error('geoPlugin class Warning: The amount passed to geoPlugin::convert is not numeric.', E_USER_WARNING);
			//return $amount;
		}
		
		if ( $num_format === true ) 
		{
			if ( $symbol === true ) 
			{
				//return $this->currencySymbol . number_format(round( ($amount * $this->currencyConverter), $float ));
				return $this->currencySymbol . number_format(($amount * $this->currencyConverter),$float);
			} 
			else 
			{
				//return number_format(round( ($amount * $this->currencyConverter), $float ));
				return number_format(($amount * $this->currencyConverter), $float);
			}
		}
		else
		{
			return round(($amount * $this->currencyConverter), $float);
		}
	}
	
	function nearby($radius=10, $limit=null) 
	{

		if ( !is_numeric($this->latitude) || !is_numeric($this->longitude) ) 
		{
			trigger_error ('geoPlugin class Warning: Incorrect latitude or longitude values.', E_USER_NOTICE);
			return array( array() );
		}
		
		$host = "http://www.geoplugin.net/extras/nearby.gp?lat=" . $this->latitude . "&long=" . $this->longitude . "&radius={$radius}";
		
		if ( is_numeric($limit) )
			$host .= "&limit={$limit}";
			
		return unserialize( $this->_fetchData($host) );

	}
	
	
}
?>