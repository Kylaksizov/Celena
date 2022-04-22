<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\panel\PropertyModel;
use Exception;

class PropertyShop{



    public function index(){

        if(!empty($_POST["title"])) self::createProperty(); // создание редактирование свойств
    }



    public function createProperty(){

        preg_match('/edit\/([0-9]+)\//is', $_GET["url"], $pid);

        $title = !empty($_POST["title"]) ? trim(htmlspecialchars(strip_tags($_POST["title"]))) : die("info::error::Укажите название!");
        $url = !empty($_POST["url"]) ? System::translit(trim(htmlspecialchars(strip_tags($_POST["url"])))) : null;
        $type = !empty($_POST["type"]) ? intval($_POST["type"]) : 1;
        $cid = !empty($_POST["cid"]) ? implode(",", $_POST["cid"]) : null;
        $option = !empty($_POST["display"]) ? 1 : 0;
        $sep = !empty($_POST["sep"]) ? 1 : 0;
        $req_p = !empty($_POST["req_p"]) ? 1 : 0;
        $req = !empty($_POST["req"]) ? 1 : 0;
        $position = 0;

        $PropertyModel = new PropertyModel();

        // добавление нового свойства
        if(empty($pid[1])){

            $id = $PropertyModel->create($title, $url, $type, $cid, $option, $sep, $req_p, $req, $position);

            if(!empty($_POST["val"])){
                foreach ($_POST["val"] as $val) {
                    $def = ($val == $_POST["def"]) ? 1 : null;
                    $PropertyModel->add($id, trim(htmlspecialchars(strip_tags($val))), $def);
                }
            }

            $script = '<script>
                $("h1").html(`Редактирование свойства для товаров: <b>'.$title.'</b>`);
                $.server_say({say: "Свойство создано!", status: "success"});
                history.pushState(null, "Редактирование свойства", "'.CONFIG_SYSTEM["home"].CONFIG_SYSTEM["panel"].'/products/properties/edit/'.$id.'/");
            </script>';

        // редактирование
        } else{

            $id = intval($pid[1]);
            $PropertyModel->editFields($id, [
                'title' => $title,
                'url' => $url,
                'f_type' => $type,
                'cid' => $cid,
                'option' => $option,
                'sep' => $sep,
                'req_p' => $req_p,
                'req' => $req,
                'position' => $position
            ]);
            
            if(!empty($_POST["val"])){

                // если очистили все значения
                if(count($_POST["val"]) == 1 && empty($_POST["val"][0])) $PropertyModel->clear($id);

                else{

                    foreach ($_POST["val"] as $val_key => $val) {

                        $def = (!empty($_POST["def"]) && $val == $_POST["def"]) ? 1 : null;

                        if(!empty($_POST["id"][$val_key])){
                            $PropertyModel->editFieldsV(intval($_POST["id"][$val_key]), [
                                'val' =>  trim(htmlspecialchars(strip_tags($val))),
                                'def' => $def
                            ]);
                        } else{
                            $PropertyModel->add($id, trim(htmlspecialchars(strip_tags($val))), $def);
                        }
                    }
                }
            }

            $script = '<script>
                $("h1").html(`Редактирование свойства для товаров: <b>'.$title.'</b>`);
                $.server_say({say: "Свойство изменено!", status: "success"});
            </script>';
        }

        System::script($script);
    }

}