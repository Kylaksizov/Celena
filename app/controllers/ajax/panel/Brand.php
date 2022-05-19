<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\panel\BrandModel;
use Exception;

class Brand{



    public function index(){

        if(!empty($_POST["name"])) self::createEditBrand(); // создание редактирование бренда
        if(!empty($_POST["deleteBrand"])) self::deleteBrand(); // удаление бренда
    }



    public function createEditBrand(){

        preg_match('/edit\/([0-9]+)\//is', $_GET["url"], $cid);

        $name = !empty($_POST["name"]) ? trim(htmlspecialchars(strip_tags($_POST["name"]))) : die("info::error::Укажите название!");
        $url = !empty($_POST["url"]) ? System::translit(trim(htmlspecialchars(strip_tags($_POST["url"])))) : System::translit($name);

        $categories = !empty($_POST["categories"]) ? implode(",", $_POST["categories"]) : '';
        $addScript = '';

        $BrandModel = new BrandModel();

        if(empty($cid[1])){ // если это добавление новой категории

            $id = $BrandModel->create($name, $url);

            if(!empty($_FILES["icon"])){
                $icon = $this->uploadIcon($id);
                $BrandModel->editFields($id, [
                    'icon' => $icon,
                    'categories' => $categories
                ]);
                $addScript = '$(".brand_icon").html(`<img src="//'.CONFIG_SYSTEM["home"].'/uploads/brands/'.$icon.'">`);';
            }

            $script = '<script>
                '.$addScript.'
                $("h1").html(`Редактирование бренда: <b>'.$name.'</b>`);
                $.server_say({say: "Бренд создан!", status: "success"});
                history.pushState(null, "Редактирование бренда", "//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/products/brands/edit/'.$id.'/");
            </script>';

        } else{ // если редактирование

            $id = intval($cid[1]);
            $BrandModel->editFields($id, [
                "name" => $name,
                "url" => $url,
                "categories" => $categories
            ]);

            if(!empty($_FILES["icon"])){

                // удаляем предыдущую картинку
                $BrandIcon = $BrandModel->get($id, "icon");
                if(!empty($BrandIcon["icon"])) @unlink(ROOT . "/uploads/brands/" . $BrandIcon["icon"]);

                $icon = $this->uploadIcon($id);
                $BrandModel->editFields($id, ['icon' => $icon]);
                $addScript = '$(".brand_icon").html(`<img src="//'.CONFIG_SYSTEM["home"].'/uploads/brands/'.$icon.'">`);';
            }

            $script = '<script>
                '.$addScript.'
                $("h1").html(`Редактирование бренда: <b>'.$name.'</b>`);
                $.server_say({say: "Категория изменена!", status: "success"});
            </script>';
        }

        System::script($script);
    }





    /**
     * @name удаление категории
     * ========================
     * @return void
     * @throws Exception
     */
    private function deleteBrand(){

        $brandId = intval($_POST["deleteBrand"]);

        if(empty($_POST['confirm'])){
            $script = '<script>
                $.confirm("Вы уверены, что хотите удалить?", function(e){
                    if(e) $.ajaxSend($(this), {"ajax": "Brand", "deleteBrand": "'.$brandId.'", "confirm": 1});
                })
            </script>';

            die(System::script($script));
        }

        if(!empty($_POST['confirm'])){

            $BrandModel = new BrandModel();

            $icon = $BrandModel->get($brandId, "icon");
            if(!empty($icon["icon"])) @unlink(ROOT . "/uploads/brands/" . $icon["icon"]);

            $result = $BrandModel->delete($brandId);

            if($result){

                $script = '<script>
                    $(\'[data-a="Brand:deleteBrand='.$brandId.'"]\').closest("tr").remove();
                    $.server_say({say: "Удалено!", status: "success"});
                </script>';
                System::script($script);

            } else{

                die("info::error::Не удалось удалить бренд!");
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
            $ext == 'svg' ||
            $ext == 'gif'
        ) {

            $dir = ROOT . '/uploads/brands'; // если директория не создана
            if(!file_exists($dir)) mkdir($dir, 0777, true);

            $milliseconds = round(microtime(true) * 1000);
            $icon_name = $id.'_'.$milliseconds.'.'.$ext;

            move_uploaded_file($_FILES["icon"]["tmp_name"], $dir . '/' . $icon_name);

            return $icon_name;

        } else die("info::error::Иконка имеет неверное расширение!");
    }

}