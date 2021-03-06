<?php

namespace app\models\panel;

use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;

class PluginModel extends Model{

    /**
     * @name получение системных значений
     * ==================================
     * @param string $fields
     * @return mixed|null
     */
    public function getPluginField($fields = '*'){

        return self::instanceFetchAll("SELECT $fields FROM " . PREFIX . "plugins");
    }


    /**
     * @name получение всех id плагинов
     * ================================
     * @return array|false
     * @throws Exception
     */
    public function getMyPluginsIds(){

        return Base::run("SELECT plugin_id FROM " . PREFIX . "plugins")->fetchAll(PDO::FETCH_COLUMN);
    }


    /**
     * @name получение всех id плагинов
     * ================================
     * @return array|false
     * @throws Exception
     */
    public function getMyPluginsInfo($fields = "plugin_id"){

        return Base::run("SELECT $fields FROM " . PREFIX . "plugins")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getPlugins(){

        $result = [];
        $params = [];

        $pagination = [
            "start" => 0,
            "limit" => 25,
            "pagination" => ""
        ];

        $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "plugins c ORDER BY id DESC", $params, $pagination["start"], $pagination["limit"]);

        $result["plugins"] = Base::run(
            "SELECT
                *
            FROM " . PREFIX . "plugins
            ORDER BY id DESC
            LIMIT {$pagination["start"]}, {$pagination["limit"]}
            ", $params)->fetchAll(PDO::FETCH_ASSOC);

        $result["pagination"] = $pagination['pagination'];

        return $result;
    }



    public function getPluginById($id, $fields = '*'){

        return Base::run("SELECT $fields FROM " . PREFIX . "plugins WHERE id = ?", [intval($id)])->fetch(PDO::FETCH_ASSOC);
    }



    public function getPluginByPluginId($id, $fields = '*'){

        return Base::run("SELECT $fields FROM " . PREFIX . "plugins WHERE plugin_id = ?", [intval($id)])->fetch(PDO::FETCH_ASSOC);
    }



    public function getPluginByBrandName($brand, $name){

        return Base::run("SELECT plugin_id, name, version, status FROM " . PREFIX . "plugins WHERE name = ?", [trim(htmlspecialchars(stripslashes($brand."/".$name)))])->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @name добавление плагина
     * ========================
     * @param $plugin_id
     * @param $name
     * @param $version
     * @param $hashfile
     * @return bool|string
     * @throws Exception
     */
    public function addPlugin($plugin_id, $name, $version, $hashfile){

        Base::run("INSERT INTO " . PREFIX . "plugins (
            plugin_id,
            name,
            version,
            hashfile,
            status
        ) VALUES (
            ?, ?, ?, ?, ?
        )", [
            $plugin_id,
            $name,
            $version,
            $hashfile,
            0
        ]);

        unset($params);

        return Base::lastInsertId();
    }



    public function power($id, $action){

        return Base::run("UPDATE " . PREFIX . "plugins SET status = ? WHERE id = ?", [$action, $id])->rowCount();
    }



    public function updateVersion($id, $newVersion){

        return Base::run("UPDATE " . PREFIX . "plugins SET version = ? WHERE plugin_id = ?", [$newVersion, $id])->rowCount();
    }



    public function removePlugin($plugin_id){

        return Base::run("DELETE FROM " . PREFIX . "plugins WHERE plugin_id = ?", [$plugin_id]);
    }

    private function instanceFetchAll($query, $params = []){
        if(!empty($this->get($query))) return $this->get($query);
        return $this->set($query, Base::run($query, $params)->fetchAll(PDO::FETCH_ASSOC));
    }
}