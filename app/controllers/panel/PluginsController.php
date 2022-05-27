<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\models\panel\PluginModel;


class PluginsController extends PanelController {


    public function indexAction(){

        $this->view->styles = ['css/addon/myPlugins.css'];
        $this->view->scripts = ['js/addon/myPlugins.js'];
        $this->view->plugins = ['rating', 'fancybox'];
        
        echo "<pre>";
        print_r($this->pluginsSystems->{'Celena/Example'});
        echo "</pre>";
        exit;

        $PluginsModel = new PluginModel();
        $Plugins = $PluginsModel->getPlugins();

        $content = '<div class="fx">
            <h1>Мои плагины</h1>
        </div>
        <div class="my_plugins">';

        if(!empty($Plugins["plugins"])){

            foreach ($Plugins["plugins"] as $row) {

                $content .= '<div class="plugin_table">
                    <a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/plugin/1/">
                        <img src="http://api.celena.io/uploads/plugins/2022-05/shop.png" alt="">
                    </a>
                    <div class="plugin_box">
                        <h2><a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/plugin/1/">Celena Shop</a> <span class="plugin_version">v 1.0.3</span></h2>
                        <p class="plugin_description">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam commodi culpa distinctio eum laborum minima necessitatibus nulla, repellat sunt suscipit.</p>
                        <div class="plugin_actions">
                            <a href="#" class="btn btn_plugin_activate">Активировать</a>
                            <a href="#" class="btn btn_plugin_deactivate">Выключить</a>
                            <a href="#" class="btn btn_plugin_remove fr">Удалить</a>
                        </div>
                    </div>
                </div>';
            }
        }
        $content .= '</div>';

        $this->view->render('Мои плагины', $content);
    }

}