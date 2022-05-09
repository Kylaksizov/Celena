<?php

namespace app\controllers\ajax;

use app\core\System;
use app\models\OrderModel;

class Cart{



    public function index(){

        if(!empty($_POST["products"])) self::createOrder(); // оформление заказа
    }


    public function createOrder(){
        
        $name = trim(htmlspecialchars(strip_tags($_POST["name"])));
        $email = trim(htmlspecialchars(strip_tags($_POST["email"])));
        $tel = trim(htmlspecialchars(strip_tags($_POST["tel"])));
        $address = !empty($_POST["address"]) ? trim(htmlspecialchars(strip_tags($_POST["address"]))) : '';
        $total = floatval($_POST["total"]);
        $comment = trim(htmlspecialchars(strip_tags($_POST["comment"])));
        $products = json_decode($_POST["products"], true);

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) die("info::error::Указанный email не действительный!");

        $products_ids = [];
        $counter = [];
        $properties = [];
        foreach ($products as $product) {
            $products_ids[] = $product["id"];
            $counter[$product["id"]] = $product["count"];

            // если были заданы свойства
            if(!empty($product["properties"])){

                foreach ($product["properties"] as $propsKey => $propertyArray) {
                    $properties[$product["id"]][$propsKey] = [];
                    $propertyArray = !empty($propertyArray[0]) ? $propertyArray[0] : $propertyArray;
                    foreach ($propertyArray as $propertyIn) {

                        if(count($propertyIn) > 1){
                            foreach ($propertyIn as $item) {
                                array_push($properties[$product["id"]][$propsKey], $item["pid"]);
                            }
                        } else{
                            array_push($properties[$product["id"]][$propsKey], $propertyIn[0]["pid"]);
                        }
                    }
                    $properties[$product["id"]][$propsKey] = implode(",", $properties[$product["id"]][$propsKey]);
                }
            }
        }

        $OrderModel = new OrderModel();
        $orderId = $OrderModel->create(null, null, $name, $email, $tel, $address, 0, $products_ids, $total, $comment);

        if($orderId){

            if(!empty($properties)){
                foreach ($products_ids as $pid) {
                    $OrderModel->ex($orderId, $pid, $counter[$pid], implode("|", $properties[$pid]));
                }
            }

            die();

            //  отправляем сообщение админу


            //  отправляем сообщение покупателю

            $script = '<script>
                $.server_say({say: "Успешная авторизация!", status: "success"});
                window.location.href = "/'.CONFIG_SYSTEM["panel"].'/";
            </script>';

            System::script($script);

        } else{

            die("info::error::Не удалось создать заказ!");
        }

    }

}