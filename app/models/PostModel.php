<?php

namespace app\models;


use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;
use PDOStatement;

class PostModel extends Model{


    /**
     * @name получение одного товара
     * =============================
     * @param string|array $urlOrArray
     * @param $fields
     * @return array
     * @throws Exception
     */
    public function get($urlOrArray, $fields = null){

        $result = [];

        if(is_array($urlOrArray)){

            $where = "p.id = ?";
            $params = [$urlOrArray["id"]];

        } else{

            $where = "p.url = ?";
            $params = [$urlOrArray];
        }

        if(!$fields){ // поля по умолчанию

            $fields = [
                'p.id, p.uid AS author_id, p.title, p.url, p.content, p.m_title, p.m_description, p.category',
                '{date}'       => 'p.created',
                '{poster}'     => 'p.poster',
                '{images}'     => '1',
            ];
        }

        $leftJoin = "";

        if(isset($fields["{brand-id}"]) || isset($fields["{brand-name}"]) || isset($fields["{brand-url}"]) || isset($fields["{brand-icon}"])){
            $leftJoin .= " LEFT JOIN " . PREFIX . "brands b ON b.id = p.brand";
        }

        // проверка на изображения
        $tagPoster = isset($fields["{poster}"]);
        $tagImages = isset($fields["{images}"]);

        if($tagPoster && $tagImages){
            $fields["{poster}"] = "p.poster";
            $leftJoin .= " LEFT JOIN " . PREFIX . "images i ON i.id = p.poster";
            unset($fields["{images}"]);
        } else if($tagPoster && !$tagImages){
            $fields["{poster}"] = "p.poster";
            $fields["{poster2}"] = "i.src AS poster_src";
            $leftJoin .= " LEFT JOIN " . PREFIX . "images i ON i.id = p.poster";
        } else if(!$tagPoster && $tagImages){
            $leftJoin .= " LEFT JOIN " . PREFIX . "images i ON i.pid = p.id";
            unset($fields["{images}"]);
        }

        $fieldsString = implode(", ", $fields);

        $result["post"] = Base::run("
            SELECT
                $fieldsString
            FROM " . PREFIX . "posts p
                $leftJoin
            WHERE $where
            ", $params)->fetch(PDO::FETCH_ASSOC);

        // достаем инфу о категориях
        if(!empty($result["post"])){

            #TODO временно сделаю выборку всех категорий
            $result["categories"] = System::setKeys(
                Base::run("SELECT
                    id,
                    title,
                    url,
                    pid
                FROM " . PREFIX . "categories", [])->fetchAll(PDO::FETCH_ASSOC),
                "id"
            );
        }

        // если есть тег на получение картинок
        if(!empty($result["post"]) && $tagImages){

            $result["images"] = System::setKeys(
                Base::run(
                    "SELECT
                    id,
                    src,
                    alt
                FROM " . PREFIX . "images
                WHERE
                    pid = ? AND itype = 1
                    ORDER BY position ASC",
                    [$result["post"]["id"]])->fetchAll(PDO::FETCH_ASSOC),
                "id"
            );
        }

        return $result;
    }


    /**
     * @name получение всех товаров
     * ============================
     * @param $categories
     * @param bool $paginationPow
     * @param int $limit
     * @param string $order
     * @param string $sort
     * @return array
     * @throws Exception
     */
    public function getPosts($categories, bool $paginationPow = false, int $limit = 10, string $order = 'id', string $sort = 'desc'){

        $where = "";
        $params = [];
        $catId = null;
        //$wherePagination = "";
        //$paramsPagination = [];

        $pagination = [
            "start" => 0,
            "limit" => CONFIG_SYSTEM["count_in_cat"],
            "pagination" => ""
        ];


        #TODO временно сделаю выборку всех категорий
        #TODO переделать этот мусор до...
        $result["categories"] = System::setKeys(
            Base::run("SELECT
                id,
                title,
                m_title,
                m_description,
                url,
                pid
            FROM " . PREFIX . "categories", $params)->fetchAll(PDO::FETCH_ASSOC),
            "id"
        );


        if(is_string($categories)){ // если передано url строка конечной категории

            foreach ($result["categories"] as $categoryRow) {
                if($categoryRow["url"] == $categories){
                    $catId = $categoryRow["id"];
                    //$wherePagination = "p.category REGEXP '[[:<:]]".$categoryRow["id"].",'";
                    $where .= "pc.cid = ?";
                    array_push($params, $categoryRow["id"]);
                    break;
                }
            }

            if(!empty($where))
                $where = "(" . $where . ") AND ";

        } else if(is_array($categories)){

        }
        #TODO сюда...

        //$join_category = "INNER JOIN (SELECT DISTINCT(" . PREFIX . "posts_cat.news_id) FROM " . PREFIX . "posts_cat pc WHERE pc.cid IN ('" . $catId . "')) c ON (p.id=c.news_id) ";


        $where .= "c.status != 0 AND p.status != 0";

        $sort = ($sort == 'desc') ? 'DESC' : 'ASC';
        switch ($order){
            case "date": $orderBy = "p.created $sort"; break;
            case "modify": $orderBy = "p.last_modify $sort"; break;
            default: $orderBy = "p.id $sort";
        }

        $limitQuery = "LIMIT $limit";

        if($paginationPow){

            if(is_string($categories)){

                //$where = "p.status != 0";

                $pagination = System::pagination("SELECT COUNT(DISTINCT pc.pid) AS count FROM " . PREFIX . "posts_cat pc
                    LEFT JOIN " . PREFIX . "categories c ON c.id = pc.cid
                    LEFT JOIN " . PREFIX . "posts p ON p.id = pc.pid
                WHERE " . $where, $params, $pagination["start"], $limit);

                $limitQuery = "LIMIT {$pagination["start"]}, $limit";

            } else if(is_array($categories)){

                $where = "p.status != 0";

                $pagination = System::pagination("SELECT COUNT(*) AS count FROM " . PREFIX . "posts p
             WHERE " . $where, $params, $pagination["start"], $limit);

                $limitQuery = "LIMIT {$pagination["start"]}, $limit";
            }
        }

        $result["posts"] = Base::run("SELECT
                pc.pid AS id,
                c.id AS category_id,
                c.title AS category_title,
                c.url AS category_url,
                c.pid AS parent_category,
                p.title AS title,
                p.url,
                p.created,
                i.src AS poster
            FROM " . PREFIX . "posts_cat pc
                LEFT JOIN " . PREFIX . "categories c ON c.id = pc.cid
                LEFT JOIN " . PREFIX . "posts p ON p.id = pc.pid
                LEFT JOIN " . PREFIX . "images i ON i.id = p.poster
            WHERE " . $where . " GROUP BY $orderBy
            $limitQuery
                ", $params)->fetchAll(PDO::FETCH_ASSOC);

        $result["pagination"] = $pagination['pagination'];

        return $result;
    }


    /**
     * @name получение всех товаров
     * ============================
     * @return array
     * @throws Exception
     */
    public function getCustomposts($categories, $limit = 10, $order = 'id', $sort = 'desc'){

        $where = "";
        $params = [];

        $pagination = [
            "start" => 0,
            "limit" => CONFIG_SYSTEM["count_in_cat"],
            "pagination" => ""
        ];


        #TODO временно сделаю выборку всех категорий
        $result["categories"] = System::setKeys(
            Base::run("SELECT
                id,
                title,
                url,
                pid
            FROM " . PREFIX . "categories", $params)->fetchAll(PDO::FETCH_ASSOC),
            "id"
        );


        if(is_array($categories)){ // если это массив из ID категорий

            foreach ($categories as $categoryId) {
                $where .= "pc.cid = ? OR ";
                array_push($params, $categoryId);
            }

            if(!empty($where)){
                $where = "(" . trim($where, " OR ") . ") AND ";
            }

        } else if(is_string($categories) && $categories != 'index'){ // если мы в категории

            $posts_cat = Base::run("
                SELECT
                    pc.pid
                FROM " . PREFIX . "categories c
                    LEFT JOIN " . PREFIX . "posts_cat pc ON pc.cid = c.id
                WHERE c.url = ?
            ", [$categories])->fetchAll(PDO::FETCH_COLUMN);

            if(empty($posts_cat)) return [];

            $addWhere = "(";
            foreach ($posts_cat as $pid) {
                $addWhere .= "p.id = ? OR ";

            }
            $addWhere = trim($addWhere, " OR ").") AND ";
            $params = $posts_cat;

            $where = $addWhere;

        }


        $where .= "c.status != 0 AND p.status != 0";

        $sort = ($sort == 'desc') ? 'DESC' : 'ASC';
        switch ($order){
            case "date": $orderBy = "p.created $sort"; break;
            case "modify": $orderBy = "p.last_modify $sort"; break;
            default: $orderBy = "p.id $sort";
        }

        $limitQuery = "LIMIT $limit";

        if($categories == 'index'){

            $where = "p.status != 0";

            $pagination = System::pagination("SELECT COUNT(*) AS count FROM " . PREFIX . "posts p
             WHERE " . $where, $params, $pagination["start"], $limit);

            $limitQuery = "LIMIT {$pagination["start"]}, $limit";
        }

        $result["posts"] = Base::run("SELECT
                pc.pid AS id,
                c.id AS category_id,
                c.title AS category_title,
                c.url AS category_url,
                c.pid AS parent_category,
                p.title AS title,
                p.url,
                p.created,
                i.src AS poster
            FROM " . PREFIX . "posts_cat pc
                LEFT JOIN " . PREFIX . "categories c ON c.id = pc.cid
                LEFT JOIN " . PREFIX . "posts p ON p.id = pc.pid
                LEFT JOIN " . PREFIX . "images i ON i.id = p.poster
            WHERE " . $where . " GROUP BY $orderBy
            $limitQuery
                ", $params)->fetchAll(PDO::FETCH_ASSOC);

        $result["pagination"] = $pagination['pagination'];

        return $result;
    }




    /**
     * @name получение всех товаров
     * ============================
     * @return array
     * @throws Exception
     */
    public function search($search, $categories, $limit = 10, $order = 'id', $sort = 'desc'){

        $search = trim(htmlspecialchars(stripslashes($search)));
        $where = "p.title LIKE ? AND ";
        $params = ["%$search%"];

        $pagination = [
            "start" => 0,
            "limit" => CONFIG_SYSTEM["count_prod_by_cat"],
            "pagination" => ""
        ];

        #TODO временно сделаю выборку всех категорий
        $result["categories"] = System::setKeys(
            Base::run("SELECT
                id,
                title,
                url,
                pid
            FROM " . PREFIX . "categories", $params)->fetchAll(PDO::FETCH_ASSOC),
            "id"
        );


        if(is_array($categories)){ // если это массив из ID категорий

            foreach ($categories as $categoryId) {
                $where .= "pc.cid = ? OR ";
                array_push($params, $categoryId);
            }

            if(!empty($where)){
                $where = "(" . trim($where, " OR ") . ") AND ";
            }
        }


        $where .= "c.status != 0 AND p.status != 0";

        $sort = ($sort == 'desc') ? 'DESC' : 'ASC';
        switch ($order){
            case "date": $orderBy = "p.created $sort"; break;
            case "modify": $orderBy = "p.last_modify $sort"; break;
            default: $orderBy = "p.id $sort";
        }

        $whereP = "p.title LIKE ? AND p.status != 0";
        $pagination = System::pagination("SELECT COUNT(*) AS count FROM " . PREFIX . "posts p
         WHERE " . $whereP, $params, $pagination["start"], $limit);

        $result["posts"] = Base::run("SELECT
                pc.pid AS id,
                c.id AS category_id,
                c.title AS category_title,
                c.url AS category_url,
                c.pid AS parent_category,
                p.title AS title,
                p.url,
                p.created,
                i.src AS poster
            FROM " . PREFIX . "posts_cat pc
                LEFT JOIN " . PREFIX . "categories c ON c.id = pc.cid
                LEFT JOIN " . PREFIX . "posts p ON p.id = pc.pid
                LEFT JOIN " . PREFIX . "images i ON i.id = p.poster
            WHERE " . $where . " GROUP BY $orderBy
            LIMIT {$pagination["start"]}, $limit
                ", $params)->fetchAll(PDO::FETCH_ASSOC);

        $result["pagination"] = $pagination['pagination'];

        return $result;
    }



    /**
     * @name получение изображений товара
     * ==================================
     * @param $post_id
     * @return array|false
     * @throws Exception
     */
    public function getImages($post_id){

        return Base::run("SELECT id, src, alt, position FROM " . PREFIX . "images WHERE itype = 1 AND pid = ? ORDER BY position DESC", [$post_id])->fetchAll(PDO::FETCH_ASSOC);
    }



    /**
     * @name получение всех товаров
     * ============================
     * @return array
     * @throws Exception
     */
    public function getAll($categories = []){

        $result = [];
        $params = [];

        $pagination = [
            "start" => 0,
            "limit" => 25,
            "pagination" => ""
        ];

        $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "posts c ORDER BY id DESC", $params, $pagination["start"], $pagination["limit"]);

        $result["posts"] = Base::run(
            "SELECT
                    p.id,
                    p.uid,
                    p.title,
                    p.category,
                    p.url,
                    p.created,
                    p.status,
                    i.src,
                    i.position
                FROM " . PREFIX . "posts p
                    LEFT JOIN " . PREFIX . "images i ON i.pid = p.id
                GROUP BY p.id
                ORDER BY p.id DESC
                LIMIT {$pagination["start"]}, {$pagination["limit"]}
                ", $params)->fetchAll(PDO::FETCH_ASSOC);

        $result["pagination"] = $pagination['pagination'];

        return $result;
    }


    /**
     * @name получение всех свойств
     * ============================
     */
    /*public function getPropertiesAll($all = false){

        if($all){

            $result = System::setKeysArray(Base::run("SELECT p.*, v.id AS vid, v.val, v.def FROM " . PREFIX . "properties p LEFT JOIN " . PREFIX . "properties_v v ON v.pid = p.id ORDER BY p.position, v.position ASC")->fetchAll(PDO::FETCH_ASSOC), "title");

        } else{

            $result = [];
            $params = [];

            $pagination = [
                "start" => 0,
                "limit" => 25,
                "pagination" => ""
            ];

            $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "properties c ORDER BY id DESC", $params, $pagination["start"], $pagination["limit"]);

            $result["properties"] = Base::run(
                "SELECT * FROM " . PREFIX . "properties ORDER BY id DESC LIMIT {$pagination["start"]}, {$pagination["limit"]}", $params)->fetchAll(PDO::FETCH_ASSOC);

            $result["pagination"] = $pagination['pagination'];
        }

        return $result;
    }*/


    /**
     * @name изменение полей произвольно
     * =================================
     */
    /*public function editFields($id, array $fields){

        $set = "";
        $params = [];

        foreach ($fields as $fieldName => $val) {
            $set .= "$fieldName = ?, ";
            array_push($params, $val);
        }
        $set .= "last_modify = ?";
        array_push($params, time());
        array_push($params, $id);

        return Base::run("UPDATE " . PREFIX . "posts SET $set WHERE id = ?", $params)->rowCount();
    }*/







    private function instanceFetch($query, $params){
        if(!empty($this->get($query))) return $this->get($query);
        return $this->set($query, Base::run($query, $params)->fetch(PDO::FETCH_ASSOC));
    }

    private function instanceFetchAll($query, $params){
        if(!empty($this->get($query))) return $this->get($query);
        return $this->set($query, Base::run($query, $params)->fetchAll(PDO::FETCH_ASSOC));
    }

}