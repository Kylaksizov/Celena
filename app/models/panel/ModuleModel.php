<?php

namespace app\models\panel;

use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;

class ModuleModel extends Model{



    public function add($module_id, $name, $descr, $version, $cv, $poster, $base_install, $base_update, $base_on, $base_off, $base_del, $routes = '', $comment = '', $status = 0){

        $params = [
            $module_id,
            $name,
            $descr,
            $version,
            $cv,
            $poster,
            $base_install,
            $base_update,
            $base_on,
            $base_off,
            $base_del,
            $routes,
            $comment,
            $status
        ];

        Base::run("INSERT INTO " . PREFIX . "modules (
            module_id,
            name,
            descr,
            version,
            cv,
            poster,
            base_install,
            base_update,
            base_on,
            base_off,
            base_del,
            routes,
            comment,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )", $params);

        unset($params);

        return Base::lastInsertId();
    }



    public function addAction($mid, $filepath, $action, $searchcode, $replacecode){

        $params = [
            $mid,
            $filepath,
            $action,
            $searchcode,
            $replacecode
        ];

        Base::run("INSERT INTO " . PREFIX . "modules_ex (
            mid,
            filepath,
            action,
            searchcode,
            replacecode
        ) VALUES (
            ?, ?, ?, ?, ?
        )", $params);

        unset($params);

        return Base::lastInsertId();
    }




    /**
     * @name получение системных значений
     * ==================================
     * @param int $id
     * @return mixed|null
     */
    public function getModule(int $id){

        $result = [];

        $result["module"] = self::instanceFetch("
            SELECT
                name,
                descr,
                version,
                cv,
                poster,
                base_install,
                base_update,
                base_on,
                base_off,
                base_del,
                routes,
                comment,
                status
            FROM " . PREFIX . "modules
            WHERE id = ?
            ", [$id]);

        $result["ex"] = System::setKeysArray(
            self::instanceFetchAll("
            SELECT
                id,
                filepath,
                action,
                searchcode,
                replacecode
            FROM " . PREFIX . "modules_ex
            WHERE mid = ?
                ORDER BY id ASC
            ", [$id]),
            "filepath"
        );

        return $result;
    }




    /**
     * @name получение системных значений
     * ==================================
     * @param int $id
     * @return mixed|null
     */
    public function getModuleMain(int $id){

        return self::instanceFetch("
            SELECT
                name,
                descr,
                version,
                cv,
                poster,
                base_install,
                base_update,
                base_on,
                base_off,
                base_del,
                comment,
                status
            FROM " . PREFIX . "modules
            WHERE id = ?
            ", [$id]);
    }


    /**
     * @name получение для инициализации
     * =================================
     * @return mixed|null
     */
    public function getByInitialize(){

        return System::setKeysArray(
            self::instanceFetchAll("
            SELECT
                -- m.base_install,
                ex.filepath,
                ex.action,
                ex.searchcode,
                ex.replacecode
            FROM " . PREFIX . "modules m
                LEFT JOIN " . PREFIX . "modules_ex ex ON ex.mid = m.id
            WHERE m.status = 1
            "),
            "filepath"
        );
    }


    /**
     * @name получение для упаковки
     * ============================
     * @return mixed|null
     */
    public function getByPackage(){

        return self::instanceFetchAll("
            SELECT
                name,
                descr,
                version,
                cv,
                poster,
                base_install,
                base_update,
                base_on,
                base_off,
                base_del,
                comment,
                status
            FROM " . PREFIX . "modules
            WHERE id = ?
            ");
    }


    /**
     * @name получение постера
     * =======================
     * @return mixed|null
     */
    public function getInfo($id){

        return self::instanceFetch("
            SELECT
                poster,
                routes
            FROM " . PREFIX . "modules
            WHERE id = ?
            ", [$id]);
    }


    /**
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

        return Base::run("UPDATE " . PREFIX . "modules SET $set WHERE id = ?", $params)->rowCount();
    }



    /**
     * @return array
     * @throws Exception
     */
    public function getModules(){

        $result = [];
        $params = [];

        $pagination = [
            "start" => 0,
            "limit" => 25,
            "pagination" => ""
        ];

        $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "modules c ORDER BY id DESC", $params, $pagination["start"], $pagination["limit"]);

        $result["modules"] = Base::run(
            "SELECT
                *
            FROM " . PREFIX . "modules
            ORDER BY id DESC
            LIMIT {$pagination["start"]}, {$pagination["limit"]}
            ", $params)->fetchAll(PDO::FETCH_ASSOC);

        $result["pagination"] = $pagination['pagination'];

        return $result;
    }


    public function clear($mid){

        return Base::run("DELETE FROM " . PREFIX . "modules_ex WHERE mid = ?", [$mid]);
    }



    public function power($id, $action){

        return Base::run("UPDATE " . PREFIX . "modules SET status = ? WHERE id = ?", [$action, $id])->rowCount();
    }



    public function removePlugin($plugin_id){

        return Base::run("DELETE FROM " . PREFIX . "plugins WHERE plugin_id = ?", [$plugin_id]);
    }


    private function instanceFetch($query, $params = []){
        if(!empty($this->get($query))) return $this->get($query);
        return $this->set($query, Base::run($query, $params)->fetch(PDO::FETCH_ASSOC));
    }

    private function instanceFetchAll($query, $params = []){
        if(!empty($this->get($query))) return $this->get($query);
        return $this->set($query, Base::run($query, $params)->fetchAll(PDO::FETCH_ASSOC));
    }
}