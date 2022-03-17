<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\CategoryModel;

class CategoryShop{

    public function index(){

        $title = !empty($_POST["title"]) ? trim(htmlspecialchars(strip_tags($_POST["title"]))) : die("info::error::Укажите название!");
        $url = !empty($_POST["url"]) ? System::translit(trim(htmlspecialchars(strip_tags($_POST["url"])))) : System::translit($title);
        $cont = !empty($_POST["description"]) ? trim(htmlspecialchars(strip_tags($_POST["description"]))) : '';

        $meta["title"] = !empty($_POST["meta"]["title"]) ? trim(htmlspecialchars(strip_tags($_POST["meta"]["title"]))) : '';
        $meta["description"] = !empty($_POST["meta"]["description"]) ? trim(htmlspecialchars(strip_tags($_POST["meta"]["description"]))) : '';

        $icon = '';
        $pid = null;

        $CategoryModel = new CategoryModel();
        $CategoryModel->create($title, $meta, $cont, $url, $icon, $pid);

        $script = '<script>
            $.server_say({say: "Категория создана!", status: "success"});
        </script>';

        System::script($script);

        return '155';
    }

}