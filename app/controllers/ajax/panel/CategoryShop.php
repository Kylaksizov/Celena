<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\CategoryModel;

class CategoryShop{

    public function index(){

        preg_match('/edit\/([0-9]+)\//is', $_GET["url"], $cid);

        $title = !empty($_POST["title"]) ? trim(htmlspecialchars(strip_tags($_POST["title"]))) : die("info::error::Укажите название!");
        $url = !empty($_POST["url"]) ? System::translit(trim(htmlspecialchars(strip_tags($_POST["url"])))) : System::translit($title);
        $cont = !empty($_POST["description"]) ? trim(htmlspecialchars(strip_tags($_POST["description"]))) : '';

        $meta["title"] = !empty($_POST["meta"]["title"]) ? trim(htmlspecialchars(strip_tags($_POST["meta"]["title"]))) : '';
        $meta["description"] = !empty($_POST["meta"]["description"]) ? trim(htmlspecialchars(strip_tags($_POST["meta"]["description"]))) : '';

        $icon = '';
        $pid = null;

        $CategoryModel = new CategoryModel();

        if(empty($cid[1])){ // если это добавление новой категории

            $id = $CategoryModel->create($title, $meta, $cont, $url, $icon, $pid);

            if(!empty($_FILES["icon"])) $this->uploadIcon($id);

            $script = '<script>
                $.server_say({say: "Категория создана!", status: "success"});
            </script>';

        } else{ // если редактирование

            $id = intval($cid);
            $CategoryModel->edit(intval($cid), $title, $meta, $cont, $url, $icon, $pid);

            if(!empty($_FILES["icon"])) $this->uploadIcon($id);

            $script = '<script>
                $("h1 b").text(`'.$title.'`);
                $.server_say({say: "Категория изменена!", status: "success"});
            </script>';
        }

        System::script($script);

        return '155';
    }


    private function uploadIcon($id){

        $dir = ROOT . '/uploads/products/' . date("Y-m", time());

        if(!file_exists($dir)) mkdir($dir, 0777, true);

        move_uploaded_file($_FILES["icon"]["tmp_name"], $dir . '/' . $_FILES["icon"]["name"]);
    }

}