<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\models\UsersModel;


class UsersController extends PanelController {


    public function indexAction(){

        $this->view->styles = ['css/addon/users.css'];
        $this->view->scripts = ['js/addon/users.js'];

        $content = '<div class="fx">
            <h1>Пользователи</h1>
            <a href="/'.CONFIG_SYSTEM["panel"].'/users/add/" class="btn">Добавить</a>
        </div>';

        $UsersModel = new UsersModel();
        $Users = $UsersModel->getAll();

        if($Users["users"]){

            $contentUsers = '';

            foreach ($Users["users"] as $row) {

                $status = 'Не активный';
                switch ($row["status"]){
                    case '-1': $status = 'Заблокирован'; break;
                    case '1': $status = 'Активный'; break;
                }

                $contentUsers .= '<tr class="role_'.$row["role"].'">
                    <td>'.$row["id"].'</td>
                    <td><a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/users/'.$row["id"].'/">'.$row["name"].'</a></td>
                    <td>'.$row["email"].'</td>
                    <td class="tc">'.$row["role_name"].'</td>
                    <td class="fs12">'.date("d.m.Y H:i", $row["created"]).'</td>
                    <td>'.$status.'</td>
                    <td>
                        <ul class="tc">
                            <li><a href="#" class="remove" data-a="Users:delete='.$row["id"].'"></a></li>
                        </ul>
                    </td>
                </tr>';
            }

        } else $contentUsers = '<tr class="tc"><td colspan="6">Пользователей нет</td></tr>';

        $content .= '<table class="no_odd">
            <tr>
                <th width="10">#</th>
                <th width="100">Имя</th>
                <th>Email</th>
                <th width="70">Группа</th>
                <th width="170">Дата регистрации</th>
                <th width="70">Статус</th>
                <th width="50"></th>
            </tr>
            '.$contentUsers.'
        </table>'.$Users["pagination"];

        $this->view->render('Заказы', $content);
    }


    public function customerAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }


    public function employeeAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }


    public function rolesAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }

}