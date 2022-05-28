<?php

namespace app\controllers\ajax\panel;


use app\core\System;
use app\core\system\shop\ShopController;
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

            Log::add("Ошибка загрузки обновления <b>$newVersion</b> с сервера!", 2);
            die("info::error::Ошибка загрузки обновления <b>$newVersion</b> с сервера!");
        }

        // удаляем временный архив
        unlink(ROOT . '/' . $updateZip);

        $Update = 'app\core\system\update\Update';

        if(class_exists($Update)){

            $UpdateClass = new $Update();

            if(method_exists($UpdateClass, 'update')){

                $updated = $UpdateClass->update();

                if($updated === true){

                    // удаляем директорию с обновлением
                    //System::removeDir(CORE . '/system/update');

                    Log::add('Обновление <b>'.$newVersion.'</b> успешно установлено!', 1);
                }

            }  else{

                Log::add('Возникла ошибка при установке обновления <b>'.$newVersion.'</b> !', 2);
                die("info::success::Ошибка при обновлении!");
            }

        } else{

            Log::add('Обновление <b>'.$newVersion.'</b> успешно установлено!', 1);
        }

        sleep(1);
        $content = ShopController::getUpdate($newVersion);

        $script = '<script>
            $.server_say({say: "Обновление <b>'.$newVersion.'</b> успешно установлено!", status: "success"});
            $("#space").html(`'.$content.'`);
        </script>';

        System::script($script);
    }

}