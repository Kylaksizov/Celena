<?php


namespace app\plugins\Celena\Shop\panel;

use app\core\PanelController;
use app\models\plugins\Celena\Shop\OrderModel;


class OrdersController extends PanelController {


    public function indexAction(){

        $this->view->styles = ['css/orders.css'];
        $this->view->scripts = ['js/orders.js'];

        $content = '<div class="fx">
            <h1>Заказы</h1>
            <a href="/'.CONFIG_SYSTEM["panel"].'/orders/add/" class="btn">Добавить</a>
        </div>';

        $OrdersModel = new OrderModel();
        $Orders = $OrdersModel->getAll();
        $OrdersStatus = $OrdersModel->getStatuses();

        if($Orders["orders"]){

            $ordersContent = '';

            $lastOrder = $Orders["orders"][0]["id"];

            /*$statuses = '<select>';
            foreach ($OrdersStatus as $statusRow) {
                $statuses .= '<option value="'.$statusRow["id"].'" style="background: #'.$statusRow["color"].';">'.$statusRow["name"].'</option>';
            }
            $statuses .= '</select>';*/

            foreach ($Orders["orders"] as $key => $row) {

                $nextOrder = '';
                $id = '';
                $order_id = '';
                $buyer = '';
                $total = '';
                $created = '';
                $status = '<td></td>';
                $actions = '';

                if($key == 0 || $lastOrder != $row["id"]){

                    if ($this->plugin->config->str_pad_id == 'int'){
                        $order_id = !empty($this->plugin->config->str_pad_id) ? str_pad($row["id"], $this->plugin->config->str_pad_id, '0', STR_PAD_LEFT) : $row["id"];
                    } else $order_id = $row["order_id"];

                    $nextOrder = ' class="newOrder"';
                    $id = $row["id"];
                    $order_id = '<a href="/'.CONFIG_SYSTEM["panel"].'/orders/'.$row["id"].'/">'.$order_id.'</a>';
                    $buyer = $row["name"];
                    $total = $row["total"].' '.$this->plugin->config->currency;
                    $created = date("d.m.Y H:i", $row["created"]);
                    $status = '<td class="tc" style="background:#'.(!empty($OrdersStatus[$row["status"]]["color"]) ? $OrdersStatus[$row["status"]]["color"] : '72798b').';color:#fff">'.(!empty($OrdersStatus[$row["status"]]["name"]) ? $OrdersStatus[$row["status"]]["name"] : '-').'</td>';
                    $actions = '<ul class="tc">
                            <li><a href="#" class="remove" data-a="Order:deleteOrder='.$row["id"].'"></a></li>
                        </ul>';
                }

                $ordersContent .= '<tr'.$nextOrder.'>
                    <td>'.$id.'</td>
                    <td>'.$order_id.'</td>
                    <td>
                        <a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/products/edit/'.$row["pid"].'/">'.$row["title"].'</a>
                    </td>
                    <td class="tc">'.$row["count"].'</td>
                    <td class="fs12">'.$buyer.'</td>
                    <td><b>'.$total.'</b></td>
                    <td class="fs12">'.$created.'</td>
                    '.$status.'
                    <td>
                        '.$actions.'
                    </td>
                </tr>';

                $lastOrder = $row["id"];
            }

        } else $ordersContent = '<tr class="tc"><td colspan="6">Заказов нет</td></tr>';

        $content .= '<table class="no_odd">
            <tr>
                <th width="10">#</th>
                <th width="100">№ заказа</th>
                <th>Наименование товара</th>
                <th width="70">Кол-во</th>
                <th width="170">Покупатель</th>
                <th width="110">Сумма</th>
                <th width="130">Дата покупки</th>
                <th width="30">Статус</th>
                <th width="50"></th>
            </tr>
            '.$ordersContent.'
        </table>';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('Заказы', $content);
    }



    
    public function orderAction(){

        $this->view->styles = ['css/orders.css'];
        $this->view->scripts = ['js/orders.js'];

        $OrdersModel = new OrderModel();
        $Order = $OrdersModel->get(intval($this->urls[2]));
        $OrdersStatus = $OrdersModel->getStatuses();

        if($Order["order"]){

            $OrderRow = $Order["order"][0];

            $content = '<div class="fx">
                <h1>Заказ № '.$this->urls[2].'</h1>
            </div>';


            $statuses = '<select class="status_select">';
            foreach ($OrdersStatus as $statusRow) {
                $statuses .= '<option value="'.$statusRow["id"].'" style="background: #'.$statusRow["color"].';">'.$statusRow["name"].'</option>';
            }
            $statuses .= '</select>';

            $products = '';
            $total = 0;
            foreach ($Order["order"] as $row) {

                $propsOpen = '';
                $properties = '';

                if(!empty($row["props"])){

                    $propsOpen = '<a href="#" class="propsOpen"></a>';
                    $properties = '<ul class="props">';

                    $propsEx = explode("|", $row["props"]);

                    if(count($propsEx) > 1){

                        foreach ($propsEx as $propExRow) {
                            $properties .= '<li class="prop_position">Позиция 1</li>';
                            $propExRowEx = explode(",", $propExRow);
                            foreach ($propExRowEx as $propExRowExRow) {
                                $val = !empty($Order["props"][$propExRowExRow]["sep"]) ? $Order["props"][$propExRowExRow]["sep"] : $Order["props"][$propExRowExRow]["val"];
                                $properties .= '<li>'.$Order["props"][$propExRowExRow]["title"].': <b>'.$val.'</b></li>';
                            }
                        }

                    } else{

                        $propExRowEx = explode(",", $propsEx[0]);
                        foreach ($propExRowEx as $propExRowExRow) {
                            $val = !empty($Order["props"][$propExRowExRow]["sep"]) ? $Order["props"][$propExRowExRow]["sep"] : $Order["props"][$propExRowExRow]["val"];
                            $properties .= '<li>'.$Order["props"][$propExRowExRow]["title"].': <b>'.$val.'</b></li>';
                        }
                    }

                    $properties .= '</ul>';
                }

                $products .= '<tr>
                    <td>'.$propsOpen.'<a href="#">'.$row["title"].'</a>'.$properties.'</td>
                    <td>'.$row["count"].'</td>
                    <td>'.$row["price"].' '.$this->plugin->config->currency.'</td>
                </tr>';

                $total += $row["price"];
            }
            $products .= '<tr>
                <td colspan="3" class="tr total">Всего: <b>'.$Order["order"][0]["total"].' '.$this->plugin->config->currency.'</b></td>
            </tr>';

            $content .= '<div class="dg order_box">
                <div class="order_content box_">
                    <table>
                        <tr>
                            <th>Наименование товара</th>
                            <th>Кол-во</th>
                            <th>Цена</th>
                        </tr>
                        '.$products.'
                    </table>
                </div>
                <div class="order_sidebar box_">
                    <h3 class="box_title">Другая информация</h3>
                    <ul>
                        <li><span>ID:</span> <b>'.$OrderRow["order_id"].'</b></li>
                        <li><span>Дата заказа:</span> <b>'.date("d.m.Y H:i:s", $OrderRow["created"]).'</b></li>
                        <li><span><br>Покупатель:</span> <b><br>'.$OrderRow["name"].'</b></li>
                        <li><span>Тел.:</span> <b><a href="tel:'.$OrderRow["tel"].'">'.$OrderRow["tel"].'</a></b></li>
                        <li><span>Email.:</span> <b><a href="mailto:'.$OrderRow["email"].'">'.$OrderRow["email"].'</a></b></li>
                    </ul>
                    <br>
                    <p>Комментарий к заказу:</p>
                    <div class="order_comment">'.$OrderRow["comment"].'</div>
                    <br>
                    <label for="">Статус заказа:</label>
                    '.$statuses.'
                </div>
            </div>';

        }

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('Заказы', $content);
    }

}