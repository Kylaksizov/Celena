<?php

namespace app\controllers\ajax\panel;


use app\core\System;
use app\core\system\shop\ShopController;
use app\models\panel\SystemModel;
use app\traits\Log;
use ZipArchive;

class CelenaPlugin{

    public function index(){

        if(!empty($_POST["action"])){

            switch ($_POST["action"]){
                case 'getPlugin': self::getPlugin(); break;
                case 'install': self::installPlugin(); break;
            }
        }

        die("info::error::Неизвестный запрос!");
    }


    private function getPlugin(){

        $result = ShopController::getPlugin(intval($_POST["id"]));

        if(strripos($result, "<script>") === false) die($result);
        else System::script($result);
    }


    private function installPlugin(){

        $plugin_id = intval($_POST["id"]);

        $result = ShopController::installPlugin($plugin_id);
        $pluginInstallName = 'install_plugin'.time().'.zip';

        $hashFile = $plugin_id.'_'.sha1(rand(100, 9999));
        $hashFileContent = '';

        $fp = fopen(ROOT . '/' . $pluginInstallName, "w");
        fwrite($fp, $result);
        fclose($fp);

        $zip = new ZipArchive;
        if ($zip->open(ROOT . '/' . $pluginInstallName) === TRUE) {

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);

                if(is_file($filename)) $hashFileContent .= $filename."\n";
            }

            $zip->extractTo(ROOT . '/');
            $zip->close();

            // создаем файл кеша
            $cache = fopen(ROOT . '/app/cache/system/plugins/' . $hashFile . '.txt', "w");
            flock($cache, LOCK_EX);
            fwrite($cache, trim($hashFileContent));
            flock($cache, LOCK_UN);
            fclose($cache);

            $SystemModel = new SystemModel();
            $SystemModel->addPlugin($plugin_id, $hashFile);

        } else {

            Log::add("Ошибка загрузки плагина с сервера!", 2);
            die("info::error::Ошибка загрузки плагина с сервера!");
        }

        unlink(ROOT . '/' . $pluginInstallName);

        die("info::success::загрузил");

        if(strripos($result, "<script>") === false) die($result);
        else System::script($result);
    }

}