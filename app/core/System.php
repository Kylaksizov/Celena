<?php

namespace app\core;

use PDO;

class System{


    /**
     * @name для input type check
     * ==========================
     * @param $val
     * @return string
     */
    public static function check($val){
        return $val ? ' checked' : '';
    }


    /**
     * @name очистка и сжатие скриптов (для AJAX)
     * ==========================================
     * @param $str
     * @return void
     */
    public static function script($str){
        die("eval::".str_replace(['<script>', '</script>', PHP_EOL, "\n", "\t", "\r", "\r\n", "  "], "", $str));
    }



    /**
     * @name установка ключей массива
     * ==============================
     * @param $rows
     * @param $field_name
     * @return array
     */
    public static function setKeys($rows, $field_name = "id"){
        $result = [];
        foreach ($rows as $row) $result[$row[$field_name]] = $row;
        return $result;
    }


    /**
     * @name установка ключей с массивами
     * ==================================
     * @param $rows
     * @param string $field_name
     * @return array
     */
    public static function setKeysArray($rows, string $field_name = "id"){

        $result = [];
        foreach ($rows as $row){
            if(!empty($result[$row[$field_name]])) array_push($result[$row[$field_name]], $row);
            else $result[$row[$field_name]][] = $row;
        }
        return $result;
    }



    /**
     * @name транслитерация
     * ====================
     * @param $str
     * @param $lang
     * @param $mb_strtolower
     * @return array|string|string[]
     */
    public static function translit($str, $lang = "en", $mb_strtolower = true){

        $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', ' ', '.', '/', ',', '(', ')', '[', ']', '=', '+', '*', '?', "\"", "'", '&', '%', '#', '@', '!', ';', '№', '^', ':', '~', '\\', '<', '>');

        $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya', '-', '', '_', '_', '', '', '', '', '_', '_', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

        if($mb_strtolower) $str = mb_strtolower($str, 'UTF-8');

        if($lang == "ru") $str = str_replace($lat, $rus, $str);
        else $str = str_replace($rus, $lat, $str);

        return str_replace(['--', '---', '__', '___'], '-', $str);
    }



    /**
     * @name размер файла
     * ==================
     * @param $file
     * @param $letter
     * @return string
     */
    public static function getFileSize($file, $letter = 1){

        if(!file_exists($file)) return '';

        if(is_numeric($file)) $filesize = $file;
        else $filesize = filesize($file);

        if($filesize > 1024){
            $filesize = ($filesize/1024);
            if($filesize > 1024){
                $filesize = ($filesize/1024);
                if($filesize > 1024) {
                    $filesize = ($filesize/1024);
                    $filesize = round($filesize, 2);
                    return $filesize." Гб";
                } else {
                    $filesize = round($filesize, 2);
                    return $filesize.(($letter) ? " Mб" : '');
                }
            } else {
                $filesize = round($filesize, 2);
                return $filesize.(($letter) ? " Кб" : '');
            }
        } else {
            $filesize = round($filesize, 2);
            return $filesize.(($letter) ? " байт" : '');
        }
    }



    /**
     * @name конвертация байтов в нормальный размер
     * ============================================
     * @param $bytes
     * @return string
     */
    public static function getNormSize($bytes){
        if($bytes<1000*1024) return number_format($bytes/1024,2)." KB";
        elseif($bytes<1000*1048576) return number_format($bytes/1048576,2)." MB";
        elseif($bytes<1000*1073741824) return number_format($bytes/1073741824,2)." GB";
        else return number_format($bytes/1099511627776,2)." TB";
    }



    /**
     * @name получение размера директории
     * ==================================
     * @param $path
     * @return false|int
     */
    public static function getDirSize($path)
    {
        $fileSize = 0;
        if(file_exists($path)){
            foreach(scandir($path) as $file) {
                if (($file!='.') && ($file!='..'))
                    if(is_dir($path . '/' . $file))
                        $fileSize += self::getDirSize($path.'/'.$file);
                    else
                        $fileSize += filesize($path . '/' . $file);
            }
        }
        return $fileSize;
    }



    /**
     * @name удаление всей директории
     * ==============================
     * @param $dir
     * @param bool $removeThisDir
     * @return bool|void
     */
    public static function removeDir($dir, bool $removeThisDir = true){
        if(file_exists($dir)){
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file){
                (is_dir("$dir/$file")) ? self::removeDir("$dir/$file") : unlink("$dir/$file");
            }
            if($removeThisDir) return rmdir($dir);
        }
    }




    public static function pagination($sql, $params, $start = 0, $limit = 25){

        $page_number = 0;
        $category = "";

        if(!empty($_GET["url"])){
            preg_match('/\/?page\-([0-9]+)\/$/i', $_GET["url"], $page);
            if(!empty($page[1]) && ctype_digit($page[1])) {
                $page_number = (int)$page[1];
                $category = str_replace($page[0], "", $_GET["url"]);
                $category = trim(htmlspecialchars(strip_tags($category)));
            } else $category = substr(trim(htmlspecialchars(strip_tags($_GET["url"]))), 0, -1);
        }

        if(!empty($page_number)) $page_number_ = $page_number - 1;
        else $page_number_ = 0;

        $result = [
            "start" => 0,
            "limit" => $limit
        ];

        $count = Base::run($sql, $params)->fetch(PDO::FETCH_COLUMN);
        $start_from = @ceil($count / $limit);
        $result["start"] = abs($page_number_ * $limit);
        
        $path = (strripos($_SERVER["REQUEST_URI"], "?") !== false) ? strstr($_SERVER["REQUEST_URI"], "?") : '';

        // если записей больше чем лимит, то выводим пагинацию
        if($count > $limit){

            $pagination = '<ul class="navigation">';

            if(!empty($category)) $category = '/'.$category;
            $pagination .= '<li class="pagination_edge"><a href="'.$category.'/'.$path.'"></a></li>';

            // предыдущая страница
            if(!empty($page_number)) {
                if($page_number <= 2) $prev = $category.'/';
                else $prev = $category.'/page-'.($page_number-1).'/';
                $pagination .= '<li><a href="'.$prev.$path.'">&laquo;</a></li>';
            }

            $j = 0;
            for ($i = 1; $i <= $start_from; $i++) {

                if($j <= $page_number+4 && $j >= $page_number-6){

                    $active = '';
                    if($i == $page_number) $active = ' class="active"';
                    if($i != 1) $pagination .= '<li><a href="'.$category.'/page-'.$i.'/'.$path.'"'.$active.'>'.$i.'</a></li>';
                }

                $j++;
            }

            // следующая страница
            if(empty($page_number)) {
                $pagination .= '<li><a href="'.$category.'/page-2/'.$path.'">&raquo;</a></li>';
            } else{
                if($page_number != $start_from) $pagination .= '<li><a href="'.$category.'/page-'.($page_number+1).'/'.$path.'">&raquo;</a></li>';
            }

            //$pagination .= '<li class="pagination_edge"><a href="'.$category.'/page-'.$start_from.'/">'.$start_from.'</a></li>';
            // <li class="count_db">записей: <b>'.$count.'</b></li>
            $pagination .= '</ul>';

        } else $pagination = "";

        $result["count"] = $count;
        $result["pagination"] = $pagination;

        unset($page_number, $page_number_, $pagination);

        return $result;
    }



    public static function editSystemConfig($newSettings, $file = CORE . '/data/config.php'){

        $new_settings = '';

        $handle = @fopen($file, "r");
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {

                $divided = explode('=>', $buffer);

                preg_match('#\"(.+?)\"#is', $divided[0], $sets_name);

                if($sets_name && isset($newSettings[$sets_name[1]]) && array_key_exists($sets_name[1], $newSettings)){ // если такая настройка найдена

                    $newSettings[$sets_name[1]] = str_replace(array("\r\n", "\n", "\r", '"'), array('\n', '\n', '\n', '\"'), $newSettings[$sets_name[1]]);

                    if(is_numeric($newSettings[$sets_name[1]])){ // если чистое число

                        $new_settings .= "\t\"".$sets_name[1]."\" => " . $newSettings[$sets_name[1]] . ',' . PHP_EOL;

                    } else if(is_bool($newSettings[$sets_name[1]]) === true){

                        if($newSettings[$sets_name[1]] === true)
                            $new_settings .= "\t\"".$sets_name[1]."\" => true," . PHP_EOL;
                        if($newSettings[$sets_name[1]] === false)
                            $new_settings .= "\t\"".$sets_name[1]."\" => false," . PHP_EOL;

                    } else if(is_array($newSettings[$sets_name[1]])){

                        $new_department = "";
                        foreach ($newSettings[$sets_name[1]] as $value) {
                            $new_department .= "\"".trim($value)."\", ";
                        }

                        $new_department = substr($new_department, 0, -2);
                        $new_settings .= "\t\"".$sets_name[1]."\" => [$new_department]," . PHP_EOL;

                    } else{

                        $new_settings .= "\t" . '"'.$sets_name[1].'" => "' . $newSettings[$sets_name[1]] . '",' . PHP_EOL;
                    }

                } else{

                    $new_settings .= $buffer;
                }
            }

            fclose($handle);

            $fp = fopen($file, "w");
            flock($fp, LOCK_EX);
            fwrite($fp, $new_settings);
            flock($fp, LOCK_UN);
            fclose($fp);

            return true;

        } else{

            if(ADMIN) die($file . ' - не найден!');
            return false;
        }
    }



    public static function editPluginConfig($newSettings){

        return self::editSystemConfig($newSettings, APP . '/plugins/'.PLUGIN_NAME.'/config.php');
    }


    /**
     * @param array $routes
     * @param bool|int $position
     * @return bool
     * @example
     * System::addRoute([
     * 'panel' => [
     * 'path/$' => ['controller' => 'plugins\Celena\Example', 'action' => 'act'],
     * 'path2/$' => ['controller' => 'plugins\Celena\Example2']
     * ],
     * 'web' => [
     * 'path3/$' => ['controller' => 'plugins\Celena\Example'],
     * 'path4/$' => ['controller' => 'plugins\Celena\Example2', 'action' => 'act']
     * ]
     * ]);
     */
    public static function addRoute(array $routes, bool|int $position = true){

        $resultRoutes = "";
        $file = ROOT . '/app/cache/routes.php';
        
        $realRoutes = require $file;

        $addPanelRouteBefore = "";
        $addPanelRouteAfter = "";
        $addWebRouteBefore = "";
        $addWebRouteAfter = "";

        foreach ($routes as $type => $newRoutes) {

            $type = ($type == 'panel') ? 'panel' : 'web';

            // DeclareNames::ROUTES

            foreach ($newRoutes as $path => $newRouteAction) {

                if(!empty($realRoutes[$type][$path])) continue;

                if($type == 'panel'){
                    $action = (!empty($newRouteAction["action"]) && $newRouteAction["action"] != 'index') ? ", 'action' => '{$newRouteAction["action"]}'" : "";
                    if(!$position) $addPanelRouteBefore .= "'$path' => ['controller' => '{$newRouteAction["controller"]}'$action],\n\t\t";
                    else $addPanelRouteAfter .= "\n\t\t'$path' => ['controller' => '{$newRouteAction["controller"]}'$action],";
                }
                if($type == 'web'){
                    $action = (!empty($newRouteAction["action"]) && $newRouteAction["action"] != 'index') ? ", 'action' => '{$newRouteAction["action"]}'" : "";
                    if(!$position) $addWebRouteBefore .= "'$path' => ['controller' => '{$newRouteAction["controller"]}'$action],\n\t\t";
                    else $addWebRouteAfter .= "\n\t\t'$path' => ['controller' => '{$newRouteAction["controller"]}'$action],";
                }
            }
        }

        $resultRoutes = "<?php

return [

    'panel' => [
        ".$addPanelRouteBefore.self::rebuildRoutes($realRoutes["panel"])."$addPanelRouteAfter
        
    ],
    
    'web' => [
        ".$addWebRouteBefore.self::rebuildRoutes($realRoutes["web"])."$addWebRouteAfter
    ],
    
];";

        $fp = fopen($file, "w");
        flock($fp, LOCK_EX);
        fwrite($fp, $resultRoutes);
        flock($fp, LOCK_UN);
        fclose($fp);

        return true;
    }


    /**
     * @name для self::addRoute()
     * ==========================
     * @param $array
     * @return string
     */
    private static function rebuildRoutes($array){

        $result = '';
        foreach ($array as $path => $controllerAction) {
            $action = !empty($controllerAction["action"]) ? ", 'action' => '{$controllerAction["action"]}'" : "";
            $result .= "'$path' => ['controller' => '{$controllerAction["controller"]}'$action],\n\t\t";
        }
        
        return trim($result);
    }


    /**
     * @name удаление роутов
     * =====================
     * @param array $routes
     * @return bool
     * @example
        System::removeRoute([
            'panel' => [
                'path/$',
                'path2/$'
            ],
            'web' => [
                'path3/$',
                'path4/$'
            ]
        ]);
     */
    public static function removeRoute(array $routes){

        $resultRoutes = "";
        $file = ROOT . '/app/cache/routes.php';

        $realRoutes = require $file;

        $addPanelRoute = "";
        $addWebRoute = "";

        foreach ($routes as $type => $newRoutes) {

            $type = ($type == 'panel') ? 'panel' : 'web';

            // DeclareNames::ROUTES

            foreach ($newRoutes as $newRoute) {

                if(!isset($realRoutes[$type][$newRoute])) continue;
                unset($realRoutes[$type][$newRoute]);
            }
        }

        $resultRoutes = "<?php

return [

    'panel' => [
        ".self::rebuildRoutes($realRoutes["panel"])."$addPanelRoute
        
    ],
    
    'web' => [
        ".self::rebuildRoutes($realRoutes["web"])."$addWebRoute
    ],
    
];";

        $fp = fopen($file, "w");
        flock($fp, LOCK_EX);
        fwrite($fp, $resultRoutes);
        flock($fp, LOCK_UN);
        fclose($fp);

        return true;
    }



    public static function getFields(){

        return file_exists(CORE . '/data/fields.json') ? json_decode(file_get_contents(CORE . '/data/fields.json'), true) : null;
    }



    public static function getField(string $tag){

        $fields = self::getFields();
        return !empty($fields[$tag]) ? $fields[$tag] : null;
    }



    public static function deleteField(string $tag){

        $fields = self::getFields();
        if(!empty($fields[$tag])) unset($fields[$tag]);

        $fp = fopen(CORE . '/data/fields.json', "w");
        flock($fp, LOCK_EX);
        fwrite($fp, json_encode($fields, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        flock($fp, LOCK_UN);
        fclose($fp);
    }



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



    public static function addField(array $fieldArray){

        $fields = file_exists(CORE . '/data/fields.json') ? json_decode(file_get_contents(CORE . '/data/fields.json'), true) : [];

        $fields = array_merge($fields, $fieldArray);

        $fp = fopen(CORE . '/data/fields.json', "w");
        flock($fp, LOCK_EX);
        fwrite($fp, json_encode($fields, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        flock($fp, LOCK_UN);
        fclose($fp);
    }



    public static function getPostFields($postFields, $categories = []){

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

                        case 'select':

                            if($field["rq"] && empty($postFields[$field["tag"]])) die("info::error::Заполните доп.поле: " . $field["name"]);

                            if(!empty($postFields[$field["tag"]])){

                                $selectResult = !empty($field["multiple"]) ? $postFields[$field["tag"]] : $postFields[$field["tag"]];
                                $fieldsData[$field["tag"]] = $selectResult;
                            }

                            break;

                        case 'image': case 'file':

                        if($field["rq"] && empty($_FILES["field"]["name"][$field["tag"]])) die("info::error::Заполните доп.поле: " . $field["name"]);

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

                            }
                        }

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
            "result" => $fieldsData
        ];
    }

}