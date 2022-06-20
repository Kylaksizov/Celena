<?php

namespace app\models\plugins\Celena\Shop\panel;


use app\core\Base;
use app\core\Model;
use Exception;
use PDO;
use PDOStatement;

class OrderModel extends Model{


    /**
     * @name удаление заказа
     * =====================
     * @param $id
     * @return bool|PDOStatement|null
     * @throws Exception
     */
    public function delete($id){

        $del = Base::run("DELETE FROM " . PREFIX . "orders WHERE id = ?", [$id]);
        Base::run("DELETE FROM " . PREFIX . "orders_ex WHERE oid = ?", [$id]);

        return $del;
    }

}