<?php


namespace app\controllers\panel;

use app\core\PanelController;


class ProductsController extends PanelController {



    public function indexAction(){

        $content = '<h1>Категории товаров</h1>';

        $content .= '<div class="">
            <table>
                <tr>
                    <th>#</th>
                    <th>Изображение</th>
                    <th>Наименование</th>
                    <th>Цена</th>
                    <th>Дата публикации</th>
                    <th>Кол-во покупок</th>
                </tr>
                <tr>
                    <td>0001</td>
                    <td>Изображение</td>
                    <td>Наименование</td>
                    <td>Цена</td>
                    <td>Дата публикации</td>
                    <td>Кол-во покупок</td>
                </tr>
            </table>
        </div>';

        $this->view->render('Товары', $content);
    }




    public function categoriesAction(){

        $content = '<h1>Категории товаров</h1>';

        $content .= '<table>
            <tr>
                <th width="20">ID</th>
                <th width="20"></th>
                <th>Название</th>
                <th width="30">Иконка</th>
                <th width="20">Статус</th>
                <th width="50">Действия</th>
            </tr>
            <tr>
                <td>0001</td>
                <td><input type="checkbox" class="ch_box" name="cat[1]" id="cat[1]"><label for="cat[1]"></label></td>
                <td><a href="#">Детские товары</a></td>
                <td><img src="" alt=""></td>
                <td><input type="checkbox" name="cat[1]" id="cat[1]"><label for="cat[1]"></label></td>
                <td>
                    <ul>
                        <li><a href="#">s</a></li>
                        <li><a href="#" class="remove"></a></li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>0001</td>
                <td><input type="checkbox" class="ch_box" name="cat[1]" id="cat[4]" disabled><label for="cat[4]"></label></td>
                <td><a href="#">Детские товары</a></td>
                <td><img src="" alt=""></td>
                <td><input type="checkbox" name="cat[1]" id="cat[1]" disabled><label for="cat[1]"></label></td>
                <td>
                    <ul>
                        <li><a href="#">s</a></li>
                        <li><a href="#" class="remove"></a></li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>0001</td>
                <td><input type="checkbox" class="ch_box" name="cat[1]" id="cat[2]" disabled checked><label for="cat[2]"></label></td>
                <td><a href="#">Детские товары</a></td>
                <td><img src="" alt=""></td>
                <td><input type="checkbox" name="cat[1]" id="cat[1]" disabled checked><label for="cat[1]"></label></td>
                <td>
                    <ul>
                        <li><a href="#">s</a></li>
                        <li><a href="#" class="remove"></a></li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>0001</td>
                <td><input type="checkbox" class="ch_box" name="cat[1]" id="cat[3]" checked><label for="cat[3]"></label></td>
                <td><a href="#">Детские товары</a></td>
                <td><img src="" alt=""></td>
                <td><input type="checkbox" name="cat[1]" id="cat[1]"><label for="cat[1]"></label></td>
                <td>
                    <ul>
                        <li><a href="#">s</a></li>
                        <li><a href="#" class="remove"></a></li>
                    </ul>
                </td>
            </tr>
        </table>';

        $this->view->render('Категории товаров', $content);
    }

}