<?php
define('_DIR_ROOT', __DIR__);

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $web_root = 'https://' . $_SERVER['HTTP_HOST'];
} else {
    $web_root = 'http://' . $_SERVER['HTTP_HOST'];
}
define('_WEB_ROOT', $web_root);

//auto load config
$config_dir = scandir('app/configs');

if (!empty($config_dir)) {
    foreach ($config_dir as $item) {
        if ($item != '.' && $item != '..' && file_exists('app/configs/' . $item)) {
            require_once 'app/configs/' . $item;
        }
    }
}

require_once 'core/Routes.php';
require_once 'core/Session.php';
require_once 'app/App.php';
// var_dump($config['database']);

// check config and load database
if (!empty($config['database'])) {
    $db_config = array_filter($config['database']);
    if (!empty($db_config)) {
        require_once 'core/Connection.php';
        require_once 'core/QueryBuilder.php';
        require_once 'core/Database.php';
        require_once 'core/DB.php';
    }
}

require_once 'core/Helper.php'; // load core helper
// load all helpers
$dirHelpers = scandir('app/helpers');
if (!empty($dirHelpers)) {
    foreach ($dirHelpers as $item) {
        if ($item != '.' && $item != '..' && file_exists('app/helpers/' . $item)) {
            require_once 'app/helpers/' . $item;
        }
    }
}

require_once 'core/Model.php'; //load base model
require_once 'core/Controller.php'; // load base controller
require_once 'core/Request.php'; // load request
require_once 'core/Response.php'; // load response
