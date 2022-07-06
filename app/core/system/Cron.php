<?php

namespace app\core\system;

use app\core\System;
use app\core\system\shop\ShopController;
use app\models\panel\PluginModel;
use app\traits\panel\Notify;
use Exception;

class Cron{

    use Notify;


    CONST TIME_UPDATES = 1800;



    public function __construct(){

        self::getSystemUpdates();
        self::getPluginsUpdates();
    }


    /**
     * @name update system
     * ===================
     * @return void
     */
    private function getSystemUpdates(){
        
        if(empty($_COOKIE["SystemCronUpdates"]) && empty(CONFIG_SYSTEM["system_update"])){

            $newVersion = ShopController::getUpdateVersion();

            if(!empty($newVersion)){
                self::addNotify("Новое обновление системы", "Вышла версия Celena ($newVersion)", "/{panel}/system/updates/");
                System::addSystemConfig(["system_update" => $newVersion]);
            }

            SetCookie("SystemCronUpdates", 1, time() + self::TIME_UPDATES, "/");
        }
    }


    /**
     * @name update plugins
     * ====================
     * @return void
     * @throws Exception
     */
    private function getPluginsUpdates(){

        if(empty($_COOKIE["PluginsCronUpdates"])){

            $PluginModel = new PluginModel();
            $Plugins = $PluginModel->getMyPluginsInfo("plugin_id, version");

            if(!empty($Plugins)){

                $infoPlugins = ShopController::getUpdatePlugins(json_encode($Plugins, JSON_FORCE_OBJECT));

                if(!empty($infoPlugins)){

                    $infoPluginsTestArray = json_decode($infoPlugins, true);

                    if(is_array($infoPluginsTestArray)){

                        if(!empty(CONFIG_SYSTEM["plugins_update"])) System::editSystemConfig(["plugins_update" => $infoPlugins]);
                        else System::addSystemConfig(["plugins_update" => $infoPlugins]);
                    }
                }
            }

            SetCookie("PluginsCronUpdates", 1, time() + (self::TIME_UPDATES * 2), "/");
        }
    }
}