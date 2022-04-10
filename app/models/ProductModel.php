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
        $result["images"] = Base::run("SELECT id, src, alt, position FROM " . PREFIX . "images WHERE itype = 1 AND nid = ?", [$id])->fetchAll(PDO::FETCH_ASSOC);
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

        return Base::run("SELECT id, src, alt, position FROM " . PREFIX . "images WHERE itype = 1 AND nid = ? ORDER BY position DESC", [$product_id])->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @name получение всех товаров
     * ============================
     * @return array
     * @throws Exception
     */
    public function getProducts($categories = [], $fields = null){

        $result = [];

        // если был передан параметр с категориями
        if(!empty($categories)){

            $where = "";
            $params = [];

            foreach ($categories as $categoryUrl) {
                $where .= "c.url = ? OR ";
                array_push($params, $categoryUrl);
            }
            $where = trim($where, " OR ");

            $result["categories"] = Base::run("SELECT
                    c.id,
                    c.title,
                    c.m_title,
                    c.m_description,
                    c.content,
                    c.icon,
                    c.url,
                    c.pid AS parent_category,
                    pc.pid AS product_id
                FROM " . PREFIX . "category c
                    LEFT JOIN " . PREFIX . "products_cat pc ON pc.cid = c.id
                WHERE " . $where . " AND c.status != 0 GROUP BY pc.pid
                ", $params)->fetchAll(PDO::FETCH_ASSOC);

            //$result["categories_ids"] = System::setKeys($result["categories"], "id");
        }

        $where = "";
        $params = [];

        $pagination = [
            "start" => 0,
            "limit" => CONFIG_SYSTEM["count_prod_by_cat"],
            "pagination" => ""
        ];

        if(!$fields){ // поля по умолчанию

            $fields = "
                p.id,
                p.uid AS author_id,
                p.title,
                p.category,
                p.vendor,
                p.price,
                p.sale,
                p.stock,
                p.url,
                p.created";
        }

        $leftJoin = "";

        if(isset($fields["{brand-id}"]) || isset($fields["{brand-name}"]) || isset($fields["{brand-url}"]) || isset($fields["{brand-icon}"])){
            $leftJoin .= " LEFT JOIN " . PREFIX . "brands b ON b.id = p.brand";
        }

        if(isset($fields["{poster}"])){
            if(!isset($fields["{images}"])) $leftJoin .= " LEFT JOIN " . PREFIX . "images i ON i.nid = p.id";
            else unset($fields['{poster}']);
        }

        $fieldsString = implode(", ", $fields);

        // перебираем категории, а именно новости из них
        if(!empty($categories) && !empty($result["categories"])){

            $lastCategoryName = end($categories);
            
            foreach ($result["categories"] as $catRow) {

                if($lastCategoryName == $catRow["url"]){
                    $where .= "p.id = ? OR ";
                    array_push($params, $catRow["product_id"]);
                }
            }
            $where = trim($where, " OR ");
        }

        $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "products p WHERE $where AND p.status != 0 ORDER BY p.id DESC", $params, $pagination["start"], $pagination["limit"]);

        #TODO в дальнейшем можно разбить запросов зависимости от настроек, для оптимизации
        $result["products"] = Base::run(
            "SELECT
                    $fieldsString
                FROM " . PREFIX . "products p
                    $leftJoin
                WHERE $where AND p.status != 0
                    GROUP BY p.id
                    ORDER BY p.id DESC
                LIMIT {$pagination["start"]}, {$pagination["limit"]}
                ", $params)->fetchAll(PDO::FETCH_ASSOC);

        // если есть тег на получение картинок
        if(!empty($result["products"]) && isset($fields["{images}"])){

            $where = "";
            $params = [];
            foreach ($result["products"] as $row) {
                $where .= "nid = ? OR ";
                array_push($params, $row["id"]);
            }
            $where = trim($where, " OR ");

            $result["images"] = System::setKeysArray(
                Base::run(
                    "SELECT
                    nid,
                    src,
                    alt
                FROM " . PREFIX . "images
                WHERE
                    $where AND itype = 1
                    ORDER BY position ASC
                ", $params)->fetchAll(PDO::FETCH_ASSOC),
                "nid"
            );
        }

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
                    i.src,
                    i.position
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