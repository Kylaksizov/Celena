<?php

namespace app\controllers\panel;

use app\core\PanelController;
use app\models\panel\SystemModel;
use Exception;

class SystemController extends PanelController {





    /**
     * @name Ошибки запросов в БД
     * ==========================
     * @return void
     */
    public function RoutesAction(){

        $this->view->styles = ["css/routes.css"];

        $content = '<h1>Роуты</h1>';
        
        $Routes = require ROOT . '/app/cache/routes.php';

        $content .= '<h2>Роуты панели:</h2>
        <table id="routs_panel">
            <tr>
                <th>URL</th>
                <th>Controller</th>
                <th>Method</th>
            </tr>';
        foreach ($Routes["panel"] as $path => $controllerAction) {

            $action = !empty($controllerAction["action"]) ? $controllerAction["action"].'Action' : 'indexAction';

            $content .= '<tr>
                <td>'.$path.'</td>
                <td>'.ucfirst($controllerAction["controller"]).'</td>
                <td>'.ucfirst($action).'</td>
            </tr>';
        }
        $content .= '</table>';

        $content .= '<h2>Роуты WEB:</h2>
        <table id="routs_web">
            <tr>
                <th>URL</th>
                <th>Controller</th>
                <th>Method</th>
            </tr>';
        foreach ($Routes["web"] as $path => $controllerAction) {

            $action = !empty($controllerAction["action"]) ? $controllerAction["action"].'Action' : 'indexAction';

            $content .= '<tr>
                <td>'.$path.'</td>
                <td>'.ucfirst($controllerAction["controller"]).'</td>
                <td>'.ucfirst($action).'</td>
            </tr>';
        }
        $content .= '</table>';


        $this->view->render('Логи', $content);
    }




    /**
     * @name Журнал логов
     * ==================
     * @return void
     * @throws Exception
     */
    public function logsAction(){


        $content = '<h1>Журнал логов</h1>';

        $SystemModel = new SystemModel();
        $Logs = $SystemModel->getLogs();

        if(!empty($Logs["logs"])){

            $content .= '<table>
                <tr>
                    <th>Инициатор</th>
                    <th>IP</th>
                    <th>Инфо</th>
                    <th>Страница</th>
                    <th>Дата / время</th>
                    <th>Статус</th>
                </tr>';
            foreach ($Logs["logs"] as $row) {

                $userLink = !empty($row["uid"]) ? '<a href="/user/'.$row["uid"].'/'.$row["name"].'">'.$row["name"].'</a>' : '';

                $status = '';
                switch ($row["status"]){
                    case '1': $status = '<span class="status_success">Успех</span>'; break;
                    case '2': $status = '<span class="status_error">Ошибка</span>'; break;
                    case '3': $status = '<span class="status_danger">Опасность</span>'; break;
                }

                $created = (date("d.m.Y", $row["created"]) == date("d.m.Y", time())) ? '<span class="today">Сегодня</span> в ' . date("H:i:s", $row["created"]) : date("d.m.Y H:i:s", $row["created"]);

                $content .= '<tr>
                    <td>'.$userLink.'</td>
                    <td>'.$row["ip"].'</td>
                    <td>'.$row["log"].'</td>
                    <td>'.$row["url"].'</td>
                    <td class="fs12">'.$created.'</td>
                    <td class="no_pad">'.$status.'</td>
                </tr>';
            }
            $content .= '</table>' . $Logs["pagination"];
        }

        $this->view->render('Логи', $content);
    }





    /**
     * @name Ошибки запросов в БД
     * ==========================
     * @return void
     */
    public function DbLogsAction(){

        $this->view->styles = ["css/logs.css"];

        $content = '<h1>Ошибки запросов в БД</h1>
        <div id="nex_logs">';

        if(file_exists(CORE . '/tmp/db_errors.txt')){

            $file = file_get_contents(CORE . '/tmp/db_errors.txt', FILE_USE_INCLUDE_PATH);

            $file = explode("\r\n", $file);
            $file = array_reverse($file);

            foreach ($file as $line) {
                if(!empty($line)) $content .= '<p>'.$line.'</p>';
            }
        }

        $this->view->render('Логи', $content.'</div>');
    }

}