<?php

namespace app\models;


use app\_classes\System;
use app\core\Base;
use app\core\Model;
use Exception;
use PDO;
use PDOStatement;

class UsersModel extends Model{


    /**
     * @name создание пользователя
     * ===========================
     * @param $name
     * @param $email
     * @param $password
     * @param $avatar
     * @param $role
     * @param $hash
     * @param $status
     * @return bool|string
     * @throws Exception
     */
    public function add($name, $email, $password, $avatar = '', $role = 2, $hash = '', $status = 1){

        $params = [
            $name,
            $email,
            $password,
            $avatar,
            $role,
            $_SERVER["REMOTE_ADDR"],
            $hash,
            time(),
            $status
        ];

        Base::run("INSERT INTO " . PREFIX . "users (
            name,
            email,
            password,
            avatar,
            role,
            ip,
            hash,
            created,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?
        )", $params);

        unset($params);

        return Base::lastInsertId();
    }


    /**
     * @name получение авторизированого пользователя
     * =============================================
     * @param $id
     * @param $hash
     * @return mixed|null
     */
    public function getAuth($id, $hash){

        return self::instanceFetch("
            SELECT
                u.*,
                r.name AS role_name,
                r.rules
            FROM " . PREFIX . "users u
                LEFT JOIN " . PREFIX . "roles r ON r.id = u.role
            WHERE u.id = ? AND u.hash = ?
        ",
            [$id, $hash]
        );
    }





    /**
     * @name получение пользователя
     * ============================
     * @param $id
     * @return mixed|null
     */
    public function getUser($id = USER["id"]){

        return self::instanceFetch("SELECT * FROM " . PREFIX . "users WHERE id = ?", [$id]);
    }





    /**
     * @name получение пользователей
     * =============================
     * @return array
     * @throws Exception
     */
    public function getUsers(){

        $result = [];
        $params = [];

        $pagination = [
            "start" => 0,
            "limit" => 25,
            "pagination" => ""
        ];

        $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "users u
            LEFT JOIN " . PREFIX . "roles r ON r.id = u.role
            ORDER BY u.id DESC", $params, $pagination["start"], $pagination["limit"]);

        $result["users"] = Base::run(
            "SELECT
            u.*,
            r.name AS role_name,
            r.rules AS rules
        FROM " . PREFIX . "users u
            LEFT JOIN " . PREFIX . "roles r ON r.id = u.role
        ORDER BY u.id DESC
        LIMIT {$pagination["start"]}, {$pagination["limit"]}", $params)->fetchAll(PDO::FETCH_ASSOC);

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