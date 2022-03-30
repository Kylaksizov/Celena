<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\PropertyModel;
use Exception;

class PropertyShop{



    public function index(){

        if(!empty($_POST["title"])) self::createProperty(); // создание редактирование свойств
    }



    public function createProperty(){

        preg_match('/edit\/([0-9]+)\//is', $_GET["url"], $pid);

        $title = !empty($_POST["title"]) ? trim(htmlspecialchars(strip_tags($_POST["title"]))) : die("info::error::Укажите название!");
        $url = !empty($_POST["url"]) ? System::translit(trim(htmlspecialchars(strip_tags($_POST["url"])))) : null;
        $cid = !empty($_POST["cid"]) ? implode(",", $_POST["cid"]) : null;
        $option = !empty($_POST["display"]) ? 1 : 0;
        $sep = !empty($_POST["sep"]) ? 1 : 0;
        $position = 0;

        $PropertyModel = new PropertyModel();

        if(empty($pid[1])){ // если это добавление новой категории

            $id = $PropertyModel->create($title, $url, $cid, $option, $sep, $position);

            if(!empty($_POST["val"])){
                foreach ($_POST["val"] as $val) {
                    $PropertyModel->add($id, trim(htmlspecialchars(strip_tags($val))));
                }
            }

            $script = '<script>
                $("h1").html(`Редактирование свойства для товаров: <b>'.$title.'</b>`);
                $.server_say({say: "Свойство создано!", status: "success"});
                history.pushState(null, "Редактирование свойства", "'.CONFIG_SYSTEM["home"].CONFIG_SYSTEM["panel"].'/products/properties/edit/'.$id.'/");
            </script>';

        } else{ // если редактирование

            $id = intval($pid[1]);
            $PropertyModel->editFields($id, [
                'title' => $title,
                'url' => $url,
                'cid' => $cid,
                'option' => $option,
                'sep' => $sep,
                'position' => $position
            ]);

            if(!empty($_POST["val"])){
                foreach ($_POST["val"] as $val_key => $val) {

                    if(!empty($_POST["id"][$val_key])){
                        $PropertyModel->editFieldsV(intval($_POST["id"][$val_key]), [
                            'val' =>  trim(htmlspecialchars(strip_tags($val)))
                        ]);
                    } else{
                        $PropertyModel->add($id, trim(htmlspecialchars(strip_tags($val))));
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