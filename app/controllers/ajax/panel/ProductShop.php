<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\ProductModel;
use Exception;
use Intervention\Image\ImageManager;


class ProductShop{

    public function index(){

        if(!empty($_POST["title"])) self::createEditProduct(); // создание редактирование товара
        if(!empty($_POST["deleteProperty"])) self::deleteProperty(intval($_POST["deleteProperty"])); // удаление одного свойства товара
        if(!empty($_POST["pp_ids"])) self::deleteProperties($_POST["pp_ids"]); // удаление нескольких свойств товара
    }


    /**
     * @name создание / редактирование товара
     * @return void
     * @throws Exception
     */
    private function createEditProduct(){

        preg_match('/edit\/([0-9]+)\//is', $_GET["url"], $edit_id);

        $title = !empty($_POST["title"]) ? trim(htmlspecialchars(strip_tags($_POST["title"]))) : die("info::error::Укажите название!");
        $price = !empty($_POST["price"]) ? floatval($_POST["price"]) : die("info::error::Укажите цену!");
        $url = !empty($_POST["url"]) ? System::translit(trim(htmlspecialchars(strip_tags($_POST["url"])))) : System::translit($title);
        $vendor = !empty($_POST["vendor"]) ? trim(htmlspecialchars(strip_tags($_POST["vendor"]))) : '';
        $sale = !empty($_POST["sale"]) ? trim(htmlspecialchars(strip_tags($_POST["sale"]))) : null;
        $stock = !empty($_POST["stock"]) ? intval($_POST["stock"]) : null;
        $created = !empty($_POST["created"]) ? strtotime($_POST["created"]) : null;
        $content = !empty($_POST["content"]) ? trim($_POST["content"]) : '';
        $status = !empty($_POST["status"]) ? 1 : 0;

        $content = str_replace('<p data-f-id="pbf" style="text-align: center; font-size: 14px; margin-top: 30px; opacity: 0.65; font-family: sans-serif;">Powered by <a href="https://www.froala.com/wysiwyg-editor?pb=1" title="Froala Editor">Froala Editor</a></p>', '', $content);

        $meta["title"] = !empty($_POST["meta"]["title"]) ? trim(htmlspecialchars(strip_tags($_POST["meta"]["title"]))) : '';
        $meta["description"] = !empty($_POST["meta"]["description"]) ? trim(htmlspecialchars(strip_tags($_POST["meta"]["description"]))) : '';

        $addScript = '';

        $ProductModel = new ProductModel();

        if(empty($edit_id[1])){ // если это добавление новой категории

            $id = $ProductModel->create($title, $vendor, $meta, $content, $price, $sale, $stock, $url, $created, $status);

            if(!empty($_FILES["images"])){
                $images = $this->uploadImages($id);
                $addScript = '$("#product_images").append(`';
                foreach ($images as $image) {
                    $addScript .= '<div class="img_item"><a href="'.CONFIG_SYSTEM["home"].'uploads/products/'.$image.'" data-fancybox="gallery"><img src="'.CONFIG_SYSTEM["home"].'uploads/products/'.str_replace('/', '/thumbs/', $image).'"></a></div>';
                    $ProductModel->addImage(1, $id, $image);
                }
                $addScript .= '`);$(".files_preload").html("").hide();';
            }


            // если были заданы свойства
            if(!empty($_POST["prop"])){

                foreach ($_POST["prop"] as $propArray) {

                    foreach ($propArray["id"] as $prop_key => $id_prop) {

                        $ProductModel->addProperty(
                            $id,
                            intval($id_prop),
                            trim(htmlspecialchars(strip_tags($propArray["vendor"][$prop_key]))),
                            floatval($propArray["price"][$prop_key]),
                            !empty($propArray["stock"][$prop_key]) ? intval($propArray["stock"][$prop_key]) : null
                        );
                    }
                }
            }


            $script = '<script>
                '.$addScript.'
                $.server_say({say: "Товар создана!", status: "success"});
                history.pushState(null, "Редактирование товара", "'.CONFIG_SYSTEM["home"].CONFIG_SYSTEM["panel"].'/products/edit/'.$id.'/");
            </script>';

        } else{ // если редактирование

            $id = intval($edit_id[1]);
            $ProductModel->editFields($id, [
                'title' => $title,
                'vendor' => $vendor,
                'm_title' => $meta["title"],
                'm_description' => $meta["description"],
                'content' => $content,
                'price' => $price,
                'sale' => $sale,
                'stock' => $stock,
                'url' => $url,
                'created' => $created,
                'status' => $status
            ]);

            if(!empty($_FILES["images"])){
                $images = $this->uploadImages($id);
                $addScript = '$("#product_images").append(`';
                foreach ($images as $image) {
                    $addScript .= '<div class="img_item"><a href="'.CONFIG_SYSTEM["home"].'uploads/products/'.$image.'" data-fancybox="gallery"><img src="'.CONFIG_SYSTEM["home"].'uploads/products/'.str_replace('/', '/thumbs/', $image).'"></a></div>';
                    $ProductModel->addImage(1, $id, $image);
                }
                $addScript .= '`);$(".files_preload").html("").hide();';
            }

            // если были заданы свойства
            if(!empty($_POST["prop"])){

                foreach ($_POST["prop"] as $propArray) {

                    foreach ($propArray["id"] as $prop_key => $id_prop) {

                        if(!empty($propArray["pp_id"][$prop_key])){

                            $ProductModel->editProperty(
                                intval($propArray["pp_id"][$prop_key]),
                                $id,
                                intval($id_prop),
                                trim(htmlspecialchars(strip_tags($propArray["vendor"][$prop_key]))),
                                floatval($propArray["price"][$prop_key]),
                                !empty($propArray["stock"][$prop_key]) ? intval($propArray["stock"][$prop_key]) : null
                            );

                        } else{

                            $ProductModel->addProperty(
                                $id,
                                intval($id_prop),
                                trim(htmlspecialchars(strip_tags($propArray["vendor"][$prop_key]))),
                                floatval($propArray["price"][$prop_key]),
                                !empty($propArray["stock"][$prop_key]) ? intval($propArray["stock"][$prop_key]) : null
                            );
                        }
                    }
                }
            }

            $script = '<script>
                '.$addScript.'
                $("h1 b").text(`'.$title.'`);
                $.server_say({say: "Изменения сохранены!", status: "success"});
            </script>';
        }

        System::script($script);
    }





    /**
     * @name удаление свойства в товаре
     * ================================
     * @param $property_id
     * @return void
     * @throws Exception
     */
    private function deleteProperty($property_id){

        $ProductModel = new ProductModel();
        $result = $ProductModel->deleteProperty($property_id);
        
        if($result){

            $script = '<script>
                $(".nex_tmp").closest(".prop_sub").remove();
                $.server_say({say: "Удалено!", status: "success"});
            </script>';
            System::script($script);

        } else{

            die("info::error::Не удалось удалить свойство!");
        }
    }





    /**
     * @name удаление свойств в товаре
     * ===============================
     * @param $properties_ids
     * @return void
     * @throws Exception
     */
    private function deleteProperties($properties_ids){
        $ProductModel = new ProductModel();
        $ProductModel->deleteProperty($properties_ids);
        die("info::success::Не удалось удалить свойство!");
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
                $image_name = $id.'_'.time().'_'.System::translit($image).'.'.$ext;



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