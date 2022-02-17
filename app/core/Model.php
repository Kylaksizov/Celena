<?php

namespace app\core;


use PDO;

/**
 * @property PDO|null pdo
 */
abstract class Model {


    public static $resultQueries = [];


    public function __construct($conf = null){
        $this->pdo = Base::instance($conf);
    }


    public function run($query, $params){
        if(!empty($this->get($query))) return $this->get($query);
        return $this->set($query, Base::run($query, $params)->fetch(PDO::FETCH_ASSOC));
    }


    public function set($sql, $result){
        self::$resultQueries[$sql] = $result;
        return $result;
    }


    public function get($sql){
        return !empty(self::$resultQueries[$sql]) ? self::$resultQueries[$sql] : null;
    }
}