<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\panel\FieldsModel;
use app\models\panel\PostModel;
use Exception;
use Intervention\Image\ImageManager;


class Post{

    public function index(){

        preg_match('/edit\/([0-9]+)\//is', $_GET["url"], $post);
        $postId = !empty($post[1]) ? intval($post[1]) : null;

        if(!empty($_POST["post"])) self::createEditPost($postId); // создание редактирование новости
        if(!empty($_POST["deletePost"])) self::deletePost(); // удаление новости
        if(!empty($_POST["statusPost"])) self::editStatus(); // изменение активности
        if(!empty($_POST["newSortImages"])) self::sortImages(); // сортировка изображения новости
        if(!empty($_POST["setMainImage"])) self::setMainImage($postId); // установка постера
        if(!empty($_POST["deleteImage"])) self::deleteImage(); // удаление фото новости
        if(!empty($_POST["photo"])) self::editImage(); // редактирование фото новости
    }


    /**
     * @name создание / редактирование новости
     * @return void
     * @throws Exception
     */
    private function createEditPost($postId){

        $title = !empty($_POST["title"]) ? trim(htmlspecialchars(strip_tags($_POST["title"]))) : die("info::error::Укажите название!");
        $url = !empty($_POST["url"]) ? System::translit(trim(htmlspecialchars(strip_tags($_POST["url"])))) : System::translit($title);
        $created = !empty($_POST["created"]) ? strtotime($_POST["created"]) : null;
        $short = !empty($_POST["short"]) ? trim($_POST["short"]) : '';
        $content = !empty($_POST["content"]) ? trim($_POST["content"]) : '';
        $category = !empty($_POST["category"]) ? $_POST["category"] : die("info::error::Выберите категорию!");
        $status = !empty($_POST["status"]) ? 1 : 0;

        $meta["title"] = !empty($_POST["meta"]["title"]) ? trim(htmlspecialchars(strip_tags($_POST["meta"]["title"]))) : '';
        $meta["description"] = !empty($_POST["meta"]["description"]) ? trim(htmlspecialchars(strip_tags($_POST["meta"]["description"]))) : '';

        $fieldsData = null;
        if(!empty($_POST["field"])){
            $fieldsData = \app\traits\Fields::getPostFields($postId, $_POST["field"], $category);
        }
        
        $addScript = '';

        $PostModel = new PostModel();

        if(!$postId){ // если это добавление новой категории

            $id = $PostModel->create($title, $meta, $short, $content, $category, $url, $created, $status);

            $postId = $id;

            if(!empty($_FILES["images"])){
                $images = $this->uploadImages($id);
                $addScript = '$("#post_images").append(`';
                foreach ($images as $image) {
                    $imgId = $PostModel->addImage(1, $id, $image);
                    $addScript .= '<div class="img_item"><a href="//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.$image.'" data-fancybox="gallery"><img src="//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.str_replace('/', '/thumbs/', $image).'"></a><a href="#" class="edit_image" data-img-id="'.$imgId.'"></a><a href="#" class="delete_image" data-a="Post:deleteImage='.$imgId.'&link='.$image.'"></a></div>';
                }
                $addScript .= '`);$(".files_preload").html("").hide();';
            }


            $script = '<script>
                '.$addScript.'
                $.server_say({say: "Новость создана!", status: "success"});
                history.pushState(null, "Редактирование новости", "//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/posts/edit/'.$id.'/");
            </script>';

        } else{ // если редактирование

            $PostModel->editFields($postId, [
                'title' => $title,
                'm_title' => $meta["title"],
                'm_description' => $meta["description"],
                'short' => $short,
                'content' => $content,
                'category' => implode(",", $category),
                'url' => $url,
                'status' => $status
            ]);

            if(!empty($_FILES["images"])){
                $images = $this->uploadImages($postId);
                $addScript = '$("#post_images").append(`';
                foreach ($images as $image) {
                    $imgId = $PostModel->addImage(1, $postId, $image);
                    $addScript .= '<div class="img_item"><a href="//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.$image.'" data-fancybox="gallery"><img src="//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.str_replace('/', '/thumbs/', $image).'"></a><a href="#" class="edit_image" data-img-id="'.$imgId.'"></a><a href="#" class="delete_image" data-a="Post:deleteImage='.$imgId.'&link='.$image.'"></a></div>';
                }
                $addScript .= '`);$(".files_preload").html("").hide();';
            }

            $script = '<script>
                '.$addScript.'
                $("h1 b").text(`'.$title.'`);
                $.server_say({say: "Изменения сохранены!", status: "success"});
                setTimeout(function(){
                    window.location.reload();
                }, 1000)
            </script>';
            #TODO из-за свойств временно сделал автообновление страницы после сохранения - исправить...
        }


        // обработка доп полей
        if($fieldsData){

            $resultAddFields = [];

            foreach ($fieldsData["result"] as $tag => $field) {

                $val = false;

                switch ($fieldsData["fields"][$tag]["type"]){
                    
                    case 'input': case 'textarea': case 'checkbox': case 'code': case 'date': case 'dateTime':

                        $val = $field;

                        break;

                    case 'select':

                        if($fieldsData["fields"][$tag]["multiple"])
                            $val = implode("|", $field);
                        else
                            $val = trim(strip_tags($field));

                        break;

                    case 'image':

                        if($field == '__ISSET__') break;

                        if(!empty($fieldsData["fields"][$tag]["maxCount"]) && $fieldsData["fields"][$tag]["maxCount"] == '1' && !empty($field["name"])){

                            $resizeOriginal = !empty($fieldsData["fields"][$tag]["resizeOriginal"]) ? intval($fieldsData["fields"][$tag]["resizeOriginal"]) : null;
                            $qualityOriginal = !empty($fieldsData["fields"][$tag]["qualityOriginal"]) ? intval($fieldsData["fields"][$tag]["qualityOriginal"]) : 100;


                            if(isset($field["__REPLACE__"])){ // если замена, то удаляем старую

                                $imgPath = strstr($fieldsData["inBase"][$tag]["val"], ":", true);

                                if(file_exists(ROOT . "/uploads/fields/" . $imgPath))
                                    unlink(ROOT . "/uploads/fields/" . $imgPath);

                                if(strripos($imgPath, "/thumbs/") !== false)
                                    unlink(ROOT . "/uploads/fields/" . str_replace("/thumbs", "", $imgPath));
                            }


                            $val = self::saveImageField($field["tmp_name"], $field["name"], $resizeOriginal, $qualityOriginal);

                            if(!empty($fieldsData["fields"][$tag]["thumb"])){

                                $resize = !empty($fieldsData["fields"][$tag]["resizeThumb"]) ? intval($fieldsData["fields"][$tag]["resizeThumb"]) : false;
                                if(!$resize) $resize = !empty(CONFIG_SYSTEM["thumb"]) ? intval(CONFIG_SYSTEM["thumb"]) : false;

                                $quality = !empty($fieldsData["fields"][$tag]["qualityThumb"]) ? intval($fieldsData["fields"][$tag]["qualityThumb"]) : false;
                                if(!$quality) $quality = !empty(CONFIG_SYSTEM["quality_thumb"]) ? intval(CONFIG_SYSTEM["quality_thumb"]) : 100;

                                if($resize){
                                    $val = explode(":", $val);
                                    $val = self::saveImageField(ROOT . '/uploads/fields/' . $val[0], end($val), $resize, $quality, true);
                                }
                            }
                            
                        } else{

                            $val = '';

                            foreach ($field["name"] as $key => $imgName) {

                                $resizeOriginal = !empty($fieldsData["fields"][$tag]["resizeOriginal"]) ? intval($fieldsData["fields"][$tag]["resizeOriginal"]) : null;
                                $qualityOriginal = !empty($fieldsData["fields"][$tag]["qualityOriginal"]) ? intval($fieldsData["fields"][$tag]["qualityOriginal"]) : 100;

                                $uploaded = self::saveImageField($field["tmp_name"][$key], $imgName, $resizeOriginal, $qualityOriginal);

                                // сделать тут проверку на кол-во вместо вышестоящей функции
                                if(!empty($fieldsData["fields"][$tag]["thumb"])){

                                    $resize = !empty($fieldsData["fields"][$tag]["resizeThumb"]) ? intval($fieldsData["fields"][$tag]["resizeThumb"]) : false;
                                    if(!$resize) $resize = !empty(CONFIG_SYSTEM["thumb"]) ? intval(CONFIG_SYSTEM["thumb"]) : false;

                                    $quality = !empty($fieldsData["fields"][$tag]["qualityThumb"]) ? intval($fieldsData["fields"][$tag]["qualityThumb"]) : false;
                                    if(!$quality) $quality = !empty(CONFIG_SYSTEM["quality_thumb"]) ? intval(CONFIG_SYSTEM["quality_thumb"]) : 100;

                                    if($resize){
                                        $uploaded = explode(":", $uploaded);
                                        $uploaded = self::saveImageField(ROOT . '/uploads/fields/' . $uploaded[0], end($uploaded), $resize, $quality, true);
                                    }
                                }

                                if($uploaded) $val .= $uploaded . '|';
                            }
                            $val = trim($val, '|');
                        }

                        break;


                    case 'file':

                        if($field == '__ISSET__') break;

                        $dir = ROOT . '/uploads/fields'; // если директория не создана
                        $dir_rel = date("Y-m", time());

                        $dir .= '/'.$dir_rel;
                        if(!file_exists($dir)) @mkdir($dir, 0777, true);


                        if(!empty($fieldsData["fields"][$tag]["maxCount"]) && $fieldsData["fields"][$tag]["maxCount"] == '1' && !empty($field["name"])){


                            if(isset($field["__REPLACE__"])){ // если замена, то удаляем старую

                                $filePath = strstr($fieldsData["inBase"][$tag]["val"], ":", true);

                                if(file_exists(ROOT . "/uploads/fields/" . $filePath))
                                    unlink(ROOT . "/uploads/fields/" . $filePath);
                            }


                            $milliseconds = round(microtime(true) * 1000);
                            $ext = mb_strtolower(pathinfo($field["name"], PATHINFO_EXTENSION), 'UTF-8');
                            $file_name = $milliseconds.'_'.System::translit(strstr($field["name"], ".", true)).'.'.$ext;

                            $fileSize = filesize($field["tmp_name"]);
                            $fileInfo = ':' . $fileSize . ':' . $field["name"];

                            move_uploaded_file($field["tmp_name"], $dir . '/' . $file_name);
                            $val = $dir_rel . '/' . $file_name . $fileInfo;

                        } else{

                            $val = '';

                            foreach ($field["name"] as $key => $fileName) {

                                $milliseconds = round(microtime(true) * 1000);
                                $ext = mb_strtolower(pathinfo($fileName, PATHINFO_EXTENSION), 'UTF-8');
                                $file_name = $milliseconds.'_'.System::translit(strstr($fileName, ".", true)).'.'.$ext;

                                move_uploaded_file($field["tmp_name"][$key], $dir . '/' . $file_name);

                                $fileSize = filesize($dir . '/' . $file_name);
                                $fileInfo = ':' . $fileSize . ':' . $fileName;

                                $val .= $dir_rel . '/' . $file_name . $fileInfo . '|';
                            }
                            $val = trim($val, '|');

                        }

                        break;
                }

                if($val){

                    $resultAddFields[$tag] = $val;
                }
            }

            $FieldsModel = new FieldsModel();
            $FieldsModel->add($resultAddFields, $fieldsData["inBase"], $fieldsData["result"], $postId);
        }

        System::script($script);
    }


    /**
     * @name сохранение картинки из доп поля
     * =====================================
     * @param $tmp_name
     * @param $name
     * @param int|null $resize
     * @param int $quality
     * @param bool $thumb
     * @return false|string
     */
    private function saveImageField($tmp_name, $name, int $resize = null, int $quality = 100, bool $thumb = false){

        $result = false;

        $dir = ROOT . '/uploads/fields'; // если директория не создана
        $dir_rel = date("Y-m", time());

        //if(!file_exists($dir)) mkdir($dir, 0777, true);

        $dir .= '/'.$dir_rel;
        if(!file_exists($dir)) @mkdir($dir, 0777, true);

        if($thumb){
            $dir = $dir.'/thumbs';
            if(!file_exists($dir)) @mkdir($dir, 0777, true);
        }

        $ext = mb_strtolower(pathinfo($name, PATHINFO_EXTENSION), 'UTF-8'); // расширение файла

        if(
            $ext == 'png' ||
            $ext == 'jpeg' ||
            $ext == 'jpg' ||
            $ext == 'webp' ||
            $ext == 'svg' ||
            $ext == 'bmp' ||
            $ext == 'gif'
        ) {

            #TODO что-то намудрил, нужно переделать
            if(!$thumb){
                $milliseconds = round(microtime(true) * 1000);
                $image_name = $milliseconds.'_'.System::translit(strstr($name, ".", true)).'.'.$ext;
            } else{
                $tName = explode("/", $tmp_name);
                $image_name = str_replace($dir_rel."/", "", strstr(end($tName), ".", true)).'.'.$ext;
            }

            $result = $dir_rel . '/' . ($thumb?'thumbs/':'') . $image_name;

            if($resize){

                $image = new ImageManager();
                $img = $image->make($tmp_name)->resize(
                    $resize,
                    null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize(); // увеличивать только если оно больше чем нужно
                });
                $img->orientate();
                $img->save($dir . '/' . $image_name, $quality);

            } else move_uploaded_file($tmp_name, $dir . '/' . $image_name);


            // image info
            $imageSize = getimagesize($dir . '/' . $image_name);
            $fileSize = filesize($dir . '/' . $image_name);

            $result .= ':' . $imageSize[0].'*'.$imageSize[1] . ':' . $fileSize . ':' . $name;
        }

        return $result;
    }





    /**
     * @name удаление новости
     * ======================
     * @return void
     * @throws Exception
     */
    private function deletePost(){

        $postId = intval($_POST["deletePost"]);

        if(empty($_POST['confirm'])){
            $script = '<script>
                $.confirm("Вы уверены, что хотите удалить?", function(e){
                    if(e) $.ajaxSend($(this), {"ajax": "Post", "deletePost": "'.$postId.'", "confirm": 1});
                })
            </script>';

            die(System::script($script));

        } else {

            $PostModel = new PostModel();
            $images = $PostModel->getImages($postId);

            if(!empty($images)){
                foreach ($images as $image) {
                    @unlink(ROOT . '/uploads/post/'.$image["src"]);
                    @unlink(ROOT . '/uploads/post/'.str_replace("/", "/thumbs/", $image["src"]));
                }
            }

            $result = $PostModel->delete($postId);

            if($result){

                $script = '<script>
                    $(\'[data-a="Post:deletePost='.$postId.'"]\').closest("tr").remove();
                    $.server_say({say: "Удалено!", status: "success"});
                </script>';
                System::script($script);

            } else{

                die("info::error::Не удалось удалить новость!");
            }
        }
    }


    /**
     * @name сортировка изображений
     * ============================
     * @return void
     * @throws Exception
     */
    private function sortImages(){

        if(!empty($_POST["newSortImages"])){

            $PostModel = new PostModel();
            foreach ($_POST["newSortImages"] as $position => $imageId) {

                $PostModel->editPositionImage($imageId, $position);
            }
        }

        die("info::success::Сортировка изменена!");
    }




    /**
     * @name установка постера
     * =======================
     * @return void
     * @throws Exception
     */
    private function setMainImage($postId){

        $PostModel = new PostModel();
        $PostModel->setPoster($postId, intval($_POST["setMainImage"]));
        $script = '<script>
                $(".is_main").removeClass("is_main");
                $(".nex_tmp").addClass("is_main");
                $.server_say({say: "Постер установлен!", status: "success"});
            </script>';
        System::script($script);
    }





    /**
     * @name изменение статуса новости
     * ===============================
     * @return void
     * @throws Exception
     */
    private function editStatus(){

        $postId = intval($_POST["postId"]);
        $statusPost = ($_POST["statusPost"] == 'true') ? 1 : 0;

        $PostModel = new PostModel();
        $result = $PostModel->editFields($postId, ["status" => $statusPost]);

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
     * @name редактирование фото новости
     * =================================
     * @return void
     * @throws Exception
     */
    private function editImage(){

        $id = intval($_POST["photo"]["id"]);
        $alt = trim(htmlspecialchars(strip_tags($_POST["photo"]["alt"])));

        $PostModel = new PostModel();
        $PostModel->editFieldsImages($id, ["alt" => $alt]);

        $script = '<script>
            $("#editPhoto, .bg_0").fadeOut(300);
            $.server_say({say: "Изменил!", status: "success"});
        </script>';
        System::script($script);
    }





    /**
     * @name удаление картинки
     * =======================
     * @return void
     * @throws Exception
     */
    private function deleteImage(){

        $deleteImage = intval($_POST["deleteImage"]);
        $link = trim(htmlspecialchars(strip_tags($_POST["link"])));

        unlink(ROOT . "/uploads/posts/".$link);

        $PostModel = new PostModel();
        $PostModel->deleteImage($deleteImage);

        $script = '<script>
            $(".nex_tmp").closest(".img_item").remove();
            $.server_say({say: "Удалено!", status: "success"});
        </script>';
        System::script($script);
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

                $dir = ROOT . '/uploads/posts'; // если директория не создана
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

}