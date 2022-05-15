<?php

use app\core\system\install\Install;

define("ROOT", dirname(__FILE__));
const CORE = ROOT . '/app/core';

require 'vendor/autoload.php';

spl_autoload_register(function($class){
    $path = str_replace('\\', '/', $class . '.php');
    if(file_exists($path)){
        require $path;
    }
});

session_start();

new Install();