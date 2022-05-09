<?php

namespace app\models;


use app\_classes\Functions;
use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;
use PDOStatement;

class OrderModel extends Model{


    public function create($buyer_id = null, $order_id = null, $name = '', $email = '', $tel = '', $address = '', $payment_id = 0, $prod_ids = [], $total = 0, $comment = '', $paid = 0, $status = 0){

        if(!$buyer_id) $buyer_id = USER ? USER["id"] : 0;
        if(!$order_id) $order_id = Functions::generationCode(2).time();
        if(empty($prod_ids)) return false;

        $hash = sha1(round(microtime(true) * 1000));

        $params = [
            USER["id"],
            $buyer_id,
            $order_id,
            $name,
            $email,
            $tel,
            $address,
            $payment_id,
            implode(",", $prod_ids),
            $total,
            $comment,
            $hash,
            time(),
            $paid,
            $status
        ];

        Base::run("INSERT INTO " . PREFIX . "orders (
            uid,
            buyer_id,
            order_id,
            name,
            email,
            tel,
            address,
            payment_id,
            prod_ids,
            total,
            comment,
            hash,
            created,
            paid,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )", $params);

        return Base::lastInsertId();
    }


    public function ex($oid, $pid, $count, $props){

        $params = [
            $oid,
            $pid,
            $count,
            $props
        ];

        Base::run("INSERT INTO " . PREFIX . "orders_ex (
            oid,
            pid,
            count,
            props
        ) VALUES (
            ?, ?, ?, ?
        )", $params);

    }



    /**
     * @name получение одного товара
     * =============================
     * @param $id
     * @return array
     * @throws Exception
     */
    public function get($id){

        $result = [];

        $result["order"] = Base::run("SELECT
                o.id,
                o.uid,
                o.buyer_id,
                o.order_id,
                o.name,
                o.email,
                o.tel,
                o.address,
                o.payment_id,
                o.total,
                o.comment,
                o.hash,
                o.created,
                o.paid,
                o.status,
                ex.pid,
                ex.count,
                ex.props,
                p.title,
                p.price
            FROM " . PREFIX . "orders o
                LEFT JOIN " . PREFIX . "orders_ex ex ON ex.oid = o.id
                LEFT JOIN " . PREFIX . "products p ON p.id = ex.pid
            WHERE o.id = ?
            ORDER BY o.id DESC", [$id])->fetchAll(PDO::FETCH_ASSOC);

        $props = '';
        foreach ($result["order"] as $row) {
            if(!empty($row["props"])){
                $props .= str_replace("|", ",", $row["props"]).',';
            }
        }
        $props = trim($props, ",");

        if(!empty($props)){

            $props_array = array_unique(explode(",", $props));

            $where = "";
            $params = [];
            foreach ($props_array as $pv) {
                $where .= "pp.id = ? OR ";
                array_push($params, $pv);
            }
            $where = trim($where, " OR ");

            $result["props_array"] = $params;


            $result["props"] = System::setKeys(Base::run("SELECT
                pp.id,
                pp.sep,
                pp.vendor,
                pp.price,
                pp.pv,
                pp.stock,
                p.title,
                pv.val
            FROM " . PREFIX . "product_prop pp
                LEFT JOIN " . PREFIX . "properties p ON p.id = pp.id_p
                LEFT JOIN " . PREFIX . "properties_v pv ON pv.id = pp.id_pv
            WHERE $where ORDER BY pp.id DESC", $params)->fetchAll(PDO::FETCH_ASSOC), "id");
        }

        return $result;
    }



    /**
     * @name получение всех товаров
     * ============================
     * @return array
     * @throws Exception
     */
    public function getAll(){

        $result = [];
        $params = [];

        $pagination = [
            "start" => 0,
            "limit" => 25,
            "pagination" => ""
        ];

        $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "orders c ORDER BY id DESC", $params, $pagination["start"], $pagination["limit"]);

        $result["orders"] = Base::run(
            "SELECT
                    o.id,
                    o.buyer_id,
                    o.order_id,
                    o.name,
                    o.total,
                    o.created,
                    o.paid,
                    o.status,
                    ex.pid,
                    ex.count,
                    p.title
                FROM " . PREFIX . "orders o
                    LEFT JOIN " . PREFIX . "orders_ex ex ON ex.oid = o.id
                    LEFT JOIN " . PREFIX . "products p ON p.id = ex.pid
                ORDER BY o.id DESC
                LIMIT {$pagination["start"]}, {$pagination["limit"]}
                ", $params)->fetchAll(PDO::FETCH_ASSOC);

        $result["pagination"] = $pagination['pagination'];

        return $result;
    }



    public function getStatuses(){

        return System::setKeys(Base::run("SELECT * FROM " . PREFIX . "orders_status ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC), "id");
    }




    private function instanceFetch($query, $params){
        if(!empty($this->get($query))) return $this->get($query);
        return $this->set($query, Base::run($query, $params)->fetch(PDO::FETCH_ASSOC));
    }

    private function instanceFetchAll($query, $params){
        if(!empty($this->get($query))) return $this->get($query);
        return $this->set($query, Base::run($query, $params)->fetchAll(PDO::FETCH_ASSOC));
    }

}