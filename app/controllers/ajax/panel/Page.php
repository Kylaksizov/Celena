<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\panel\PageModel;
use Exception;
use Intervention\Image\ImageManager;


class Page{

    public function index(){

        preg_match('/edit\/([0-9]+)\//is', $_GET["url"], $page);
        $pageId = !empty($page[1]) ? intval($page[1]) : null;

        if(!empty($_POST["page"])) self::createEditPage($pageId); // создание редактирование
        if(!empty($_POST["deletePage"])) self::deletePage(); // удаление страницы
        if(!empty($_POST["statusPage"])) self::editStatus(); // изменение активности
        if(!empty($_POST["setMainImage"])) self::setMainImage($pageId); // установка постера
        if(!empty($_POST["newSortImages"])) self::sortImages(); // сортировка изображений
        if(!empty($_POST["deleteImage"])) self::deleteImage(); // удаление фото
        if(!empty($_POST["photo"])) self::editImage(); // редактирование фото
    }


    /**
     * @name создание / редактирование страницы
     * @return void
     * @throws Exception
     */
    private function createEditPage($pageId){

        $title = !empty($_POST["title"]) ? trim(htmlspecialchars(strip_tags($_POST["title"]))) : die("info::error::Укажите название!");
        $url = !empty($_POST["url"]) ? System::translit(trim(htmlspecialchars(strip_tags($_POST["url"])))) : System::translit($title);
        $created = !empty($_POST["created"]) ? strtotime($_POST["created"]) : time();
        $content = !empty($_POST["content"]) ? trim($_POST["content"]) : '';
        $tpl = !empty($_POST["tpl"]) ? trim($_POST["tpl"]) : '';
        $status = !empty($_POST["status"]) ? 1 : 0;

        $meta["title"] = !empty($_POST["meta"]["title"]) ? trim(htmlspecialchars(strip_tags($_POST["meta"]["title"]))) : '';
        $meta["description"] = !empty($_POST["meta"]["description"]) ? trim(htmlspecialchars(strip_tags($_POST["meta"]["description"]))) : '';

        $addScript = '';

        $PageModel = new PageModel();

        if(!$pageId){ // если это добавление новой категории

            $id = $PageModel->create($title, $meta, $content, $url, $tpl, $created, $status);

            if(!empty($_FILES["images"])){
                $images = $this->uploadImages($id);
                $addScript = '$("#product_images").append(`';
                foreach ($images as $image) {
                    $imgId = $PageModel->addImage($id, $image);
                    $addScript .= '<div class="img_item"><a href="//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.$image.'" data-fancybox="gallery"><img src="//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.str_replace('/', '/thumbs/', $image).'"></a><a href="#" class="edit_image" data-img-id="'.$imgId.'"></a><a href="#" class="delete_image" data-a="Page:deleteImage='.$imgId.'&link='.$image.'"></a></div>';
                }
                $addScript .= '`);$(".files_preload").html("").hide();';
            }


            $script = '<script>
                '.$addScript.'
                $.server_say({say: "Страница создана!", status: "success"});
                history.pushState(null, "Редактирование страницы", "//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/pages/edit/'.$id.'/");
            </script>';

        } else{ // если редактирование

            $PageModel->editFields($pageId, [
                'title' => $title,
                'm_title' => $meta["title"],
                'm_description' => $meta["description"],
                'content' => $content,
                'url' => $url,
                'tpl' => $tpl,
                'created' => $created,
                'status' => $status
            ]);

            if(!empty($_FILES["images"])){
                $images = $this->uploadImages($pageId);
                $addScript = '$("#page_images").append(`';
                foreach ($images as $image) {
                    $imgId = $PageModel->addImage($pageId, $image);
                    $addScript .= '<div class="img_item"><a href="//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.$image.'" data-fancybox="gallery"><img src="//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.str_replace('/', '/thumbs/', $image).'"></a><a href="#" class="edit_image" data-img-id="'.$imgId.'"></a><a href="#" class="delete_image" data-a="Page:deleteImage='.$imgId.'&link='.$image.'"></a></div>';
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
     * @name удаление товара
     * =====================
     * @return void
     * @throws Exception
     */
    private function deletePage(){

        $pageId = intval($_POST["deletePage"]);

        if(empty($_POST['confirm'])){
            $script = '<script>
                $.confirm("Вы уверены, что хотите удалить?", function(e){
                    if(e) $.ajaxSend($(this), {"ajax": "Page", "deletePage": "'.$pageId.'", "confirm": 1});
                })
            </script>';

            die(System::script($script));
        }

        if(!empty($_POST['confirm'])){

            $PageModel = new PageModel();
            $images = $PageModel->getImages($pageId);

            if(!empty($images)){
                foreach ($images as $image) {
                    @unlink(ROOT . '/uploads/posts/'.$image["src"]);
                    @unlink(ROOT . '/uploads/posts/'.str_replace("/", "/thumbs/", $image["src"]));
                }
            }

            $result = $PageModel->delete($pageId);

            if($result){

                $script = '<script>
                    $(\'[data-a="Page:deletePage='.$pageId.'"]\').closest("tr").remove();
                    $.server_say({say: "Удалено!", status: "success"});
                </script>';
                System::script($script);

            } else{

                die("info::error::Не удалось удалить товар!");
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

            $PageModel = new PageModel();
            foreach ($_POST["newSortImages"] as $position => $imageId) {
                $PageModel->editPositionImage($imageId, $position);
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
    private function setMainImage($pageId){

        $PageModel = new PageModel();
        $PageModel->setPoster($pageId, intval($_POST["setMainImage"]));
        $script = '<script>
                $(".is_main").removeClass("is_main");
                $(".cel_tmp").addClass("is_main");
                $.server_say({say: "Постер установлен!", status: "success"});
            </script>';
        System::script($script);
    }





    /**
     * @name изменение статуса товара
     * ==============================
     * @return void
     * @throws Exception
     */
    private function editStatus(){

        $pageId = intval($_POST["pageId"]);
        $statusPage = ($_POST["statusPage"] == 'true') ? 1 : 0;

        $PageModel = new PageModel();
        $result = $PageModel->editFields($pageId, ["status" => $statusPage]);

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
     * @name редактирование фото товара
     * =================================
     * @return void
     * @throws Exception
     */
    private function editImage(){

        $id = intval($_POST["photo"]["id"]);
        $alt = trim(htmlspecialchars(strip_tags($_POST["photo"]["alt"])));

        $PageModel = new PageModel();
        $PageModel->editFieldsImages($id, ["alt" => $alt]);

        $script = '<script>
            $("#editPhoto, .bg_0").fadeOut(300);
            $.server_say({say: "Изменил!", status: "success"});
        </script>';
        System::script($script);
    }





    /**
     * @name удаление свойств в товаре
     * ===============================
     * @return void
     * @throws Exception
     */
    private function deleteImage(){

        $deleteImage = intval($_POST["deleteImage"]);
        $link = trim(htmlspecialchars(strip_tags($_POST["link"])));

        unlink(ROOT . "/uploads/posts/".$link);

        $PageModel = new PageModel();
        $PageModel->deleteImage($deleteImage);

        $script = '<script>
            $(".cel_tmp").closest(".img_item").remove();
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