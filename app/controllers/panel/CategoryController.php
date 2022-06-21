<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\models\panel\CategoryModel;
use Exception;


class CategoryController extends PanelController {


    /**
     * @name товары
     * ============
     * @return void
     * @throws Exception
     */
    public function indexAction(){

        $this->view->styles = ['css/category.css'];
        $this->view->scripts = ['js/category.js'];

        $content = '<div class="fx">
            <h1>Категории</h1>
            <a href="/'.CONFIG_SYSTEM["panel"].'/category/add/" class="btn">Добавить</a>
        </div>';

        $CategoryModel = new CategoryModel();
        $Categories = $CategoryModel->getAll();

        if($Categories){

            $categoryContent = '';

            foreach ($Categories["categories"] as $row) {

                $status = $row["status"] ? ' checked' : '';

                $categoryContent .= '<tr>
                    <td>'.$row["id"].'</td>
                    <td><a href="" target="_blank" class="ico_see" title="В разработке"></a></td>
                    <td>
                        <a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/category/edit/'.$row["id"].'/">'.(!empty($row["icon"])?'<img src="//'.CONFIG_SYSTEM["home"].'/uploads/categories/'.$row["icon"].'" alt="">':'<span class="no_image"></span>').'</a>
                    </td>
                    <td><a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/category/edit/'.$row["id"].'/">'.$row["title"].'</a></td>
                    <td><input type="checkbox" name="status['.$row["id"].']" class="ch_min status_category" data-id="'.$row["id"].'" id="status_'.$row["id"].'"'.$status.'><label for="status_'.$row["id"].'"></label></td>
                    <td>
                        <ul class="tc">
                            <li><a href="#" class="remove" data-a="Category:deleteCategory='.$row["id"].'"></a></li>
                        </ul>
                    </td>
                </tr>';
            }

        } else $categoryContent = '<tr class="tc"><td colspan="6">Категорий нет</td></tr>';

        $content .= '<table>
            <tr>
                <th width="20">ID</th>
                <th width="10"><span class="ico_see"></span></th>
                <th width="30">Иконка</th>
                <!--<th width="10"><input type="checkbox" class="ch_box_min" name="" id="cat_sel"><label for="cat_sel"></label></th>-->
                <th>Название</th>
                <th width="20">Статус</th>
                <th width="50">Действия</th>
            </tr>
            '.$categoryContent.'
        </table>';

        $this->view->render('Категории', $content);
    }





    /**
     * @name добавление и редактирование категории
     * ===========================================
     * @return void
     * @throws Exception
     */
    public function addAction(){

        $this->view->styles = ['css/category.css'];
        $this->view->plugins = ['select2'];

        $title = 'Добавление категории';

        $CategoryModel = new CategoryModel();
        $Categories = $CategoryModel->getAll(true);

        if(!empty($this->urls[3])){

            $id = intval($this->urls[3]);
            $Category = $CategoryModel->get($id);

            $title = 'Редактирование категории: <b>'.$Category["title"].'</b>';
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

        $icon = (!empty($Category["icon"]) && file_exists(ROOT . '/uploads/categories/'.$Category["icon"])) ? '<img src="//'.CONFIG_SYSTEM["home"].'/uploads/categories/'.$Category["icon"].'" alt="">' : '<span class="no_image"></span>';

        $categoryStatus = ' checked';
        if(!empty($Category)){
            if(empty($Category["status"])) $categoryStatus = '';
        }

        $content .= '<form action method="POST">
            <div class="tabs">
                <ul class="tabs_caption">
                    <li class="active">Основа</li>
                    <li>Дополнительно</li>
                </ul>
                <div class="tabs_content active">
                    <div class="dg dg_auto">
                        <div>
                            <label for="" class="rq">Название:</label>
                            <input type="text" name="title" value="'.(!empty($Category["title"])?$Category["title"]:'').'" autocomplete="off">
                        </div>
                        <div>
                            <label for="" class="pr">URL категории: <span class="q"><i>Для поисковых систем</i></span></label>
                            <input type="text" name="url" placeholder="Только латинские символы без пробелов" value="'.(!empty($Category["url"])?$Category["url"]:'').'" autocomplete="off">
                        </div>
                    </div>
                    <p class="title_box hr_d">Meta-данные</p>
                    <div class="dg dg_auto">
                        <div>
                            <label for="">Meta Title:</label>
                            <input type="text" name="meta[title]" value="'.(!empty($Category["m_title"])?$Category["m_title"]:'').'" autocomplete="off">
                        </div>
                        <div>
                            <label for="">Meta Description:</label>
                            <input type="text" name="meta[description]" value="'.(!empty($Category["m_description"])?$Category["m_description"]:'').'" autocomplete="off">
                        </div>
                    </div>
                    <input type="checkbox" name="status" class="ch_min status_news" id="category_status"'.$categoryStatus.'><label for="category_status">Активна</label>
                    <br>
                    <p class="title_box hr_d">Дополнительно</p>
                    <div class="dg dg_auto">
                        <div>
                            <div class="category_icon">
                                <!-- тут картинка -->
                                '.$icon.'
                            </div>
                            <label for="icon" class="upload_files">
                                <input type="file" name="icon" id="icon"> выбрать изображение
                            </label>
                        </div>
                        <div>
                            <label for="">Подкатегория:</label>
                            <select name="pid">
                                '.$parents.'
                            </select>
                            <label for="description">Описание:</label>
                            <textarea name="description" rows="5" id="description">'.(!empty($Category["content"])?$Category["content"]:'').'</textarea>
                        </div>
                    </div>
                </div>
                <div class="tabs_content">
                    <div class="dg dg_auto">
                        <div>
                            <label for="" class="pr">Для плагинов (в разработке): <span class="q" title="Если пусто, то категории будут "></label>
                            <select name="plugins[]" class="multipleSelect" multiple>
                                <option value="1">Celena Shop</option>
                                <option value="2">Example</option>
                            </select>
                        </div>
                        <div>
                            <label for="">Шаблон анонса:</label>
                            <input type="text" name="tpl_min" value="'.(!empty($Category["tpl_min"])?$Category["tpl_min"]:'').'" autocomplete="off">
                        </div>
                        <div>
                            <label for="">Шаблон полной новости:</label>
                            <input type="text" name="tpl_max" value="'.(!empty($Category["tpl_max"])?$Category["tpl_max"]:'').'" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <input type="submit" class="btn" data-a="Category" value="Сохранить">
        </form>';

        $this->view->render($title, $content);
    }

}