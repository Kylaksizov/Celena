<?php

namespace app\controllers\ajax\panel;


use app\core\Base;
use app\core\System;
use app\core\system\modules\Modules;
use app\models\panel\ModuleModel;
use app\traits\Functions;
use app\traits\Log;
use Exception;
use Intervention\Image\ImageManager;

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
     * @name создание модуля
     * =====================
     * @return void
     * @throws Exception
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

        if(!empty($_FILES["icon"]))
            $poster = $this->uploadImages();

        $routes = !empty($_POST["route"]) ? json_encode($_POST["route"], JSON_UNESCAPED_UNICODE) : '';

        $ModuleModel = new ModuleModel();

        if(!empty($id)) $ModuleInfo = $ModuleModel->getInfo($id);

        // если были изменены или добавлены роуты
        if(!empty($ModuleInfo["routes"]) && $ModuleInfo["routes"] != $routes || empty($id))
            Modules::buildRoutes($routes, !empty($ModuleInfo["routes"]) ? $ModuleInfo["routes"] : []); // перестраиваем роуты

        if(empty($id)){

            $mid = $ModuleModel->add(null, $name, $descr, $version, $cv, $poster, $base_install, $base_update, $base_on, $base_off, $base_del, $routes, $comment, $status);

            if(!empty($base_install)) Base::run(str_replace("{prefix}", PREFIX, $base_install));
        }
        else{

            $params = [
                "name" => $name,
                "descr" => $descr,
                "version" => $version,
                "cv" => $cv,
                "base_install" => $base_install,
                "base_update" => $base_update,
                "base_on" => $base_on,
                "base_off" => $base_off,
                "base_del" => $base_del,
                "routes" => $routes,
                "comment" => $comment,
                "status" => $status
            ];

            if(!empty($poster)) { // удаляем старую иконку
                $params["poster"] = $poster;
                if(!empty($ModuleInfo["poster"])) unlink(ROOT . '/uploads/modules/' . $ModuleInfo["poster"]);
            }

            $mid = $id;
            $ModuleModel->editFields(
                $id,
                $params
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

        Modules::initialize();

        header("Location: /".CONFIG_SYSTEM["panel"]."/modules/");
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

        Modules::initialize();

        if($power){

            Modules::buildRoutes($ModuleInfo["routes"]); // перестраиваем роуты

            if(!empty($ModuleInfo["base_on"])) Base::run(str_replace("{prefix}", PREFIX, $ModuleInfo["base_on"]));
            Log::add('Модуль <b>'.$ModuleInfo["name"].'</b> включен', 1);

            $script = '<script>
                $.server_say({say: "Плагин активирован!", status: "success"});
                $(\'[data-a="CelenaModule:action=enable&id='.$moduleId.'"]\').replaceWith(`<a href="#" class="btn btn_module_deactivate" data-a="CelenaModule:action=disable&id='.$moduleId.'">Выключить</a>`);
            </script>';

        } else{

            if(!empty($ModuleInfo["base_off"])) Base::run(str_replace("{prefix}", PREFIX, $ModuleInfo["base_off"]));

            Log::add('Модуль <b>'.$ModuleInfo["name"].'</b> отключен', 1);

            $script = '<script>
                $.server_say({say: "Плагин отключен!", status: "success"});
                $(\'[data-a="CelenaModule:action=disable&id='.$moduleId.'"]\').replaceWith(`<a href="#" class="btn btn_module_activate" data-a="CelenaModule:action=enable&id='.$moduleId.'">Включить</a>`);
            </script>';

        }

        System::script($script);
    }


    /**
     * @name удаление модуля
     * =====================
     * @return void
     * @throws Exception
     */
    private function remove(){

        $module_id = intval($_POST["id"]);

        if(empty($_POST['confirm'])){

            $script = '<script>
                $.confirm("Вы уверены, что хотите удалить?", function(e){
                    if(e) $.ajaxSend($(this), {"ajax": "CelenaModule", "action": "remove", "id": "'.$module_id.'", "confirm": 1});
                })
            </script>';

            die(System::script($script));

        } else {

            $ModuleModel = new ModuleModel();
            $ModuleInfo = $ModuleModel->getInfoByDel($module_id);

            # TODO нужно будет как-то перенести это в Modules::initialize();
            foreach ($ModuleInfo as $row) {
                if($row["action"] == '5' && !empty($row["filepath"]) && file_exists(ROOT . '/' . $row["filepath"])) unlink(ROOT . '/' . $row["filepath"]);
            }

            $ModuleModel->editFields($module_id, ["status" => 0]);

            if(!empty($ModuleInfo[0]["poster"])) unlink(ROOT . '/uploads/modules/' . $ModuleInfo[0]["poster"]);

            if(!empty($ModuleInfo[0]["routes"])){

                $routes = json_decode($ModuleInfo[0]["routes"], true);

                $routePrepare = [];
                if(!empty($routes["panel"]["url"])) $routePrepare["panel"] = $routes["panel"]["url"];
                if(!empty($routes["web"]["url"])) $routePrepare["web"] = $routes["web"]["url"];

                System::removeRoute($routePrepare);
            }

            if(!empty($ModuleInfo[0]["base_del"])) Base::run(str_replace("{prefix}", PREFIX, $ModuleInfo[0]["base_del"]));

            // удаляем модуль
            $result = $ModuleModel->remove($module_id);

            if($result){

                Modules::initialize();

                Log::add('Модуль <b>'.$ModuleInfo[0]["name"].'</b> удален', 1);

                $script = '<script>
                    $(\'[data-a="CelenaModule:action=remove&id='.$module_id.'"]\').closest(".module_table").remove();
                    $.server_say({say: "Модуль удален!", status: "success"});
                </script>';
                System::script($script);

            } else{

                die("info::error::Не удалось удалить модуль!");
            }
        }

        die("info::error::Модуль не найден!");
    }




    /**
     * @name загрузка иконки
     * =====================
     * @return false|string
     */
    private function uploadImages(){

        $ext = mb_strtolower(pathinfo($_FILES["icon"]["name"], PATHINFO_EXTENSION), 'UTF-8'); // расширение файла

        if(
            $ext == 'png' ||
            $ext == 'jpeg' ||
            $ext == 'jpg' ||
            $ext == 'webp' ||
            $ext == 'bmp' ||
            $ext == 'gif'
        ) {

            $dir = ROOT . '/uploads/modules'; // если директория не создана
            if(!file_exists($dir)) mkdir($dir, 0777, true);

            //$milliseconds = round(microtime(true) * 1000);
            $image_name = Functions::generationCode(3).'_'.uniqid()./*'_'.System::translit(strstr($_FILES["icon"]["name"], ".", true)).*/'.'.$ext;

            $imageSize = getimagesize($_FILES["icon"]["tmp_name"]);

            if(!empty($imageSize[0]) && !empty($imageSize[1]) && $imageSize[0] == '256' && $imageSize[1] == '256'){

                move_uploaded_file($_FILES["icon"]["tmp_name"], $dir . '/' . $image_name);

            } else {

                $image = new ImageManager();
                $img = $image->make($_FILES["icon"]["tmp_name"])->resize(
                    256,
                    256, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize(); // увеличивать только если оно больше чем нужно
                });
                $img->orientate();
                $img->save($dir . '/' . $image_name, (!empty(CONFIG_SYSTEM["quality_image"]) ? intval(CONFIG_SYSTEM["quality_image"]) : 100));
            }

        } else return false;

        return $image_name;
    }

}