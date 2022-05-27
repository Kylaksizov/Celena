<?php


namespace app\controllers\panel;

use app\core\PanelController;


class PluginSettingsController extends PanelController {


    public function indexAction(){

        $this->view->styles = ['css/addon/myPlugins.css'];
        $this->view->scripts = ['js/addon/myPlugins.js'];
        $this->view->plugins = ['rating', 'fancybox'];

        $content = '<h1>Настройки плагина</h1>';

        $this->view->render('Настройки плагина', $content);
    }

}