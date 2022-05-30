<?php

namespace app\controllers\panel;

use app\core\PanelController;
use app\models\panel\moduleModel;


class ModulesController extends PanelController {


    public function indexAction(){

        $this->view->styles = ['css/myModules.css'];
        $this->view->scripts = ['js/myModules.js'];

        $modulesModel = new moduleModel();
        $modules = $modulesModel->getmodules();

        $content = '<div class="fx">
            <h1>Мои плагины</h1>
            <a href="/'.CONFIG_SYSTEM["panel"].'/modules/add/" class="btn">Добавить модуль</a>
        </div>
        <div class="my_modules">';

        if(!empty($modules["modules"])){

            foreach ($modules["modules"] as $row) {

                $buttonStatus = ($row["status"] == '1') ? '<a href="#" class="btn btn_module_deactivate" data-a="Celenamodule:action=disable&id='.$row["id"].'">Выключить</a>' : '<a href="#" class="btn btn_module_activate" data-a="Celenamodule:action=enable&id='.$row["id"].'">Активировать</a>';

                $content .= '<div class="module_table">
                    <a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/module/'.$row["id"].'/">
                        <img src="//'.CONFIG_SYSTEM["home"].'/templates/modules/'.$row["name"].'/panel/'.$this->modulesSystems->{$row["name"]}->icon.'" alt="">
                    </a>
                    <div class="module_box">
                        <h2><a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/module/'.$row["id"].'/">'.$this->modulesSystems->{$row["name"]}->name.'</a> <span class="module_version">v '.$row["version"].'</span></h2>
                        <p class="module_description">'.$this->modulesSystems->{$row["name"]}->description.'</p>
                        <div class="module_actions">
                            '.$buttonStatus.'
                            <a href="#" class="btn btn_module_remove fr" data-a="Celenamodule:action=remove&id='.$row["module_id"].'">Удалить</a>
                        </div>
                    </div>
                </div>';
            }
        }
        $content .= '</div>';

        $this->view->render('Мои плагины', $content);
    }



    public function addAction(){

        $this->view->styles = ['css/myModules.css'];
        $this->view->scripts = ['js/myModules.js'];
        $this->view->plugins = ['codemirror'];

        $modulesModel = new moduleModel();
        $modules = $modulesModel->getmodules();

        $content = '<div class="fx">
            <h1>Название плагина</h1>
        </div>
        
        <div class="tabs">
            <ul class="tabs_caption">
                <li class="active">Инфо</li>
                <li>Файловая система</li>
                <li>MySql</li>
            </ul>
            <div class="tabs_content active">
                1
            </div>
            <div class="tabs_content">
                <textarea name="" id="code1" cols="30" rows="10"></textarea>
                <a href="#" class="add btn">+</a>
            </div>
            <div class="tabs_content">
                2
            </div>
        </div>
        
        <div class="my_modules">';

        $content .= '</div>';

        $this->view->render('Мои плагины', $content);
    }

}