<?php

namespace app\models;


use app\core\Base;
use app\core\Model;
use Exception;
use PDO;

class CategoryModel extends Model{


    /**
     * @name добавление категории
     * ==========================
     * @param $title
     * @param $meta
     * @param $cont
     * @param $url
     * @param $icon
     * @param $pid
     * @param $status
     * @return bool|string
     * @throws Exception
     */
    public function create($title, $meta = [], $cont, $url, $icon, $pid, $status = 1){

        $params = [
            $title,
            $meta["title"],
            $meta["description"],
            $cont,
            $url,
            $icon,
            $pid,
            $status
        ];

        Base::run("INSERT INTO " . PREFIX . "category (
            title,
            m_title,
            m_description,
            cont,
            url,
            icon,
            pid,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?
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
    public function getAll(){

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

        return $result;
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