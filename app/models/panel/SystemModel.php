<?php

namespace app\models\panel;


use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;
use PDOStatement;

class SystemModel extends Model{


    /**
     * @name получение логов
     * =====================
     * @return array
     * @throws Exception
     */
    public function getLogs(){

        $result = [];
        $params = [];

        $pagination = [
            "start" => 0,
            "limit" => 25,
            "pagination" => ""
        ];

        $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "log l
            LEFT JOIN " . PREFIX . "users u ON u.id = l.uid
            ORDER BY l.id DESC", $params, $pagination["start"], $pagination["limit"]);

        $result["logs"] = Base::run(
            "SELECT
            l.*,
            u.name
        FROM " . PREFIX . "log l
            LEFT JOIN " . PREFIX . "users u ON u.id = l.uid
        ORDER BY l.id DESC
        LIMIT {$pagination["start"]}, {$pagination["limit"]}", $params)->fetchAll(PDO::FETCH_ASSOC);

        $result["pagination"] = $pagination['pagination'];

        return $result;
    }




    public function setLog($text, $status = 0){

        $params = [
            (USER && !empty(USER["id"])) ? USER["id"] : null,
            $_SERVER["REMOTE_ADDR"],
            !empty($_GET["url"]) ? trim(htmlspecialchars($_GET["url"])) : '/',
            $text,
            time(),
            $status
        ];

        Base::run("INSERT INTO " . PREFIX . "log (
            uid,
            ip,
            url,
            log,
            created,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?
        )", $params);

        unset($params);

        return Base::lastInsertId();
    }

}