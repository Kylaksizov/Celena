<?php

namespace app\models;


use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;

class CategoryModel extends Model{


    /**
     * @name добавление категории
     * ==========================
     * @param $title
     * @param array $meta
     * @param $content
     * @param $url
     * @param $pid
     * @param int $status
     * @return bool|string
     * @throws Exception
     */
    public function create($title, array $meta = [], $content, $url, $pid, int $status = 1){

        $params = [
            $title,
            $meta["title"],
            $meta["description"],
            $content,
            $url,
            $pid,
            $status
        ];

        Base::run("INSERT INTO " . PREFIX . "category (
            title,
            m_title,
            m_description,
            content,
            url,
            pid,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?
        )", $params);

        unset($params);

        return Base::lastInsertId();
    }


    /**
     * @name получение инфы одной категории
     * ====================================
     * @param $id
     * @return mixed|null
     * @throws Exception
     */
    public function get($id){

        return Base::run("SELECT * FROM " . PREFIX . "category WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @name получение всех категорий
     * ==============================
     * @return array
     * @throws Exception
     */
    public function getAll($all = false){

        if($all){

            $result = Base::run("SELECT id, title FROM " . PREFIX . "category ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        } else{

            $result = [];
            $params = [];

            $pagination = [
                "start" => 0,
                "limit" => 25,
                "pagination" => ""
            ];

            $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "category c ORDER BY id DESC", $params, $pagination["start"], $pagination["limit"]);

            $result["categories"] = Base::run(
                "SELECT * FROM " . PREFIX . "category ORDER BY id DESC LIMIT {$pagination["start"]}, {$pagination["limit"]}", $params)->fetchAll(PDO::FETCH_ASSOC);

            $result["pagination"] = $pagination['pagination'];
        }

        return $result;
    }


    /**
     * @name редактирование категории
     * ==============================
     * @param $id
     * @param $title
     * @param array $meta
     * @param $content
     * @param $url
     * @param $pid
     * @param int $status
     * @return void
     * @throws Exception
     */
    public function edit($id, $title, array $meta = [], $content, $url, $pid, int $status = 1){

        Base::run("
            UPDATE " . PREFIX . "category SET
                title = ?,
                m_title = ?,
                m_description = ?,
                content = ?,
                url = ?,
                pid = ?,
                status = ?
            WHERE id = ?",

            [$title, $meta["title"], $meta["description"], $content, $url, $pid, $status, $id])->rowCount();
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

        Base::run("UPDATE " . PREFIX . "category SET $set WHERE id = ?", $params)->rowCount();
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