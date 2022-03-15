<?php

/////////////////////////////////////////////////////////////////
///															////
///////////////////////////////////////////////////////////////

$config = array(
    "title" => "",
    "base_currency" => "USD",
    "curlVersion" => CURL_HTTP_VERSION_1_1,
    "proxy" => array(
        "set" => false,
        "country" => ""
    ),
    "sandbox" => array(
        "debug" => true,
        "base_url" => "http://localhost:8080/",
        "default_controller" => "Pages",
        "default_method" => "index",
        "default_page" => "index",
        "default_template" => "",
        "project_root" => "/",
        "db" => array(
            "host" => "127.0.0.1",
            "user" => "root",
            "password" => "",
            "dbname" => ""
        ),
        "paypal" => array(
            "url" => "",
            "id" => "",
            "secret" => ""
        ),
        "stripe" => array(
            "secret" => "",
            "webhook" => ""
        ),
        "paystack" => array(
            "api_key" => "",
            "api_pk" => ""
        )
    ),
    "live" => array(
        "debug" => false,
        "base_url" => "",
        "default_controller" => "Pages",
        "default_method" => "index",
        "default_page" => "index",
        "default_template" => "",
        "project_root" => "/",
        "db" => array(
            "host" => "",
            "user" => "",
            "password" => "",
            "dbname" => ""
        ),
        "paypal" => array(
            "url" => "",
            "id" => "",
            "secret" => ""
        ),
        "stripe" => array(
            "secret" => "",
            "webhook" => ""
        ),
        "paystack" => array(
            "api_key" => "",
            "api_pk" => ""
        )
    )
);

/////////////////////////////////////////////////////////////////
///															////
///////////////////////////////////////////////////////////////
define("CONFIG", $config);
define('DB_DRIVER', 'MySQLi');
define("ROOT_PATH", realpath(dirname(__FILE__) . DS."..".DS)); // Path to a file

?>
