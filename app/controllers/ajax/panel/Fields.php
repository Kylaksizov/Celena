<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\panel\FieldsModel;
use Exception;


class Fields{

    public function index(){

        /*preg_match('/edit\/([0-9]+)\//is', $_GET["url"], $post);
        $postId = !empty($post[1]) ? intval($post[1]) : null;*/

        if(!empty($_POST["name"])) self::createEditFields(); // создание редактирование поля
        if(!empty($_POST["delete"])) self::delete(); // удаление поля
        if(!empty($_POST["statusField"])) self::editStatus(); // изменение активности
        if(!empty($_POST["deleteFieldB"])) self::deleteFieldB(); // удаление картинки или файла из базы

        die("info::error::Неизвестный запрос!");
    }



    /**
     * @name удаление картинки
     * =======================
     * @return void
     * @throws Exception
     */
    private function createEditFields(){

        $name = trim(htmlspecialchars(strip_tags($_POST["name"])));
        $tag = !empty($_POST["tag"]) ? trim(htmlspecialchars(strip_tags($_POST["tag"]))) : System::translit($name);
        $hint = !empty($_POST["hint"]) ? trim(htmlspecialchars(strip_tags($_POST["hint"]))) : '';
        $category = !empty($_POST["category"]) && is_array($_POST["category"]) ? $_POST["category"] : [];
        $type = $_POST["type"];

        $resultField[$tag] = [
            "name"     => $name,
            "tag"      => $tag,
            "hint"     => $hint,
            "category" => $category,
            "type"     => $type,
        ];
        
        switch ($type){

            case 'input':

                if(!empty($_POST["defaultInput"])) $resultField[$tag]["default"] = trim(htmlspecialchars(strip_tags($_POST["defaultInput"])));

                break;

            case 'textarea':

                if(!empty($_POST["defaultTextarea"])) $resultField[$tag]["default"] = trim(htmlspecialchars(strip_tags($_POST["defaultTextarea"])));

                break;

            case 'select':

                if(!empty($_POST["list"])) $resultField[$tag]["list"] = explode(PHP_EOL, trim(htmlspecialchars(strip_tags($_POST["list"]))));
                else die("info::error::Вы не указали сам список!");
                $resultField[$tag]["multiple"] = !empty($_POST["multiple"]) ? 1 : 0;

                break;

            case 'code':

                if(!empty($_POST["defaultTextarea"])) $resultField[$tag]["default"] = trim(htmlspecialchars($_POST["defaultTextarea"]));

                break;

            case 'image':

                if(!empty($_POST["maxCount"])) $resultField[$tag]["maxCount"] = trim(htmlspecialchars(strip_tags($_POST["maxCount"])));
                if(!empty($_POST["resizeOriginal"])) $resultField[$tag]["resizeOriginal"] = trim(htmlspecialchars(strip_tags($_POST["resizeOriginal"])));
                if(!empty($_POST["qualityOriginal"])){
                    $resultField[$tag]["qualityOriginal"] = intval($_POST["qualityOriginal"]);
                    if($resultField[$tag]["qualityOriginal"] < 1) die("info::error::Качество оригинала не может быть меньше 1!");
                    if($resultField[$tag]["qualityOriginal"] > 100) die("info::error::Качество оригинала не может превышать 100!");
                }
                if(!empty($_POST["thumb"])){
                    $resultField[$tag]["thumb"] = 1;
                    if(!empty($_POST["resizeThumb"])) $resultField[$tag]["resizeThumb"] = trim(htmlspecialchars(strip_tags($_POST["resizeThumb"])));
                    if(!empty($_POST["qualityThumb"])){
                        $resultField[$tag]["qualityThumb"] = intval($_POST["qualityThumb"]);
                        if($resultField[$tag]["qualityThumb"] < 1) die("info::error::Качество уменьшенной копии не может быть меньше 1!");
                        if($resultField[$tag]["qualityThumb"] > 100) die("info::error::Качество уменьшенной копии не может превышать 100!");
                    }
                }

                break;

            case 'file':

                if(!empty($_POST["maxCount"])) $resultField[$tag]["maxCount"] = intval($_POST["maxCount"]);
                if(!empty($_POST["format"])) $resultField[$tag]["format"] = trim(htmlspecialchars(strip_tags($_POST["format"])));

                break;

            case 'checkbox':

                $resultField[$tag]["default"] = empty($_POST["chDefault"]) ? 1 : 0;

                break;
        }

        $resultField[$tag]["rq"] = !empty($_POST["required"]) ? 1 : 0;
        $resultField[$tag]["status"] = !empty($_POST["status"]) ? 1 : 0;

        System::addField($resultField);



        $script = '<script>
            $.server_say({say: "Новое поле создано!", status: "success"});
        </script>';
        System::script($script);
    }





    /**
     * @name удаление поля
     * ===================
     * @return void
     * @throws Exception
     */
    private function delete(){

        $fieldTag = trim(htmlspecialchars(strip_tags($_POST["delete"])));

        if(empty($_POST['confirm'])){
            $script = '<script>
                $.confirm("Вы уверены, что хотите удалить поле?", function(e){
                    if(e) $.ajaxSend($(this), {"ajax": "Fields", "delete": "'.$fieldTag.'", "confirm": 1});
                })
            </script>';

            die(System::script($script));

        } else {

            System::deleteField($fieldTag);

            $script = '<script>
                $(\'[data-a="Fields:delete='.$fieldTag.'"]\').closest("tr").remove();
                $.server_say({say: "Удалено!", status: "success"});
            </script>';
            System::script($script);
        }
    }



    /**
     * @name изменение статуса поля
     * ============================
     * @return void
     * @throws Exception
     */
    private function editStatus(){

        $tag = trim(htmlspecialchars(strip_tags($_POST["tag"])));
        $statusField = ($_POST["statusField"] == 'true') ? 1 : 0;

        System::editField($tag, "status", $statusField);

        $script = '<script>
            $.server_say({say: "Статус изменен!", status: "success"});
        </script>';
        System::script($script);
    }



    /**
     * @name удаление картинки или файла из базы
     * =========================================
     * @return void
     * @throws Exception
     */
    private function deleteFieldB(){

        $fieldId = !empty($_POST["fieldId"]) ? intval($_POST["fieldId"]) : die("info::error::Что-то пошло не так!");
        $fieldVal = !empty($_POST["deleteFieldB"]) ? trim(htmlspecialchars($_POST["deleteFieldB"])) : die("info::error::Что-то пошло не так!");

        $FieldsModel = new FieldsModel();
        $Field = $FieldsModel->getFieldById($fieldId);
        
        if(!empty($Field["val"])){

            $fDbSource = explode("|", $Field["val"]);
            foreach ($fDbSource as $key => $item) {
                if(strripos($item, $fieldVal) !== false)
                    unset($fDbSource[$key]);
            }

            if(!empty($fDbSource)){
                $fDbSource = implode("|", $fDbSource);
                $FieldsModel->editFieldVal($fieldId, $fDbSource);
            } else{
                $FieldsModel->remove($fieldId);
            }
        }

        if(file_exists(ROOT . "/uploads/fields/" . $fieldVal))
            unlink(ROOT . "/uploads/fields/" . $fieldVal);

        if(strripos($fieldVal, "/thumbs/") !== false)
            unlink(ROOT . "/uploads/fields/" . str_replace("/thumbs", "", $fieldVal));

        $script = '<script>
            $(".nex_tmp").parent().css({
                "transform": "scale(0)",
                "transition": ".5s"
            });
            $.server_say({say: "Удалено!", status: "success"});
        </script>';
        System::script($script);
    }

}