<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\models\panel\PluginModel;


class PluginsController extends PanelController {


    public function indexAction(){

        $this->view->styles = ['css/myPlugins.css'];
        $this->view->scripts = ['js/myPlugins.js'];
        $this->view->plugins = ['rating', 'fancybox'];

        $PluginsModel = new PluginModel();
        $Plugins = $PluginsModel->getPlugins();

        $content = '<div class="fx">
            <h1>Мои плагины</h1>
        </div>
        <div class="my_plugins">';

        if(!empty($Plugins["plugins"])){

            foreach ($Plugins["plugins"] as $row) {

                $buttonStatus = ($row["status"] == '1') ? '<a href="#" class="btn btn_plugin_deactivate" data-a="CelenaPlugin:action=disable&id='.$row["id"].'">Выключить</a>' : '<a href="#" class="btn btn_plugin_activate" data-a="CelenaPlugin:action=enable&id='.$row["id"].'">Активировать</a>';

                $content .= '<div class="plugin_table">
                    <a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/plugin/'.$row["id"].'/">
                        <img src="//'.CONFIG_SYSTEM["home"].'/templates/plugins/'.$row["name"].'/panel/'.$this->pluginsSystems->{$row["name"]}->icon.'" alt="">
                    </a>
                    <div class="plugin_box">
                        <h2><a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/plugin/'.$row["id"].'/">'.$this->pluginsSystems->{$row["name"]}->name.'</a> <span class="plugin_version">v '.$row["version"].'</span></h2>
                        <p class="plugin_description">'.$this->pluginsSystems->{$row["name"]}->description.'</p>
                        <div class="plugin_actions">
                            '.$buttonStatus.'
                            <a href="#" class="btn btn_plugin_remove fr" data-a="CelenaPlugin:action=remove&id='.$row["plugin_id"].'">Удалить</a>
                        </div>
                    </div>
                </div>';
            }
        }
        $content .= '</div>';

        $this->view->render('Мои плагины', $content);
    }

}