<?php

namespace app\models;


use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;
use PDOStatement;

class ProductModel extends Model{


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
                'p.id, p.uid AS author_id, p.title, p.url, p.content, p.m_title, p.m_description, p.category, p.price',
                '{sale}'       => 'p.sale',
                '[sale]'       => 'p.sale',
                '{old-price}'  => 'p.sale',
                '{stock}'      => 'p.stock',
                '{vendor}'     => 'p.vendor',
                '{date}'       => 'p.created',
                '{poster}'     => 'p.poster',
                '{brand-id}'   => 'p.brand AS brand_id',
                '{brand-name}' => 'b.name AS brand_name',
                '{brand-url}'  => 'b.url AS brand_url',
                '{brand-icon}' => 'b.icon AS brand_icon',
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
            $leftJoin .= " LEFT JOIN " . PREFIX . "images i ON i.nid = p.id";
            unset($fields["{images}"]);
        }

        $fieldsString = implode(", ", $fields);

        $result["product"] = Base::run("
            SELECT
                $fieldsString
            FROM " . PREFIX . "products p
                $leftJoin
            WHERE $where
            ", $params)->fetch(PDO::FETCH_ASSOC);

        // достаем инфу о категориях
        if(!empty($result["product"])){

            /*$catsIds = explode(",", $result["product"]["category"]);

            if(count($catsIds) == 1){
                $whereCategory = "id = ?";
                $paramsCategory = $catsIds;
            } else{
                $whereCategory = "";
                $paramsCategory = [];
                foreach ($catsIds as $cId) {
                    $whereCategory .= "id = ? OR ";
                    array_push($paramsCategory, $cId);
                }
                $whereCategory = trim($whereCategory, " OR ");
            }

            $result["categories"] = System::setKeys(
                Base::run(
                    "SELECT
                    id,
                    title,
                    icon,
                    url
                FROM " . PREFIX . "categories
                WHERE $whereCategory",
                    $paramsCategory)->fetchAll(PDO::FETCH_ASSOC),
                "id"
            );*/

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
        if(!empty($result["product"]) && $tagImages){

            $result["images"] = System::setKeys(
                Base::run(
                    "SELECT
                    id,
                    src,
                    alt
                FROM " . PREFIX . "images
                WHERE
                    nid = ? AND itype = 1
                    ORDER BY position ASC",
                    [$result["product"]["id"]])->fetchAll(PDO::FETCH_ASSOC),
                "id"
            );
        }

        // если есть тег на получение свойств
        if(isset($fields["{properties}"]) && !empty($result["product"])){

            $result["properties"] = System::setKeysArray(
                Base::run(
                    "SELECT
                    ps.title,
                    ps.f_type,
                    pp.id,
                    pp.sep,
                    pp.vendor,
                    pp.price,
                    pp.pv,
                    pp.stock,
                    pv.def,
                    pv.val
                FROM " . PREFIX . "product_prop pp
                    LEFT JOIN " . PREFIX . "properties_v pv ON pv.id = pp.id_pv
                    LEFT JOIN " . PREFIX . "properties ps ON ps.id = pp.id_p
                WHERE
                    pp.pid = ?
                    ORDER BY pv.position ASC",
                    [$result["product"]["id"]])->fetchAll(PDO::FETCH_ASSOC),
                "title"
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
    public function getProducts($categories, bool $paginationPow = false, int $limit = 10, string $order = 'id', string $sort = 'desc'){

        $where = "";
        $params = [];
        $catId = null;
        //$wherePagination = "";
        //$paramsPagination = [];

        $pagination = [
            "start" => 0,
            "limit" => CONFIG_SYSTEM["count_prod_by_cat"],
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

        //$join_category = "INNER JOIN (SELECT DISTINCT(" . PREFIX . "products_cat.news_id) FROM " . PREFIX . "products_cat pc WHERE pc.cid IN ('" . $catId . "')) c ON (p.id=c.news_id) ";


        $where .= "c.status != 0 AND p.status != 0";

        $sort = ($sort == 'desc') ? 'DESC' : 'ASC';
        switch ($order){
            case "date": $orderBy = "p.created $sort"; break;
            case "price": $orderBy = "p.price $sort"; break;
            case "modify": $orderBy = "p.last_modify $sort"; break;
            default: $orderBy = "p.id $sort";
        }

        $limitQuery = "LIMIT $limit";

        if($paginationPow){

            if(is_string($categories)){

                //$where = "p.status != 0";

                $pagination = System::pagination("SELECT COUNT(DISTINCT pc.pid) AS count FROM " . PREFIX . "products_cat pc
                    LEFT JOIN " . PREFIX . "categories c ON c.id = pc.cid
                    LEFT JOIN " . PREFIX . "products p ON p.id = pc.pid
                WHERE " . $where, $params, $pagination["start"], $limit);

                $limitQuery = "LIMIT {$pagination["start"]}, $limit";

            } else if(is_array($categories)){

                $where = "p.status != 0";

                $pagination = System::pagination("SELECT COUNT(*) AS count FROM " . PREFIX . "products p
             WHERE " . $where, $params, $pagination["start"], $limit);

                $limitQuery = "LIMIT {$pagination["start"]}, $limit";
            }
        }

        $result["products"] = Base::run("SELECT
                pc.pid AS id,
                c.id AS category_id,
                c.title AS category_title,
                c.url AS category_url,
                c.pid AS parent_category,
                p.title AS title,
                p.vendor,
                p.price,
                p.sale,
                p.stock,
                p.url,
                p.created,
                i.src AS poster
            FROM " . PREFIX . "products_cat pc
                LEFT JOIN " . PREFIX . "categories c ON c.id = pc.cid
                LEFT JOIN " . PREFIX . "products p ON p.id = pc.pid
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
    public function getCustomProducts($categories, $limit = 10, $order = 'id', $sort = 'desc'){

        $where = "";
        $params = [];

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

        } else if(is_string($categories) && $categories != 'index'){ // если мы в категории

            $products_cat = Base::run("
                SELECT
                    pc.pid
                FROM " . PREFIX . "categories c
                    LEFT JOIN " . PREFIX . "products_cat pc ON pc.cid = c.id
                WHERE c.url = ?
            ", [$categories])->fetchAll(PDO::FETCH_COLUMN);

            if(empty($products_cat)) return [];

            $addWhere = "(";
            foreach ($products_cat as $pid) {
                $addWhere .= "p.id = ? OR ";

            }
            $addWhere = trim($addWhere, " OR ").") AND ";
            $params = $products_cat;

            $where = $addWhere;

        }


        $where .= "c.status != 0 AND p.status != 0";

        $sort = ($sort == 'desc') ? 'DESC' : 'ASC';
        switch ($order){
            case "date": $orderBy = "p.created $sort"; break;
            case "price": $orderBy = "p.price $sort"; break;
            case "modify": $orderBy = "p.last_modify $sort"; break;
            default: $orderBy = "p.id $sort";
        }

        $limitQuery = "LIMIT $limit";

        if($categories == 'index'){

            $where = "p.status != 0";

            $pagination = System::pagination("SELECT COUNT(*) AS count FROM " . PREFIX . "products p
             WHERE " . $where, $params, $pagination["start"], $limit);

            $limitQuery = "LIMIT {$pagination["start"]}, $limit";
        }

        $result["products"] = Base::run("SELECT
                pc.pid AS id,
                c.id AS category_id,
                c.title AS category_title,
                c.url AS category_url,
                c.pid AS parent_category,
                p.title AS title,
                p.vendor,
                p.price,
                p.sale,
                p.stock,
                p.url,
                p.created,
                i.src AS poster
            FROM " . PREFIX . "products_cat pc
                LEFT JOIN " . PREFIX . "categories c ON c.id = pc.cid
                LEFT JOIN " . PREFIX . "products p ON p.id = pc.pid
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
            case "price": $orderBy = "p.price $sort"; break;
            case "modify": $orderBy = "p.last_modify $sort"; break;
            default: $orderBy = "p.id $sort";
        }

        $whereP = "p.title LIKE ? AND p.status != 0";
        $pagination = System::pagination("SELECT COUNT(*) AS count FROM " . PREFIX . "products p
         WHERE " . $whereP, $params, $pagination["start"], $limit);

        $result["products"] = Base::run("SELECT
                pc.pid AS id,
                c.id AS category_id,
                c.title AS category_title,
                c.url AS category_url,
                c.pid AS parent_category,
                p.title AS title,
                p.vendor,
                p.price,
                p.sale,
                p.stock,
                p.url,
                p.created,
                i.src AS poster
            FROM " . PREFIX . "products_cat pc
                LEFT JOIN " . PREFIX . "categories c ON c.id = pc.cid
                LEFT JOIN " . PREFIX . "products p ON p.id = pc.pid
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
     * @param $product_id
     * @return array|false
     * @throws Exception
     */
    public function getImages($product_id){

        return Base::run("SELECT id, src, alt, position FROM " . PREFIX . "images WHERE itype = 1 AND nid = ? ORDER BY position DESC", [$product_id])->fetchAll(PDO::FETCH_ASSOC);
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

        $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "products c ORDER BY id DESC", $params, $pagination["start"], $pagination["limit"]);

        $result["products"] = Base::run(
            "SELECT
                    p.id,
                    p.uid,
                    p.title,
                    p.category,
                    p.price,
                    p.sale,
                    p.stock,
                    p.url,
                    p.created,
                    p.status,
                    i.src,
                    i.position
                FROM " . PREFIX . "products p
                    LEFT JOIN " . PREFIX . "images i ON i.nid = p.id
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

        return Base::run("UPDATE " . PREFIX . "products SET $set WHERE id = ?", $params)->rowCount();
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