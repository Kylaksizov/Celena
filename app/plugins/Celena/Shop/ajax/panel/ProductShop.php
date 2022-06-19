<?php

namespace app\plugins\Celena\Shop\ajax\panel;

use app\core\System;
use app\models\panel\FieldsModel;
use app\models\plugins\Celena\Shop\panel\ProductModel;
use Exception;
use Intervention\Image\ImageManager;


class ProductShop{

    public function index(){

        preg_match('/edit\/([0-9]+)\//is', $_GET["url"], $product);
        $productId = !empty($product[1]) ? intval($product[1]) : null;

        if(!empty($_POST["product"])) self::createEditProduct($productId); // создание редактирование товара
        if(!empty($_POST["deleteProduct"])) self::deleteProduct($productId); // удаление товара
        if(!empty($_POST["statusProduct"])) self::editStatus(); // изменение активности
        if(!empty($_POST["deleteProperty"])) self::deleteProperty(intval($_POST["deleteProperty"])); // удаление одного свойства товара
        if(!empty($_POST["newSortImages"])) self::sortImages(); // сортировка изображения товаров
        if(!empty($_POST["setMainImage"])) self::setMainImage($productId); // установка постера
        if(!empty($_POST["pp_ids"])) self::deleteProperties($_POST["pp_ids"]); // удаление нескольких свойств товара
        if(!empty($_POST["deleteImage"])) self::deleteImage(); // удаление фото товара
        if(!empty($_POST["photo"])) self::editImage(); // редактирование фото товара
    }


    /**
     * @name создание / редактирование товара
     * @return void
     * @throws Exception
     */
    private function createEditProduct($productId){

        $title = !empty($_POST["title"]) ? trim(htmlspecialchars(strip_tags($_POST["title"]))) : die("info::error::Укажите название!");
        $price = !empty($_POST["price"]) ? floatval($_POST["price"]) : die("info::error::Укажите цену!");
        $url = !empty($_POST["url"]) ? System::translit(trim(htmlspecialchars(strip_tags($_POST["url"])))) : System::translit($title);
        $vendor = !empty($_POST["vendor"]) ? trim(htmlspecialchars(strip_tags($_POST["vendor"]))) : '';
        $sale = !empty($_POST["sale"]) ? trim(htmlspecialchars(strip_tags($_POST["sale"]))) : null;
        $stock = !empty($_POST["stock"]) ? intval($_POST["stock"]) : null;
        $created = !empty($_POST["created"]) ? strtotime($_POST["created"]) : null;
        $content = !empty($_POST["content"]) ? trim($_POST["content"]) : '';
        $category = !empty($_POST["category"]) ? $_POST["category"] : die("info::error::Выберите категорию!");
        $brand = !empty($_POST["brand"]) ? intval($_POST["brand"]) : null;
        $status = !empty($_POST["status"]) ? 1 : 0;

        $content = str_replace('<p data-f-id="pbf" style="text-align: center; font-size: 14px; margin-top: 30px; opacity: 0.65; font-family: sans-serif;">Powered by <a href="https://www.froala.com/wysiwyg-editor?pb=1" title="Froala Editor">Froala Editor</a></p>', '', $content);

        $meta["title"] = !empty($_POST["meta"]["title"]) ? trim(htmlspecialchars(strip_tags($_POST["meta"]["title"]))) : '';
        $meta["description"] = !empty($_POST["meta"]["description"]) ? trim(htmlspecialchars(strip_tags($_POST["meta"]["description"]))) : '';

        $fieldsData = null;
        if(!empty($_POST["field"])){
            $fieldsData = \app\traits\Fields::getPostFields($productId, $_POST["field"], $category);
        }

        $addScript = '';

        $ProductModel = new ProductModel();

        if(!$productId){ // если это добавление новой категории

            $id = $ProductModel->create($title, $vendor, $meta, $content, $category, $brand, $price, $sale, $stock, $url, $created, $status);

            if(!empty($_FILES["images"])){
                $images = $this->uploadImages($id);
                $addScript = '$("#product_images").append(`';
                foreach ($images as $image) {
                    $imgId = $ProductModel->addImage(1, $id, $image);
                    $addScript .= '<div class="img_item"><a href="//'.CONFIG_SYSTEM["home"].'/uploads/products/'.$image.'" data-fancybox="gallery"><img src="//'.CONFIG_SYSTEM["home"].'/uploads/products/'.str_replace('/', '/thumbs/', $image).'"></a><a href="#" class="edit_image" data-img-id="'.$imgId.'"></a><a href="#" class="delete_image" data-a="ProductShop:deleteImage='.$imgId.'&link='.$image.'"></a></div>';
                }
                $addScript .= '`);$(".files_preload").html("").hide();';
            }


            // если были заданы свойства
            if(!empty($_POST["prop"])){

                foreach ($_POST["prop"] as $property_id => $propArray) {

                    foreach ($propArray["id"] as $prop_key => $id_pv) {

                        if(is_numeric($id_pv)){
                            $sep = '';
                            $id_pv = intval($id_pv);
                        } else{
                            $sep = trim(htmlspecialchars(strip_tags($id_pv)));
                            $id_pv = 0;
                        }

                        $pv = null;
                        if($propArray["pv"][$prop_key] == '-')  $pv = '0';
                        if($propArray["pv"][$prop_key] == '+')  $pv = '1';
                        if($propArray["pv"][$prop_key] == '-%') $pv = '2';
                        if($propArray["pv"][$prop_key] == '+%') $pv = '3';

                        $ProductModel->addProperty(
                            $id,
                            $property_id,
                            $id_pv,
                            $sep,
                            trim(htmlspecialchars(strip_tags($propArray["vendor"][$prop_key]))),
                            floatval($propArray["price"][$prop_key]),
                            $pv,
                            !empty($propArray["stock"][$prop_key]) ? intval($propArray["stock"][$prop_key]) : null
                        );
                    }
                }
            }


            $script = '<script>
                '.$addScript.'
                $.server_say({say: "Товар создана!", status: "success"});
                history.pushState(null, "Редактирование товара", "//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/products/edit/'.$id.'/");
            </script>';

        } else{ // если редактирование

            $ProductModel->editFields($productId, [
                'title' => $title,
                'vendor' => $vendor,
                'm_title' => $meta["title"],
                'm_description' => $meta["description"],
                'content' => $content,
                'category' => implode(",", $category),
                'brand' => $brand,
                'price' => $price,
                'sale' => $sale,
                'stock' => $stock,
                'url' => $url,
                'status' => $status
            ]);

            if(!empty($_FILES["images"])){
                $images = $this->uploadImages($productId);
                $addScript = '$("#product_images").append(`';
                foreach ($images as $image) {
                    $imgId = $ProductModel->addImage(1, $productId, $image);
                    $addScript .= '<div class="img_item"><a href="//'.CONFIG_SYSTEM["home"].'/uploads/products/'.$image.'" data-fancybox="gallery"><img src="//'.CONFIG_SYSTEM["home"].'/uploads/products/'.str_replace('/', '/thumbs/', $image).'"></a><a href="#" class="edit_image" data-img-id="'.$imgId.'"></a><a href="#" class="delete_image" data-a="ProductShop:deleteImage='.$imgId.'&link='.$image.'"></a></div>';
                }
                $addScript .= '`);$(".files_preload").html("").hide();';
            }

            // если были заданы свойства
            if(!empty($_POST["prop"])){

                foreach ($_POST["prop"] as $property_id => $propArray) {

                    foreach ($propArray["id"] as $prop_key => $id_pv) {

                        if(is_numeric($id_pv)){
                            $sep = '';
                            $id_pv = intval($id_pv);
                        } else{
                            $sep = trim(htmlspecialchars(strip_tags($id_pv)));
                            $id_pv = 0;
                        }

                        $pv = null;
                        if($propArray["pv"][$prop_key] == '-')  $pv = '0';
                        if($propArray["pv"][$prop_key] == '+')  $pv = '1';
                        if($propArray["pv"][$prop_key] == '-%') $pv = '2';
                        if($propArray["pv"][$prop_key] == '+%') $pv = '3';

                        if(!empty($propArray["pp_id"][$prop_key])){

                            $ProductModel->editProperty(
                                intval($propArray["pp_id"][$prop_key]),
                                $productId,
                                $property_id,
                                $id_pv,
                                $sep,
                                trim(htmlspecialchars(strip_tags($propArray["vendor"][$prop_key]))),
                                floatval($propArray["price"][$prop_key]),
                                $pv,
                                !empty($propArray["stock"][$prop_key]) ? intval($propArray["stock"][$prop_key]) : null
                            );

                        } else{

                            $ProductModel->addProperty(
                                $productId,
                                $property_id,
                                $id_pv,
                                $sep,
                                trim(htmlspecialchars(strip_tags($propArray["vendor"][$prop_key]))),
                                floatval($propArray["price"][$prop_key]),
                                $pv,
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
            $FieldsModel->add($resultAddFields, $fieldsData["inBase"], $fieldsData["result"], $productId, PLUGIN["plugin_id"]);
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
     * @name удаление товара
     * =====================
     * @return void
     * @throws Exception
     */
    private function deleteProduct(){

        $productId = intval($_POST["deleteProduct"]);

        if(empty($_POST['confirm'])){
            $script = '<script>
                $.confirm("Вы уверены, что хотите удалить?", function(e){
                    if(e) $.ajaxSend($(this), {"ajax": "ProductShop", "deleteProduct": "'.$productId.'", "confirm": 1});
                })
            </script>';

            die(System::script($script));
        }

        if(!empty($_POST['confirm'])){

            $ProductModel = new ProductModel();
            $images = $ProductModel->getImages($productId);
            
            if(!empty($images)){
                foreach ($images as $image) {
                    @unlink(ROOT . '/uploads/products/'.$image["src"]);
                    @unlink(ROOT . '/uploads/products/'.str_replace("/", "/thumbs/", $image["src"]));
                }
            }
            
            $result = $ProductModel->delete($productId);

            if($result){

                $script = '<script>
                    $(\'[data-a="ProductShop:deleteProduct='.$productId.'"]\').closest("tr").remove();
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

            $ProductModel = new ProductModel();
            foreach ($_POST["newSortImages"] as $position => $imageId) {

                $ProductModel->editPositionImage($imageId, $position);
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
    private function setMainImage($productId){

        $ProductModel = new ProductModel();
        $ProductModel->setPoster($productId, intval($_POST["setMainImage"]));
        $script = '<script>
                $(".is_main").removeClass("is_main");
                $(".nex_tmp").addClass("is_main");
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

        $productId = intval($_POST["productId"]);
        $statusProduct = ($_POST["statusProduct"] == 'true') ? 1 : 0;

        $ProductModel = new ProductModel();
        $result = $ProductModel->editFields($productId, ["status" => $statusProduct]);

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
     * @name редактирование фото товара
     * =================================
     * @return void
     * @throws Exception
     */
    private function editImage(){

        $id = intval($_POST["photo"]["id"]);
        $alt = trim(htmlspecialchars(strip_tags($_POST["photo"]["alt"])));

        $ProductModel = new ProductModel();
        $ProductModel->editFieldsImages($id, ["alt" => $alt]);

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

        unlink(ROOT . "/uploads/products/".$link);

        $ProductModel = new ProductModel();
        $ProductModel->deleteImage($deleteImage);

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