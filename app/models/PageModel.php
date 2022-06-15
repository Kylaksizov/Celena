<?php

namespace app\models;


use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;
use PDOStatement;

class PageModel extends Model{


    /**
     * @name получение одного товара
     * =============================
     * @param string|array $urlOrArray
     * @return array
     * @throws Exception
     */
    public function get($urlOrArray){

        $result = [];

        if(is_array($urlOrArray)){

            $where = "id = ?";
            $params = [$urlOrArray["id"]];

        } else{

            $where = "url = ?";
            $params = [$urlOrArray];
        }

        $result["page"] = Base::run("
            SELECT
                *
            FROM " . PREFIX . "pages
            WHERE $where
            ", $params)->fetch(PDO::FETCH_ASSOC);

        // если есть тег на получение картинок
        if(!empty($result["page"])){

            $result["images"] = System::setKeys(
                Base::run(
                    "SELECT
                    id,
                    src,
                    alt
                FROM " . PREFIX . "images
                WHERE
                    pid = ? AND itype = 3
                    ORDER BY position ASC",
                    [$result["page"]["id"]])->fetchAll(PDO::FETCH_ASSOC),
                "id"
            );
        }

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