<?php

namespace app\controllers\ajax\panel;


use app\core\System;
use app\core\system\shop\ShopController;
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

        $result = ShopController::installPlugin(intval($_POST["id"]));
        //copy($result, ROOT . '/');

        $pluginInstallName = 'install_plugin'.time().'.zip';

        $hashFile = time().'_'.sha1(rand(100, 9999)).'.cache';
        $hashFileContent = '';

        $fp = fopen(ROOT . '/' . $pluginInstallName, "w");
        fwrite($fp, $result);
        fclose($fp);

        $zip = new ZipArchive;
        if ($zip->open(ROOT . '/' . $pluginInstallName) === TRUE) {

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);

                if(is_file($filename)){
                    $hashFileContent .= $filename."\n";
                }
            }

            $zip->extractTo(ROOT . '/');
            $zip->close();

            // создаем файл кеша
            $fp = fopen(ROOT . '/app/cache/system/plugins/' . $hashFile, "w");
            fwrite($fp, trim($hashFileContent));
            fclose($fp);

            die("info::success::загрузил");

        } else {
            die("info::error::не загрузил");
        }

        if(strripos($result, "<script>") === false) die($result);
        else System::script($result);
    }

}