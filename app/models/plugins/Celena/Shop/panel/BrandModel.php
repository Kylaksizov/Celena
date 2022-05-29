<?php

namespace app\models\plugins\Celena\Shop\panel;


use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;
use PDOStatement;

class BrandModel extends Model{


    /**
     * @name добавление бренда
     * ========================
     * @param $name
     * @param string $url
     * @param string $icon
     * @param string $categories
     * @return bool|string
     * @throws Exception
     */
    public function create($name, string $url = '', string $icon = '', string $categories = ''){

        $params = [
            $name,
            $url,
            $icon,
            $categories
        ];

        Base::run("INSERT INTO " . PREFIX . "brands (
            name,
            url,
            icon,
            categories
        ) VALUES (
            ?, ?, ?, ?
        )", $params);

        unset($params);

        return Base::lastInsertId();
    }


    /**
     * @name получение бренда
     * ======================
     * @param $id
     * @param string $fields
     * @return mixed|null
     * @throws Exception
     */
    public function get($id, string $fields = "*"){

        return Base::run("SELECT $fields FROM " . PREFIX . "brands WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @name получение всех категорий
     * ==============================
     * @return array
     * @throws Exception
     */
    public function getAll($all = false){

        if($all){

            $result = System::setKeys(Base::run("SELECT id, name, icon, categories FROM " . PREFIX . "brands ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC), "id");

        } else{

            $result = [];
            $params = [];

            $pagination = [
                "start" => 0,
                "limit" => 25,
                "pagination" => ""
            ];

            $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "brands c ORDER BY id DESC", $params, $pagination["start"], $pagination["limit"]);

            $result["brands"] = Base::run(
                "SELECT * FROM " . PREFIX . "brands ORDER BY id DESC LIMIT {$pagination["start"]}, {$pagination["limit"]}", $params)->fetchAll(PDO::FETCH_ASSOC);

            $result["pagination"] = $pagination['pagination'];
        }

        return $result;
    }


    /**
     * @name изменение полей произвольно
     * =================================
     * @param $id
     * @param array $fields
     * @return int
     * @throws Exception
     */
    public function editFields($id, array $fields){

        $set = "";
        $params = [];

        foreach ($fields as $fieldName => $val) {
            $set .= "$fieldName = ?, ";
            array_push($params, $val);
        }
        $set = trim($set, ", ");
        array_push($params, $id);

        return Base::run("UPDATE " . PREFIX . "brands SET $set WHERE id = ?", $params)->rowCount();
    }


    /**
     * @name удаление категории
     * ========================
     * @param $id
     * @return bool|PDOStatement
     * @throws Exception
     */
    public function delete($id){

        return Base::run("DELETE FROM " . PREFIX . "brands WHERE id = ?", [$id]);
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