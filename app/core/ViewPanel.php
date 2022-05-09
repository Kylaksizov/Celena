<?php

namespace app\core;

class ViewPanel{

    public  $route;
    public  $template;
    public  $tplIndex;
    private $includeSource = [];
    public  $include = [];
    private $lastInc; // имя файла последнего подгруженного
    public  $styles = [];
    public  $scripts = [];
    public  $plugins = [];


    public function __construct($route){

        $this->route = $route;
    }


    // подгружаем главный файл шаблона index.tpl
    public function load($template = CONFIG_SYSTEM["template"]){

        $this->template = $template;
        if(file_exists(ROOT.'/templates/'.$this->template.'/index.tpl')){

            $this->tplIndex = file_get_contents(ROOT.'/templates/'.$this->template.'/index.tpl');

        } else {

            if(ADMIN) die('Шаблон <b>' . $this->template . '</b> не найден!');
            View::errorCode(404);
        }
    }




    public function include($view){

        $viewPrev = (CONFIG_PLUGIN !== false) ? 'plugins/'.CONFIG_PLUGIN["plugin"]["brand"].'/'.CONFIG_PLUGIN["plugin"]["name"].'/' : '';

        if($view != $this->lastInc){

            if(file_exists(ROOT.'/templates/'.$this->template.'/'.$viewPrev.$view.'.tpl')){

                $this->lastInc = $view;
                $this->includeSource[$view] = file_get_contents(ROOT.'/templates/'.$this->template.'/'.$viewPrev.$view.'.tpl');
                $this->include[$view] = $this->includeSource[$view];
                return $this->include[$view];

            }

        } else{

            $this->include[$view] = $this->includeSource[$view];
        }
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
            if($file) return $this->include[$file];
            else return $this->include[$this->lastInc];
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
     * @name Обработка и вывод
     * =======================
     * @return void
     */
    public function render($title = 'Admin', $content = ''){

        $style = (!empty($_COOKIE["style"]) && $_COOKIE["style"] == 'dark') ? 'dark.css' : 'white.css';

        $styles = '<link rel="stylesheet" href="//'.CONFIG_SYSTEM['home'].'/templates/_system/css/panel.css">';

        // https://air-datepicker.com/ru/examples

        $scripts = '
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/_system/js/jquery.min.js"></script>
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/_system/js/prevent.js"></script>
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/_system/js/panel.js"></script>';

        if(!empty($this->plugins)){

            if(in_array("jquery-ui", $this->plugins)){
                $scripts .= '<script src="//'.CONFIG_SYSTEM['home'].'/templates/_system/js/jquery-ui.min.js"></script>';
            }
            if(in_array("select2", $this->plugins)){
                $styles .= '<link rel="stylesheet" href="//'.CONFIG_SYSTEM['home'].'/templates/_system/css/select2.min.css">';
                $scripts .= '<script src="//'.CONFIG_SYSTEM['home'].'/templates/_system/js/select2.full.min.js"></script>';
            }
            if(in_array("datepicker", $this->plugins)){
                $styles .= '<link rel="stylesheet" href="//'.CONFIG_SYSTEM['home'].'/templates/_system/css/air-datepicker.css">';
                $scripts .= '<script src="//'.CONFIG_SYSTEM['home'].'/templates/_system/js/air-datepicker.js"></script>';
            }
            if(in_array("fancybox", $this->plugins)){
                $styles .= '<link rel="stylesheet" href="//'.CONFIG_SYSTEM['home'].'/templates/_system/css/fancybox.css">';
                $scripts .= '<script src="//'.CONFIG_SYSTEM['home'].'/templates/_system/js/fancybox.umd.js"></script>';
            }
        }

        $scripts .= '
    <script src="{THEME}/js/script.js"></script>';

        $styles .= '<link rel="stylesheet" href="//'.CONFIG_SYSTEM['home'].'/templates/_system/css/air-datepicker.css">
    <link rel="stylesheet" href="//'.CONFIG_SYSTEM['home'].'/templates/Panel/css/'.$style.'">';

        #TODO тут нужно подумать что сначала что с конца, учитывая что стили и скрипты могут подключаться как в tpl так и в контроллере !!!!!!!!
        if(!empty($this->styles)){
            foreach ($this->styles as $style) {
                $styles .= '
    <link rel="stylesheet" href="{PLUGIN_THEME}/'.$style.'">';
            }
        }

        if(!empty($this->scripts)){
            foreach ($this->scripts as $script) {
                $scripts .= '
    <script src="{PLUGIN_THEME}/'.$script.'"></script>';
            }
        }


        // если были подключаемые файлы
        if(!empty($this->include)){
            $content = implode('', $this->include);
        }


        //$this->mainReplace(); // ищем все возможные замены


        $styles .= '
    <link rel="stylesheet" href="//'.CONFIG_SYSTEM['home'].'/templates/_system/css/admin.css">';
        $scripts .= '
    <script src="//'.CONFIG_SYSTEM['home'].'/templates/_system/js/admin.js"></script>';

        if(CONFIG_SYSTEM["dev_tools"]) $scripts .= $this->dev();

        $systems = '<div class="bg_0"></div>
<div id="main_answer_server"></div>

<div id="loading">
    <div id="loading-text">Подождите...</div>
    <div id="loading-content"></div>
</div>

<ul class="contextmenu"><!--TMP-->
    <li><a href="#">Редактировать</a></li>
    <li><a href="#">Удалить</a></li>
</ul>';

        $pluginTheme = (CONFIG_PLUGIN !== false) ? 'plugins/'.CONFIG_PLUGIN["plugin"]["brand"].'/'.CONFIG_PLUGIN["plugin"]["name"] : '';

        $this->tplIndex = str_replace('{META}', '<title>'.$title.'</title>', $this->tplIndex);
        $this->tplIndex = str_replace('{CONTENT}', $content, $this->tplIndex);
        $this->tplIndex = str_replace('{STYLES}', $styles, $this->tplIndex);
        $this->tplIndex = str_replace('{SCRIPTS}', $scripts, $this->tplIndex);
        $this->tplIndex = str_replace('{SYSTEMS}', $systems, $this->tplIndex);
        $this->tplIndex = str_replace('{panel}', '/'.CONFIG_SYSTEM["panel"], $this->tplIndex);
        $this->tplIndex = str_replace('{THEME}', '//'.CONFIG_SYSTEM['home'].'/templates/'.$this->template, $this->tplIndex);
        $this->tplIndex = str_replace('{HOME}', '//'.CONFIG_SYSTEM['home'].'/', $this->tplIndex);
        $this->tplIndex = str_replace('{PLUGIN_THEME}', '//'.CONFIG_SYSTEM['home'].'/templates/'.$this->template.$pluginTheme, $this->tplIndex);

        $this->tplIndex = preg_replace('/\{\*(.+?)\*\}/is', "", $this->tplIndex);


        echo $this->tplIndex;
    }





    public function dev(){

        $devContent = '<ul>';

        if(!empty(CONFIG_PLUGIN["plugin"]["id"])){

            $devContent .= '<li>Плагин: <b>'.CONFIG_PLUGIN["plugin"]["id"].' ('.CONFIG_PLUGIN["plugin"]["name"].')</b></li>';
        }

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