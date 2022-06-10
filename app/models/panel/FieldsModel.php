<?php

namespace app\models\panel;

use app\core\Base;
use app\core\Model;
use app\core\System;
use Exception;
use PDO;

class FieldsModel extends Model{



    public function add($tag, $val, $pid = null, $plugin_id = null, $module_id = null){

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