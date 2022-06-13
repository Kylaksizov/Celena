<?php

namespace app\models\panel;

use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;

class FieldsModel extends Model{



    public function add($fields, $fieldsData, $pid = null, $plugin_id = null, $module_id = null){

        $inFields = self::getFieldsByPostId($pid);

        /*echo "<pre>";
        print_r($fields);
        print_r($fieldsData);
        echo "</pre>";
        exit;*/

        foreach ($fields as $tag => $val) {

            // если ничего не изменилось, не сохраняем
            if((!empty($inFields[$tag]["val"]) && $inFields[$tag]["val"] == $val) || isset($fieldsData[$tag]["__ISSET__"])) continue;

            if(isset($fieldsData[$tag]["__ADD__"])) $val = $inFields[$tag]["val"] . '|' . $val;

            if(isset($fieldsData[$tag]["__REPLACE__"])){

                self::remove($inFields[$tag]["id"]);

                $imgPath = strstr($inFields[$tag]["val"], ":", true);

                if(file_exists(ROOT . "/uploads/fields/" . $imgPath))
                    unlink(ROOT . "/uploads/fields/" . $imgPath);

                if(strripos($imgPath, "/thumbs/") !== false)
                    unlink(ROOT . "/uploads/fields/" . str_replace("/thumbs", "", $imgPath));
            }

            $params = [
                $pid,
                $plugin_id,
                $module_id,
                $tag,
                $val
            ];

            Base::run("INSERT INTO " . PREFIX . "fields (
                    pid,
                    plugin_id,
                    module_id,
                    tag,
                    val
                ) VALUES (
                    ?, ?, ?, ?, ?
                )", $params);
        }
    }



    public function getFieldById($id){

        return Base::run("SELECT tag, val FROM " . PREFIX . "fields WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
    }



    public function getFieldsByPostId($pid){

        return System::setKeys(Base::run("SELECT id, tag, val FROM " . PREFIX . "fields WHERE pid = ?", [$pid])->fetchAll(PDO::FETCH_ASSOC), "tag");
    }



    public function editFieldVal($fieldId, $val){

        return Base::run("UPDATE " . PREFIX . "fields SET val = ? WHERE id = ?", [$val, $fieldId])->rowCount();
    }



    public function clear($pid = null, $plugin_id = null, $module_id = null){

        $rowName = 'pid';
        $id  = $pid;
        if($plugin_id){
            $rowName = 'plugin_id';
            $id  = $plugin_id;
        }
        if($module_id){
            $rowName = 'module_id';
            $id  = $module_id;
        }
        return Base::run("DELETE FROM " . PREFIX . "fields WHERE $rowName = ?", [$id]);
    }



    public function remove($field_id){

        return Base::run("DELETE FROM " . PREFIX . "fields WHERE id = ?", [$field_id]);
    }

}