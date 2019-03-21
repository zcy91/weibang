<?php
// database host
$db_host   = "127.0.0.1";

// database name
$db_name   = "weibang";

// database username
$db_user   = "root";

// database password
$db_pass   = "root";

// table prefix
$prefix    = "ecs_";

$timezone    = "UTC";

$cookie_path    = "/";

$cookie_domain    = "";

$session = "1440";
define('DEBUG_MODE', 3);
define('EC_CHARSET','utf-8');

if(!defined('ADMIN_PATH'))
{
define('ADMIN_PATH','admins');
}

define('AUTH_KEY', 'this is a key');

define('OLD_AUTH_KEY', '');

define('API_TIME', '2019-03-21 02:47:29');

?>
