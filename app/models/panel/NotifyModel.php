<?php

namespace app\models\panel;

use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;

class NotifyModel extends Model{



    public function add($title, $message, $link = '', $status = 1){

        $params = [
            $title,
            $message,
            $link,
            time(),
            $status
        ];

        Base::run("INSERT INTO " . PREFIX . "notify (
            title,
            message,
            link,
            created,
            status
        ) VALUES (
            ?, ?, ?, ?, ?
        )", $params);

        return Base::lastInsertId();
    }



    /**
     * @return array
     * @throws Exception
     */
    public function getMessages(){

        return Base::run(
            "SELECT
                *
            FROM " . PREFIX . "notify
            ORDER BY id DESC
            LIMIT 20", [])->fetchAll(PDO::FETCH_ASSOC);
    }



    /**
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

        $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "notify c ORDER BY id DESC", $params, $pagination["start"], $pagination["limit"]);

        $result["modules"] = Base::run(
            "SELECT
                *
            FROM " . PREFIX . "notify
            ORDER BY id DESC
            LIMIT {$pagination["start"]}, {$pagination["limit"]}
            ", $params)->fetchAll(PDO::FETCH_ASSOC);

        $result["pagination"] = $pagination['pagination'];

        return $result;
    }


    public function see($id){

        return Base::run("UPDATE " . PREFIX . "notify SET see = ? WHERE id = ?", [1, $id])->rowCount();
    }



    public function remove($id){

        return Base::run("DELETE FROM " . PREFIX . "notify WHERE id = ?", [$id]);
    }
}