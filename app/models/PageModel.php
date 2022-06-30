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
     * @name получение одной страницы
     * ==============================
     * @param $urlOrArray
     * @param $title
     * @return array
     * @throws Exception
     */
    public function get($urlOrArray, $title = false){

        $result = [];

        if($title){

            $where = "title = ?";
            $params = [$title];

        } else{

            if(is_array($urlOrArray)){

                $where = "id = ?";
                $params = [$urlOrArray["id"]];

            } else{

                $where = "url = ?";
                $params = [$urlOrArray];
            }
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

}