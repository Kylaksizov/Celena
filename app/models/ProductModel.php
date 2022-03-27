<?php

namespace app\models;


use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;
use PDOStatement;

class ProductModel extends Model{


    /**
     * @param $title
     * @param string $vendor
     * @param array $meta
     * @param $content
     * @param $price
     * @param string|null $sale
     * @param null $stock
     * @param null $url
     * @param null $created
     * @param int $status
     * @return bool|string
     * @throws Exception
     */
    public function create($title, string $vendor = '', array $meta = [], $content, $price, string $sale = null, $stock = null, $url = null, $created = null, int $status = 1){

        if($url === null) $url = System::translit($title);
        if($created === null) $created = time();

        $params = [
            USER["id"],
            $vendor,
            $title,
            $meta["title"],
            $meta["description"],
            $content,
            $price,
            $sale,
            $stock,
            $url,
            $created,
            null,
            $status
        ];

        Base::run("INSERT INTO " . PREFIX . "products (
            uid,
            vendor,
            title,
            m_title,
            m_description,
            content,
            price,
            sale,
            stock,
            url,
            created,
            last_modify,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )", $params);

        unset($params);

        return Base::lastInsertId();
    }


    /**
     * @name получение одного товара
     * =============================
     * @param $id
     * @return mixed|null
     * @throws Exception
     */
    public function get($id){

        $result = [];

        $result["product"] = Base::run("SELECT * FROM " . PREFIX . "products WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
        $result["images"] = Base::run("SELECT id, src, alt FROM " . PREFIX . "images WHERE itype = 1 AND nid = ?", [$id])->fetchAll(PDO::FETCH_ASSOC);

        $result["props"] = Base::run("SELECT
                p.id,
                p.title,
                pp.id AS pp_id,
                pp.id_prop,
                pp.vendor,
                pp.price,
                pp.stock,
                pv.pid,
                pv.val
            FROM " . PREFIX . "product_prop pp
                LEFT JOIN " . PREFIX . "properties_v pv ON pv.id = pp.id_prop
                LEFT JOIN " . PREFIX . "properties p ON p.id = pv.pid
            WHERE pp.pid = ?", [$id])->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }


    /**
     * @name получение всех товаров
     * ============================
     * @return array
     * @throws Exception
     */
    public function getAll($all = false){

        if($all){

            $result = Base::run("SELECT * FROM " . PREFIX . "products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        } else{

            $result = [];
            $params = [];

            $pagination = [
                "start" => 0,
                "limit" => 25,
                "pagination" => ""
            ];

            $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "products c ORDER BY id DESC", $params, $pagination["start"], $pagination["limit"]);

            $result["products"] = Base::run(
                "SELECT * FROM " . PREFIX . "products ORDER BY id DESC LIMIT {$pagination["start"]}, {$pagination["limit"]}", $params)->fetchAll(PDO::FETCH_ASSOC);

            $result["pagination"] = $pagination['pagination'];
        }

        return $result;
    }


    /**
     * @name получение всех свойств
     * ============================
     * @return array
     * @throws Exception
     */
    public function getPropertiesAll($all = false){

        if($all){

            $result = System::setKeysArray(Base::run("SELECT p.*, v.id AS vid, v.val, v.def FROM " . PREFIX . "properties p LEFT JOIN " . PREFIX . "properties_v v ON v.pid = p.id ORDER BY p.position, v.position ASC")->fetchAll(PDO::FETCH_ASSOC), "title");

        } else{

            $result = [];
            $params = [];

            $pagination = [
                "start" => 0,
                "limit" => 25,
                "pagination" => ""
            ];

            $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "properties c ORDER BY id DESC", $params, $pagination["start"], $pagination["limit"]);

            $result["properties"] = Base::run(
                "SELECT * FROM " . PREFIX . "properties ORDER BY id DESC LIMIT {$pagination["start"]}, {$pagination["limit"]}", $params)->fetchAll(PDO::FETCH_ASSOC);

            $result["pagination"] = $pagination['pagination'];
        }

        return $result;
    }


    /**
     * @name изменение полей произвольно
     * =================================
     * @param $id
     * @param array $fields
     * @return void
     * @throws Exception
     */
    public function editFields($id, array $fields){

        $set = "";
        $params = [];

        foreach ($fields as $fieldName => $val) {
            $set .= "$fieldName = ?, ";
            array_push($params, $val);
        }
        $set .= "last_modify = ?";
        array_push($params, time());
        array_push($params, $id);

        Base::run("UPDATE " . PREFIX . "products SET $set WHERE id = ?", $params)->rowCount();
    }


    /**
     * @name добавление картинок
     * =========================
     * @param $type
     * @param $nid
     * @param $crs
     * @param string $alt
     * @return bool|string
     * @throws Exception
     */
    public function addImage($type, $nid, $crs, string $alt = ''){

        Base::run("INSERT INTO " . PREFIX . "images (
            itype,
            nid,
            src,
            alt
        ) VALUES (
            ?, ?, ?, ?
        )", [$type, $nid, $crs, $alt]);

        return Base::lastInsertId();
    }





    /**
     * @name добавление свойств товару
     * ===============================
     * @param $pid
     * @param $id_prop
     * @param $vendor
     * @param $price
     * @param $stock
     * @return bool|string
     * @throws Exception
     */
    public function addProperty($pid, $id_prop, $vendor, $price, $stock = null){

        $params = [
            $pid,
            $id_prop,
            $vendor,
            $price,
            $stock
        ];

        Base::run("INSERT INTO " . PREFIX . "product_prop (
            pid,
            id_prop,
            vendor,
            price,
            stock
        ) VALUES (
            ?, ?, ?, ?, ?
        )", $params);

        unset($params);

        return Base::lastInsertId();
    }





    /**
     * @name изменение свойств для товара
     * ==================================
     * @param $id
     * @param $pid
     * @param $id_prop
     * @param $vendor
     * @param $price
     * @param $stock
     * @return int
     * @throws Exception
     */
    public function editProperty($id, $pid, $id_prop, $vendor, $price, $stock = null){

        return Base::run("UPDATE " . PREFIX . "product_prop SET pid = ?, id_prop = ?, vendor = ?, price = ?, stock = ? WHERE id = ?", [$pid, $id_prop, $vendor, $price, $stock, $id])->rowCount();
    }


    /**
     * @name удаление свойств из товара
     * ================================
     * @param $id
     * @return bool|PDOStatement
     * @throws Exception
     */
    public function deleteProperty($id){

        if(is_array($id)){

            $where = "";
            $params = [];

            foreach ($id as $item) {
                $where .= "id = ? OR ";
                array_push($params, $item);
            }
            $where = trim($where, " OR ");

        } else{

            $where = "id = ?";
            $params = [$id];
        }

        return Base::run("DELETE FROM " . PREFIX . "product_prop WHERE " . $where, $params);
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