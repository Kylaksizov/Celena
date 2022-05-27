<?php

namespace app\models\panel;

use app\core\Base;
use app\core\Model;
use PDO;

class PluginModel extends Model{

    /**
     * @name получение системных значений
     * ==================================
     * @param string $fields
     * @param int|string $status
     * @return mixed|null
     */
    public function getUniqPlugins($fields = '*', $status = 'all'){

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



    public function getPlugin($brand, $name){

        return Base::run("SELECT name, status FROM " . PREFIX . "plugins WHERE name = ?", [trim(htmlspecialchars(stripslashes($brand."/".$name)))])->fetch(PDO::FETCH_ASSOC);
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


    private function instanceFetch($query, $params){
        if(!empty($this->get($query))) return $this->get($query);
        return $this->set($query, Base::run($query, $params)->fetch(PDO::FETCH_ASSOC));
    }

    private function instanceFetchAll($query, $params){
        if(!empty($this->get($query))) return $this->get($query);
        return $this->set($query, Base::run($query, $params)->fetchAll(PDO::FETCH_ASSOC));
    }
}