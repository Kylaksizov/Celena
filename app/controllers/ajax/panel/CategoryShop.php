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

        $pid = !empty($_POST["pid"]) ? intval($_POST["pid"]) : null;
        $addScript = '';

        $CategoryModel = new CategoryModel();

        if(empty($cid[1])){ // если это добавление новой категории

            $id = $CategoryModel->create($title, $meta, $cont, $url, $pid);

            if(!empty($_FILES["icon"])){
                $icon = $this->uploadIcon($id);
                $CategoryModel->editFields($id, ['icon' => $icon]);
                $addScript = '$(".category_icon").html(`<img src="'.CONFIG_SYSTEM["home"].'uploads/categories/'.$icon.'">`);';
            }

            $script = '<script>
                '.$addScript.'
                $.server_say({say: "Категория создана!", status: "success"});
                history.pushState(null, "Редактирование категории", "'.CONFIG_SYSTEM["home"].CONFIG_SYSTEM["panel"].'/products/categories/edit/'.$id.'/");
            </script>';

        } else{ // если редактирование

            $id = intval($cid[1]);
            $CategoryModel->edit($id, $title, $meta, $cont, $url, $pid);

            if(!empty($_FILES["icon"])){
                $icon = $this->uploadIcon($id);
                $CategoryModel->editFields($id, ['icon' => $icon]);
                $addScript = '$(".category_icon").html(`<img src="'.CONFIG_SYSTEM["home"].'uploads/categories/'.$icon.'">`);';
            }

            $script = '<script>
                '.$addScript.'
                $("h1 b").text(`'.$title.'`);
                $.server_say({say: "Категория изменена!", status: "success"});
            </script>';
        }

        System::script($script);
    }


    /**
     * @name загрузка иконки
     * =====================
     * @param $id
     * @return string|void
     */
    private function uploadIcon($id){

        $ext = mb_strtolower(pathinfo($_FILES["icon"]["name"], PATHINFO_EXTENSION), 'UTF-8'); // расширение файла

        if(
            $ext == 'png' ||
            $ext == 'jpeg' ||
            $ext == 'jpg' ||
            $ext == 'webp' ||
            $ext == 'bmp' ||
            $ext == 'gif'
        ) {

            $dir = ROOT . '/uploads/categories'; // если директория не создана
            if(!file_exists($dir)) mkdir($dir, 0777, true);

            $milliseconds = round(microtime(true) * 1000);
            $icon_name = $id.'_'.$milliseconds.'.'.$ext;

            move_uploaded_file($_FILES["icon"]["tmp_name"], $dir . '/' . $icon_name);

            return $icon_name;

        } else die("info::error::Иконка имеет неверное расширение!");
    }

}