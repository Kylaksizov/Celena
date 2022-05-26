<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\core\system\shop\ShopController;


class PluginsController extends PanelController {


    public function indexAction(){

        $this->view->styles = ['css/addon/myPlugins.css'];
        $this->view->scripts = ['js/addon/myPlugins.js'];
        $this->view->plugins = ['rating', 'fancybox'];

        $content = '<div class="fx">
            <h1>Мои плагины</h1>
        </div>';

        $content .= '<div class="my_plugins">
            <div class="plugin_table">
                <a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/plugin/1/">
                    <img src="http://api.celena.io/uploads/plugins/2022-05/shop.png" alt="">
                </a>
                <div class="plugin_box">
                    <h2><a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/plugin/1/">Celena Shop</a> <span class="plugin_version">v 1.0.3</span></h2>
                    <p class="plugin_description">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam commodi culpa distinctio eum laborum minima necessitatibus nulla, repellat sunt suscipit.</p>
                    <div class="plugin_actions">
                        <a href="#" class="btn btn_plugin_activate">Активировать</a>
                        <a href="#" class="btn btn_plugin_remove fr">Удалить</a>
                    </div>
                </div>
            </div>
            <div class="plugin_table">
                <a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/plugin/1/">
                    <img src="https://ps.w.org/embedpress/assets/icon-128x128.gif?rev=2597557" alt="">
                </a>
                <div class="plugin_box">
                    <h2><a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/plugin/1/">EmbedPress – Embed Google Docs, YouTube, Maps</a> <span class="plugin_version">v 1.0.3</span></h2>
                    <p class="plugin_description">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Architecto at dolorem placeat provident quaerat sit vitae. Alias dolor error molestiae officia omnis perferendis sed soluta. Assumenda consectetur, est ex hic nisi omnis perspiciatis quod reiciendis?</p>
                    <div class="plugin_actions">
                        <a href="#" class="btn btn_plugin_deactivate">Выключить</a>
                        <a href="#" class="btn btn_plugin_remove fr">Удалить</a>
                    </div>
                </div>
            </div>
        </div>';

        $this->view->render('', $content);
    }

}