<?php

namespace app\controllers\ajax\panel;


use app\core\System;
use app\core\system\shop\ShopController;
use app\models\panel\PluginModel;
use app\traits\Log;
use Exception;
use ZipArchive;

class CelenaPlugin{

    use Log;

    public function index(){

        if(!empty($_POST["action"])){

            switch ($_POST["action"]){
                case 'getPlugin': self::getPlugin(); break;
                case 'install':   self::install(); break;
                case 'enable':    self::power(true); break;
                case 'disable':   self::power(false); break;
                case 'update':    self::update(); break;
                case 'remove':    self::remove(); break;
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
    private function install(){

        $plugin_id = intval($_POST["id"]);

        $pluginInfo = ShopController::getPlugin(intval($_POST["id"]), 'json');
        $pluginInfo = json_decode($pluginInfo);

        $result = ShopController::installPlugin($plugin_id);

        if(empty($result)) die("info::error::Плохая связь с сервером, попробуйте ещё раз");
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
                if(file_exists(ROOT . '/' . $pluginInstallZip))
                    unlink(ROOT . '/' . $pluginInstallZip);
                die("info::error::В плагине отсутствует папка с брендом и названием плагина!");
            }

            $zip->extractTo(ROOT . '/');
            $zip->close();

            // создаем файл кеша
            $cache = fopen(ROOT . '/app/cache/system/plugins_scan/' . $hashFile . '.txt', "w");
            flock($cache, LOCK_EX);
            fwrite($cache, trim($hashFileContent));
            flock($cache, LOCK_UN);
            fclose($cache);

            // добавляем в базу
            $PluginModel = new PluginModel();
            $PluginModel->addPlugin($plugin_id, $pluginBrandName, $pluginInfo->plugin->plugin_v, $hashFile);

        } else {

            self::addLog("Ошибка загрузки плагина с сервера!", 2);
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

                    // TODO IS DOUBLE
                    $script = '<script>
                        $.server_say({say: "Плагин установлен!", status: "success"});
                        setTimeout(function(){
                            window.location.href = "/'.CONFIG_SYSTEM["panel"].'/plugins/";
                        }, 1000)
                    </script>';

                    System::script($script);

                } else{

                    self::addLog("Ошибка <b style='color:#e82a2a'>$installed</b> при установке плагина <b>$pluginBrandName</b>! Обратитесь к разработчику плагина.", 2);
                    die("info::error::Отсутствует при установке!<br>Обратитесь к разработчику плагина.");
                }

            } else{

                self::addLog("Отсутствует метод установки в плагине <b>$pluginBrandName</b>! Обратитесь к разработчику плагина.", 2);
                die("info::error::Отсутствует метод установки!<br>Обратитесь к разработчику плагина.");
            }
        }

        if(strripos($result, "<script>") === false) die($result);
        else{

            $script = '<script>
                $.server_say({say: "Плагин установлен!", status: "success"});
                setTimeout(function(){
                    window.location.href = "/'.CONFIG_SYSTEM["panel"].'/plugins/";
                }, 1000)
            </script>';

            System::script($script);
        }
    }





    /**
     * @name Включение/выключение плагина
     * ==================================
     * @return void
     */
    private function power($power){

        $pluginId = intval($_POST["id"]);

        $PluginModel = new PluginModel();
        $PluginInfo = $PluginModel->getPluginById($pluginId, 'name');
        $activated = $PluginModel->power($pluginId, $power ? 1 : 0);

        if($activated){

            $pluginPath = 'app\plugins\\'.str_replace('/', '\\', $PluginInfo["name"]).'\Init';

            if(!class_exists($pluginPath)){
                self::addLog('В плагине <b>'.$PluginInfo["name"].'</b> отсутствует клас Init', 2);
                die("info::error::Ошибка плагина!<br>Смотрите логи...");
            }

            $PluginInit = new $pluginPath();
            $resultPower = ($power) ? $PluginInit->powerOn() : $PluginInit->powerOff();

            if($resultPower !== true){
                self::addLog('В плагине <b>'.$PluginInfo["name"].'</b> произошла ошибка при включении', 2);
                die("info::error::Ошибка плагина!<br>Смотрите логи...");
            }

            if($power){
                $script = '<script>
                    $.server_say({say: "Плагин активирован!", status: "success"});
                    $(\'[data-a="CelenaPlugin:action=enable&id='.$pluginId.'"]\').replaceWith(`<a href="#" class="btn btn_plugin_deactivate" data-a="CelenaPlugin:action=disable&id='.$pluginId.'">Выключить</a>`);
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000)
                </script>';
            } else{
                $script = '<script>
                    $.server_say({say: "Плагин отключен!", status: "success"});
                    $(\'[data-a="CelenaPlugin:action=disable&id='.$pluginId.'"]\').replaceWith(`<a href="#" class="btn btn_plugin_activate" data-a="CelenaPlugin:action=enable&id='.$pluginId.'">Активировать</a>`);
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000)
                </script>';
            }

            System::script($script);

        } else die("info::error::Не удалось активировать плагин!");
    }


    /**
     * @name обновление плагина
     * ========================
     * @return void
     * @throws Exception
     */
    private function update(){

        $pluginId = intval($_POST["plugin_id"]);
        $pluginPath = trim(strip_tags($_POST["plugin_path"]));
        $pluginVersion = trim(strip_tags($_POST["plugin_version"]));

        $NewZipSystem = ShopController::installUpdatePlugin($pluginId, $pluginVersion);

        $updateZip = 'update_plugin_'.round(microtime(true) * 1000).'.zip';

        $fp = fopen(ROOT . '/' . $updateZip, "w");
        fwrite($fp, $NewZipSystem);
        fclose($fp);

        $zip = new ZipArchive;
        if ($zip->open(ROOT . '/' . $updateZip) === TRUE) {

            $zip->extractTo(ROOT . '/');
            $zip->close();

        } else {

            self::addLog("Ошибка загрузки обновления плагина <b>$pluginPath</b> с сервера!", 2);
            die("info::error::Ошибка загрузки обновления плагина <b>$pluginPath</b> с сервера!");
        }

        // удаляем временный архив
        unlink(ROOT . '/' . $updateZip);

        $Update = 'app\plugins\\'.str_replace("/", "\\", $pluginPath).'\Init';

        if(class_exists($Update)){

            $UpdateClass = new $Update();

            if(method_exists($UpdateClass, 'update')){

                $updated = $UpdateClass->update();

                if($updated === true){

                    // удаляем директорию с обновлением
                    //System::removeDir(CORE . '/system/update');

                    $pluginSystem = json_decode(file_get_contents(APP . '/plugins/'.$pluginPath.'/system.json'));

                    $PluginModel = new PluginModel();
                    $PluginModel->updateVersion($pluginId, $pluginSystem->version);

                    $plugins_update = json_decode(CONFIG_SYSTEM["plugins_update"], true);

                    if(!empty($plugins_update[$pluginId])) unset($plugins_update[$pluginId]);

                    if(!empty($plugins_update)) System::editSystemConfig(["plugins_update" => $plugins_update]);
                    else System::editSystemConfig(["plugins_update" => $plugins_update], true);

                    //self::addLog('Обновление плагина <b>'.$pluginPath.'</b> успешно установлено!', 1);
                }

            } /*else{

                self::addLog('Возникла ошибка при установке обновления плагина <b>'.$pluginPath.'</b> !', 2);
                die("info::success::Ошибка при обновлении плагина!");
            }*/

        } else{

            self::addLog('Обновление плагина <b>'.$pluginPath.'</b> успешно установлено!', 1);
        }

        $script = '<script>
            $.server_say({say: "Обновление плагина <b>'.$pluginPath.'</b> успешно установлено!", status: "success"});
            $(".cel_tmp").remove();
        </script>';

        System::script($script);
    }





    /**
     * @name удаление плагина
     * ======================
     * @return void
     */
    private function remove(){

        $plugin_id = intval($_POST["id"]);
        $PluginModel = new PluginModel();
        $PluginInfo = $PluginModel->getPluginByPluginId($plugin_id);

        if(!empty($PluginInfo["name"])){

            // DeclareNames::FOLDERS

            $pluginClass = 'app\plugins\\'.str_replace('/', '\\', $PluginInfo["name"]).'\Init';
            $pluginClass = new $pluginClass();
            $resultClassDelete = $pluginClass->delete();

            if($resultClassDelete !== true){
                self::addLog('В плагине <b>'.$PluginInfo["name"].'</b> произошла ошибка при удалении', 2);
                die("info::error::Ошибка при удалении плагина!<br>Смотрите логи...");
            }

            #TODO отложим удаление загруженных через плагин файлов на попозже
            //$pluginFiles = file_get_contents(APP . '/cache/system/plugins/'.$PluginInfo["hashfile"].'.txt');

            if(file_exists(APP . '/plugins/'.$PluginInfo["name"]))
                System::removeDir(APP . '/plugins/'.$PluginInfo["name"]);

            if(file_exists(APP . '/models/plugins/'.$PluginInfo["name"]))
                System::removeDir(APP . '/models/plugins/'.$PluginInfo["name"]);

            if(file_exists(ROOT . '/templates/plugins/'.$PluginInfo["name"]))
                System::removeDir(ROOT . '/templates/plugins/'.$PluginInfo["name"]);

            if(file_exists(APP . '/cache/system/plugins/'.$PluginInfo["hashfile"].'.txt'))
                unlink(APP . '/cache/system/plugins/'.$PluginInfo["hashfile"].'.txt');

            $PluginModel->removePlugin($plugin_id);

            $script = '<script>
                $.server_say({say: "Плагин удален!", status: "success"});
                $(".cel_tmp").closest(".plugin_table").remove();
            </script>';

            System::script($script);

        }

        die("info::error::Плагин не найден!");
    }

}