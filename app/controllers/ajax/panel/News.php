<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\panel\NewsModel;
use Exception;
use Intervention\Image\ImageManager;


class News{

    public function index(){

        preg_match('/edit\/([0-9]+)\//is', $_GET["url"], $news);
        $newsId = !empty($news[1]) ? intval($news[1]) : null;

        if(!empty($_POST["news"])) self::createEditNews($newsId); // создание редактирование новости
        if(!empty($_POST["deleteNews"])) self::deleteNews(); // удаление новости
        if(!empty($_POST["statusNews"])) self::editStatus(); // изменение активности
        if(!empty($_POST["newSortImages"])) self::sortImages(); // сортировка изображения новости
        if(!empty($_POST["setMainImage"])) self::setMainImage($newsId); // установка постера
        if(!empty($_POST["deleteImage"])) self::deleteImage(); // удаление фото новости
        if(!empty($_POST["photo"])) self::editImage(); // редактирование фото новости
    }


    /**
     * @name создание / редактирование новости
     * @return void
     * @throws Exception
     */
    private function createEditNews($newsId){

        $title = !empty($_POST["title"]) ? trim(htmlspecialchars(strip_tags($_POST["title"]))) : die("info::error::Укажите название!");
        $url = !empty($_POST["url"]) ? System::translit(trim(htmlspecialchars(strip_tags($_POST["url"])))) : System::translit($title);
        $created = !empty($_POST["created"]) ? strtotime($_POST["created"]) : null;
        $short = !empty($_POST["short"]) ? trim($_POST["short"]) : '';
        $content = !empty($_POST["content"]) ? trim($_POST["content"]) : '';
        $category = !empty($_POST["category"]) ? $_POST["category"] : die("info::error::Выберите категорию!");
        $status = !empty($_POST["status"]) ? 1 : 0;

        $meta["title"] = !empty($_POST["meta"]["title"]) ? trim(htmlspecialchars(strip_tags($_POST["meta"]["title"]))) : '';
        $meta["description"] = !empty($_POST["meta"]["description"]) ? trim(htmlspecialchars(strip_tags($_POST["meta"]["description"]))) : '';

        $addScript = '';

        $NewsModel = new NewsModel();

        if(!$newsId){ // если это добавление новой категории

            $id = $NewsModel->create($title, $meta, $short, $content, $category, $url, $created, $status);

            if(!empty($_FILES["images"])){
                $images = $this->uploadImages($id);
                $addScript = '$("#news_images").append(`';
                foreach ($images as $image) {
                    $imgId = $NewsModel->addImage(1, $id, $image);
                    $addScript .= '<div class="img_item"><a href="//'.CONFIG_SYSTEM["home"].'/uploads/news/'.$image.'" data-fancybox="gallery"><img src="//'.CONFIG_SYSTEM["home"].'/uploads/news/'.str_replace('/', '/thumbs/', $image).'"></a><a href="#" class="edit_image" data-img-id="'.$imgId.'"></a><a href="#" class="delete_image" data-a="News:deleteImage='.$imgId.'&link='.$image.'"></a></div>';
                }
                $addScript .= '`);$(".files_preload").html("").hide();';
            }


            $script = '<script>
                '.$addScript.'
                $.server_say({say: "Новость создана!", status: "success"});
                history.pushState(null, "Редактирование новости", "//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/news/edit/'.$id.'/");
            </script>';

        } else{ // если редактирование

            $NewsModel->editFields($newsId, [
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
                $images = $this->uploadImages($newsId);
                $addScript = '$("#news_images").append(`';
                foreach ($images as $image) {
                    $imgId = $NewsModel->addImage(1, $newsId, $image);
                    $addScript .= '<div class="img_item"><a href="//'.CONFIG_SYSTEM["home"].'/uploads/news/'.$image.'" data-fancybox="gallery"><img src="//'.CONFIG_SYSTEM["home"].'/uploads/news/'.str_replace('/', '/thumbs/', $image).'"></a><a href="#" class="edit_image" data-img-id="'.$imgId.'"></a><a href="#" class="delete_image" data-a="News:deleteImage='.$imgId.'&link='.$image.'"></a></div>';
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

        System::script($script);
    }





    /**
     * @name удаление новости
     * ======================
     * @return void
     * @throws Exception
     */
    private function deleteNews(){

        $newsId = intval($_POST["deleteNews"]);

        if(empty($_POST['confirm'])){
            $script = '<script>
                $.confirm("Вы уверены, что хотите удалить?", function(e){
                    if(e) $.ajaxSend($(this), {"ajax": "News", "deleteNews": "'.$newsId.'", "confirm": 1});
                })
            </script>';

            die(System::script($script));
        }

        if(!empty($_POST['confirm'])){

            $NewsModel = new NewsModel();
            $images = $NewsModel->getImages($newsId);

            if(!empty($images)){
                foreach ($images as $image) {
                    @unlink(ROOT . '/uploads/news/'.$image["src"]);
                    @unlink(ROOT . '/uploads/news/'.str_replace("/", "/thumbs/", $image["src"]));
                }
            }

            $result = $NewsModel->delete($newsId);

            if($result){

                $script = '<script>
                    $(\'[data-a="News:deleteNews='.$newsId.'"]\').closest("tr").remove();
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

            $NewsModel = new NewsModel();
            foreach ($_POST["newSortImages"] as $position => $imageId) {

                $NewsModel->editPositionImage($imageId, $position);
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
    private function setMainImage($newsId){

        $NewsModel = new NewsModel();
        $NewsModel->setPoster($newsId, intval($_POST["setMainImage"]));
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

        $newsId = intval($_POST["newsId"]);
        $statusNews = ($_POST["statusNews"] == 'true') ? 1 : 0;

        $NewsModel = new NewsModel();
        $result = $NewsModel->editFields($newsId, ["status" => $statusNews]);

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

        $NewsModel = new NewsModel();
        $NewsModel->editFieldsImages($id, ["alt" => $alt]);

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

        unlink(ROOT . "/uploads/news/".$link);

        $NewsModel = new NewsModel();
        $NewsModel->deleteImage($deleteImage);

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

                $dir = ROOT . '/uploads/news'; // если директория не создана
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