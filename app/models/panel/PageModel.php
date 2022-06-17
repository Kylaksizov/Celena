<?php

namespace app\models\panel;


use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;
use PDOStatement;

class PageModel extends Model{


    /**
     * @name создание страницы
     * =======================
     * @param $title
     * @param array $meta
     * @param string $content
     * @param string|null $url
     * @param int|null $created
     * @param int $status
     * @return bool|string
     * @throws Exception
     */
    public function create($title, array $meta = [], string $content = '', ?string $url = '', int $created = null, int $status = 1){

        if($url === null) $url = System::translit($title);
        if($created === null) $created = time();

        $params = [
            USER["id"],
            $title,
            $meta["title"],
            $meta["description"],
            $content,
            $url,
            $created,
            $status
        ];

        Base::run("INSERT INTO " . PREFIX . "pages (
            uid,
            title,
            m_title,
            m_description,
            content,
            url,
            created,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?
        )", $params);

        unset($params);

        return Base::lastInsertId();
    }


    /**
     * @name получение одного товара
     * =============================
     * @param $id
     * @return array
     * @throws Exception
     */
    public function get($id){

        return Base::run("SELECT
                *
            FROM " . PREFIX . "pages
            WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * @name получение изображений товара
     * ==================================
     * @param $page_id
     * @return array|false
     * @throws Exception
     */
    public function getImages($page_id){

        return Base::run("SELECT id, src, alt FROM " . PREFIX . "images WHERE itype = 3 AND pid = ?", [$page_id])->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @name получение всех товаров
     * ============================
     * @return array
     * @throws Exception
     */
    public function getAll(){

        $result = [];
        $params = [];

        $pagination = [
            "start" => 0,
            "limit" => 25,
            "pagination" => ""
        ];

        $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "pages ORDER BY id DESC", $params, $pagination["start"], $pagination["limit"]);

        $result["pages"] = Base::run(
            "SELECT
                id,
                uid,
                title,
                url,
                status
            FROM " . PREFIX . "pages
            ORDER BY id DESC
            LIMIT {$pagination["start"]}, {$pagination["limit"]}
            ", $params)->fetchAll(PDO::FETCH_ASSOC);

        $result["pagination"] = $pagination['pagination'];

        return $result;
    }




    /**
     * @name изменение полей произвольно
     * =================================
     * @param $id
     * @param array $fields
     * @return void
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

        return Base::run("UPDATE " . PREFIX . "pages SET $set WHERE id = ?", $params)->rowCount();
    }


    /**
     * @name изменение позиции изображений
     * ===================================
     * @param $id
     * @param $position
     * @return void
     * @throws Exception
     */
    public function editPositionImage($id, $position){

        return Base::run("UPDATE " . PREFIX . "images SET position = ? WHERE id = ?", [$position, $id])->rowCount();
    }


    /**
     * @name установка постера
     * =======================
     * @param $productId
     * @param $imageId
     * @return int
     * @throws Exception
     */
    public function setPoster($productId, $imageId){

        return Base::run("UPDATE " . PREFIX . "pages SET poster = ? WHERE id = ?", [$imageId, $productId])->rowCount();
    }



    /**
     * @name добавление картинок
     * =========================
     * @param $nid
     * @param $crs
     * @param string $alt
     * @return bool|string
     * @throws Exception
     */
    public function addImage($nid, $crs, string $alt = ''){

        Base::run("INSERT INTO " . PREFIX . "images (
            itype,
            nid,
            src,
            alt
        ) VALUES (
            ?, ?, ?, ?
        )", [3, $nid, $crs, $alt]);

        return Base::lastInsertId();
    }



    /**
     * @name удаление свойств из товара
     * ================================
     * @param $id
     * @return bool|PDOStatement
     * @throws Exception
     */
    public function deleteImage($id){

        return Base::run("DELETE FROM " . PREFIX . "images WHERE id = ?", [$id]);
    }


    /**
     * @name удаление товара
     * =====================
     * @param $id
     * @return bool|PDOStatement
     * @throws Exception
     */
    public function delete($id){

        Base::run("DELETE FROM " . PREFIX . "images WHERE itype =  AND nid = ?", [$id]);
        return Base::run("DELETE FROM " . PREFIX . "pages WHERE id = ?", [$id]);
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