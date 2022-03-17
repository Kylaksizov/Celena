<?php

namespace app\core;

use PDO;

class System{


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
        if($bytes<1000*1024) return number_format($bytes/1024,2)."KB";
        elseif($bytes<1000*1048576) return number_format($bytes/1048576,2)."MB";
        elseif($bytes<1000*1073741824) return number_format($bytes/1073741824,2)."GB";
        else return number_format($bytes/1099511627776,2)."TB";
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
                (is_dir("$dir/$file")) ? self::remove_dir("$dir/$file") : unlink("$dir/$file");
            }
            if($removeThisDir) return rmdir($dir);
        }
    }




    public static function pagination($sql, $params, $start = 0, $limit = 25){

        $page_number = 0;
        $category = $pagination = "";

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


        // если записей больше чем лимит, то выводим пагинацию
        if($count > $limit){

            $pagination = '<ul class="navigation">';

            if(!empty($category)) $category = '/'.$category;
            $pagination .= '<li class="pagination_edge"><a href="'.$category.'/"></a></li>';

            // предыдущая страница
            if(!empty($page_number)) {
                if($page_number <= 2) $prev = $category.'/';
                else $prev = $category.'/page-'.($page_number-1).'/';
                $pagination .= '<li><a href="'.$prev.'">&laquo;</a></li>';
            }

            $j = 0;
            for ($i = 1; $i <= $start_from; $i++) {

                if($j <= $page_number+4 && $j >= $page_number-6){

                    $active = '';
                    if($i == $page_number) $active = ' class="active"';
                    if($i != 1) $pagination .= '<li><a href="'.$category.'/page-'.$i.'/"'.$active.'>'.$i.'</a></li>';
                }

                $j++;
            }

            // следующая страница
            if(empty($page_number)) {
                $pagination .= '<li><a href="'.$category.'/page-2/">&raquo;</a></li>';
            } else{
                if($page_number != $start_from) $pagination .= '<li><a href="'.$category.'/page-'.($page_number+1).'/">&raquo;</a></li>';
            }

            $pagination .= '<li class="pagination_edge"><a href="'.$category.'/page-'.$start_from.'/">'.$start_from.'</a></li>';
            // <li class="count_db">записей: <b>'.$count.'</b></li>
            $pagination .= '</ul>';

        } else{

            $pagination = "";
        }
        $result["count"] = $count;
        $result["pagination"] = $pagination;

        unset($page_number, $page_number_, $pagination);

        return $result;
    }



    public function editConfig($file, $newSettings){

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

}