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
     * @param null $rowName
     * @return array
     */
    public static function setKeysArray($rows, string $field_name = "id", $rowName = null){

        $result = [];
        foreach ($rows as $row){
            if(!$rowName){
                if(!empty($result[$row[$field_name]])) array_push($result[$row[$field_name]], $row);
                else if(empty($rowName)) $result[$row[$field_name]][] = $row;
            } else{
                if(!empty($result[$row[$field_name]][$row[$rowName]])) array_push($result[$row[$field_name]][$row[$rowName]], $row);
                else $result[$row[$field_name]][$row[$rowName]] = $row;
            }
        }
        return $result;
    }


    /**
     * @name транслитерация
     * ====================
     * @param $str
     * @return string
     */
    public static function translit($str){

        $converter = array(
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'є' => 'e',
            'ё' => 'e',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'і' => 'i',
            'ї' => 'i',
            'й' => 'y',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'sch',
            'ь' => '',
            'ы' => 'y',
            'ъ' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
        );

        $value = mb_strtolower($str);
        $value = strtr($value, $converter);
        $value = mb_ereg_replace('[^-0-9a-z]', '-', $value);
        $value = mb_ereg_replace('[-]+', '-', $value);
        return trim($value, '-');
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
        if($bytes<1000*1024) return number_format($bytes/1024,2)." Kб";
        elseif($bytes<1000*1048576) return number_format($bytes/1048576,2)." Mб";
        elseif($bytes<1000*1073741824) return number_format($bytes/1073741824,2)." Гб";
        else return number_format($bytes/1099511627776,2)." Tб";
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






    public static function editSystemConfig($newSettings, $delete = false, $file = CORE . '/data/config.php'){

        $new_settings = '';

        $handle = @fopen($file, "r");
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {

                $divided = explode('=>', $buffer);

                preg_match('#\"(.+?)\"#is', $divided[0], $sets_name);

                if($sets_name && isset($newSettings[$sets_name[1]]) && array_key_exists($sets_name[1], $newSettings)){ // если такая настройка найдена

                    if($delete) continue;

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


    /**
     * @name добавление настройки в конфиг
     * ===================================
     * @param $newSettings
     * @param $file
     * @return bool
     */
    public static function addSystemConfig($newSettings, $file = CORE . '/data/config.php'){

        $config = file_get_contents($file);

        $new_setting = '';

        foreach ($newSettings as $setKey => $setVal) {

            $setVal = str_replace(array("\r\n", "\n", "\r", '"'), array('\n', '\n', '\n', '\"'), $setVal);

            if(is_numeric($setVal)){ // если чистое число

                $new_setting .= "\t\"".$setKey."\" => " . $setVal . ',' . PHP_EOL;

            } else if(is_bool($setVal) === true){

                if($setVal === true)
                    $new_setting .= "\t\"".$setKey."\" => true," . PHP_EOL;
                if($setVal === false)
                    $new_setting .= "\t\"".$setKey."\" => false," . PHP_EOL;

            } else if(is_array($setVal)){

                $new_department = "";
                foreach ($setVal as $value) {
                    $new_department .= "\"".trim($value)."\", ";
                }

                $new_department = substr($new_department, 0, -2);
                $new_setting .= "\t\"".$setKey."\" => [$new_department]," . PHP_EOL;

            } else{

                $new_setting .= "\t" . '"'.$setKey.'" => "' . $setVal . '",' . PHP_EOL;
            }
        }

        $config = str_replace('];', $new_setting.'];' , $config);

        $fp = fopen($file, "w");
        flock($fp, LOCK_EX);
        fwrite($fp, $config);
        flock($fp, LOCK_UN);
        fclose($fp);

        return true;
    }



    public static function addPluginConfig($newSettings, $plugin_name = PLUGIN_NAME){

        return self::addSystemConfig($newSettings, APP . '/plugins/'.$plugin_name.'/config.php');
    }



    public static function editPluginConfig($newSettings, $plugin_name = PLUGIN_NAME){

        return self::editSystemConfig($newSettings, false, APP . '/plugins/'.$plugin_name.'/config.php');
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
     * $position 0 - в начало, 1 - в конец
     */
    public static function addRoute(array $routes, bool $position = true, $newRoutes = false){

        $resultRoutesSource = [];
        $resultRoutes = "";
        $file = ROOT . '/app/cache/routes.php';

        if(!$newRoutes){

            $realRoutes = require $file;

            if(!empty($routes["panel"])) $resultRoutesSource["panel"] = array_merge($realRoutes["panel"], $routes["panel"]);
            if(!empty($routes["web"])) $resultRoutesSource["web"] = array_merge($realRoutes["web"], $routes["web"]);

        } else $realRoutes = $newRoutes;

        $addPanelRouteBefore = "";
        $addPanelRouteAfter = "";
        $addWebRouteBefore = "";
        $addWebRouteAfter = "";

        foreach ($routes as $type => $newRoutes) {

            $type = ($type == 'panel') ? 'panel' : 'web';

            // DeclareNames::ROUTES

            foreach ($newRoutes as $path => $newRouteAction) {

                #TODO было
                //if(!empty($realRoutes[$type][$path])) continue;
                if(!empty($realRoutes[$type][$path])) unset($realRoutes[$type][$path]);

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

        return $resultRoutesSource;
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

        foreach ($routes as $type => $rows) {

            $type = ($type == 'panel') ? 'panel' : 'web';

            // DeclareNames::ROUTES

            foreach ($rows as $urlRoute => $newRoute) {

                if(!isset($realRoutes[$type][$urlRoute])) continue;
                unset($realRoutes[$type][$urlRoute]);
            }
        }

        // по умолчанию
        if(empty($realRoutes["web"]["(page-[0-9]+/)?$"])) $realRoutes["web"]["sitemap.xml$"] = ["controller" => "siteMap"];
        if(empty($realRoutes["web"]["(page-[0-9]+/)?$"])) $realRoutes["web"]["(page-[0-9]+/)?$"] = ["controller" => "index"];
        if(empty($realRoutes["web"]["([a-z-0-9]+).html$"])) $realRoutes["web"]["([a-z-0-9]+).html$"] = ["controller" => "page"];
        if(empty($realRoutes["web"]["([a-z-/0-9]+).html$"])) $realRoutes["web"]["([a-z-/0-9]+).html$"] = ["controller" => "post"];
        if(empty($realRoutes["web"]["search/(page-[0-9]+/)?$"])) $realRoutes["web"]["search/(page-[0-9]+/)?$"] = ["controller" => "search"];
        if(empty($realRoutes["web"]["(.+?)/$"])) $realRoutes["web"]["(.+?)/$"] = ["controller" => "category"];
        if(empty($realRoutes["web"]["404/$"])) $realRoutes["web"]["404/$"] = ["controller" => "NotFound"];

        $resultRoutes = "<?php

return [

    'panel' => [
        ".self::rebuildRoutes($realRoutes["panel"])."
        
    ],
    
    'web' => [
        ".self::rebuildRoutes($realRoutes["web"])."
    ],
    
];";

        $fp = fopen($file, "w");
        flock($fp, LOCK_EX);
        fwrite($fp, $resultRoutes);
        flock($fp, LOCK_UN);
        fclose($fp);

        return true;
    }





}