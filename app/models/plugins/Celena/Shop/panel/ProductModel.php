<?php

namespace app\models\plugins\Celena\Shop\panel;


use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;
use PDOStatement;

class ProductModel extends Model{


    /**
     * @param $title
     * @param string $vendor
     * @param array $meta
     * @param string $content
     * @param array $categories
     * @param $brand
     * @param $price
     * @param string|null $sale
     * @param null $stock
     * @param null $url
     * @param null $created
     * @param int $status
     * @return bool|string
     * @throws Exception
     */
    public function create($title, string $vendor = '', array $meta = [], string $content = '', array $categories = [], $brand = null, $price = 0, string $sale = null, $stock = null, $url = null, $created = null, int $status = 1){
        
        if($url === null) $url = System::translit($title);
        if($created === null) $created = time();

        $params = [
            USER["id"],
            $vendor,
            $title,
            $meta["title"],
            $meta["description"],
            $content,
            implode(",", $categories),
            $brand,
            $price,
            $sale,
            $stock,
            $url,
            $created,
            null,
            $status
        ];

        Base::run("INSERT INTO " . PREFIX . "products (
            uid,
            vendor,
            title,
            m_title,
            m_description,
            content,
            category,
            brand,
            price,
            sale,
            stock,
            url,
            created,
            last_modify,
            status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )", $params);

        unset($params);

        $product_id =  Base::lastInsertId();

        if(!empty($categories) && $product_id){
            foreach ($categories as $categoryId) {
                Base::run("INSERT INTO " . PREFIX . "products_cat (pid, cid) VALUES (?, ?)", [$product_id, $categoryId]);
            }
        }

        return $product_id;
    }


    /**
     * @name ?????????????????? ???????????? ????????????
     * =============================
     * @param $id
     * @return array
     * @throws Exception
     */
    public function get($id){

        $result = [];

        $result["product"] = Base::run("SELECT * FROM " . PREFIX . "products WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
        $result["fields"] = System::setKeys(Base::run("SELECT id, tag, val FROM " . PREFIX . "fields WHERE pid = ? AND plugin_id = ?", [$id, PLUGIN["plugin_id"]])->fetchAll(PDO::FETCH_ASSOC), "tag");
        $result["images"] = Base::run("SELECT id, src, alt FROM " . PREFIX . "images WHERE itype = 1 AND pid = ? ORDER BY position ASC", [$id])->fetchAll(PDO::FETCH_ASSOC);
        $result["brands"] = Base::run("SELECT id, name, icon, categories FROM " . PREFIX . "brands ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        $result["props"] = Base::run("SELECT
                p.id,
                p.title,
                pp.id AS pp_id,
                pp.id_p,
                pp.id_pv,
                pp.sep,
                pp.vendor,
                pp.price,
                pp.pv,
                pp.stock,
                pv.pid,
                pv.val
            FROM " . PREFIX . "product_prop pp
                LEFT JOIN " . PREFIX . "properties_v pv ON pv.id = pp.id_pv
                LEFT JOIN " . PREFIX . "properties p ON p.id = pp.id_p
            WHERE pp.pid = ? ORDER BY pp.id_p DESC", [$id])->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }


    /**
     * @name ?????????????????? ?????????????????????? ????????????
     * ==================================
     * @param $product_id
     * @return array|false
     * @throws Exception
     */
    public function getImages($product_id){

        return Base::run("SELECT id, src, alt FROM " . PREFIX . "images WHERE itype = 1 AND pid = ?", [$product_id])->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * @name ?????????????????? ???????? ??????????????
     * ============================
     * @return array
     * @throws Exception
     */
    public function getAll($all = false){

        if($all){

            $result = Base::run("SELECT * FROM " . PREFIX . "products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

        } else{

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
                        p.poster,
                        p.created,
                        p.status,
                        i.src
                    FROM " . PREFIX . "products p
                        LEFT JOIN " . PREFIX . "images i ON i.id = p.poster
                    GROUP BY p.id
                    ORDER BY p.id DESC
                    LIMIT {$pagination["start"]}, {$pagination["limit"]}
                    ", $params)->fetchAll(PDO::FETCH_ASSOC);

            $result["pagination"] = $pagination['pagination'];
        }

        return $result;
    }


    /**
     * @name ?????????????????? ???????? ??????????????
     * ============================
     * @return array
     * @throws Exception
     */
    public function getPropertiesAll($all = false){

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
    }


    /**
     * @name ?????????????????? ?????????? ??????????????????????
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


        // ???????????????? ???????????????? ?????????????????? ?? ??????????????????
        if(!empty($fields["category"])){

            $cats = explode(",", $fields["category"]);

            $Products_cat = System::setKeys(Base::run("SELECT id, cid FROM " . PREFIX . "products_cat WHERE pid = ?", [$id])->fetchAll(PDO::FETCH_ASSOC), "cid");

            foreach ($cats as $catId) {

                if(!empty($Products_cat[$catId])){

                    Base::run("UPDATE " . PREFIX . "products_cat SET cid = ? WHERE id = ?", [$catId, $Products_cat[$catId]["id"]])->rowCount();
                    unset($Products_cat[$catId]);

                } else Base::run("INSERT INTO " . PREFIX . "products_cat (pid, cid) VALUES (?, ?)", [$id, $catId]);
            }

            if(!empty($Products_cat)){ // ???????? ???????????????? ????????????, ??????????????
                foreach ($Products_cat as $pc) {
                    Base::run("DELETE FROM " . PREFIX . "products_cat WHERE id = ?", [$pc["id"]]);
                }
            }
        }


        return Base::run("UPDATE " . PREFIX . "products SET $set WHERE id = ?", $params)->rowCount();
    }


    /**
     * @name ?????????????????? ?????????? ??????????????????????
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
     * @name ?????????????????? ?????????????? ??????????????????????
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
     * @name ?????????????????? ??????????????
     * =======================
     * @param $productId
     * @param $imageId
     * @return int
     * @throws Exception
     */
    public function setPoster($productId, $imageId){

        return Base::run("UPDATE " . PREFIX . "products SET poster = ? WHERE id = ?", [$imageId, $productId])->rowCount();
    }


    /**
     * @name ???????????????????? ????????????????
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
     * @name ???????????????????? ?????????????? ????????????
     * ===============================
     * @param $product_id
     * @param $property_id
     * @param $property_v_id
     * @param $sep
     * @param $vendor
     * @param $price
     * @param $pv
     * @param $stock
     * @return bool|string
     * @throws Exception
     */
    public function addProperty($product_id, $property_id, $property_v_id, $sep, $vendor, $price, $pv, $stock = null){

        $params = [
            $product_id,
            $property_id,
            $property_v_id,
            $sep,
            $vendor,
            $price,
            $pv,
            $stock
        ];

        Base::run("INSERT INTO " . PREFIX . "product_prop (
            pid,
            id_p,
            id_pv,
            sep,
            vendor,
            price,
            pv,
            stock
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?
        )", $params);

        unset($params);

        return Base::lastInsertId();
    }





    /**
     * @name ?????????????????? ?????????????? ?????? ????????????
     * ==================================
     * @param $id
     * @param $product_id
     * @param $property_id
     * @param $property_v_id
     * @param $sep
     * @param $vendor
     * @param $price
     * @param $pv
     * @param $stock
     * @return int
     * @throws Exception
     */
    public function editProperty($id, $product_id, $property_id, $property_v_id, $sep, $vendor, $price, $pv, $stock = null){

        return Base::run("UPDATE " . PREFIX . "product_prop SET pid = ?, id_p = ?, id_pv = ?, sep = ?, vendor = ?, price = ?, pv = ?, stock = ? WHERE id = ?", [$product_id, $property_id, $property_v_id, $sep, $vendor, $price, $pv, $stock, $id])->rowCount();
    }


    /**
     * @name ???????????????? ?????????????? ???? ????????????
     * ================================
     * @param $id
     * @return bool|PDOStatement
     * @throws Exception
     */
    public function deleteProperty($id){

        if(is_array($id)){

            $where = "";
            $params = [];

            foreach ($id as $item) {
                $where .= "id = ? OR ";
                array_push($params, $item);
            }
            $where = trim($where, " OR ");

        } else{

            $where = "id = ?";
            $params = [$id];
        }

        return Base::run("DELETE FROM " . PREFIX . "product_prop WHERE " . $where, $params);
    }


    /**
     * @name ???????????????? ?????????????? ???? ????????????
     * ================================
     * @param $id
     * @return bool|PDOStatement
     * @throws Exception
     */
    public function deleteImage($id){

        return Base::run("DELETE FROM " . PREFIX . "images WHERE id = ?", [$id]);
    }


    /**
     * @name ???????????????? ????????????
     * =====================
     * @param $id
     * @return bool|PDOStatement
     * @throws Exception
     */
    public function delete($id){

        Base::run("DELETE FROM " . PREFIX . "images WHERE itype = 1 AND pid = ?", [$id]);
        Base::run("DELETE FROM " . PREFIX . "product_prop WHERE pid = ?", [$id]);
        Base::run("DELETE FROM " . PREFIX . "products_cat WHERE pid = ?", [$id]);

        $Orders = Base::run("SELECT oid FROM " . PREFIX . "orders_ex WHERE pid = ?", [$id])->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($Orders)){

            Base::run("DELETE FROM " . PREFIX . "orders_ex WHERE pid = ?", [$id]);
            foreach ($Orders as $order) {
                Base::run("DELETE FROM " . PREFIX . "orders WHERE id = ?", [$order["oid"]]);
            }
        }

        return Base::run("DELETE FROM " . PREFIX . "products WHERE id = ?", [$id]);
    }

}