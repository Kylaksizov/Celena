<?php

namespace app\models;


use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;
use PDOStatement;

class UsersModel extends Model{


    // НЕ УДАЛЯТЬ!
    public function __construct($conf = null){
        parent::__construct($conf);
    }

    /**
     * @name создание пользователя
     * ===========================
     * @param $name
     * @param $email
     * @param $password
     * @param string $avatar
     * @param int $role
     * @param int $status
     * @return bool|string
     * @throws Exception
     */
    public function add($name, $email, $password, string $avatar = '', int $role = 2, int $status = 1){

        $hash = sha1(time() . rand(100, 9999));

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

        $id = Base::lastInsertId();

        return [
            "id" => $id,
            "hash" => $hash,
        ];
    }


    /**
     * @name получение авторизированого пользователя
     * =============================================
     * @param $id
     * @param $hash
     * @return mixed
     * @throws Exception
     */
    public function getAuth($id, $hash){

        return Base::run("
            SELECT
                u.*,
                r.name AS role_name,
                r.rules
            FROM " . PREFIX . "users u
                LEFT JOIN " . PREFIX . "roles r ON r.id = u.role
            WHERE u.id = ? AND u.hash = ?
        ",
            [$id, $hash]
        )->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @name авторизация
     * =================
     * @param $email
     * @return mixed
     * @throws Exception
     */
    public function login($email){

        return Base::run("
            SELECT
                u.*,
                r.name AS role_name,
                r.rules
            FROM " . PREFIX . "users u
                LEFT JOIN " . PREFIX . "roles r ON r.id = u.role
            WHERE u.email = ?
        ",
            [$email]
        )->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @name получение пользователя
     * ============================
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function getUser($id = USER["id"]){

        return Base::run("SELECT * FROM " . PREFIX . "users WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
    }





    /**
     * @name получение пользователей
     * =============================
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


    /**
     * @name добавление роли
     * =====================
     * @param $name
     * @param $rule
     * @return bool|string
     * @throws Exception
     */
    public function addRole($name, $rule){

        Base::run("INSERT INTO " . PREFIX . "roles (
            name,
            rules
        ) VALUES (
            ?, ?
        )", [$name, $rule]);

        return Base::lastInsertId();
    }


    /**
     * @name редактирование роли
     * =========================
     * @param $id
     * @param $name
     * @param $rules
     * @return int
     * @throws Exception
     */
    public function editRole($id, $name, $rules){

        return Base::run("UPDATE " . PREFIX . "roles SET name = ?, rules = ? WHERE id = ?", [$name, $rules, $id])->rowCount();
    }




    /**
     * @name получение роли
     * ====================
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function getRole($id){

        return Base::run("SELECT * FROM " . PREFIX . "roles WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
    }




    /**
     * @name получение ролей
     * =====================
     * @return array
     * @throws Exception
     */
    public function getRoles(){

        $result = [];
        $params = [];

        $pagination = [
            "start" => 0,
            "limit" => 25,
            "pagination" => ""
        ];

        $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "roles ORDER BY id DESC", $params, $pagination["start"], $pagination["limit"]);

        $result["roles"] = Base::run("SELECT * FROM " . PREFIX . "roles ORDER BY id DESC
        LIMIT {$pagination["start"]}, {$pagination["limit"]}", $params)->fetchAll(PDO::FETCH_ASSOC);

        $result["pagination"] = $pagination['pagination'];

        return $result;
    }




    /**
     * @name удаление пользователя
     * ===========================
     * @param $id
     * @return bool|PDOStatement
     * @throws Exception
     */
    public function delete($id){

        return Base::run("DELETE FROM " . PREFIX . "users WHERE id = ?", [$id]);
    }




    /**
     * @name удаление роли
     * ===================
     * @param $id
     * @return bool|PDOStatement
     * @throws Exception
     */
    public function deleteRole($id){

        return Base::run("DELETE FROM " . PREFIX . "roles WHERE id = ?", [$id]);
    }

}