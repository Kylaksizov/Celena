<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\core\system\shop\ShopController;


class CelenaShopController extends PanelController {


    public function indexAction(){

        $this->view->styles = ['css/addon/celena_shop.css'];
        $this->view->scripts = ['js/addon/celena_shop.js'];
        $this->view->plugins = ['rating', 'fancybox'];

        $plugins = ShopController::getPlugins();
echo "<pre>";
print_r($plugins);
echo "</pre>";
exit;
        $content = '<div>
            <h1>Плагины</h1>
        </div>';

        if(!empty($plugins["plugins"])){

            $content .= '<div class="dg dg_auto">';

            foreach ($plugins["products"] as $plugin) {

                $content .= '<div class="plugin">
                    <a href="#plugin_detail" class="plug_poster open_modal" data-a="CelenaPlugin:action=showDetail&id=1"><img src="https://ps.w.org/embedpress/assets/icon-128x128.gif?rev=2597557" alt=""></a>
                    <div class="plug_content">
                        <h2 class="plug_title"><a href="#plugin_detail" class="open_modal" data-a="CelenaPlugin:action=showDetail&id=1">EmbedPress – Embed Google Docs, YouTube, Maps</a></h2>
                        <p class="plug_desc">Otter is a dynamic collection of page building blocks and templates for the WordPress Gutenberg. Otter is a dynamic collection of page building blocks and templates for the WordPress Gutenberg.</p>
                    </div>
                    <div class="plug_info">
                        <div class="fx jc_fs ai_c">
                            <div class="rating_plugin" data-rateyo-rating="80%"></div>&nbsp;
                            <p>(2300)</p>
                        </div>
                        <div class="plug_buttons">
                            <a href="#" class="btn btn_install" data-a="CelenaPlugin:action=install&id=1">Установить</a>
                        </div>
                    </div>
                </div>';

                $content .= '<div class="plugin">
                    <a href="#" class="plug_poster"><img src="https://ps.w.org/woocommerce/assets/icon-256x256.png?rev=2366418" alt=""></a>
                    <div class="plug_content">
                        <h2 class="plug_title"><a href="#">Lorem ipsum dolor</a></h2>
                        <p class="plug_desc">Otter is a dynamic collection of page building blocks and templates for the WordPress Gutenberg…</p>
                    </div>
                    <div class="plug_info">
                        <div class="fx jc_fs ai_c">
                            <div class="rating_plugin" data-rateyo-rating="80%"></div>&nbsp;
                            <p>(17)</p>
                        </div>
                        <div class="plug_buttons">
                            <a href="#" class="btn btn_uninstall">Удалить</a>
                        </div>
                    </div>
                </div>';
            }

            $content .= '</div>
            <div id="plugin_detail" class="modal_big">
                <div id="result_response_plugin"></div>
                <a href="#" class="close"></a>
            </div>';
        }

        $this->view->render('', $content);
    }





    public function templatesAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }


    public function pluginsAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }


    public function modulesAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }


    public function orderDevelopmentAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }

}