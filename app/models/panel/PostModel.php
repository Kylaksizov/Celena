<?php

namespace app\models\panel;


use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;
use PDOStatement;

class PostModel extends Model{


    public function create($title, array $meta = [], string $short = '', string $content = '', array $categories = [], $url = null, $created = null, int $status = 1){

        if($url === null) $url = System::translit($title);
        if($created === null) $created = time();

        $params = [
            USER["id"],
            $title,
            $meta["title"],
            $meta["description"],
            $short,
            $content,
            implode(",", $categories),
            $url,
            $created,
            null,
            $status
        ];

        Base::run("INSERT INTO " . PREFIX . "posts (
            uid,
            title,
            m_title,
            m_description,
            short,
            content,
            category,
            url,
            created,
            last_modify,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )", $params);

        unset($params);

        $news_id =  Base::lastInsertId();

        if(!empty($categories)){
            foreach ($categories as $categoryId) {
                Base::run("INSERT INTO " . PREFIX . "posts_cat (pid, cid) VALUES (?, ?)", [$news_id, $categoryId]);
            }
        }

        return $news_id;
    }


    /**
     * @name получение одной новости
     * =============================
     * @param $id
     * @return array
     * @throws Exception
     */
    public function get($id){

        $result = [];

        $result["posts"] = Base::run("SELECT * FROM " . PREFIX . "posts WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
        $result["fields"] = System::setKeys(Base::run("SELECT id, tag, val FROM " . PREFIX . "fields WHERE pid = ?", [$id])->fetchAll(PDO::FETCH_ASSOC), "tag");
        $result["images"] = Base::run("SELECT id, src, alt FROM " . PREFIX . "images WHERE itype = 1 AND pid = ? ORDER BY position ASC", [$id])->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }


    /**
     * @name получение изображений
     * ===========================
     * @param $news_id
     * @return array|false
     * @throws Exception
     */
    public function getImages($news_id){

        return Base::run("SELECT id, src, alt FROM " . PREFIX . "images WHERE itype = 1 AND pid = ?", [$news_id])->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @name получение всех новостей
     * =============================
     * @return array
     * @throws Exception
     */
    public function getAll($all = false){

        if($all){

            $result = Base::run("SELECT * FROM " . PREFIX . "posts ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        } else{

            $result = [];
            $params = [];

            $pagination = [
                "start" => 0,
                "limit" => 25,
                "pagination" => ""
            ];

            $pagination = System::pagination("SELECT COUNT(1) AS count FROM " . PREFIX . "posts n ORDER BY id DESC", $params, $pagination["start"], $pagination["limit"]);

            $result["posts"] = Base::run(
                "SELECT
                        id,
                        uid,
                        title,
                        category,
                        url,
                        created,
                        status
                    FROM " . PREFIX . "posts n
                    GROUP BY id
                    ORDER BY id DESC
                    LIMIT {$pagination["start"]}, {$pagination["limit"]}
                    ", $params)->fetchAll(PDO::FETCH_ASSOC);

            $result["pagination"] = $pagination['pagination'];
        }

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
        $set .= "last_modify = ?";
        array_push($params, time());
        array_push($params, $id);


        // изменяем привязку продуктов и категорий
        if(!empty($fields["category"])){

            $cats = explode(",", $fields["category"]);

            $News_cat = System::setKeys(Base::run("SELECT id, cid FROM " . PREFIX . "posts_cat WHERE pid = ?", [$id])->fetchAll(PDO::FETCH_ASSOC), "cid");

            foreach ($cats as $catId) {

                if(!empty($News_cat[$catId])){

                    Base::run("UPDATE " . PREFIX . "posts_cat SET cid = ? WHERE id = ?", [$catId, $News_cat[$catId]["id"]])->rowCount();
                    unset($News_cat[$catId]);

                } else Base::run("INSERT INTO " . PREFIX . "posts_cat (pid, cid) VALUES (?, ?)", [$id, $catId]);
            }

            if(!empty($News_cat)){ // если остались лишние, удаляем
                foreach ($News_cat as $pc) {
                    Base::run("DELETE FROM " . PREFIX . "posts_cat WHERE id = ?", [$pc["id"]]);
                }
            }
        }


        return Base::run("UPDATE " . PREFIX . "posts SET $set WHERE id = ?", $params)->rowCount();
    }


    /**
     * @name изменение полей произвольно
     * =================================
     * @param $id
     * @param array $fields
     * @return void
     * @throws Exception
     */
    public function editFieldsImages($id, array $fields){

        $set = "";
        $params = [];

        foreach ($fields as $fieldName => $val) {
            $set .= "$fieldName = ?, ";
            array_push($params, $val);
        }
        $set = trim($set, ", ");

        array_push($params, $id);

        Base::run("UPDATE " . PREFIX . "images SET $set WHERE id = ?", $params)->rowCount();
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
     * @param $news_id
     * @param $imageId
     * @return int
     * @throws Exception
     */
    public function setPoster($news_id, $imageId){

        return Base::run("UPDATE " . PREFIX . "posts SET poster = ? WHERE id = ?", [$imageId, $news_id])->rowCount();
    }


    /**
     * @name добавление картинок
     * =========================
     * @param $type
     * @param $nid
     * @param $crs
     * @param string $alt
     * @return bool|string
     * @throws Exception
     */
    public function addImage($type, $nid, $crs, string $alt = ''){

        Base::run("INSERT INTO " . PREFIX . "images (
            itype,
            pid,
            src,
            alt
        ) VALUES (
            ?, ?, ?, ?
        )", [$type, $nid, $crs, $alt]);

        return Base::lastInsertId();
    }


    /**
     * @name удаление изображения
     * ==========================
     * @param $id
     * @return bool|PDOStatement
     * @throws Exception
     */
    public function deleteImage($id){

        return Base::run("DELETE FROM " . PREFIX . "images WHERE id = ?", [$id]);
    }


    /**
     * @name удаление новости
     * ======================
     * @param $id
     * @return bool|PDOStatement
     * @throws Exception
     */
    public function delete($id){

        Base::run("DELETE FROM " . PREFIX . "images WHERE itype = 1 AND pid = ?", [$id]);
        Base::run("DELETE FROM " . PREFIX . "posts_cat WHERE pid = ?", [$id]);
        return Base::run("DELETE FROM " . PREFIX . "posts WHERE id = ?", [$id]);
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