<?php

namespace app\controllers\ajax\panel;


use app\core\System;
use app\core\system\shop\ShopController;
use app\models\panel\SystemModel;
use app\traits\Log;
use Exception;
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


    /**
     * @name Получение детальной информации о плагине
     * ==============================================
     * @return void
     */
    private function getPlugin(){

        $result = ShopController::getPlugin(intval($_POST["id"]));

        if(strripos($result, "<script>") === false) die($result);
        else System::script($result);
    }


    /**
     * @name Установка плагина
     * =======================
     * @return void
     * @throws Exception
     */
    private function installPlugin(){

        $plugin_id = intval($_POST["id"]);

        $result = ShopController::installPlugin($plugin_id);
        $pluginInstallZip = 'install_plugin_'.time().'.zip';

        $hashFile = $plugin_id.'_'.sha1(rand(100, 9999));
        $hashFileContent = '';

        $fp = fopen(ROOT . '/' . $pluginInstallZip, "w");
        fwrite($fp, $result);
        fclose($fp);

        $zip = new ZipArchive;
        if ($zip->open(ROOT . '/' . $pluginInstallZip) === TRUE) {

            $pluginBrandName = false;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);

                #TODO проверка на файл не работает из-за прав доступа скорей всего, нужно записать только файлы в кеш а не весь лист
                /*echo $filename."\n";

                print_r(mime_content_type($filename));
                echo $filename."\n\n";*/

                //if(is_file($filename)){

                    // получаем бренд и имя плагина
                    if(!$pluginBrandName && strripos($filename, "app/plugins/") !== false){
                        $pluginBrandName_ = explode("/", $filename);
                        if(!empty($pluginBrandName_[2]) && !empty($pluginBrandName_[3])){
                            $pluginBrandName = $pluginBrandName_[2].'/'.$pluginBrandName_[3];
                            unset($pluginBrandName_);
                        }
                    }

                    $hashFileContent .= $filename."\n";
                //}
            }

            if(empty($pluginBrandName)){

                $zip->close();
                unlink(ROOT . '/' . $pluginInstallZip);
                die("info::error::В плагине отсутствует папка брендом и названием плагина!");
            }

            $zip->extractTo(ROOT . '/');
            $zip->close();

            // создаем файл кеша
            $cache = fopen(ROOT . '/app/cache/system/plugins/' . $hashFile . '.txt', "w");
            flock($cache, LOCK_EX);
            fwrite($cache, trim($hashFileContent));
            flock($cache, LOCK_UN);
            fclose($cache);
            
            // добавляем в базу
            $SystemModel = new SystemModel();
            $SystemModel->addPlugin($plugin_id, $pluginBrandName, $hashFile);

        } else {

            Log::add("Ошибка загрузки плагина с сервера!", 2);
            die("info::error::Ошибка загрузки плагина с сервера!");
        }

        // удаляем временный архив
        unlink(ROOT . '/' . $pluginInstallZip);

        $PI = 'app\plugins\\'.str_replace('/', '\\', $pluginBrandName).'\Init';

        if(class_exists($PI)){

            if(method_exists($PI, 'install')){

                $PluginInit = new $PI();
                $installed = $PluginInit->install();

                if($installed === true){

                    die("info::success::Все заебись!");

                } else{

                    Log::add("Ошибка <b style='color:#e82a2a'>$installed</b> при установке плагина <b>$pluginBrandName</b>! Обратитесь к разработчику плагина.", 2);
                    die("info::error::Отсутствует при установке!<br>Обратитесь к разработчику плагина.");
                }

            } else{

                Log::add("Отсутствует метод установки в плагине <b>$pluginBrandName</b>! Обратитесь к разработчику плагина.", 2);
                die("info::error::Отсутствует метод установки!<br>Обратитесь к разработчику плагина.");
            }
        }

        die("info::success::Ok");

        //if(strripos($result, "<script>") === false) die($result);
        //else System::script($result);
    }

}