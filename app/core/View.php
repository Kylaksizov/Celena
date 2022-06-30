<?php

namespace app\core;

class View{

    public  $route;
    public  $template;
    public  $tplIndex;
    private $includeSource = [];
    public  $include = [];
    private $includeCache; // TMP
    private $lastInc; // имя файла последнего подгруженного
    public  $styles = [];
    public  $scripts = [];
    public  $plugins = [];


    public function __construct($route){

        $this->route = $route;

        if(!CONFIG_SYSTEM["power"]){
            $this->tplIndex = file_get_contents(ROOT.'/templates/'.CONFIG_SYSTEM["template"].'/power.tpl');
            $this->tplIndex = str_replace('{power_text}', CONFIG_SYSTEM["power_text"], $this->tplIndex);
            self::setMeta("Сайт выключен");
            self::render();
            die();
        }
    }


    // подгружаем главный файл шаблона index.tpl
    public function load($template = true, $repeat = false){

        $this->template = ($template) ? CONFIG_SYSTEM["template"] : $template;

        if(!$repeat){

            if(file_exists(ROOT.'/templates/'.$this->template.'/index.tpl')){

                $this->tplIndex = file_get_contents(ROOT.'/templates/'.$this->template.'/index.tpl');

            } else {

                if(ADMIN) die('Шаблон <b>' . $this->template . '</b> не найден!');
                View::errorCode(404);
            }
        }
    }


    /**
     * @name подключаем другие файлы
     * =============================
     * @param $view
     * @param bool $cache
     * @return false|string|void
     */
    public function include($view, $cache = false){

        $viewPlugin = (!empty($this->route["plugin"]->system->status) && $this->route["plugin"]->system->status !== false) ? 'plugins/'.$this->route["plugin"]->system->name.'/' : '';

        if($view != $this->lastInc || $cache){

            $incFile = $viewPlugin ? ROOT.'/templates/'.$viewPlugin.'web/'.$view.'.tpl' : ROOT.'/templates/'.$this->template.'/'.$viewPlugin.$view.'.tpl';

            if(file_exists($incFile)){

                if($cache) $this->includeCache = file_get_contents($incFile);
                else{
                    $this->lastInc = $view;
                    $this->includeSource[$view] = file_get_contents($incFile);
                    $this->include[$view] = $this->includeSource[$view];
                    return $this->include[$view];
                }

            } else{

                $this->lastInc = $view;
                $this->includeSource[$view] = '<b class="cel_l_error">[[ шаблон: '.$view.' не найден! ]]</b>';
                $this->include[$view] = $this->includeSource[$view];
                return $this->include[$view];
            }

        } else{

            $this->include[$view] = $this->includeSource[$view];
        }
    }


    /**
     * @name поиск тегов в шаблоне
     * ===========================
     * @param $tagName
     * @param bool $viewType
     * @return int
     */
    public function findTag($tagName, bool $viewType = false){

        if($viewType) return substr_count($this->tplIndex, $tagName);
        else return !empty($this->include[$this->lastInc]) ? substr_count($this->include[$this->lastInc], $tagName) : '';
    }



    /**
     * @name поиск тегов в подключаемом файле
     * ======================================
     * @param array $searchAssocArray
     * @return string
     * @example :
     * --------------------------------------
     * $this->findTags([
     *      'title, url',
     *      '{tagName}' => 'fieldName',
     *      '{tagName2}' => 'fieldName2'
     * ]);
     */
    public function findTags(array $searchAssocArray = []){

        $result = [];

        if(!empty($searchAssocArray[0])){
            array_push($result, $searchAssocArray[0]);
            unset($searchAssocArray[0]);
        }

        if(!empty($this->include[$this->lastInc])){

            foreach ($searchAssocArray as $tag => $strField){

                if(strripos($this->include[$this->lastInc], $tag) !== false){
                    if(array_search($strField, $result) === false) $result[$tag] = $strField;
                }
            }
        }

        return $result;
    }




    /**
     * @name заменяем в include
     * ========================
     * @param $search
     * @param $replace
     * @return void
     */
    public function set($search, $replace){
        if(isset($this->include[$this->lastInc]))
            $this->include[$this->lastInc] = str_replace($search, $replace, $this->include[$this->lastInc]);
        if(isset($this->include[$this->includeCache]))
            $this->includeCache = str_replace($search, $replace, $this->includeCache);
    }




    /**
     * @name заменяем в include
     * ========================
     * @param $search
     * @param $replace
     * @return void
     */
    public function setPreg($search, $replace){
        if(isset($this->include[$this->lastInc]))
            $this->include[$this->lastInc] = preg_replace($search, $replace, $this->include[$this->lastInc]);
        if(isset($this->includeCache))
            $this->includeCache = preg_replace($search, $replace, $this->includeCache);
    }




    /**
     * @name заменяем в index
     * ======================
     * @param $search
     * @param $replace
     * @return void
     */
    public function setMain($search, $replace){
        $this->tplIndex = str_replace($search, $replace, $this->tplIndex);
    }






    public function get($file = null){
        if(isset($this->include[$this->lastInc])){
            if(empty($this->include[$this->lastInc])) return '<b class="cel_l_error">[[ шаблон: '.$this->lastInc.' не найден! ]]</b>';
            if($file) return $this->include[$file];
            else return $this->include[$this->lastInc];
        }
        if(isset($this->includeCache)){
            $includeCache = $this->includeCache;
            $this->includeCache = '';
            return $includeCache;
        }
    }






    public function push(){
        if(isset($this->include[$this->lastInc]))
            $this->include[$this->lastInc] .= $this->includeSource[$this->lastInc];
    }






    public function clearPush(){
        if(isset($this->include[$this->lastInc]))
            $this->include[$this->lastInc] = str_replace($this->includeSource[$this->lastInc], '', $this->include[$this->lastInc]);
    }






    public function clear($file = null){
        if(isset($this->include[$this->lastInc])){
            if($file) unset($this->include[$file]);
            else unset($this->include[$this->lastInc]);
        }
    }




    /**
     * @name Установка мета тегов
     * ==========================
     * @param $title
     * @param $description
     * @param array $array
     * @return void
     */
    public function setMeta($title, $description = '', array $array = []){

        $meta = '<meta charset="UTF-8">
    <title>'.$title.'</title>
    <meta name="description" content="'.$description.'">
    <meta name="generator" content="Celena">';

        if(!empty($array)){
            foreach ($array as $arr) {
                $meta .= '
    <meta';
                foreach ($arr as $key => $val) {
                    $meta .= ' '.$key.'="'.$val.'"';
                }
                $meta .= '>';
            }

        }

        $this->tplIndex = str_replace('{META}', $meta, $this->tplIndex);
    }







    private function mainReplace(){

        $controller = str_replace("\\", "/", $this->route["controller"]);

        if(strripos($this->tplIndex, '{include') !== false){

            preg_match_all('/\{include\s+file=\"([a-z0-9-_\/\.]+)\"\}/is', $this->tplIndex, $includes);
            if(!empty($includes[1])){

                // перебираем все шорт-коды {include file="..."}
                // $includes[0] - шорт-код
                // $includes[1] - путь к файлу от корня шаблона
                foreach ($includes[1] as $key => $file) {
                    if(!empty($file) && file_exists(ROOT.'/templates/'.$this->template.'/'.$file)) {
                        $include_file = file_get_contents(ROOT.'/templates/'.$this->template.'/'.$file);
                        $this->tplIndex = str_replace($includes[0][$key], $include_file, $this->tplIndex);
                    } else $this->tplIndex = str_replace($includes[0][$key], '', $this->tplIndex);
                }
                unset($includes);

                // снова проверяем, есть ли в подключаемых файлах теги {include ...}
                preg_match_all('/\{include\s+file=\"([a-z0-9-_\/\.]+)\"\}/is', $this->tplIndex, $includes);

                // если есть, запускаем рекурсивно этот метод
                if(!empty($includes[0])) $this->mainReplace();
            }
        }

        if(strripos($this->tplIndex, '[show') !== false){

            // отображение контента в зависимости от типа страницы
            preg_match_all('/(\[show\s?=\s?\"(.+?)\"\])(.+?)(\[\/show\])/is', $this->tplIndex, $show);

            // $show[0][0] - содержит все целиком
            // $show[1][0] - содержит [show = "..."]
            // $show[2][0] - содержит типа страницы (home, category, page, ...)
            // $show[3][0] - содержит то что внутри, то есть од, без тегов
            // $show[4][0] - содержит [/show]

            if(!empty($show[1])){

                foreach ($show[2] as $key => $type) {

                    $types = explode(",", $type);

                    $pattern = str_replace(["\"", "[", "]", " ", "/"], ["\\\"", "\[", "\]", "\s+", "\/"], $show[1][$key]);

                    // если тип страницы совпадает с указанным значением в теге, то показываем код внутри тегов
                    if(in_array($controller, $types))
                        $this->tplIndex = preg_replace('/'.$pattern.'([^[]+)\[\/show\]/i', "$1", $this->tplIndex);
                    else
                        $this->tplIndex = preg_replace('/'.$pattern.'([^[]+)\[\/show\]/i', "", $this->tplIndex);

                }
            }
        }

        if(strripos($this->tplIndex, '[not-show') !== false){

            // скрытие контента в зависимости от типа страницы
            preg_match_all('/(\[not-show\s?=\s?\"(.+?)\"\])(.+?)(\[\/not-show\])/is', $this->tplIndex, $show);

            // $show[0][0] - содержит все целиком
            // $show[1][0] - содержит [not-show = "..."]
            // $show[2][0] - содержит типа страницы (home, category, page, ...)
            // $show[3][0] - содержит то что внутри, то есть од, без тегов
            // $show[4][0] - содержит [/not-show]

            if(!empty($show[1])){

                foreach ($show[2] as $key => $type) {

                    $types = explode(",", $type);

                    $pattern = str_replace(["\"", "[", "]", " ", "/"], ["\\\"", "\[", "\]", "\s+", "\/"], $show[1][$key]);

                    // если тип страницы совпадает с указанным значением в теге, то показываем код внутри тегов
                    if(in_array($controller, $types))
                        $this->tplIndex = preg_replace('/'.$pattern.'([^[]+)\[\/not-show\]/i', "", $this->tplIndex);
                    else
                        $this->tplIndex = preg_replace('/'.$pattern.'([^[]+)\[\/not-show\]/i', "$1", $this->tplIndex);

                }
            }
        }


        if(strripos($this->tplIndex, '[role') !== false){

            // отображение контента в зависимости от типа страницы
            preg_match_all('/(\[role\s?=\s?\"(.+?)\"\])(.+?)(\[\/role\])/is', $this->tplIndex, $rolesA);

            // $rolesA[0][0] - содержит все целиком
            // $rolesA[1][0] - содержит [role = "..."]
            // $rolesA[2][0] - содержит ID группы (0 - гость, или ID группы)
            // $rolesA[3][0] - содержит то что внутри, то есть од, без тегов
            // $rolesA[4][0] - содержит [/role]

            if(!empty($rolesA[1])){

                foreach ($rolesA[2] as $key => $type) {

                    $roles = explode(",", $type);

                    $pattern = str_replace(["\"", "[", "]", " ", "/"], ["\\\"", "\[", "\]", "\s+", "\/"], $rolesA[1][$key]);

                    // если тип страницы совпадает с указанным значением в теге, то показываем код внутри тегов
                    if(USER && in_array(USER["role"], $roles) || !USER && in_array('0', $roles))
                        $this->tplIndex = preg_replace('/'.$pattern.'([^[]+)\[\/role\]/i', "$1", $this->tplIndex);
                    else
                        $this->tplIndex = preg_replace('/'.$pattern.'([^[]+)\[\/role\]/i', "", $this->tplIndex);

                }
            }
        }

        if(strripos($this->tplIndex, '[not-role') !== false){

            // скрытие контента в зависимости от типа страницы
            preg_match_all('/(\[not-role\s?=\s?\"(.+?)\"\])(.+?)(\[\/not-role\])/is', $this->tplIndex, $rolesA);

            // $rolesA[0][0] - содержит все целиком
            // $rolesA[1][0] - содержит [not-show = "..."]
            // $rolesA[2][0] - содержит типа страницы (home, category, page, ...)
            // $rolesA[3][0] - содержит то что внутри, то есть од, без тегов
            // $rolesA[4][0] - содержит [/not-show]

            if(!empty($rolesA[1])){

                foreach ($rolesA[2] as $key => $type) {

                    $roles = explode(",", $type);

                    $pattern = str_replace(["\"", "[", "]", " ", "/"], ["\\\"", "\[", "\]", "\s+", "\/"], $rolesA[1][$key]);

                    // если тип страницы совпадает с указанным значением в теге, то показываем код внутри тегов
                    if(USER && in_array(USER["role"], $roles) || !USER && in_array('0', $roles))
                        $this->tplIndex = preg_replace('/'.$pattern.'([^[]+)\[\/not-role\]/i', "", $this->tplIndex);
                    else
                        $this->tplIndex = preg_replace('/'.$pattern.'([^[]+)\[\/not-role\]/i', "$1", $this->tplIndex);

                }
            }
        }


        if(strripos($this->tplIndex, '[category') !== false){

            // отображение контента в зависимости от типа страницы
            preg_match_all('/(\[category\s?=\s?\"([\d\,]+)\"\])(.+?)(\[\/category\])/is', $this->tplIndex, $categories);

            //if(defined("CATEGORY")){
                
            //} else $this->tplIndex = preg_replace('/'.$pattern.'([^[]+)\[\/category\]/i', "", $this->tplIndex);

            // $show[0][0] - содержит все целиком
            // $show[1][0] - содержит [category = "..."]
            // $show[2][0] - содержит ID категории
            // $show[3][0] - содержит то что внутри, то есть од, без тегов
            // $show[4][0] - содержит [/category]

            if(defined("CATEGORY") && !empty($categories[0][0])){

                foreach ($categories[2] as $key => $ids) {

                    $ids = explode(",", $ids);

                    $pattern = str_replace(["\"", "[", "]", " ", "/"], ["\\\"", "\[", "\]", "\s+", "\/"], $categories[1][$key]);

                    // если тип страницы совпадает с указанным значением в теге, то показываем код внутри тегов
                    if(in_array(CATEGORY["id"], $ids))
                        $this->tplIndex = preg_replace('/'.$pattern.'([^[]+)\[\/category\]/i', "$1", $this->tplIndex);
                    else
                        $this->tplIndex = preg_replace('/'.$pattern.'([^[]+)\[\/category\]/i', "", $this->tplIndex);

                }

            } else $this->tplIndex = preg_replace('/\[category\s?=\s?\"[\d\,]+\"\].+?\[\/category\]/i', "", $this->tplIndex);
        }


        if(strripos($this->tplIndex, '[page') !== false){

            // отображение контента в зависимости от типа страницы
            preg_match_all('/(\[page\s?=\s?\"(.+?)\"\])(.+?)(\[\/page\])/is', $this->tplIndex, $pages);

            if(defined("PAGE") && !empty($pages[0][0])){

                foreach ($pages[2] as $key => $titles) {

                    $titles = explode(",", $titles);

                    $pattern = str_replace(["\"", "[", "]", " ", "/"], ["\\\"", "\[", "\]", "\s+", "\/"], $pages[1][$key]);

                    // если тип страницы совпадает с указанным значением в теге, то показываем код внутри тегов
                    if(in_array(PAGE["title"], $titles))
                        $this->tplIndex = preg_replace('/'.$pattern.'([^[]+)\[\/page\]/i', "$1", $this->tplIndex);
                    else
                        $this->tplIndex = preg_replace('/'.$pattern.'([^[]+)\[\/page\]/i', "", $this->tplIndex);

                }

            } else $this->tplIndex = preg_replace('/\[page\s?=\s?\".+?\"\].+?\[\/page\]/i', "", $this->tplIndex);
        }

        $this->tplIndex = preg_replace('/\[show(.+?)show\]/is', "", $this->tplIndex);
        $this->tplIndex = preg_replace('/\[not-show(.+?)not-show\]/is', "", $this->tplIndex);
        $this->tplIndex = preg_replace('/\[role(.+?)role\]/is', "", $this->tplIndex);
        $this->tplIndex = preg_replace('/\[not-role(.+?)not-role\]/is', "", $this->tplIndex);

        //$this->tplIndex = str_replace('{currency}', '$', $this->tplIndex);

    }








    /**
     * @name Обработка и вывод
     * =======================
     * @return void
     */
    public function render($display = true){

        $styles = '<link rel="stylesheet" href="//'.CONFIG_SYSTEM['home'].'/templates/system/css/celena.css?v='.CONFIG_SYSTEM["version"].'">';

        $scripts = '
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/system/js/jquery.min.js"></script>
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/system/js/celena.js?v='.CONFIG_SYSTEM["version"].'"></script>';

        if(!empty($this->plugins)){

            if(in_array("jquery-ui", $this->plugins)){
                $scripts .= '
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/system/js/jquery-ui.min.js"></script>';
            }
            if(in_array("select2", $this->plugins)){
                $styles .= '
    <link rel="stylesheet" href="//'.CONFIG_SYSTEM['home'].'/templates/system/css/select2.min.css">';
                $scripts .= '
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/system/js/select2.full.min.js"></script>';
            }
            if(in_array("datepicker", $this->plugins)){
                $styles .= '
    <link rel="stylesheet" href="//'.CONFIG_SYSTEM['home'].'/templates/system/css/air-datepicker.css">';
                $scripts .= '
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/system/js/air-datepicker.js"></script>';
            }
            if(in_array("fancybox", $this->plugins)){
                $styles .= '
    <link rel="stylesheet" href="//'.CONFIG_SYSTEM['home'].'/templates/system/css/fancybox.css">';
                $scripts .= '
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/system/js/fancybox.umd.js"></script>';
            }
            if(in_array("rating", $this->plugins)){
                $styles .= '
    <link rel="stylesheet" href="//'.CONFIG_SYSTEM['home'].'/templates/system/css/jquery.rateyo.min.css">';
                $scripts .= '
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/system/js/jquery.rateyo.min.js"></script>';
            }
            if(in_array("codemirror", $this->plugins)){
                $styles .= '
    <link rel="stylesheet" href="//'.CONFIG_SYSTEM['home'].'/templates/system/css/codemirror.css">';
                $styles .= '
    <link rel="stylesheet" href="//'.CONFIG_SYSTEM['home'].'/templates/system/css/theme/celena.css?v='.CONFIG_SYSTEM['version'].'">';
                $scripts .= '
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/system/js/codemirror.js"></script>
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/system/js/mode/clike/clike.js"></script>
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/system/js/mode/xml/xml.js"></script>
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/system/js/mode/javascript/javascript.js"></script>
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/system/js/mode/css/css.js"></script>
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/system/js/mode/htmlmixed/htmlmixed.js"></script>
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/system/js/mode/sql/sql.js"></script>
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/system/js/mode/php/php.js"></script>';
            }
        }

        if(!empty($this->styles)){
            foreach ($this->styles as $style) {

                $style = !empty($this->route["plugin"]->system->status) ? '//'.CONFIG_SYSTEM['home'].'/templates/plugins/' . $this->route["plugin"]->system->name . '/web/' . $style : '{THEME}/' . $style;

                $styles .= '
    <link rel="stylesheet" href="'.$style.'">';
            }
        }
        if(!empty($this->scripts)){
            foreach ($this->scripts as $script) {

                $script = !empty($this->route["plugin"]->system->status) ? '//'.CONFIG_SYSTEM['home'].'/templates/plugins/' . $this->route["plugin"]->system->name . '/web/' . $script : '{THEME}/' . $script;

                $scripts .= '
    <script src="'.$script.'"></script>';
            }
        }


        $content = '';
        // если были подключаемые файлы
        if(!empty($this->include)){
            $content = implode('', $this->include);
        }



        $this->mainReplace(); // ищем все возможные замены


        if(ADMIN){

            $styles .= '
    <link rel="stylesheet" href="//'.CONFIG_SYSTEM['home'].'/templates/system/css/admin.css?v='.CONFIG_SYSTEM["version"].'">';

            $scripts .= '
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/system/js/admin.js?v='.CONFIG_SYSTEM["version"].'"></script>';

            if(CONFIG_SYSTEM["dev_tools"]) $scripts .= $this->dev();

        }

        $systems = '<div class="bg_0"></div>
<div id="main_answer_server"></div>

<div id="loading">
    <div id="loading-text">Подождите...</div>
    <div id="loading-content"></div>
</div>';


        if($display) $this->tplIndex = str_replace('{CONTENT}', $content, $this->tplIndex);
        $this->tplIndex = str_replace('{STYLES}', $styles, $this->tplIndex);
        $this->tplIndex = str_replace('{SCRIPTS}', $scripts, $this->tplIndex);
        $this->tplIndex = str_replace('{SYSTEMS}', $systems, $this->tplIndex);
        $this->tplIndex = str_replace('{THEME}', '//'.CONFIG_SYSTEM['home'].'/templates/'.$this->template, $this->tplIndex);

        #TODO тут проблема если ставить комментарий в include
        $this->tplIndex = str_replace('{version}', CONFIG_SYSTEM["version"], $this->tplIndex);
        $this->tplIndex = preg_replace('/\{\*(.+?)\*\}/is', "", $this->tplIndex);

        if($display) echo $this->tplIndex;
        else return $this->tplIndex;
    }




    public function display(){
        $this->tplIndex = preg_replace('{version}', CONFIG_SYSTEM["version"], $this->tplIndex);
        $this->tplIndex = preg_replace('/\{\*(.+?)\*\}/is', "", $this->tplIndex);
        echo $this->tplIndex;
    }





    public function dev(){

        $devContent = '<ul>';

        if(!empty($this->route["plugin"]->config->brand))
            $devContent .= '<li>Плагин: <b>'.$this->route["plugin"]->config->brand.' ('.$this->route["plugin"]->config->name.')</b></li>';

        $dbLogs = Base::log();

        global $mem_start;

        $devContent .= '<li>Контроллер: <b>'.$this->route["controller"].'</b></li>
            <li>DB соединение: <b>'.($dbLogs->connection?'установлено':'-').'</b></li>
            <li>DB запросов: <a href="#" class="dev_show_log">'.$dbLogs->countQuery.'</a><span class="db_hidden">'.implode('<br>', $dbLogs->queries).'</span></li>
            <li'.($dbLogs->countErrors?' class="error"':'').'>DB ошибок: <a href="#" class="dev_show_log log_e">'.$dbLogs->countErrors.'</a><span class="db_hidden">'.implode('<br>', $dbLogs->errors).'</span></li>
            <li>Время обработки: '.round(microtime(true) - $mem_start, 3).'</li>
        </ul>';

        return '<div id="nex_dev">
                <div class="nex_dev_info">
                    '.$devContent.'
                </div>
                <a href="#" class="nex_debug"></a>
            </div>';
    }



    public function redirect($url){
        header('Location: ' . $url);
        exit;
    }



    public static function errorCode($code){
        http_response_code($code);
        header("Location: /404/");
        exit;
    }

}