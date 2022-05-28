<?php

namespace app\controllers\ajax\panel;


use app\core\System;
use app\core\system\shop\ShopController;
use app\models\panel\PluginModel;
use app\traits\Log;
use Exception;
use ZipArchive;

class CelenaUpdate{

    public function index(){

        if(!empty($_POST["action"])) self::updateSystem();
        die("info::error::Неизвестный запрос!");
    }


    /**
     * @name Обновление системы
     * =======================
     * @return void
     * @throws Exception
     */
    private function updateSystem(){

        $newVersion = ShopController::getUpdateVersion();

        if(!$newVersion) die("info::error::Обновление не найдено!");

        sleep(1);
        $NewZipSystem = ShopController::installUpdate();

        $updateZip = 'update_system_'.time().'.zip';

        $fp = fopen(ROOT . '/' . $updateZip, "w");
        fwrite($fp, $NewZipSystem);
        fclose($fp);

        $zip = new ZipArchive;
        if ($zip->open(ROOT . '/' . $updateZip) === TRUE) {

            $zip->extractTo(ROOT . '/');
            $zip->close();

        } else {

            Log::add("Ошибка загрузки плагина с сервера!", 2);
            die("info::error::Ошибка загрузки плагина с сервера!");
        }

        die("info::success::Обновил!");

        // удаляем временный архив
        unlink(ROOT . '/' . $NewZipSystem);

        $PI = 'app\plugins\\'.str_replace('/', '\\', $pluginBrandName).'\Init';

        if(class_exists($PI)){

            if(method_exists($PI, 'install')){

                $PluginInit = new $PI();
                $installed = $PluginInit->install();

                if($installed === true){

                    die("info::success::Все супер!");

                } else{

                    Log::add("Ошибка <b style='color:#e82a2a'>$installed</b> при установке плагина <b>$pluginBrandName</b>! Обратитесь к разработчику плагина.", 2);
                    die("info::error::Отсутствует при установке!<br>Обратитесь к разработчику плагина.");
                }

            } else{

                Log::add("Отсутствует метод установки в плагине <b>$pluginBrandName</b>! Обратитесь к разработчику плагина.", 2);
                die("info::error::Отсутствует метод установки!<br>Обратитесь к разработчику плагина.");
            }
        }

        if(strripos($result, "<script>") === false) die($result);
        else{

            Log::add("Обновление системы до версии <b>$newVersion</b>", 1);

            $script = '<script>
                $.server_say({say: "Плагин установлен!", status: "success"});
                setTimeout(function(){
                    window.location.href = "/'.CONFIG_SYSTEM["panel"].'/plugins/";
                }, 1000)
            </script>';

            System::script($script);
        }
    }

}