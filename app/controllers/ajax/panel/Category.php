<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\panel\CategoryModel;
use Exception;

class Category{



    public function index(){

        if(!empty($_POST["title"])) self::createEditCategory(); // создание редактирование категории
        if(!empty($_POST["deleteCategory"])) self::deleteCategory(); // удаление категории
        if(!empty($_POST["statusCategory"])) self::editStatus(); // изменение активности
    }



    public function createEditCategory(){

        preg_match('/edit\/([0-9]+)\//is', $_GET["url"], $cid);

        $title = !empty($_POST["title"]) ? trim(htmlspecialchars(strip_tags($_POST["title"]))) : die("info::error::Укажите название!");
        $url = !empty($_POST["url"]) ? System::translit(trim(htmlspecialchars(strip_tags($_POST["url"])))) : System::translit($title);
        $cont = !empty($_POST["description"]) ? trim(htmlspecialchars(strip_tags($_POST["description"]))) : '';

        $meta["title"] = !empty($_POST["meta"]["title"]) ? trim(htmlspecialchars(strip_tags($_POST["meta"]["title"]))) : '';
        $meta["description"] = !empty($_POST["meta"]["description"]) ? trim(htmlspecialchars(strip_tags($_POST["meta"]["description"]))) : '';

        $tpl_min = !empty($_POST["tpl_min"]) ? trim(htmlspecialchars(strip_tags($_POST["tpl_min"]))) : '';
        $tpl_max = !empty($_POST["tpl_max"]) ? trim(htmlspecialchars(strip_tags($_POST["tpl_max"]))) : '';

        $pid = !empty($_POST["pid"]) ? intval($_POST["pid"]) : null;
        $addScript = '';

        $status = !empty($_POST["status"]) ? 1 : 0;

        $CategoryModel = new CategoryModel();

        if(empty($cid[1])){ // если это добавление новой категории

            $id = $CategoryModel->create($title, $cont, $url, $tpl_min, $tpl_max, $pid, $meta, $status);

            if(!empty($_FILES["icon"])){
                $icon = $this->uploadIcon($id);
                $CategoryModel->editFields($id, ['icon' => $icon]);
                $addScript = '$(".category_icon").html(`<img src="//'.CONFIG_SYSTEM["home"].'/uploads/categories/'.$icon.'">`);';
            }

            $script = '<script>
                '.$addScript.'
                $("h1").html(`Редактирование категории: <b>'.$title.'</b>`);
                $.server_say({say: "Категория создана!", status: "success"});
                setTimeout(function(){
                    window.location.href = "/'.CONFIG_SYSTEM["panel"].'/category/";
                }, 1000)
            </script>';

        } else{ // если редактирование

            $id = intval($cid[1]);
            $CategoryModel->edit($id, $title, $cont, $url, $tpl_min, $tpl_max, $pid, $meta, $status);

            if(!empty($_FILES["icon"])){

                // удаляем предыдущую картинку
                $CategoryIcon = $CategoryModel->get($id, "icon");
                if(!empty($CategoryIcon["icon"])) @unlink(ROOT . "/uploads/categories/" . $CategoryIcon["icon"]);

                $icon = $this->uploadIcon($id);
                $CategoryModel->editFields($id, ['icon' => $icon]);
                $addScript = '$(".category_icon").html(`<img src="//'.CONFIG_SYSTEM["home"].'/uploads/categories/'.$icon.'">`);';
            }

            $script = '<script>
                '.$addScript.'
                $("h1").html(`Редактирование категории: <b>'.$title.'</b>`);
                $.server_say({say: "Категория изменена!", status: "success"});
                setTimeout(function(){
                    window.location.href = "/'.CONFIG_SYSTEM["panel"].'/category/";
                }, 1000)
            </script>';
        }

        System::script($script);
    }





    /**
     * @name изменение статуса категории
     * =================================
     * @return void
     * @throws Exception
     */
    private function editStatus(){

        $categoryId = intval($_POST["categoryId"]);
        $statusCategory = ($_POST["statusCategory"] == 'true') ? 1 : 0;

        $CategoryModel = new CategoryModel();
        $result = $CategoryModel->editFields($categoryId, ["status" => $statusCategory]);

        if($result){

            $script = '<script>
                $.server_say({say: "Изменено!", status: "success"});
            </script>';
            System::script($script);

        } else{

            die("info::error::Не удалось внести изменения!");
        }
    }





    /**
     * @name удаление категории
     * ========================
     * @return void
     * @throws Exception
     */
    private function deleteCategory(){

        $categoryId = intval($_POST["deleteCategory"]);

        if(empty($_POST['confirm'])){
            $script = '<script>
                $.confirm("Вы уверены, что хотите удалить?", function(e){
                    if(e) $.ajaxSend($(this), {"ajax": "Category", "deleteCategory": "'.$categoryId.'", "confirm": 1});
                })
            </script>';

            die(System::script($script));
        }

        if(!empty($_POST['confirm'])){

            $CategoryModel = new CategoryModel();

            $icon = $CategoryModel->get($categoryId, "icon");
            if(!empty($icon["icon"])) @unlink(ROOT . "/uploads/categories/" . $icon["icon"]);

            $result = $CategoryModel->delete($categoryId);

            if($result){

                $script = '<script>
                    $(\'[data-a="Category:deleteCategory='.$categoryId.'"]\').closest("tr").remove();
                    $.server_say({say: "Удалено!", status: "success"});
                </script>';
                System::script($script);

            } else{

                die("info::error::Не удалось удалить категорию!");
            }
        }
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