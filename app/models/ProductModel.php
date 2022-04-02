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
     * @name получение одного товара
     * =============================
     * @param $id
     * @return array
     * @throws Exception
     */
    public function get($id){

        $result = [];

        $result["product"] = Base::run("SELECT * FROM " . PREFIX . "products WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
        $result["images"] = Base::run("SELECT id, src, alt FROM " . PREFIX . "images WHERE itype = 1 AND nid = ?", [$id])->fetchAll(PDO::FETCH_ASSOC);
        $result["brands"] = Base::run("SELECT id, name, icon, categories FROM " . PREFIX . "brands ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        $result["props"] = Base::run("SELECT
                p.id,
                p.title,
                pp.id AS pp_id,
                pp.id_p,
                pp.id_pv,
                pp.sep,
                pp.vendor,
                pp.price,
                pp.stock,
                pv.pid,
                pv.val
            FROM " . PREFIX . "product_prop pp
                LEFT JOIN " . PREFIX . "properties_v pv ON pv.id = pp.id_pv
                LEFT JOIN " . PREFIX . "properties p ON p.id = pp.id_p
            WHERE pp.pid = ? ORDER BY pp.id_p DESC", [$id])->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }


    /**
     * @name получение изображений товара
     * ==================================
     * @param $product_id
     * @return array|false
     * @throws Exception
     */
    public function getImages($product_id){

        return Base::run("SELECT id, src, alt FROM " . PREFIX . "images WHERE itype = 1 AND nid = ?", [$product_id])->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @name получение всех товаров
     * ============================
     * @return array
     * @throws Exception
     */
    public function getProducts($categories = [], $fields = "*"){

        $result = [];

        // если был передан параметр с категориями
        if(!empty($categories)){

            $where = "";
            $params = [];

            foreach ($categories as $categoryUrl) {
                $where .= "url = ? OR ";
                array_push($params, $categoryUrl);
            }
            $where = trim($where, " OR ");

            $result["categories"] = System::setKeys(Base::run("SELECT id, title, m_title, m_description, content, icon, url, pid FROM " . PREFIX . "category WHERE " . $where, $params)->fetchAll(PDO::FETCH_ASSOC), "id");
        }


        $pagination = [
            "start" => 0,
            "limit" => 25,
            "pagination" => ""
        ];

        $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "products c ORDER BY id DESC", $params, $pagination["start"], $pagination["limit"]);

        $result["products"] = Base::run(
            "SELECT
                    p.id,
                    p.uid,
                    p.title,
                    p.category,
                    p.price,
                    p.sale,
                    p.stock,
                    p.url,
                    p.created,
                    p.status,
                    i.src
                FROM " . PREFIX . "products p
                    LEFT JOIN " . PREFIX . "images i ON i.nid = p.id
                GROUP BY p.id
                ORDER BY p.id DESC
                LIMIT {$pagination["start"]}, {$pagination["limit"]}
                ", $params)->fetchAll(PDO::FETCH_ASSOC);

        $result["pagination"] = $pagination['pagination'];

        return $result;
    }


    /**
     * @name получение всех товаров
     * ============================
     * @return array
     * @throws Exception
     */
    public function getAll($categories = []){

        $result = [];
        $params = [];

        $pagination = [
            "start" => 0,
            "limit" => 25,
            "pagination" => ""
        ];

        $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "products c ORDER BY id DESC", $params, $pagination["start"], $pagination["limit"]);

        $result["products"] = Base::run(
            "SELECT
                    p.id,
                    p.uid,
                    p.title,
                    p.category,
                    p.price,
                    p.sale,
                    p.stock,
                    p.url,
                    p.created,
                    p.status,
                    i.src
                FROM " . PREFIX . "products p
                    LEFT JOIN " . PREFIX . "images i ON i.nid = p.id
                GROUP BY p.id
                ORDER BY p.id DESC
                LIMIT {$pagination["start"]}, {$pagination["limit"]}
                ", $params)->fetchAll(PDO::FETCH_ASSOC);

        $result["pagination"] = $pagination['pagination'];

        return $result;
    }


    /**
     * @name получение всех свойств
     * ============================
     */
    /*public function getPropertiesAll($all = false){

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
    }*/


    /**
     * @name изменение полей произвольно
     * =================================
     */
    /*public function editFields($id, array $fields){

        $set = "";
        $params = [];

        foreach ($fields as $fieldName => $val) {
            $set .= "$fieldName = ?, ";
            array_push($params, $val);
        }
        $set .= "last_modify = ?";
        array_push($params, time());
        array_push($params, $id);

        return Base::run("UPDATE " . PREFIX . "products SET $set WHERE id = ?", $params)->rowCount();
    }*/







    private function instanceFetch($query, $params){
        if(!empty($this->get($query))) return $this->get($query);
        return $this->set($query, Base::run($query, $params)->fetch(PDO::FETCH_ASSOC));
    }

    private function instanceFetchAll($query, $params){
        if(!empty($this->get($query))) return $this->get($query);
        return $this->set($query, Base::run($query, $params)->fetchAll(PDO::FETCH_ASSOC));
    }

}