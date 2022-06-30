<?php

namespace app\models;


use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;
use PDOStatement;

class CommentsModel extends Model{


    /**
     * @name добавление комментария
     * ============================
     * @param $pid
     * @param $plugin_id
     * @param $uid
     * @param $comment
     * @param $parent_id
     * @param $status
     * @return bool|string
     * @throws Exception
     */
    public function add($pid = null, $plugin_id = null, $uid = USER["id"], $comment = '', $parent_id = null, $status = 0){

        Base::run("INSERT INTO " . PREFIX . "comments (
            pid,
            plugin_id,
            uid,
            comment,
            parent_id,
            created,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?
        )", [
            $pid,
            $plugin_id,
            $uid,
            $comment,
            $parent_id,
            time(),
            $status
        ]);

        return Base::lastInsertId();
    }



    /**
     * @name получение комментариев по pid или plugin_id
     * =================================================
     * @return array
     * @throws Exception
     */
    public function getAll($pid = null, $plugin_id = null, $status = 1, $sort = 'ASC'){

        $result = [];
        $where = "";
        $params = [];

        if($pid){
            $where .= " c.pid = ?";
            array_push($params, $pid);
        }
        if($plugin_id){
            $where .= " c.plugin_id = ?";
            array_push($params, $plugin_id);
        }

        if($status){
            $where .= " AND c.status = ?";
            array_push($params, $status);
        }

        if(!empty($where)) $where = "WHERE" . $where;

        $pagination = [
            "start" => 0,
            "limit" => 25,
            "pagination" => ""
        ];

        $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "comments c $where", $params, $pagination["start"], $pagination["limit"]);

        $result["comments"] = Base::run(
            "SELECT
                    c.id,
                    c.uid,
                    c.comment,
                    c.parent_id,
                    c.created,
                    c.status,
                    u.name,
                    u.avatar,
                    u.role,
                    r.name AS role_name
                FROM " . PREFIX . "comments c
                    LEFT JOIN " . PREFIX . "users u ON u.id = c.uid
                    LEFT JOIN " . PREFIX . "roles r ON r.id = u.role
                $where
                ORDER BY c.id $sort
                LIMIT {$pagination["start"]}, {$pagination["limit"]}
                ", $params)->fetchAll(PDO::FETCH_ASSOC);

        $result["pagination"] = $pagination['pagination'];

        return $result;
    }



    public function getCounter(){

        return Base::run("SELECT COUNT(*) FROM " . PREFIX . "comments WHERE status = 0")->fetchColumn();
    }


    /**
     * @name измнение статуса
     * ======================
     * @param $id
     * @param $status
     * @return int
     * @throws Exception
     */
    public function editStatus($id, $status){

        return Base::run("UPDATE " . PREFIX . "comments SET status = ? WHERE id = ?", [$status, $id])->rowCount();
    }



    /**
     * @name удаление комментария
     * ==========================
     * @param $id
     * @return bool|PDOStatement
     * @throws Exception
     */
    public function delete($id){

        return Base::run("DELETE FROM " . PREFIX . "comments WHERE id = ?", [$id]);
    }

}