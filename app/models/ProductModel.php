<?php

namespace app\models;


use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;

class ProductModel extends Model{


    /**
     * @name добавление товара
     * =======================
     * @param $title
     * @param array $meta
     * @param $content
     * @param $price
     * @param $sale
     * @param $url
     * @param int $status
     * @return bool|string
     * @throws Exception
     */
    public function create($title, array $meta = [], $content, $price, $sale, $url, int $status = 1){

        $params = [
            USER["id"],
            $title,
            $meta["title"],
            $meta["description"],
            $content,
            $price,
            $sale,
            $url,
            $status
        ];

        Base::run("INSERT INTO " . PREFIX . "products (
            uid,
            title,
            m_title,
            m_description,
            content,
            price,
            sale,
            url,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?
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

        return Base::run("SELECT * FROM " . PREFIX . "products WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
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

            $result = System::setKeysArray(Base::run("SELECT p.*, v.val, v.def FROM " . PREFIX . "properties p LEFT JOIN " . PREFIX . "properties_v v ON v.pid = p.id ORDER BY p.position, v.position ASC")->fetchAll(PDO::FETCH_ASSOC), "title");

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







    private function instanceFetch($query, $params){
        if(!empty($this->get($query))) return $this->get($query);
        return $this->set($query, Base::run($query, $params)->fetch(PDO::FETCH_ASSOC));
    }

    private function instanceFetchAll($query, $params){
        if(!empty($this->get($query))) return $this->get($query);
        return $this->set($query, Base::run($query, $params)->fetchAll(PDO::FETCH_ASSOC));
    }

}