<?php

namespace app\models;


use app\core\Base;
use app\core\Model;
use PDO;

class CelenaSystemModel extends Model{


    public function __construct($conf = null){
        parent::__construct($conf);
    }



    public function getPlugin($brand, $name){

        return Base::run("SELECT name, status FROM " . PREFIX . "systems WHERE s_type = ? AND name = ?", ["plugin", trim(htmlspecialchars(stripslashes($brand."/".$name)))])->fetch(PDO::FETCH_ASSOC);
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