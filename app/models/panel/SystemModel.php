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
     * @name получение системных значений
     * ==================================
     * @param string $fields
     * @param int|string $status
     * @return mixed|null
     */
    public function getPlugins($fields = '*', $status = 'all'){

        $where = "";
        $params = [];

        if($status != 'all'){
            $where = "WHERE status = ?";
            array_push($params, $status);
        }

        return self::instanceFetchAll("
            SELECT
                $fields
            FROM " . PREFIX . "plugins
            $where
        ",
            $params
        );
    }


    /**
     * @name добавление плагина
     * ========================
     * @param $plugin_id
     * @param $name
     * @param $hashfile
     * @return bool|string
     * @throws Exception
     */
    public function addPlugin($plugin_id, $name, $hashfile){

        Base::run("INSERT INTO " . PREFIX . "plugins (
            plugin_id,
            name,
            hashfile,
            status
        ) VALUES (
            ?, ?, ?, ?
        )", [
            $plugin_id,
            $name,
            $hashfile,
            0
        ]);

        unset($params);

        return Base::lastInsertId();
    }


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





    private function instanceFetch($query, $params){
        if(!empty($this->get($query))) return $this->get($query);
        return $this->set($query, Base::run($query, $params)->fetch(PDO::FETCH_ASSOC));
    }

    private function instanceFetchAll($query, $params){
        if(!empty($this->get($query))) return $this->get($query);
        return $this->set($query, Base::run($query, $params)->fetchAll(PDO::FETCH_ASSOC));
    }

}