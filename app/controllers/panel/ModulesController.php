<?php

namespace app\controllers\panel;

use app\core\PanelController;
use app\models\panel\ModuleModel;


class ModulesController extends PanelController {


    public function indexAction(){

        $this->view->styles = ['css/myModules.css'];
        $this->view->scripts = ['js/myModules.js'];

        $ModuleModel = new ModuleModel();
        $Modules = $ModuleModel->getModules();

        $content = '<div class="fx">
            <h1>Установленные модули</h1>
            <a href="/'.CONFIG_SYSTEM["panel"].'/modules/add/" class="btn">Добавить модуль</a>
        </div>
        <div class="my_modules">';

        if(!empty($Modules["modules"])){

            foreach ($Modules["modules"] as $row) {

                $buttonStatus = ($row["status"] == '1') ? '<a href="#" class="btn btn_module_deactivate" data-a="CelenaModule:action=disable&id='.$row["id"].'">Выключить</a>' : '<a href="#" class="btn btn_module_activate" data-a="CelenaModule:action=enable&id='.$row["id"].'">Включить</a>';

                $poster = !empty($row["poster"]) ? '<img src="//'.CONFIG_SYSTEM["home"].'/uploads/modules/'.$row["poster"].'" alt="">' : '<img src="//'.CONFIG_SYSTEM["home"].'/uploads/system/celena_photo.png" alt="">';

                $content .= '<div class="module_table">
                    <a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/modules/edit/'.$row["id"].'/">
                        '.$poster.'
                    </a>
                    <div class="module_box">
                        <h2><a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/modules/edit/'.$row["id"].'/">'.$row["name"].'</a> <span class="module_version">'.$row["version"].'</span></h2>
                        <p class="module_description">'.$row["descr"].'</p>
                        <div class="module_actions">
                            '.$buttonStatus.'
                            <a href="#" class="btn btn_module_remove fr" data-a="CelenaModule:action=remove&id='.$row["id"].'">Удалить</a>
                        </div>
                    </div>
                </div>';
            }
        }
        $content .= '</div>';

        $this->view->render('Мои плагины', $content);
    }



    public function addAction(){

        preg_match('/edit\/([0-9]+)\//is', $_GET["url"], $modId);
        if(!empty($modId[1]) && is_numeric($modId[1])){

            $ModuleModel = new ModuleModel();
            $Module = $ModuleModel->getModule(intval($modId[1]));
        }

        $this->view->styles = ['css/myModules.css'];
        $this->view->scripts = ['js/myModules.js'];
        $this->view->plugins = ['codemirror'];

        $title = 'Создание плагина';

        $filesMod = '';
        if(!empty($Module["ex"])){

            $fileId = 1;
            foreach ($Module["ex"] as $filePath => $actions) {

                $filesMod .= '
                <div class="fileMod" data-fileId="'.$fileId.'">
                    <div class="fx ai_c">
                        <div class="fpb">
                            <label for="">Путь к файлу:</label>
                            <input type="text" name="filePath['.$fileId.']" class="filePath" value="'.$filePath.'" placeholder="app/...">
                        </div>
                        <a href="#" class="remove remove_file"></a>
                    </div>';

                foreach ($actions as $actionRow) {

                    $filesMod .= '
                    <div class="actionsFile">
                        <div class="fx ai_c">
                            <select name="actionsFile['.$fileId.'][]" class="actionsFileSelect">
                                <option value="">Выбрать действие</option>
                                <option value="1"'.($actionRow["action"] == '1' ? ' selected' : '').'>Найти и заменить</option>
                                <option value="2"'.($actionRow["action"] == '2' ? ' selected' : '').'>Найти и добавить выше</option>
                                <option value="3"'.($actionRow["action"] == '3' ? ' selected' : '').'>Найти и добавить ниже</option>
                                <option value="4"'.($actionRow["action"] == '4' ? ' selected' : '').'>Заменить файл</option>
                                <option value="5"'.($actionRow["action"] == '5' ? ' selected' : '').'>Создать новый файл</option>
                            </select>
                            <a href="#" class="remove remove_action"></a>
                        </div>
                        <div class="actionsBox">';

                    switch ($actionRow["action"]){

                        case '1': case '2': case '3':

                            $repl = 'Заменить на';
                            if($actionRow["action"] == '2') $repl = 'Добавить выше';
                            if($actionRow["action"] == '3') $repl = 'Добавить ниже';

                            $filesMod .= '
                            <div class="actionBox_">
                                <label for="">Найти:</label>
                                <textarea name="'.$fileId.'[search][]" id="code'.$actionRow["id"].'" class="mirror" rows="1">'.$actionRow["searchcode"].'</textarea>
                            </div>
                            <div class="actionBox_">
                                <label for="">'.$repl.':</label>
                                <textarea name="'.$fileId.'[act][]" id="code'.$actionRow["id"].'1" class="mirror" rows="1">'.$actionRow["replacecode"].'</textarea>
                            </div>';

                            break;

                        case '4': case '5':

                            $repl = ($actionRow["action"] == '4') ? 'Заменить на' : 'Содержимое файла';

                            $filesMod .= '<div class="actionBox_">
                                    <label for="">'.$repl.':</label>
                                    <textarea name="'.$fileId.'[act][]" id="code'.$actionRow["id"].'" class="mirror" rows="1">'.$actionRow["replacecode"].'</textarea>
                                </div>';

                            break;
                    }

                    $filesMod .= '
                        </div>
                    </div>';
                }

                $filesMod .= '
                    <a href="#" class="add_action">Добавить действие</a>
                </div>';

                $fileId++;
            }
        }

        $panelRoutes = '';
        $webRoutes = '';
        
        if(!empty($Module["module"]["routes"])){
            
            $routes = json_decode($Module["module"]["routes"], true);

            $counterRoute = 1;
            if(!empty($routes["panel"])){

                if(!empty($routes["panel"]["url"])){
                    foreach ($routes["panel"]["url"] as $key => $url) {

                        $action = !empty($routes["panel"]["action"][$key]) ? $routes["panel"]["action"][$key] : '';

                        $panelRoutes .= '<tr>
                            <td><input type="text" name="route[panel][url]['.$counterRoute.']" value="'.$url.'" placeholder="example/url.html$"></td>
                            <td><input type="text" name="route[panel][controller]['.$counterRoute.']" value="'.$routes["panel"]["controller"][$key].'" placeholder="Example"></td>
                            <td><input type="text" name="route[panel][action]['.$counterRoute.']" value="'.$action.'" placeholder="Index"></td>
                            <td><input type="checkbox" name="route[panel][position]['.$counterRoute.']" class="ch_min" id="position'.$counterRoute.'" value="1"'.(!empty($routes["panel"]["position"][$key]) ? ' checked' : '').'><label for="position'.$counterRoute.'">в конец</label></td>
                        </tr>';

                        $counterRoute++;
                    }
                }

                if(!empty($routes["web"]["url"])){
                    foreach ($routes["web"]["url"] as $key => $url) {

                        $action = !empty($routes["web"]["action"][$key]) ? $routes["web"]["action"][$key] : '';

                        $webRoutes .= '<tr>
                            <td><input type="text" name="route[web][url]['.$counterRoute.']" value="'.$url.'" placeholder="example/url.html$"></td>
                            <td><input type="text" name="route[web][controller]['.$counterRoute.']" value="'.$routes["web"]["controller"][$key].'" placeholder="Example"></td>
                            <td><input type="text" name="route[web][action]['.$counterRoute.']" value="'.$action.'" placeholder="Index"></td>
                            <td><input type="checkbox" name="route[web][position]['.$counterRoute.']" class="ch_min" id="position'.$counterRoute.'" value="1"'.(!empty($routes["web"]["position"][$key]) ? ' checked' : '').'><label for="position'.$counterRoute.'">в конец</label></td>
                        </tr>';

                        $counterRoute++;
                    }
                }
            }
        }

        $poster = !empty($Module["module"]["poster"]) ? '<img src="//'.CONFIG_SYSTEM["home"].'/uploads/modules/'.$Module["module"]["poster"].'" alt="" class="poster">' : '<img src="//'.CONFIG_SYSTEM["home"].'/uploads/system/celena_photo.png" alt="" class="poster">';


        $content = '<div class="fx">
            <h1>'.$title.'</h1>
        </div>
        
        <form action method="POST" enctype="multipart/form-data">
            <div class="tabs">
                <ul class="tabs_caption">
                    <li class="active">Основа</li>
                    <li>Файловая система</li>
                    <li>Роуты</li>
                    <li>MySql</li>
                </ul>
                
                <div class="tabs_content active">
                
                    <div class="dg dg_auto">
                        <div>
                            <label for="" class="rq">Название модуля</label>
                            <input type="text" name="name" value="'.(!empty($Module["module"]["name"]) ? $Module["module"]["name"] : '').'" required>
                            <label for="" class="rq">Версия модуля</label>
                            <input type="text" name="version" placeholder="0.0.1" value="'.(!empty($Module["module"]["version"]) ? $Module["module"]["version"] : '').'" required>
                            <label for="">Версия <b>Celena</b></label>
                            <input type="text" name="cv" value="'.(!empty($Module["module"]["cv"]) ? $Module["module"]["cv"] : '').'" placeholder="Если пусто, то любая">
                            <p class="title_box hr_d">Комментарий</p>
                            <textarea name="comment" rows="3">'.(!empty($Module["module"]["comment"]) ? $Module["module"]["comment"] : '').'</textarea>
                        </div>
                        <div class="tr">
                            <p class="icon_desc">Иконка должна быть 256x256</p>
                            '.$poster.'
                            <div class="clr"></div>
                            <label for="icon" class="upload_files">
                                <input type="file" name="icon" id="icon"> иконка модуля
                            </label>
                        </div>
                    </div>
                    <p class="title_box hr_d">Описание</p>
                    <textarea name="descr" rows="5">'.(!empty($Module["module"]["descr"]) ? $Module["module"]["descr"] : '').'</textarea>
                    <input type="checkbox" name="status" value="1"'.(!empty($Module["module"]["status"]) ? ' checked' : '').' id="statusModule"><label for="statusModule">Статус</label>
                </div>
                
                <div class="tabs_content">
                
                    <div id="filesMod">
                        '.$filesMod.'
                    </div>
                    
                    <a href="#" class="btn add_file">Добавить файл</a>
                    
                </div>
                
                <div class="tabs_content" id="routes">
                    
                    <h2>Panel</h2>
                    <table>
                        <tr>
                            <th>URL</th>
                            <th>Controller</th>
                            <th>Action</th>
                            <th>Позиция</th>
                        </tr>
                        '.$panelRoutes.'
                    </table>
                    <a href="#" class="btn addRoute">Добавить</a>
                    
                    <br><br><br>
                    <h2>WEB</h2>
                    <table>
                        <tr>
                            <th>URL</th>
                            <th>Controller</th>
                            <th>Action</th>
                            <th>Позиция</th>
                        </tr>
                        '.$webRoutes.'
                    </table>
                    <a href="#" class="btn addRouteWeb">Добавить</a>
                    
                </div>
                
                <div class="tabs_content">
                    <div class="baseQueries">
                        <label for="">При установке:</label>
                        <textarea name="base[install]" rows="1" id="baseInstall">'.(!empty($Module["module"]["base_install"]) ? $Module["module"]["base_install"] : '').'</textarea>
                    </div>
                    <div class="baseQueries">
                        <label for="">При обновлении:</label>
                        <textarea name="base[update]" rows="1" id="baseUpdate">'.(!empty($Module["module"]["base_update"]) ? $Module["module"]["base_update"] : '').'</textarea>
                    </div>
                    <div class="baseQueries">
                        <label for="">При включении:</label>
                        <textarea name="base[on]" rows="1" id="baseOn">'.(!empty($Module["module"]["base_on"]) ? $Module["module"]["base_on"] : '').'</textarea>
                    </div>
                    <div class="baseQueries">
                        <label for="">При выключении:</label>
                        <textarea name="base[off]" rows="1" id="baseOff">'.(!empty($Module["module"]["base_off"]) ? $Module["module"]["base_off"] : '').'</textarea>
                    </div>
                    <div class="baseQueries">
                        <label for="">При удалении:</label>
                        <textarea name="base[del]" rows="1" id="baseDel">'.(!empty($Module["module"]["base_del"]) ? $Module["module"]["base_del"] : '').'</textarea>
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