<?php

namespace app\core\protected;

use app\traits\Log;

class InstallApplications{

    private $params;

    public function __construct($params){

        $this->params = $params;
        /**
         * $params[1] = plugins, modules, ...
         * $params[2] = install
         * $params[3] = brand
         * $params[4] = plugin
         */
        switch ($this->params->urls[1]){

            case 'plugins':
                self::installPlugin();
                break;

            case 'modules':
                self::installModules();
                break;
        }
    }


    /**
     * @name запускаем установку плагина
     * =================================
     * @return void
     */
    public function installPlugin(){

        $pluginBrand = ucfirst($this->params->urls[3]);
        $pluginName = ucfirst($this->params->urls[4]);


        $path = 'app\controllers\plugins\\'.$pluginBrand.'\\'.$pluginName.'\init';

        if(class_exists($path)){

            $controller = new $path();
            $resultInstall = $controller->install();

            if($resultInstall === true){ // если установка прошла успешно, заносим в базу инфу

                Log::add('Установлен "плагин" <b>'.$pluginName.'</b> от разработчика <b>'.$pluginBrand.'</b>', 1);

                die("Plugin installed...");
            }

        } else{

            Log::add('Не удалось установить \'плагин\' <b>'.$pluginName.'</b> от разработчика <b>'.$pluginBrand.'</b>', 2);
            die("error::Не удается найти папку с плагином на сервере!");
        }
    }


    public function installModules(){

    }

}