<?php

namespace app\models;


use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;
use PDOStatement;

class PropertyModel extends Model{


    /**
     * @name создание свойства
     * =======================
     * @param $title
     * @param $url
     * @param $cid
     * @param int $option
     * @param int $sep
     * @param int $position
     * @return bool|string
     * @throws Exception
     */
    public function create($title, $url = null, $cid = null, int $option = 1, $sep = 0, int $position = 0){

        if($url === null) $url = System::translit($title);

        $params = [
            $title,
            $url,
            $cid,
            $option,
            $sep,
            $position
        ];

        Base::run("INSERT INTO " . PREFIX . "properties (
            title,
            url,
            cid,
            option,
            sep,
            position
        ) VALUES (
            ?, ?, ?, ?, ?, ?
        )", $params);

        unset($params);

        return Base::lastInsertId();
    }


    /**
     * @name добавление значения свойства
     * ==================================
     * @param $pid
     * @param $val
     * @param $def
     * @param int $position
     * @return bool|string
     * @throws Exception
     */
    public function add($pid, $val, $def = null, int $position = 0){

        $params = [
            $pid,
            $val,
            $def,
            $position
        ];

        Base::run("INSERT INTO " . PREFIX . "properties_v (
            pid,
            val,
            def,
            position
        ) VALUES (
            ?, ?, ?, ?
        )", $params);

        unset($params);

        return Base::lastInsertId();
    }




    /**
     * @name получение одного свойства
     * ===============================
     * @param $id
     * @return array|false
     * @throws Exception
     */
    public function get($id){

        return Base::run("SELECT
                p.id,
                p.title,
                p.url,
                p.cid,
                p.option,
                p.sep,
                p.position,
                v.id AS pv_id,
                v.val,
                v.position AS pv_position
            FROM " . PREFIX . "properties p
                LEFT JOIN " . PREFIX . "properties_v v ON v.pid = p.id
            WHERE p.id = ? ORDER BY p.position DESC", [$id])->fetchAll(PDO::FETCH_ASSOC);
    }





    /**
     * @name получение всех свойств
     * ============================
     * @return array
     * @throws Exception
     */
    public function getAll($all = false){

        if($all){

            $result = Base::run("SELECT * FROM " . PREFIX . "properties ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

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
                "SELECT
                        id,
                        title
                    FROM " . PREFIX . "properties
                    ORDER BY position DESC
                    LIMIT {$pagination["start"]}, {$pagination["limit"]}
                    ", $params)->fetchAll(PDO::FETCH_ASSOC);

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
        $set = trim($set, ", ");
        array_push($params, $id);

        return Base::run("UPDATE " . PREFIX . "properties SET $set WHERE id = ?", $params)->rowCount();
    }


    /**
     * @name изменение полей произвольно
     * =================================
     * @param $id
     * @param array $fields
     * @return void
     * @throws Exception
     */
    public function editFieldsV($id, array $fields){

        $set = "";
        $params = [];

        foreach ($fields as $fieldName => $val) {
            $set .= "$fieldName = ?, ";
            array_push($params, $val);
        }
        $set = trim($set, ", ");
        array_push($params, $id);

        return Base::run("UPDATE " . PREFIX . "properties_v SET $set WHERE id = ?", $params)->rowCount();
    }


    /**
     * @name удаление свойства
     * =======================
     * @param $id
     * @return bool|PDOStatement
     * @throws Exception
     */
    public function delete($id){

        Base::run("DELETE FROM " . PREFIX . "properties_prop WHERE id_prop = ?", [$id]);
        Base::run("DELETE FROM " . PREFIX . "properties_v WHERE pid = ?", [$id]);
        return Base::run("DELETE FROM " . PREFIX . "properties WHERE id = ?", [$id]);
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