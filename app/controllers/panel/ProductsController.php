<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\models\CategoryModel;


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

        $content = '<div class="fx">
            <h1>Категории товаров</h1>
            <a href="/panel/products/categories/add/" class="btn">Добавить</a>
        </div>';

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




    public function actionsAction(){

        if(!empty($this->urls[3])){
            if($this->urls[3] == 'add') $this->addCategory();
            if($this->urls[3] == 'edit' && !empty($this->urls[4])) $this->addCategory($this->urls[4]);
        }

    }




    private function addCategory($id = null){

        $this->view->styles = ['css/addon/category.css'];

        $title = 'Добавление категории для товаров';

        if($id){

            $id = intval($id);
            $title = 'Редактирование категории для товаров';

            $CategoryModel = new CategoryModel();
            $Category = $CategoryModel->get($id);
            
            echo "<pre>";
            print_r($Category);
            echo "</pre>";
            exit;
        }

        $content = '<h1>'.$title.'</h1>';

        $content .= '<form action method="POST" class="box_">
            <div class="dg dg_auto">
                <div>
                    <label for="" class="rq">Название</label>
                    <input type="text" name="title" autocomplete="off">
                </div>
                <div>
                    <label for="" class="pr">URL категории <span class="q"><i>Для поисковых систем</i></span></label>
                    <input type="text" name="url" placeholder="Только латинские символы без пробелов" autocomplete="off">
                </div>
            </div>
            <p class="title_box hr_d">Meta-данные</p>
            <div class="dg dg_auto">
                <div>
                    <div class="category_icon">
                        <img src="http://nexshop/templates/Web/img/ad.jpg" alt="">
                    </div>
                    <label for="icon" class="upload_files" data-toggle="tooltip" data-placement="top">
                        <input type="file" name="icon" id="icon"> выбрать изображение
                    </label>
                    <div class="clr"></div>
                    <div class="files_preload"></div>
                </div>
                <div>
                    <label for="">Meta Title</label>
                    <input type="text" name="meta[title]" autocomplete="off">
                </div>
                <div>
                    <label for="">Meta Description</label>
                    <input type="text" name="meta[description]" autocomplete="off">
                </div>
            </div>
            <br>
            <p class="title_box hr_d"></p>
            <label for="" class="rq">Описание</label>
            <textarea name="description" rows="5"></textarea>
            <!--<textarea name="description" id="editor" rows="5"></textarea>
            <br>
            <script>
                let editor = new FroalaEditor("#editor", {
                    inlineMode: true,
                    countCharacters: false
                });
            </script>-->
            <input type="submit" class="btn" data-a="CategoryShop" value="Сохранить">
        </form>';

        $this->view->render($title, $content);
    }

}