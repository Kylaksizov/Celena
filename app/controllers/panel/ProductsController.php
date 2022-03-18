<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\models\CategoryModel;
use Exception;


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
        
        $CategoryModel = new CategoryModel();
        $Categories = $CategoryModel->getAll();

        if($Categories){

            $categoryContent = '';

            foreach ($Categories["categories"] as $row) {

                $status = $row["status"] ? ' checked' : '';

                $categoryContent .= '<tr>
                    <td>'.$row["id"].'</td>
                    <!--<td><input type="checkbox" class="ch_box_min" name="cat['.$row["id"].']" id="cat['.$row["id"].']"><label for="cat['.$row["id"].']"></label></td>-->
                    <td><a href="'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/products/categories/edit/'.$row["id"].'/">'.$row["title"].'</a></td>
                    <td>'.(!empty($row["icon"])?'<img src="'.CONFIG_SYSTEM["home"].'uploads/categories/'.$row["icon"].'" alt="">':'').'</td>
                    <td><input type="checkbox" name="status['.$row["id"].']" class="ch_min" id="status['.$row["id"].']"'.$status.'><label for="status['.$row["id"].']"></label></td>
                    <td>
                        <ul class="tc">
                            <li><a href="#" class="remove"></a></li>
                        </ul>
                    </td>
                </tr>';
            }

        } else $categoryContent = '<tr class="tc"><td colspan="6">Категорий нет</td></tr>';

        $content .= '<table>
            <tr>
                <th width="20">ID</th>
                <!--<th width="10"><input type="checkbox" class="ch_box_min" name="" id="cat_sel"><label for="cat_sel"></label></th>-->
                <th>Название</th>
                <th width="30">Иконка</th>
                <th width="20">Статус</th>
                <th width="50">Действия</th>
            </tr>
            '.$categoryContent.'
        </table>';

        $this->view->render('Категории товаров', $content);
    }




    public function actionsAction(){

        if(!empty($this->urls[3])){
            if($this->urls[3] == 'add') $this->addCategory();
            if($this->urls[3] == 'edit' && !empty($this->urls[4])) $this->addCategory($this->urls[4]);
        }

    }





    /**
     * @name добавление и редактирование категории
     * ===========================================
     * @param $id
     * @return void
     * @throws Exception
     */
    private function addCategory($id = null){

        $this->view->styles = ['css/addon/product.css'];

        $title = 'Добавление категории для товаров';

        $CategoryModel = new CategoryModel();
        $Categories = $CategoryModel->getAll(true);

        if($id){

            $id = intval($id);
            $Category = $CategoryModel->get($id);

            $title = 'Редактирование категории для товаров: <b>'.$Category["title"].'</b>';
        }

        // родительская категория
        $parents = '<option value="">-- не выбрано --</option>';
        if(!empty($Categories)){
            foreach ($Categories as $row) {

                if(!empty($Category["title"]) && $Category["title"] == $row["title"]) continue;

                $selected = (!empty($Category["pid"]) && $Category["pid"] == $row["id"]) ? ' selected' : '';
                $parents .= '<option value="'.$row["id"].'"'.$selected.'>'.$row["title"].'</option>';
            }
        }

        $content = '<h1>'.$title.'</h1>';

        $icon = (!empty($Category["icon"]) && file_exists(ROOT . '/uploads/categories/'.$Category["icon"])) ? '<img src="'.CONFIG_SYSTEM["home"].'uploads/categories/'.$Category["icon"].'" alt="">' : '';

        $content .= '<form action method="POST" class="box_">
            <div class="dg dg_auto">
                <div>
                    <label for="" class="rq">Название</label>
                    <input type="text" name="title" value="'.(!empty($Category["title"])?$Category["title"]:'').'" autocomplete="off">
                </div>
                <div>
                    <label for="" class="pr">URL категории <span class="q"><i>Для поисковых систем</i></span></label>
                    <input type="text" name="url" placeholder="Только латинские символы без пробелов" value="'.(!empty($Category["url"])?$Category["url"]:'').'" autocomplete="off">
                </div>
            </div>
            <p class="title_box hr_d">Meta-данные</p>
            <div class="dg dg_auto">
                <div>
                    <label for="">Meta Title</label>
                    <input type="text" name="meta[title]" value="'.(!empty($Category["m_title"])?$Category["m_title"]:'').'" autocomplete="off">
                </div>
                <div>
                    <label for="">Meta Description</label>
                    <input type="text" name="meta[description]" value="'.(!empty($Category["m_description"])?$Category["m_description"]:'').'" autocomplete="off">
                </div>
            </div>
            <br>
            <p class="title_box hr_d">Дополнительно</p>
            <div class="dg dg_auto">
                <div>
                    <div class="category_icon">
                        <!-- тут картинка -->
                        '.$icon.'
                    </div>
                    <label for="icon" class="upload_files" data-toggle="tooltip" data-placement="top">
                        <input type="file" name="icon" id="icon"> выбрать изображение
                    </label>
                    <div class="clr"></div>
                    <div class="files_preload"></div>
                </div>
                <div>
                    <label for="">Подкатегория</label>
                    <select name="pid">
                        '.$parents.'
                    </select>
                    <label for="description">Описание</label>
                    <textarea name="description" rows="5" id="description">'.(!empty($Category["cont"])?$Category["cont"]:'').'</textarea>
                    <!--<textarea name="description" id="editor" rows="5"></textarea>
                    <br>
                    <script>
                        let editor = new FroalaEditor("#editor", {
                            inlineMode: true,
                            countCharacters: false
                        });
                    </script>-->
                </div>
            </div>
            <input type="submit" class="btn" data-a="CategoryShop" value="Сохранить">
        </form>';

        $this->view->render($title, $content);
    }

}