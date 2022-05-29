<?php

namespace app\plugins\Celena\Example\panel;

use app\core\PanelController;
use app\models\plugins\Celena\Example\panel\ExampleModel;

class IndexController extends PanelController{


    public function indexAction(){

        //$this->view->include('index');

        $this->view->styles = ['css/style.css'];
        $this->view->scripts = ['js/script.js'];
        
        if($this->ajax) die($this->ajax);

        $ExampleMode = new ExampleModel();
        $Example = $ExampleMode->getTmp();

        $table = '<table>
            <tr>
                <th>ID</th>
                <th>Имя</th>
            </tr>';

        if(!empty($Example)){

            foreach ($Example as $row) {

                $table .= '<tr>
                    <td>'.$row["id"].'</td>
                    <td>'.$row["name"].'</td>
                </tr>';
            }
        }

        $table .= '</table>';

        $content = $table.'<br>
        <div class="fx jc_c">
            <a href="#" data-a="Test:example=1" class="btn">Первая 1</a>&nbsp;
            <a href="#" data-a="Test:example=2" class="btn">Вторая 2</a>&nbsp;
            <a href="#" data-a="Test:param=1&param=2" class="btn">Вторая 3</a>
        </div>';

        $this->view->render('Example Plugin', $content);
    }

}