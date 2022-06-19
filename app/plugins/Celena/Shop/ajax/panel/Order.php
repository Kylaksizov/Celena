<?php

namespace app\plugins\Celena\Shop\ajax\panel;

use app\core\System;
use app\models\plugins\Celena\Shop\panel\OrderModel;


class Order{

    public function index(){

        if(!empty($_POST["deleteOrder"])) self::deleteOrder(intval($_POST["deleteOrder"])); // создание редактирование товара
    }



    private function deleteOrder($orderId){

        $OrderModel = new OrderModel();
        $OrderModel->delete($orderId);

        $script = '<script>
            $(\'[data-a="Order:deleteOrder='.$orderId.'"]\').closest("tr").remove();
            $.server_say({say: "Заказ удален!", status: "success"});
        </script>';

        System::script($script);
    }

}