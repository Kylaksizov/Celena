<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\models\UsersModel;


class RolesController extends PanelController {


    public function indexAction(){

        $this->view->styles = ['css/roles.css'];
        $this->view->scripts = ['js/roles.js'];

        $content = '<div class="fx">
            <h1>Группы пользователей</h1>
            <a href="/'.CONFIG_SYSTEM["panel"].'/roles/add/" class="btn">Добавить</a>
        </div>';

        $UsersModel = new UsersModel();
        $Roles = $UsersModel->getRoles();

        $contentRoles = '';

        if($Roles["roles"]){

            foreach ($Roles["roles"] as $row) {

                $rules = json_decode($row["rules"]);
                $panel_allowed = ($row["id"] == 1 || !empty($rules->panel)) ? ' panel_allowed' : '';

                $actions = ($row["id"] != 1 && $row["id"] != 2) ? '<li><a href="#" class="remove" data-a="Roles:delete='.$row["id"].'"></a></li>' : '';

                $contentRoles .= '<tr class="role_'.$row["id"].$panel_allowed.'">
                    <td>'.$row["id"].'</td>
                    <td><a href="/'.CONFIG_SYSTEM["panel"].'/roles/edit/'.$row["id"].'/">'.$row["name"].'</a></td>
                    <td>
                        <ul class="tc">
                            '.$actions.'
                        </ul>
                    </td>
                </tr>';
            }
        }

        $content .= '<table class="no_odd">
            <tr>
                <th width="10">ID</th>
                <th>Название группы</th>
                <th width="50"></th>
            </tr>
            '.$contentRoles.'
        </table>'.$Roles["pagination"];

        $this->view->render('Группы пользователей', $content);
    }





    public function addAction(){

        $this->view->styles = ['css/roles.css'];
        $this->view->scripts = ['js/roles.js'];

        $title = 'Создание группы';

        if(!empty($this->urls[3]) && is_numeric($this->urls[3])){

            $UsersModel = new UsersModel();
            $Role = $UsersModel->getRole(intval($this->urls[3]));
            $rules = json_decode($Role["rules"]);

            if($Role["id"] == 1) $title = '<h1><span style="color:red">Супер группа' . CONFIG_SYSTEM["separator"] . $Role["name"] . '</span></h1>
                <p class="page_description">Группа имеет полный доступ ко всему! Любые изменения в ней не имеют реакции.</p>';
            else $title = '<h1>Группа' . CONFIG_SYSTEM["separator"] . $Role["name"] . '</h1>';

            $content = $title;

        } else{

            $content = '<div class="fx">
                <h1>Создание группы</h1>
                <a href="/'.CONFIG_SYSTEM["panel"].'/roles/add/" class="btn">Добавить</a>
            </div>';
        }

        $content .= '<form action="" method="POST">
            <div class="tabs">
                <ul class="tabs_caption">
                    <li class="active">Общее</li>
                    <li>Комментарии</li>
                    <li>Админ-панель</li>
                </ul>
                
                <div class="tabs_content active">
                    
                    <div class="dg settings">
                        <div class="set_item">
                            <div>
                                <h3>Название группы</h3>
                            </div>
                            <div>
                                <input type="text" name="name" value="'.(!empty($Role["name"]) ? $Role["name"] : '').'">
                            </div>
                        </div>
                        <!--:roles_main-->
                    </div>
                    
                </div>
                
                <div class="tabs_content">
                
                    <div class="dg settings">
                        <div class="set_item">
                            <div>
                                <h3>Разрешить добавлять комментарии</h3>
                            </div>
                            <div>
                                <input type="checkbox" name="addComment" id="ch_addComment"'.(!empty($rules->addComment)?' checked':'').' value="1"><label for="ch_addComment"></label>
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Отправлять комментарии на модерацию</h3>
                                <p class="setDescription">Если включено, то комментарии будут отправлятся на модерацию.</p>
                            </div>
                            <div>
                                <input type="checkbox" name="addCommentModer" id="ch_addCommentModer"'.(!empty($rules->addCommentModer)?' checked':'').' value="1"><label for="ch_addCommentModer"></label>
                            </div>
                        </div>
                        <!--:roles_comments-->
                    </div>
                    
                </div>
                
                <div class="tabs_content">
                
                    <div class="dg settings">
                        <div class="set_item" style="color:#f00">
                            <div>
                                <h3>Доступ в админ-панель</h3>
                            </div>
                            <div>
                                <input type="checkbox" name="panel" id="ch_panel"'.(!empty($rules->panel)?' checked':'').' value="1"><label for="ch_panel"></label>
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Разрешить добавлять новости через панель</h3>
                            </div>
                            <div>
                                <input type="checkbox" name="panel_add_news" id="ch_panel_add_news"'.(!empty($rules->panel_add_news)?' checked':'').' value="1"><label for="ch_panel_add_news"></label>
                            </div>
                        </div>
                        <!--:roles_panel-->
                    </div>
                    
                </div>
                
            </div>
            
            <input type="submit" class="btn" data-a="Roles" value="Сохранить">
            
        </form>';

        $this->view->render(strip_tags($title), $content);
    }

}