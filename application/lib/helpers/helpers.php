<?php

$config = CONFIG;
$env = 'sandbox';

define('BASE_URL', $config[$env]["base_url"]); // https://www.example.com/

define('DEBUG', $config[$env]["debug"]);

define("CURLVERSION", $config["curlVersion"]);

define("PROXY", $config["proxy"]["set"]);
define("PROXY_COUNTRY", $config["proxy"]["country"]);

define('DEFAULT_CONTROLLER', $config[$env]["default_controller"]); // Default controller if error found
define('DEFAULT_METHOD', $config[$env]["default_method"]); // Default controller if error found

define('DEFAULT_PAGE', $config[$env]["default_page"]);
define('DEFAULT_TEMPLATE', $config[$env]["default_template"]); // if no layout is set in the view class

define('SITE_TITLE', $config["title"]); // default site title if not set
define("PROJECT_ROOT", $config[$env]["project_root"]); // use "/" for live server

define("BASE_CURRENCY", $config["base_currency"]);

define("PAYPAL_URL", $config[$env]["paypal"]["url"]);
define("PAYPAL_ID", $config[$env]["paypal"]["id"]);
define("PAYPAL_SECRET", $config[$env]["paypal"]["secret"]);

define("STRIPE_API_KEY",$config[$env]["stripe"]["secret"]);
define("STRIPE_WH_KEY",$config[$env]["stripe"]["webhook"]);

define("PAYSTACK_API_KEY",$config[$env]["paystack"]["api_key"]);
define("PAYSTACK_API_PK",$config[$env]["paystack"]["api_pk"]);
        
    /**
     * dnd
     *
     * @param  mixed $data
     * @return void
     */
    function dnd($data)
    {
        echo "<pre>";
            var_dump($data);
        echo "</pre>";
        die();
    }
?>