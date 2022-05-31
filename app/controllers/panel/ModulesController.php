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

        $title = 'Создание плагина';

        $content = '<div class="fx">
            <h1>'.$title.'</h1>
        </div>
        
        <form action method="POST">
            <div class="tabs">
                <ul class="tabs_caption">
                    <li class="active">Основа</li>
                    <li>Файловая система</li>
                    <li>MySql</li>
                </ul>
                
                <div class="tabs_content active">
                
                    <div class="dg dg_auto">
                        <div>
                            <label for="" class="rq">Название модуля</label>
                            <input type="text" name="name" required>
                            <label for="" class="rq">Версия модуля</label>
                            <input type="text" name="version" placeholder="0.0.1" required>
                            <label for="">Версия <b>Celena</b></label>
                            <input type="text" name="cv" placeholder="Если пусто, то любая">
                        </div>
                        <div class="tr">
                            <label for="icon" class="upload_files">
                                <input type="file" name="icon" id="icon"> иконка модуля
                            </label>
                            <p class="title_box hr_d">Комментарий</p>
                            <textarea name="comment" rows="3"></textarea>
                        </div>
                    </div>
                    <p class="title_box hr_d">Описание</p>
                    <textarea name="descr" rows="5"></textarea>
                    <input type="checkbox" name="status" value="1" id="statusModule"><label for="statusModule">Статус</label>
                </div>
                
                <div class="tabs_content">
                
                    <div id="filesMod">
                        <!--<div class="fileMod">
                            <div class="fx ai_c">
                                <div class="fpb">
                                    <label for="">Путь к файлу:</label>
                                    <input type="text" name="filePath[]" class="filePath" placeholder="controllers/...">
                                </div>
                                <a href="#" class="remove remove_file"></a>
                            </div>
                            <div class="actionsFile">
                                <div class="fx ai_c">
                                    <select name="actionsFile[]">
                                        <option value="">Выбрать действие</option>
                                        <option value="1">Найти и заменить</option>
                                        <option value="2">Найти и добавить выше</option>
                                        <option value="3">Найти и добавить ниже</option>
                                        <option value="4">Заменить файл</option>
                                        <option value="5">Создать новый файл</option>
                                    </select>
                                    <a href="#" class="remove remove_action"></a>
                                </div>
                                <div class="actionsBox">
                                    <div class="actionBox_">
                                        <label for="">Найти:</label>
                                        <textarea name="search[]" id="code" rows="1"></textarea>
                                    </div>
                                    <div class="actionBox_">
                                        <label for="">Заменить на:</label>
                                        <textarea name="replace[]" id="code2" rows="1"></textarea>
                                    </div>
                                </div>
                            </div>
                            <a href="#" class="add_action">Добавить действие</a>
                        </div>-->
                    </div>
                    
                    <a href="#" class="btn add_file">Добавить файл</a>
                    
                </div>
                
                <div class="tabs_content">
                    <div class="baseQueries">
                        <label for="">При установке:</label>
                        <textarea name="base[install]" rows="5" id="baseInstall"></textarea>
                    </div>
                    <div class="baseQueries">
                        <label for="">При обновлении:</label>
                        <textarea name="base[update]" rows="5" id="baseUpdate"></textarea>
                    </div>
                    <div class="baseQueries">
                        <label for="">При включении:</label>
                        <textarea name="base[on]" rows="5" id="baseOn"></textarea>
                    </div>
                    <div class="baseQueries">
                        <label for="">При выключении:</label>
                        <textarea name="base[off]" rows="5" id="baseOff"></textarea>
                    </div>
                    <div class="baseQueries">
                        <label for="">При удалении:</label>
                        <textarea name="base[del]" rows="5" id="baseDel"></textarea>
                    </div>
                </div>
                
            </div>
            <br>
            <input type="hidden" name="ajax" value="CelenaModule">
            <input type="submit" class="btn" value="Сохранить">
        </form>
        
        <div class="my_modules">';

        $content .= '</div>';

        $this->view->render($title, $content);
    }

}