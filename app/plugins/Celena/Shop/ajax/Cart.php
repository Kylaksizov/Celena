<?php

namespace app\plugins\Celena\Shop\ajax;

use app\core\System;
use app\models\plugins\Celena\Shop\OrderModel;
use app\traits\Mail;

class Cart{



    public function index(){

        if(!empty($_POST["products"])) self::createOrder(); // оформление заказа
        else die("info::error::Ваша корзина пуста!"); #TODO нужна ещё проверка на формат который в корзине
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

            foreach ($products_ids as $pid) {

                $props = !empty($properties[$pid]) ? implode("|", $properties[$pid]) : '';
                $OrderModel->ex($orderId, $pid, $counter[$pid], $props);
            }

            //  отправляем сообщение админу
            $theme = 'Новый заказ № ' . $orderId;
            $body = 'На сайте оформлен новый заказ на сумму: <b>'.$total.' '.CONFIG_PLUGIN["currency"].'</b>';

            Mail::send(CONFIG_SYSTEM["admin_email"], $theme, $body);

            //  отправляем сообщение покупателю в

            $script = '<script>
                $.server_say({say: "Заказ оформлен!", status: "success"});
                localStorage.removeItem("cart");
                setTimeout(function(){
                    window.location.href = "'.CONFIG_PLUGIN["after_cart"].'";
                }, 1000)
            </script>';

            System::script($script);

        } else{

            die("info::error::Не удалось создать заказ!");
        }

    }

}