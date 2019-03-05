<?php

/////////////////////////////////////////////////////////////////
///															////
///															///
///															///
///////////////////////////////////////////////////////////////
define('DEBUG', true);
define('DEFAULT_CONTROLLER','Pages'); // Default controller if error found
define('DEFAULT_METHOD','index'); // Default controller if error found
define('DEFAULT_PAGE', 'index');
define('DEFAULT_LAYOUT', 'default_template'); // if no layout is set in the view class
define('SITE_TITLE', ''); // default site title if not set
define("ROOT_PATH", realpath(dirname(__FILE__) . DS."..".DS));

define("BASE_CURRENCY", "USD");
define('DB_DRIVER', 'MySQLi');
/////////////////////////////////////////////////////////////////
///															////
///															///
///															///
///////////////////////////////////////////////////////////////

?>
