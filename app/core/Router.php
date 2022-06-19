<?php

namespace app\core;

use app\core\system\plugins\InstallLocalPlugins;
use app\models\panel\PluginModel;
use app\models\panel\SystemModel;
use app\traits\Users;

class Router{

    protected $routes = [];
    protected $params = [];

    public $is_panel = false;
    public $url = '/';
    public $urls = [];
    public $author = '';
    public $plugin = false;
    public $ajax = '';

    use Users;



    /**
     * @name подключаем роуты
     */
    public function __construct() {

        // Выход
        if(!empty($_GET["logout"])){
            SetCookie("uid", "", time() - (3600 * 10000), "/");
            SetCookie("uhash", "", time() - (3600 * 10000), "/");
            header("Location: /");
            die();
        }

        // install
        if(!file_exists(CORE . '/data/db_config.php')){
            header("Location: /install.php");
            exit;
        }

        $db_conf = require CORE . '/data/db_config.php';
        define('CONFIG_SYSTEM', require CORE . '/data/config.php');
        define("PREFIX", $db_conf["PREFIX"]);

        $this->url = !empty($_GET["url"]) ? trim(htmlspecialchars(strip_tags($_GET["url"]))) : '';
        $this->urls = explode("/", trim($this->url, "/"));

        $this->getAuthUser(); // получаем инфу о пользователе

        // вывод ошибок
        if(CONFIG_SYSTEM["errors"] || in_array($_SERVER["REMOTE_ADDR"], CONFIG_SYSTEM["dev"]) !== false){
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else{
            error_reporting(0);
            ini_set('display_errors', 0);
        }

        if(USER && USER["role"] == '1') define('ADMIN', true);
        else define('ADMIN', false);

        $routes = require APP . '/cache/routes.php';

        $routes["panel"]['$'] = ['controller' => 'index'];
        $routes["web"]['$'] = ['controller' => 'index'];

        $route_type = 'web';
        $panelKey = '';

        if($this->urls[0] == CONFIG_SYSTEM["panel"]){

            if(!USER && $this->urls[1] != 'auth'){
                header("Location: /".CONFIG_SYSTEM["panel"]."/auth/");
                die();
            }

            $this->is_panel = true;
            $route_type = 'panel';
            $panelKey = $this->urls[0].'/';
        }

        #TODO сделать проверку, если кеш не существует, создать его !!!!!!!!!!!!!!!!!!

        foreach ($routes[$route_type] as $route => $params) {
            $this->add($panelKey.$route, $params);
        }
    }




    public function add($route, $params){
        $route = '#^'.$route.'#';
        $this->routes[$route] = $params;
    }




    /**
     * @name поиск маршрутов в наших роутерах
     * @return bool
     */
    public function match(){
        foreach ($this->routes as $route => $params) {
            if(preg_match($route, $this->url, $matches)){
                $this->params = $params;
                return $params;
            }
        }
        return false;
    }


    /**
     * @name запускаем контроллеры и методы
     * @return void
     */
    public function run(){
        

        
        // install plugins, modules, ...
        if(!empty($this->urls[2]) && $this->urls[2] == 'install'){
            new InstallLocalPlugins($this);
            die();
        }
        

        
        $match = $this->match();

        if($match){
            
            $explodeMatch = explode("\\", $match["controller"]);

            $panel = ($this->is_panel) ? 'panel\\' : '';

            // если это плагин
            if($explodeMatch[0] == 'plugins' && !empty($explodeMatch[1])){
                unset($explodeMatch[0]);
                $this->plugin = [
                    'brand' => $explodeMatch[1],
                    'name' => $explodeMatch[2]
                ];
                $this->params['controller'] = 'plugins\\'.$explodeMatch[1].'\\'.$explodeMatch[2].'\\'.$panel.$explodeMatch[3];
            }


            // если это плагин
            if($this->plugin){

                define('CONFIG_PLUGIN', require APP . '/plugins/'.$this->plugin["brand"].'/'.$this->plugin["name"].'/config.php');

                // проверяем установлен ли плагин и активен ли он
                $PluginModel = new PluginModel();
                $pluginActive = $PluginModel->getPluginByBrandName($this->plugin["brand"], $this->plugin["name"]);

                // если плагин зарегистрирован в базе
                if($pluginActive){

                    // если есть необязательный конфиг, подключаем
                    $config_plugin = file_exists(APP . '/plugins/'.$this->plugin["brand"].'/'.$this->plugin["name"].'/config.php') ? require APP . '/plugins/'.$this->plugin["brand"].'/'.$this->plugin["name"].'/config.php' : [];

                    // обязательный системный конфиг
                    $system_plugin = file_exists(APP . '/plugins/'.$this->plugin["brand"].'/'.$this->plugin["name"].'/system.json') ? json_decode(file_get_contents(APP . '/plugins/'.$this->plugin["brand"].'/'.$this->plugin["name"].'/system.json'), true) : die("В плагине <b>{$this->plugin["brand"]}/{$this->plugin["name"]}</b> отсутствует конфигурационный файл, который должен быть обязательно!");

                    $this->plugin = json_decode(
                        json_encode([
                            "system" => array_merge($system_plugin, $pluginActive),
                            "config" => $config_plugin
                        ], JSON_UNESCAPED_UNICODE)
                    );

                    define("PLUGIN_NAME", $this->plugin->system->name);
                    define("PLUGIN", $pluginActive);

                    // если плагин активен
                    if($this->plugin->system->status != '1' && !ADMIN){

                        header("Location: ".$this->panel_is()."/404/");
                        View::errorCode(404);

                    }
                }
            }
            

            // AJAX
            if(self::isAjax()){

                $ajax_file = trim(htmlspecialchars(strip_tags($_POST["ajax"])));

                // преобразуем строку вида a=1&b=2 в нормальный вид пост запроса
                if(!empty($_POST["params"])) mb_parse_str($_POST["params"], $_POST);

                if($this->plugin) $path = 'app\plugins\\'.str_replace('/', '\\', $this->plugin->system->name).'\ajax\\'.$panel.$ajax_file;
                else $path = 'app\controllers\ajax\\'.$panel.$ajax_file;

                if(class_exists($path)){

                    if(method_exists($path, 'index')){

                        $controller = new $path();
                        $this->ajax = $controller->index();

                    } else{

                        die("error ajax controller");
                    }
                }
            }

            if($this->plugin) $path = 'app\\'.$this->params['controller'].'Controller';
            else $path = 'app\controllers\\'.$panel.ucfirst($this->params['controller']).'Controller';

            // если контроллер найден
            if(class_exists($path)){

                $action = !empty($this->params['action']) ? $this->params['action'].'Action' : 'indexAction';

                if(method_exists($path, $action)){

                    $this->params["urls"] = $this->urls;
                    $this->params["plugin"] = $this->plugin;

                    $controller = new $path($this->params, $this->ajax);
                    $controller->$action();

                } else{

                    if(ADMIN) die("Метод не найден!");
                    View::errorCode(404);
                }

            } else{

                if(ADMIN) die("Контроллер <b>". $panel . $this->params['controller'] ."</b> не найден!");
                header("Location: ".$this->panel_is()."/404/");
                View::errorCode(404);
            }
            
        } else{

            if(ADMIN) die('Router Not Found!');
            header("Location: ".$this->panel_is()."/404/");
            View::errorCode(404);
        }
    }



    protected function panel_is(){
        return ($this->urls[0] == CONFIG_SYSTEM["panel"]) ? '/' . CONFIG_SYSTEM["panel"] : '';
    }



    private static function isAjax(){
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' || !empty($_POST["ajax"]);
    }



    /**
     * @name получаем инфу о пользователе
     * ==================================
     * @return void
     */
    protected function getAuthUser(){

        // проверка на пользователя
        if(!empty($_COOKIE["uid"]) && !empty($_COOKIE["uhash"])){

            $User = $this->getAuth($_COOKIE["uid"], $_COOKIE["uhash"]);
            define("USER", $User ?: FALSE);
            // если false - значит кто-то пытается подменить данные !!!!!!!!!!!!!!!!!!!!!
            if(USER === FALSE){
                //Log::add();
                SetCookie("uid", "", time() - (3600 * 10000), "/");
                SetCookie("uhash", "", time() - (3600 * 10000), "/");
                header("Location: /");
                die();
            }

        } else define("USER", NULL);
    }

}