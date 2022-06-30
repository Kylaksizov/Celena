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
    public function getFields(){

        return file_exists(CORE . '/data/fields.json') ? json_decode(file_get_contents(CORE . '/data/fields.json'), true) : null;
    }


    /**
     * @name получение одного поля из конфига
     * ======================================
     * @param string $tag
     * @return mixed|null
     */
    public function getField(string $tag){

        $fields = self::getFields();
        return !empty($fields[$tag]) ? $fields[$tag] : null;
    }


    /**
     * @name удаление одного поля из конфига
     * =====================================
     * @param string $tag
     * @return void
     */
    public function deleteField(string $tag){

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
    public function editField(string $tag, string $optionName, $optionValue){

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
    public function addField(array $fieldArray){

        $fields = file_exists(CORE . '/data/fields.json') ? json_decode(file_get_contents(CORE . '/data/fields.json'), true) : [];

        $fields = array_merge($fields, $fieldArray);

        $fp = fopen(CORE . '/data/fields.json', "w");
        flock($fp, LOCK_EX);
        fwrite($fp, json_encode($fields, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        flock($fp, LOCK_UN);
        fclose($fp);
    }


    private function strCaseCmp($v1, $v2){
        if ($v1 === $v2) return 0;
        return 1;
    }


    public function getPostFields($postId, $postFields, $categories = []){

        $FieldsModel = new FieldsModel();
        $fieldsBase = $FieldsModel->getFieldsByPostId($postId);

        $Fields = self::getFields();

        $fieldsData = [];

        if(!empty($postFields)){

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
    public function setTags($template, $postIds = null, $plugin_id = null, $module_id = null){

        preg_match_all('/\{field\:(.*?)(\:name)?\}/is', $template, $tags);


        // $tags[0] - {field:name}
        // $tags[1] - name

        if(!empty($tags[1])){

            $FieldsSource = self::getFields();

            if(empty($FieldsSource)) return preg_replace('/\{field\:(.*?)(\:name)?\}/is', "", $template);

            $FieldsModel = new FieldsModel();
            $Fields = $FieldsModel->getFieldsByPostIds($tags[1], $postIds, $plugin_id, $module_id);

            foreach ($tags[1] as $key => $tag) {

                if(empty($FieldsSource[$tag])){
                    $template = str_replace($tags[0][$key], "", $template);
                    continue;
                }

                if($tags[2][$key] == ':name'){
                    $replace = $FieldsSource[$tag]["name"];
                    $template = str_replace($tags[0][$key], $replace, $template);
                    continue;
                }

                if(!empty($Fields[$postIds][$tag]["val"]) && $FieldsSource[$tag]["status"]){

                    $replace = '';

                    switch ($FieldsSource[$tag]["type"]){

                        case 'input': case 'textarea': case 'checkbox':

                        $replace = $Fields[$postIds][$tag]["val"];

                        break;

                        case 'select':

                            if($FieldsSource[$tag]["multiple"]){

                                $ms = explode("|", $Fields[$postIds][$tag]["val"]);

                                foreach ($ms as $m) {
                                    $replace .= '<span class="opt_item">'.$m.'</span>';
                                }

                            } else $replace = $Fields[$postIds][$tag]["val"];

                            break;

                        case 'code':

                            $replace = htmlspecialchars_decode($Fields[$postIds][$tag]["val"]);

                            break;

                        case 'image':

                            $images = explode("|", $Fields[$postIds][$tag]["val"]);

                            foreach ($images as $image) {

                                $imgInfo = explode(":", $image);

                                $alt = !empty($imgInfo[3]) ? $imgInfo[3] : '';

                                if(isset($FieldsSource[$tag]["gallery"]))
                                    $replace .= '<a href="//'.CONFIG_SYSTEM["home"].'/uploads/fields/'.str_replace("/thumbs", "", $imgInfo[0]).'" data-fancybox="1"><img src="//'.CONFIG_SYSTEM["home"].'/uploads/fields/'.$imgInfo[0].'" alt="'.$alt.'"></a>';
                                else
                                    $replace .= '<img src="//'.CONFIG_SYSTEM["home"].'/uploads/fields/'.$imgInfo[0].'" alt="'.$alt.'">';
                            }

                            break;

                        case 'file':

                            $tplSource = file_exists(ROOT . '/templates/' . CONFIG_SYSTEM["template"] . '/download.tpl') ? file_get_contents(ROOT . '/templates/' . CONFIG_SYSTEM["template"] . '/download.tpl') : '<a href="{link}" class="download_link">{name} <span class="download_count">(загрузок: {counter})</span></a>';

                            if(strripos("|", $replace) !== false){

                                $files = explode("|", $Fields[$postIds][$tag]["val"]);

                                foreach ($files as $file) {

                                    $fileInfo = explode(":", $file);

                                    $tplResult = str_replace('{link}', '//'.CONFIG_SYSTEM["home"].'/download/'.$Fields[$postIds][$tag]["id"].'/'.$fileInfo[2], $tplSource);
                                    $tplResult = str_replace('{name}', $fileInfo[2], $tplResult);
                                    $replace .= str_replace('{counter}', !empty($fileInfo[3]) ? intval($fileInfo[3]) : 0, $tplResult);
                                }

                            } else{

                                $fileInfo = explode(":", $Fields[$postIds][$tag]["val"]);

                                $tplResult = str_replace('{link}', '//'.CONFIG_SYSTEM["home"].'/download/'.$Fields[$postIds][$tag]["id"].'/'.$fileInfo[2], $tplSource);
                                $tplResult = str_replace('{name}', $fileInfo[2], $tplResult);
                                $replace = str_replace('{counter}', !empty($fileInfo[3]) ? intval($fileInfo[3]) : 0, $tplResult);
                            }

                            break;

                        case 'date':

                            $replace = date("d.m.Y", $Fields[$postIds][$tag]["val"]);

                            break;

                        case 'dateTime':

                            $replace = date("d.m.Y H:i", $Fields[$postIds][$tag]["val"]);

                            break;
                    }

                    $template = str_replace($tags[0][$key], $replace, $template);

                } else $template = str_replace($tags[0][$key], "", $template);
            }

        }

        return $template;
    }

}