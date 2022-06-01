<?php

namespace app\controllers\ajax\panel;


use app\core\Base;
use app\core\System;
use app\models\panel\ModuleModel;
use app\traits\Log;
use Exception;

class CelenaModule{


    public function index(){

        if(!empty($_POST["name"])) self::create();

        if(!empty($_POST["action"])){

            switch ($_POST["action"]){
                case 'enable':    self::power(true); break;
                case 'disable':   self::power(false); break;
                case 'remove':    self::remove(); break;
            }
        }

        die("info::error::Неизвестный запрос!");
    }




    /**
     * @name удаление плагина
     * ======================
     * @return void
     */
    private function create(){

        preg_match('/edit\/([0-9]+)\//is', $_GET["url"], $id);
        if(!empty($id[1])) $id = intval($id[1]);

        $name = trim(htmlspecialchars(strip_tags($_POST["name"])));
        $version = !empty($_POST["version"]) ? trim(htmlspecialchars(strip_tags($_POST["version"]))) : die("Заполните версию плагина");
        $cv = !empty($_POST["cv"]) ? trim(htmlspecialchars(strip_tags($_POST["cv"]))) : null;
        $descr = !empty($_POST["descr"]) ? trim(htmlspecialchars(strip_tags($_POST["descr"]))) : '';
        $comment = !empty($_POST["comment"]) ? trim(htmlspecialchars(strip_tags($_POST["comment"]))) : '';
        $status = !empty($_POST["status"]) ? 1 : 0;

        if(mb_substr_count($version, ".") != 2) die("Укажите версию плагина в формате X.X.X");
        if(!empty($cv)){
            if(mb_substr_count($cv, ".") != 2) die("Укажите версию Celena в формате X.X.X");
        }

        $base_install = !empty($_POST["base"]["install"]) ? trim(htmlspecialchars(strip_tags($_POST["base"]["install"]))) : '';
        $base_update  = !empty($_POST["base"]["update"]) ? trim(htmlspecialchars(strip_tags($_POST["base"]["update"]))) : '';
        $base_on      = !empty($_POST["base"]["on"]) ? trim(htmlspecialchars(strip_tags($_POST["base"]["on"]))) : '';
        $base_off     = !empty($_POST["base"]["off"]) ? trim(htmlspecialchars(strip_tags($_POST["base"]["off"]))) : '';
        $base_del     = !empty($_POST["base"]["del"]) ? trim(htmlspecialchars(strip_tags($_POST["base"]["del"]))) : '';

        $poster = '';

        $ModuleModel = new ModuleModel();

        if(empty($id))
            $mid = $ModuleModel->add(null, $name, $descr, $version, $cv, $poster, $base_install, $base_update, $base_on, $base_off, $base_del, $comment, $status);
        else{

            $mid = $id;
            $ModuleModel->editFields(
                $id,
                [
                    "name" => $name,
                    "descr" => $descr,
                    "version" => $version,
                    "cv" => $cv,
                    "poster" => $poster,
                    "base_install" => $base_install,
                    "base_update" => $base_update,
                    "base_on" => $base_on,
                    "base_off" => $base_off,
                    "base_del" => $base_del,
                    "comment" => $comment,
                    "status" => $status
                ]
            );
        }

        if(!empty($_POST["filePath"])){

            if(!empty($id)) $ModuleModel->clear($id);

            foreach ($_POST["filePath"] as $fileKey => $filePath) {

                foreach ($_POST["actionsFile"][$fileKey] as $actionKey => $action) {

                    if(!empty($action)){

                        $searchcode = !empty($_POST[$fileKey]["search"][$actionKey]) ? $_POST[$fileKey]["search"][$actionKey] : '';
                        $replacecode = !empty($_POST[$fileKey]["act"][$actionKey]) ? $_POST[$fileKey]["act"][$actionKey] : '';

                        $ModuleModel->addAction($mid, $filePath, $action, $searchcode, $replacecode);
                    }
                }

            }
        }

        header("Location:".$_SERVER["HTTP_REFERER"]);
        die();
    }


    /**
     * @name Включение/выключение модуля
     * =================================
     * @param $power
     * @return void
     * @throws Exception
     */
    private function power($power){

        $moduleId = intval($_POST["id"]);

        $ModuleModel = new ModuleModel();
        $ModuleInfo = $ModuleModel->getModuleMain($moduleId);
        $ModuleModel->power($moduleId, $power ? 1 : 0);

        if($power){

            if(!empty($ModuleInfo["base_on"])) Base::run(str_replace("{prefix}", PREFIX, $ModuleInfo["base_on"]));

            Log::add('Модуль <b>'.$ModuleInfo["name"].'</b> включен', 1);

            $script = '<script>
                $.server_say({say: "Плагин активирован!", status: "success"});
                $(\'[data-a="CelenaModule:action=enable&id='.$moduleId.'"]\').replaceWith(`<a href="#" class="btn btn_module_deactivate" data-a="CelenaModule:action=disable&id='.$moduleId.'">Выключить</a>`);
            </script>';

            System::script($script);

        } else{

            if(!empty($ModuleInfo["base_off"])) Base::run(str_replace("{prefix}", PREFIX, $ModuleInfo["base_off"]));

            Log::add('Модуль <b>'.$ModuleInfo["name"].'</b> отключен', 1);

            $script = '<script>
                $.server_say({say: "Плагин отключен!", status: "success"});
                $(\'[data-a="CelenaModule:action=disable&id='.$moduleId.'"]\').replaceWith(`<a href="#" class="btn btn_module_activate" data-a="CelenaModule:action=enable&id='.$moduleId.'">Активировать</a>`);
            </script>';

            System::script($script);
        }
    }

}