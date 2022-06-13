<?php

namespace app\traits;

use app\models\panel\FieldsModel;
use Exception;

trait Fields {


    /**
     * @name получение всех полей из конфига
     * =====================================
     * @return mixed|null
     */
    public static function getFields(){

        return file_exists(CORE . '/data/fields.json') ? json_decode(file_get_contents(CORE . '/data/fields.json'), true) : null;
    }


    /**
     * @name получение одного поля из конфига
     * ======================================
     * @param string $tag
     * @return mixed|null
     */
    public static function getField(string $tag){

        $fields = self::getFields();
        return !empty($fields[$tag]) ? $fields[$tag] : null;
    }


    /**
     * @name удаление одного поля из конфига
     * =====================================
     * @param string $tag
     * @return void
     */
    public static function deleteField(string $tag){

        $fields = self::getFields();
        if(!empty($fields[$tag])) unset($fields[$tag]);

        $fp = fopen(CORE . '/data/fields.json', "w");
        flock($fp, LOCK_EX);
        fwrite($fp, json_encode($fields, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        flock($fp, LOCK_UN);
        fclose($fp);
    }


    /**
     * @name редактирование поля
     * =========================
     * @param string $tag
     * @param string $optionName
     * @param $optionValue
     * @return void|null
     */
    public static function editField(string $tag, string $optionName, $optionValue){

        $fields = self::getFields();
        if(empty($fields[$tag])) return null;

        $fields[$tag][$optionName] = $optionValue;

        $fp = fopen(CORE . '/data/fields.json', "w");
        flock($fp, LOCK_EX);
        fwrite($fp, json_encode($fields, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        flock($fp, LOCK_UN);
        fclose($fp);
    }


    /**
     * @name добавление поля
     * =====================
     * @param array $fieldArray
     * @return void
     */
    public static function addField(array $fieldArray){

        $fields = file_exists(CORE . '/data/fields.json') ? json_decode(file_get_contents(CORE . '/data/fields.json'), true) : [];

        $fields = array_merge($fields, $fieldArray);

        $fp = fopen(CORE . '/data/fields.json', "w");
        flock($fp, LOCK_EX);
        fwrite($fp, json_encode($fields, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        flock($fp, LOCK_UN);
        fclose($fp);
    }



    public static function getPostFields($postId, $postFields, $categories = []){

        $FieldsModel = new FieldsModel();
        $fieldsBase = $FieldsModel->getFieldsByPostId($postId);

        $Fields = self::getFields();

        $fieldsData = [];

        if(!empty($postFields)){

            function strCaseCmp($v1, $v2){
                if ($v1 === $v2) return 0;
                return 1;
            }

            foreach ($Fields as $field) {

                if($field["status"] && (empty($field["category"]) || array_uintersect($categories, $field["category"], "strCaseCmp"))){

                    switch ($field["type"]){

                        case 'input': case 'textarea':

                        if($field["rq"] && empty($postFields[$field["tag"]])) die("info::error::Заполните доп.поле: " . $field["name"]);

                        if(!empty($postFields[$field["tag"]]))
                            $fieldsData[$field["tag"]] = trim(strip_tags($postFields[$field["tag"]]));

                        break;

                        case 'code':

                            if($field["rq"] && empty($postFields[$field["tag"]])) die("info::error::Заполните доп.поле: " . $field["name"]);

                            if(!empty($postFields[$field["tag"]]))
                                $fieldsData[$field["tag"]] = trim(htmlspecialchars($postFields[$field["tag"]]));

                            break;

                        case 'select':

                            if($field["rq"] && empty($postFields[$field["tag"]])) die("info::error::Заполните доп.поле: " . $field["name"]);

                            if(!empty($postFields[$field["tag"]])){

                                $selectResult = !empty($field["multiple"]) ? $postFields[$field["tag"]] : $postFields[$field["tag"]];
                                $fieldsData[$field["tag"]] = $selectResult;
                            }

                            break;

                        case 'image': case 'file':

                        if($field["rq"] && empty($postFields[$field["tag"]]) && empty($_FILES["field"]["name"][$field["tag"]])) die("info::error::Заполните доп.поле: " . $field["name"]);

                        if(!empty($_FILES["field"]["name"][$field["tag"]])){

                            if(!empty($field["maxCount"]) && $field["maxCount"] == '1'){

                                if($field["type"] == 'image'){

                                    $fieldsData[$field["tag"]] = [
                                        'tag'      => $field["tag"],
                                        'name'     => $_FILES["field"]["name"][$field["tag"]],
                                        'type'     => $_FILES["field"]["type"][$field["tag"]],
                                        'tmp_name' => $_FILES["field"]["tmp_name"][$field["tag"]],
                                        'error'    => $_FILES["field"]["error"][$field["tag"]],
                                        'size'     => $_FILES["field"]["size"][$field["tag"]],
                                    ];

                                } else{

                                    $ext = mb_strtolower(pathinfo($_FILES["field"]["name"][$field["tag"]], PATHINFO_EXTENSION), 'UTF-8');

                                    $format = !empty($field["format"]) ? explode(",", $field["format"]) : [
                                        "zip",
                                        "rar",
                                        "docx",
                                        "excel",
                                        "txt"
                                    ];

                                    if(in_array($ext, $format)){

                                        $fieldsData[$field["tag"]] = [
                                            'tag'      => $field["tag"],
                                            'name'     => $_FILES["field"]["name"][$field["tag"]],
                                            'type'     => $_FILES["field"]["type"][$field["tag"]],
                                            'tmp_name' => $_FILES["field"]["tmp_name"][$field["tag"]],
                                            'error'    => $_FILES["field"]["error"][$field["tag"]],
                                            'size'     => $_FILES["field"]["size"][$field["tag"]],
                                        ];
                                    }
                                }

                                if(!empty($fieldsBase[$field["tag"]])) $fieldsData[$field["tag"]]["__REPLACE__"] = 1;
                                //else $fieldsData[$field["tag"]]["__ADD__"] = 1;

                            } else {

                                $fieldsData[$field["tag"]] = [
                                    'tag'      => $field["tag"],
                                    'name'     => [],
                                    'type'     => [],
                                    'tmp_name' => [],
                                    'error'    => [],
                                    'size'     => [],
                                ];

                                $countMax = !empty($field["maxCount"]) ? intval($field["maxCount"]) : null;

                                $i = 0;

                                if($field["type"] == 'image'){

                                    foreach ($_FILES["field"]["name"][$field["tag"]] as $key => $file) {

                                        if(!$countMax || $i < $countMax){
                                            array_push($fieldsData[$field["tag"]]["name"], $file);
                                            array_push($fieldsData[$field["tag"]]["type"], $_FILES["field"]["type"][$field["tag"]][$key]);
                                            array_push($fieldsData[$field["tag"]]["tmp_name"], $_FILES["field"]["tmp_name"][$field["tag"]][$key]);
                                            array_push($fieldsData[$field["tag"]]["error"], $_FILES["field"]["error"][$field["tag"]][$key]);
                                            array_push($fieldsData[$field["tag"]]["size"], $_FILES["field"]["size"][$field["tag"]][$key]);
                                        }

                                        $i++;
                                    }

                                } else{

                                    foreach ($_FILES["field"]["name"][$field["tag"]] as $key => $file) {

                                        $ext = mb_strtolower(pathinfo($file, PATHINFO_EXTENSION), 'UTF-8');

                                        $format = !empty($field["format"]) ? explode(",", $field["format"]) : [
                                            "zip",
                                            "rar",
                                            "docx",
                                            "excel",
                                            "txt"
                                        ];

                                        if(in_array($ext, $format)){

                                            if(!$countMax || $i < $countMax){
                                                array_push($fieldsData[$field["tag"]]["name"], $file);
                                                array_push($fieldsData[$field["tag"]]["type"], $_FILES["field"]["type"][$field["tag"]][$key]);
                                                array_push($fieldsData[$field["tag"]]["tmp_name"], $_FILES["field"]["tmp_name"][$field["tag"]][$key]);
                                                array_push($fieldsData[$field["tag"]]["error"], $_FILES["field"]["error"][$field["tag"]][$key]);
                                                array_push($fieldsData[$field["tag"]]["size"], $_FILES["field"]["size"][$field["tag"]][$key]);
                                            }
                                        }

                                        $i++;
                                    }
                                }

                                if(empty($fieldsData[$field["tag"]]["name"])) unset($fieldsData[$field["tag"]]);
                                else if(!empty($fieldsBase[$field["tag"]])) $fieldsData[$field["tag"]]["__ADD__"] = 1;
                            }

                        } else if(!empty($postFields[$field["tag"]])) $fieldsData[$field["tag"]] = "__ISSET__";

                        break;

                        case 'checkbox':

                            if($field["rq"] && empty($postFields[$field["tag"]])) die("info::error::Заполните доп.поле: " . $field["name"]);

                            if(!empty($postFields[$field["tag"]]))
                                $fieldsData[$field["tag"]] = 1;

                            break;

                        case 'date': case 'dateTime':

                        if($field["rq"] && empty($postFields[$field["tag"]])) die("info::error::Заполните доп.поле: " . $field["name"]);

                        if(!empty($postFields[$field["tag"]]))
                            $fieldsData[$field["tag"]] = strtotime($postFields[$field["tag"]]);

                        break;
                    }
                }
            }
        }

        return [
            "fields" => $Fields,
            "inBase" => $fieldsBase,
            "result" => $fieldsData
        ];
    }


    /**
     * @name замена доп полей в шаблоне
     * ================================
     * @param $template
     * @param $postIds
     * @param $plugin_id
     * @param $module_id
     * @return mixed
     * @throws Exception
     */
    public static function setTags($template, $postIds = null, $plugin_id = null, $module_id = null){

        preg_match_all('/\{field\:(.*?)\}/is', $template, $tags);

        // $tags[0] - {field:name}
        // $tags[1] - name

        if(!empty($tags[1])){

            $FieldsModel = new FieldsModel();
            $Fields = $FieldsModel->getFieldsByPostIds($tags[1], $postIds, $plugin_id, $module_id);

            foreach ($tags[1] as $key => $tag) {

                if(!empty($Fields[$postIds][$tag]["val"])){

                    $val = htmlspecialchars_decode($Fields[$postIds][$tag]["val"]);

                    $template = str_replace($tags[0][$key], $val, $template);

                } else $template = str_replace($tags[0][$key], "", $template);
            }

        }

        return $template;
    }

}