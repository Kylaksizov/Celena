<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\UsersModel;
use app\traits\Log;
use Exception;
use Intervention\Image\ImageManager;


class Users{

    public function index(){

        preg_match('/edit\/([0-9]+)\//is', $_GET["url"], $user);
        $userId = !empty($user[1]) ? intval($user[1]) : null;

        if(!empty($_POST["product"])) self::createUser($userId); // создание редактирование пользователя
        if(!empty($_POST["delete"])) self::deleteUser(); // удаление пользователя
    }


    /**
     * @name создание / редактирование товара
     * @return void
     * @throws Exception
     */
    private function createUser($userId){

        $name = !empty($_POST["name"]) ? trim(htmlspecialchars(strip_tags($_POST["name"]))) : die("info::error::Укажите имя!");
        
        $script = '<script>
            $.server_say({say: "Изменения сохранены!", status: "success"});
            setTimeout(function(){
                window.location.reload();
            }, 1000)
        </script>';

        System::script($script);
    }





    /**
     * @name удаление товара
     * =====================
     * @return void
     * @throws Exception
     */
    private function deleteUser(){

        $userId = intval($_POST["delete"]);

        if(empty($_POST['confirm'])){
            $script = '<script>
                $.confirm("Вы уверены, что хотите удалить?", function(e){
                    if(e) $.ajaxSend($(this), {"ajax": "Users", "delete": "'.$userId.'", "confirm": 1});
                })
            </script>';

            die(System::script($script));
        }

        if(!empty($_POST['confirm'])){

            $UsersModel = new UsersModel();
            $User = $UsersModel->getUser($userId);

            if($User["role"] == '1') die("info::error::Администратора нельзя пока удалять!<br>В разработке!");

            if(!empty($User["avatar"]))
                @unlink(ROOT . '/uploads/users/'.$User["avatar"]);

            $result = $UsersModel->delete($userId);

            if($result){

                Log::add("Пользователь <b>{$User["name"]}</b> удален!", 1);

                $script = '<script>
                    $(\'[data-a="Users:delete='.$userId.'"]\').closest("tr").remove();
                    $.server_say({say: "Пользователь удален!", status: "success"});
                </script>';
                System::script($script);

            } else{

                die("info::error::Не удалось удалить пользователя!");
            }
        }
    }





    /**
     * @name загрузка картинок
     * =======================
     * @return array|void
     */
    private function uploadImages($id){

        $images = [];

        foreach ($_FILES["images"]["name"] as $imageKey => $image) {

            $ext = mb_strtolower(pathinfo($image, PATHINFO_EXTENSION), 'UTF-8'); // расширение файла

            if(
                $ext == 'png' ||
                $ext == 'jpeg' ||
                $ext == 'jpg' ||
                $ext == 'webp' ||
                $ext == 'bmp' ||
                $ext == 'gif'
            ) {

                $dir = ROOT . '/uploads/products'; // если директория не создана
                $dir_rel = date("Y-m", time());

                if(!file_exists($dir)) mkdir($dir, 0777, true);

                $dir .= '/'.$dir_rel;
                if(!file_exists($dir)) mkdir($dir, 0777, true);

                //$milliseconds = round(microtime(true) * 1000);
                $image_name = $id.'_'.time().'_'.System::translit(strstr($image, ".", true)).'.'.$ext;



                if(!empty(CONFIG_SYSTEM["origin_image"])){

                    $image = new ImageManager();
                    $img = $image->make($_FILES["images"]["tmp_name"][$imageKey])->resize(
                        intval(CONFIG_SYSTEM["origin_image"]),
                        null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize(); // увеличивать только если оно больше чем нужно
                    });
                    $img->orientate();
                    $img->save($dir . '/' . $image_name, (!empty(CONFIG_SYSTEM["quality_image"]) ? intval(CONFIG_SYSTEM["quality_image"]) : 100));

                } else move_uploaded_file($_FILES["images"]["tmp_name"][$imageKey], $dir . '/' . $image_name);



                if(CONFIG_SYSTEM["thumb"]){ // если требуется создание миниатюру

                    if(!file_exists($dir.'/thumbs')) mkdir($dir.'/thumbs', 0777, true);

                    $image = new ImageManager();
                    $img = $image->make($dir . '/' . $image_name)->resize(
                        intval(CONFIG_SYSTEM["thumb"]),
                        null,
                        function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize(); // увеличивать только если оно больше чем нужно
                        });
                    $img->orientate();
                    $img->save($dir . '/thumbs/' . $image_name, (!empty(CONFIG_SYSTEM["quality_thumb"]) ? intval(CONFIG_SYSTEM["quality_thumb"]) : 100));
                }

                array_push($images, $dir_rel.'/'.$image_name);

            } else die("info::error::Изображение имеет неверное расширение!");
        }

        return $images;
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

            $dir = ROOT . '/uploads/categories'; // если директория не создана
            if(!file_exists($dir)) mkdir($dir, 0777, true);

            $milliseconds = round(microtime(true) * 1000);
            $icon_name = $id.'_'.$milliseconds.'.'.$ext;

            move_uploaded_file($_FILES["icon"]["tmp_name"], $dir . '/' . $icon_name);

            return $icon_name;

        } else die("info::error::Иконка имеет неверное расширение!");
    }

}