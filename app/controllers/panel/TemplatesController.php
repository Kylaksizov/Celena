<?php

namespace app\controllers\panel;

use app\core\PanelController;


class TemplatesController extends PanelController {


    public function indexAction(){

        $this->view->styles = ['css/myTemplates.css'];
        $this->view->scripts = ['js/myTemplates.js'];
        $this->view->plugins = ['fancybox'];

        $content = '<div class="fx">
            <h1>Мои шаблоны</h1>
        </div>
        <div class="dg dg_auto">';
        
        $templatesDir = scandir(ROOT . '/templates');

        foreach ($templatesDir as $dir) {

            if(
                $dir != '.' &&
                $dir != '..' &&
                is_dir(ROOT . '/templates/' . $dir) &&
                $dir != 'Panel' &&
                $dir != 'PanelAuth' &&
                $dir != 'system' &&
                $dir != 'plugins'
            ){

                $tplConfig = file_exists(ROOT . '/templates/' . $dir . '/config.json') ? json_decode(file_get_contents(ROOT . '/templates/' . $dir . '/config.json')) : [];

                $buttonTemplate = (CONFIG_SYSTEM["template"] == $dir) ? '<a href="#" class="btn btn_template_deactivate">Активен</a>' : '<a href="#" class="btn btn_plugin_activate">Активировать</a>';

                $name = !empty($tplConfig->name) ? $tplConfig->name : '';

                $poster = !empty($tplConfig->poster) ? '//'.CONFIG_SYSTEM["home"].'/templates/' . $dir . '/' . $tplConfig->poster : '//'.CONFIG_SYSTEM["home"].'/app/core/system/img/celena.svg';

                $description = !empty($tplConfig->description) ? $tplConfig->description : '';
                $description = str_replace(["[b]", "[/b]"], ["<b>", "</b>"], $description);

                $content .= '<div class="template">
                    <a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/template/" class="template_poster" style="background: url('.$poster.') no-repeat;"></a>
                    <div class="plug_content">
                        <h2 class="template_title"><a href="#" data-a="CelenaTemplate:action=getTemplate&amp;id=1">'.$name.'</a> <span class="dir">('.$dir.')</span></h2>
                        <p class="template_desc">'.$description.'</p>
                    </div>
                    <div class="template_info">
                        <div class="template_buttons">
                            '.$buttonTemplate.'
                        </div>
                    </div>
                </div>';
            }
        }

        $content .= '</div>';

        $this->view->render('Мои плагины', $content);
    }

}