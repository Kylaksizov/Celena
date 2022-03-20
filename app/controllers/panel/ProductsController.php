<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\models\CategoryModel;
use app\models\ProductModel;
use Exception;


class ProductsController extends PanelController {



    public function indexAction(){

        $content = '<div class="fx">
            <h1>Товары</h1>
            <a href="/panel/products/add/" class="btn">Добавить</a>
        </div>';

        $ProductsModel = new ProductModel();
        $Products = $ProductsModel->getAll();

        if($Products){

            $productsContent = '';

            foreach ($Products["products"] as $row) {

                $status = $row["status"] ? ' checked' : '';

                $productsContent .= '<tr>
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

        } else $productsContent = '<tr class="tc"><td colspan="6">Товаров нет</td></tr>';

        $content .= '<div class="">
            <table>
                <tr>
                    <th>#</th>
                    <th>Изображение<br>категория</th>
                    <th>Наименование</th>
                    <th>Цена</th>
                    <th>Дата публикации</th>
                    <th>Кол-во</th>
                </tr>
                '.$productsContent.'
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

        if(!empty($this->urls[2])){
            if($this->urls[2] == 'add') $this->addProduct();
            if($this->urls[2] == 'edit' && !empty($this->urls[3])) $this->addProduct($this->urls[3]);
        }

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
    private function addProduct($id = null){

        $this->view->styles = ['css/addon/product.css'];

        $title = 'Добавление товара';

        $CategoryModel = new CategoryModel();
        $Categories = $CategoryModel->getAll(true);

        if($id){

            $id = intval($id);
            $ProductModel = new ProductModel();
            $Product = $ProductModel->get($id);

            $title = 'Редактирование товара: <b>'.$Product["title"].'</b>';
        }

        // категории
        $parents = '';
        if(!empty($Categories)){
            foreach ($Categories as $row) {

                $selected = (!empty($Product["cid"]) && $Product["cid"] == $row["cid"]) ? ' selected' : '';
                $parents .= '<option value="'.$row["id"].'"'.$selected.'>'.$row["title"].'</option>';
            }
        }

        $content = '<h1>'.$title.'</h1>';

        $icon = (!empty($Product["icon"]) && file_exists(ROOT . '/uploads/categories/'.$Product["icon"])) ? '<img src="'.CONFIG_SYSTEM["home"].'uploads/categories/'.$Product["icon"].'" alt="">' : '';

        $content .= '<form action method="POST">
            <div class="tabs">
                <ul class="tabs_caption">
                    <li class="active">Товар</li>
                    <li>SEO</li>
                </ul>
                <div class="tabs_content active">
                    <div class="dg dg_auto">
                        <div>
                            <div>
                                <label for="" class="rq">Название</label>
                                <input type="text" name="title" value="'.(!empty($Product["title"])?$Product["title"]:'').'" autocomplete="off">
                            </div>
                            <div>
                                <label for="">Категория</label>
                                <select name="pid" class="multipleSelect" multiple>
                                    '.$parents.'
                                </select>
                            </div>
                            <div>
                                <label for="">Дата публикации</label>
                                <input type="date" name="created" value="'.(!empty($Product["created"])?$Product["created"]:'').'" autocomplete="off">
                            </div>
                        </div>
                        <div>
                            <div>
                                <div>
                                    <label for="" class="rq">Цена</label>
                                    <input type="number" name="price" min="0" value="'.(!empty($Product["price"])?$Product["price"]:'').'" autocomplete="off">
                                </div>
                                <div>
                                    <label for="">Скидка</label>
                                    <input type="text" name="sale" value="'.(!empty($Product["sale"])?$Product["sale"]:'').'" autocomplete="off">
                                </div>
                                <div>
                                    <label for="">На складе</label>
                                    <input type="number" name="stock" value="'.(!empty($Product["stock"])?$Product["stock"]:'').'" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="dg dg-2">
                                <div>
                                    <label for="icon" class="upload_files" data-toggle="tooltip" data-placement="top">
                                        <input type="file" name="icon" id="icon"> выбрать изображение
                                    </label>
                                    <div class="clr"></div>
                                    <div class="files_preload"></div>
                                </div>
                                <div class="tr">
                                    <input type="checkbox" name="status" id="p_status" value="1"><label for="p_status">Активен</label>
                                </div>
                            </div>
                            
                            <!-- изображения товара -->
                            <div id="product_images">
                                <div class="img_item">
                                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQF-Gw0TCrdL9ahAfYKIg4B4pdu86EmvwBVpv-7P5uA0-E_vbNbWwNw94SMbOdaE6BSudo&usqp=CAU" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="title_box hr_d">Описание</p>
                    <div>
                        <textarea name="content" id="product_content" rows="5"></textarea>
                        <br>
                        <script>
                            let editor = new FroalaEditor("#product_content", {
                                inlineMode: true,
                                countCharacters: false
                            });
                        </script>
                    </div>
                </div>
                
                <!-- tab SEO -->
                <div class="tabs_content">
                    <div class="dg dg_auto">
                        <div>
                            <label for="" class="pr">URL <span class="q"><i>Для поисковых систем</i></span></label>
                            <input type="text" name="url" placeholder="Только латинские символы без пробелов" value="'.(!empty($Category["url"])?$Category["url"]:'').'" autocomplete="off">
                        </div>
                        <div>
                            <label for="">Meta Title</label>
                            <input type="text" name="meta[title]" value="'.(!empty($Category["m_title"])?$Category["m_title"]:'').'" autocomplete="off">
                        </div>
                        <div>
                            <label for="">Meta Description</label>
                            <input type="text" name="meta[description]" value="'.(!empty($Category["m_description"])?$Category["m_description"]:'').'" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
            
            <input type="submit" class="btn" data-a="CategoryShop" value="Сохранить">
            
        </form>';

        $this->view->render($title, $content);
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