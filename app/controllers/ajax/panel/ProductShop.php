<?php

namespace app\controllers\ajax\panel;

use app\core\System;
use app\models\ProductModel;
use Exception;


class ProductShop{

    public function index(){


        if(!empty($_POST["title"])) self::createEditProduct(); // создание редактирование товара
        if(!empty($_POST["deleteProperty"])) self::deleteProperty(); // удаление одного свойства товара
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

            if(!empty($_FILES["icon"])){
                $icon = $this->uploadIcon($id);
                $ProductModel->editFields($id, ['icon' => $icon]);
                $addScript = '$(".category_icon").html(`<img src="'.CONFIG_SYSTEM["home"].'uploads/categories/'.$icon.'">`);';
            }


            // если были заданы свойства
            if(!empty($_POST["prop"])){

                foreach ($_POST["prop"] as $prod_id => $propArray) {

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

            if(!empty($_FILES["icon"])){
                $icon = $this->uploadIcon($id);
                $ProductModel->editFields($id, ['icon' => $icon]);
                $addScript = '$(".category_icon").html(`<img src="'.CONFIG_SYSTEM["home"].'uploads/categories/'.$icon.'">`);';
            }

            // если были заданы свойства
            if(!empty($_POST["prop"])){

                foreach ($_POST["prop"] as $prod_id => $propArray) {

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
                $.server_say({say: "Категория изменена!", status: "success"});
            </script>';
        }

        System::script($script);
    }





    private function deleteProperty(){}





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