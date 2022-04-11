<?php

namespace app\core;

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

        $config = require CORE . '/data/config.php'; // подключаем главный конфиг
        $db_conf = require CORE . '/data/db_config.php';
        define('CONFIG_SYSTEM', $config);
        define("PREFIX", $db_conf["PREFIX"]);

        $this->url = !empty($_GET["url"]) ? trim(htmlspecialchars(strip_tags($_GET["url"]))) : '';
        $this->urls = explode("/", trim($this->url, "/"));

        // вывод ошибок
        if(CONFIG_SYSTEM["errors"]){
            // если пусто или совпадает по IP
            if(empty(CONFIG_SYSTEM["dev"]) || (in_array($_SERVER["REMOTE_ADDR"], CONFIG_SYSTEM["dev"]) !== false)){
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
                define('ADMIN', true);
            }
        }

        $this->getAuthUser(); // получаем инфу о пользователе

        $routes = require APP . '/cache/routes.php';

        $routes["panel"]['$'] = ['controller' => 'index'];
        $routes["web"]['$'] = ['controller' => 'index'];

        $route_type = 'web';
        $panelKey = '';

        if($this->urls[0] == CONFIG_SYSTEM["panel"] && USER && USER["role"] == '1'){

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

            new InstallApplications($this);
            die();
        }
        

        
        $match = $this->match();

        if($match){
            
            $explodeMatch = explode("\\", $match["controller"]);

            $panel = ($this->is_panel) ? 'panel\\' : '';
            
            // если это плагин
            if($explodeMatch[0] == 'plugins'){
                unset($explodeMatch[0]);
                $this->plugin = [
                    'brand' => $explodeMatch[1],
                    'name' => $explodeMatch[2]
                ];
                $panel = '';
            }

            // AJAX
            if(self::isAjax() && !empty($_POST["ajax"])){

                $ajax_file = trim(htmlspecialchars(strip_tags($_POST["ajax"])));

                // преобразуем строку вида a=1&b=2 в нормальный вид пост запроса
                if(!empty($_POST["params"])) mb_parse_str($_POST["params"], $_POST);

                $path = 'app\controllers\ajax\\'.$panel.$ajax_file;

                if(class_exists($path)){

                    if(method_exists($path, 'index')){

                        $controller = new $path();
                        $this->ajax = $controller->index();

                    } else{

                        die("error ajax controller");
                    }
                }
            }

            $path = 'app\controllers\\'.$panel.ucfirst($this->params['controller']).'Controller';

            // если контроллер найден
            if(class_exists($path)){

                $action = !empty($this->params['action']) ? $this->params['action'].'Action' : 'indexAction';

                if(method_exists($path, $action)){

                    // если это плагин
                    if($this->plugin)
                        define("CONFIG_PLUGIN", require APP . '/controllers/plugins/'.$this->plugin["brand"].'/'.$this->plugin["name"].'/config.php');
                    else
                        define("CONFIG_PLUGIN", false);

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
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
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
            if(USER === FALSE) System::log();

        } else define("USER", NULL);
    }

}