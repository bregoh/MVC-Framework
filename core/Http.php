<?php

namespace Core;

class Http
{
    public static $curl;
    public $url = null;
    public $fields;
    public $headers;
    public $requestType = null;
    public $httpError = array();
    public $response = null;
    public $curlOptions = [];

    public function __construct()
    {
        Http::$curl = curl_init();
    }

    public function setHttpOptions($url = null, $requestType = "GET", $fields = null, $header = [])
    {
        if(!is_null($fields))
        {
            $this->curlOptions[CURLOPT_POSTFIELDS] = $fields;
        }

        if(!empty($header))
        {
            $this->curlOptions[CURLOPT_HTTPHEADER] = $header;
        }

        $this->curlOptions[CURLOPT_CUSTOMREQUEST] = $requestType;
        $this->url = $url;

        return $this;
    }

    private function _validateRequest()
    {
        if($this->url == null)
        {
            $this->httpError = json_encode(array("message" => "Request URL is null"));
        }

        if(!empty( $this->httpError ))
        {
            return false;
        }

        return true;
    }

    public function executeHttpRequest()
    {
        if (function_exists('curl_init')) 
		{						
			$this->_executeCurlRequest();
		} 
		else if(ini_get('allow_url_fopen')) 
		{			
			$this->_executeFileRequest();
        }
        
        return $this;
    }

    private function _executeCurlRequest()
    {
        $curl = curl_init();
        $this->curlOptions[CURLOPT_URL] = $this->url;
        $this->curlOptions[CURLOPT_RETURNTRANSFER] = true;
        $this->curlOptions[CURLOPT_ENCODING] = "utf-8";
        $this->curlOptions[CURLOPT_MAXREDIRS] = 10;
        $this->curlOptions[CURLOPT_TIMEOUT] = 30;
        $this->curlOptions[CURLOPT_HTTP_VERSION] = CURLVERSION;

        curl_setopt_array($curl, $this->curlOptions);

        if($this->_validateRequest())
        {
            $res = curl_exec($curl);
            $err = curl_error($curl);
            $errno = curl_errno($curl);
            if ($errno) 
            {
                $this->httpError = json_encode(array("message" => $err));
            }
            else
            {
                $this->response = $res;
            }
        }
        else
        {
            $this->httpError = json_encode(array("message" => "Request not validated, "));
        }

        curl_close($curl);
    }

    private function _executeFileRequest ()
    {
        if($this->_validateRequest())
        {
            $this->response = file_get_contents($this->url, 'r');
        }
    }

    public function httpResponse()
    {
        return $this->response;
    }
    public function httpError()
    {
        return $this->httpError;
    }
}
?>