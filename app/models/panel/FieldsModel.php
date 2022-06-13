<?php

namespace app\models\panel;

use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;

class FieldsModel extends Model{



    public function add($fields, $fieldsBase, $fieldsData, $pid = null, $plugin_id = null, $module_id = null){

        foreach ($fieldsData as $tag => $val) {

            // если ничего не изменилось, не сохраняем
            if((!empty($fieldsBase[$tag]["val"]) && $fieldsBase[$tag]["val"] == $val) || $val == '__ISSET__'){
                unset($fieldsBase[$tag]);
                continue;
            }

            if(isset($val["__ADD__"])){
                if(!empty($fieldsBase[$tag]["val"])){
                    $val = $fieldsBase[$tag]["val"] . '|' . $fields[$tag];
                    self::editFieldVal($fieldsBase[$tag]["id"], $val);
                    unset($fieldsBase[$tag]);
                    continue;
                } else{
                    $val = $fields[$tag];
                    unset($fieldsBase[$tag]);
                }
            }

            if(isset($val[$tag]["__REPLACE__"])){

                unset($fieldsBase[$tag]);
                self::editFieldVal($fieldsBase[$tag]["id"], $fields[$tag]);
                continue;
            }

            if(is_array($val)) $val = $fields[$tag];

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

        if(!empty($fieldsBase)){ // очистка лишних записей
            foreach ($fieldsBase as $fb) {
                self::remove($fb["id"]);
            }
        }
    }



    public function getFieldById($id){

        return Base::run("SELECT tag, val FROM " . PREFIX . "fields WHERE id = ?", [$id])->fetch(PDO::FETCH_ASSOC);
    }



    public function getFieldsByPostId($postIds = null, $plugin_id = null, $module_id = null){

        $row = "pid";
        $params = [$postIds];
        if($plugin_id){
            $row = "plugin_id";
            $params = [$plugin_id];
        }
        if($module_id){
            $row = "module_id";
            $params = [$module_id];
        }

        return System::setKeys(Base::run("SELECT id, tag, val FROM " . PREFIX . "fields WHERE $row = ?", $params)->fetchAll(PDO::FETCH_ASSOC), "tag");
    }


    /**
     * @name олучение полей из базы
     * ============================
     * @param array $fields
     * @param $postIds
     * @param $plugin_id
     * @param $module_id
     * @return array
     * @throws Exception
     */
    public function getFieldsByPostIds(array $fields, $postIds = null, $plugin_id = null, $module_id = null){

        $where = "";
        $params = [];

        $row = "pid";
        if($plugin_id) $row = "plugin_id";
        if($module_id) $row = "module_id";

        if(is_numeric($postIds)){

            $where = "pid = ? AND ";
            $params = [$postIds];
            if($plugin_id) $where = "plugin_id = ? AND ";
            if($module_id) $where = "module_id = ? AND ";

        } else if(is_array($postIds)){

            $where = "(";
            foreach ($postIds as $id) {
                $where .= "pid = ? OR ";
                if($plugin_id) $where .= "plugin_id = ? OR ";
                if($module_id) $where .= "module_id = ? OR ";
            }
            $where = trim($where, " OR ") . ") AND";
            $params = $postIds;
        }

        $where .= "(";
        foreach ($fields as $field) {
            $where .= "tag = ? OR ";
            array_push($params, $field);
        }
        $where = trim($where, " OR ") . ")";

        return System::setKeysArray(
            Base::run("SELECT id, $row, tag, val FROM " . PREFIX . "fields WHERE $where", $params)->fetchAll(PDO::FETCH_ASSOC),
            $row,
            "tag"
        );
    }



    public function editFieldVal($fieldId, $val){

        return Base::run("UPDATE " . PREFIX . "fields SET val = ? WHERE id = ?", [$val, $fieldId])->rowCount();
    }



    public function clear($pid = null, $plugin_id = null, $module_id = null){

        $rowName = 'pid';
        $id = $pid;
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