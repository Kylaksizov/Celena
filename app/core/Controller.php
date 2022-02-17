<?php
/**
 * @name Главный контроллер, который вызывается при старте любого контроллера
 */

namespace app\core;

abstract class Controller{

    public $urls;
    public $route;
    public $view;
    public $ajax = false;



    /**
     * @param $route
     * @param $ajax
     */
    public function __construct($route, $ajax){

        $this->urls = $route["urls"];
        $this->ajax = $ajax;
        $this->route = $route;
        $this->view = new View($route);

        $this->view->load(CONFIG_SYSTEM["template"]); // если в дочернем контроллере будет вызов load, то выходит что эта обработка лишняя. Но и городить кучу проверок ради удаления этой загрузки не стоит
    }
}