<?php

use app\core\Router;

$mem_start = microtime(true);

//ob_start();

//const NEX = true;
define("ROOT", dirname(__FILE__));
const APP = ROOT . '/app';
const CORE = ROOT . '/app/core';
const CONTROLLERS = ROOT . '/app/controllers';
//const PLUGIN_DIR = ROOT . '/app';

require 'vendor/autoload.php';

spl_autoload_register(function($class){
    $path = str_replace('\\', '/', $class . '.php');
    if(file_exists($path)){
        require $path;
    }
});

session_start();

$router = new Router();
$router->run();

//ob_end_flush();

//echo memory_get_usage() - $mem_start;