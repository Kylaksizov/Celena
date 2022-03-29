<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\models\CategoryModel;
use app\models\ProductModel;
use Exception;


class ProductsController extends PanelController {



    public function indexAction(){

        $this->view->styles = ['css/addon/product.css'];
        $this->view->scripts = ['js/addon/product.js'];

        $content = '<div class="fx">
            <h1>Товары</h1>
            <a href="/panel/products/add/" class="btn">Добавить</a>
        </div>';

        $ProductsModel = new ProductModel();
        $CategoryModel = new CategoryModel();
        $Products = $ProductsModel->getAll();
        $Categories = $CategoryModel->getAll(true);

        if($Products){

            $productsContent = '';

            foreach ($Products["products"] as $row) {

                $img = !empty($row["src"]) ? '<img src="'.CONFIG_SYSTEM["home"].'uploads/products/'.$row["src"].'" alt="">' : '<span class="no_image"></span>';

                $category = '';
                if(!empty($row["category"])){
                    $categoryArr = explode(",", $row["category"]);
                    $category = !empty($Categories[$categoryArr[0]]["title"]) ? $Categories[$categoryArr[0]]["title"].'...' : '';
                }

                $stock = !empty($row["stock"]) ? $row["stock"] : '<span class="infinity">∞</span>';

                $status = $row["status"] ? ' checked' : '';

                $productsContent .= '<tr>
                    <td>'.$row["id"].'</td>
                    <td>
                        '.$img.'
                    </td>
                    <td>
                        <a href="'.CONFIG_SYSTEM["home"].CONFIG_SYSTEM["panel"].'/products/edit/'.$row["id"].'/">'.$row["title"].'</a>
                        <span class="br_min">'.$category.'</span>
                    </td>
                    <td><b>'.$row["price"].' $</b></td>
                    <td class="fs12">'.date("d.m.Y H:i", $row["created"]).'</td>
                    <td>'.$stock.'</td>
                    <td><input type="checkbox" name="status['.$row["id"].']" class="ch_min status_product" data-id="'.$row["id"].'"  id="status_'.$row["id"].'"'.$status.'><label for="status_'.$row["id"].'"></label></td>
                    <td>
                        <ul class="tc">
                            <li><a href="#" class="remove" data-a="ProductShop:deleteProduct='.$row["id"].'"></a></li>
                        </ul>
                    </td>
                </tr>';
            }

        } else $productsContent = '<tr class="tc"><td colspan="6">Товаров нет</td></tr>';

        $content .= '<div class="">
            <table>
                <tr>
                    <th width="20">#</th>
                    <th width="100">Изображение</th>
                    <th>Наименование<span class="br_min">категория</span></th>
                    <th width="100">Цена</th>
                    <th width="130">Дата публикации</th>
                    <th width="70">Кол-во</th>
                    <th width="30">Статус</th>
                    <th width="50"></th>
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
                    <td><a href="'.CONFIG_SYSTEM["home"].CONFIG_SYSTEM["panel"].'/products/categories/edit/'.$row["id"].'/">'.$row["title"].'</a></td>
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




    public function propertiesAction(){

        $content = '<div class="fx">
            <h1>Свойства товаров</h1>
            <a href="/panel/products/properties/add/" class="btn">Добавить</a>
        </div>';

        $PropertiesModel = new ProductModel();
        $Properties = $PropertiesModel->getPropertiesAll();

        if($Properties){

            $propertiesContent = '';

            foreach ($Properties["properties"] as $row) {

                //$status = $row["status"] ? ' checked' : '';

                $propertiesContent .= '<tr>
                    <td>'.$row["id"].'</td>
                    <!--<td><input type="checkbox" class="ch_box_min" name="cat['.$row["id"].']" id="cat['.$row["id"].']"><label for="cat['.$row["id"].']"></label></td>-->
                    <td><a href="'.CONFIG_SYSTEM["home"].CONFIG_SYSTEM["panel"].'/products/categories/edit/'.$row["id"].'/">'.$row["title"].'</a></td>
                    <td>'.(!empty($row["icon"])?'<img src="'.CONFIG_SYSTEM["home"].'uploads/categories/'.$row["icon"].'" alt="">':'').'</td>
                    <td><input type="checkbox" name="status['.$row["id"].']" class="ch_min" id="status['.$row["id"].']"'.$status.'><label for="status['.$row["id"].']"></label></td>
                    <td>
                        <ul class="tc">
                            <li><a href="#" class="remove"></a></li>
                        </ul>
                    </td>
                </tr>';
            }

        } else $propertiesContent = '<tr class="tc"><td colspan="6">Свойств нет</td></tr>';

        $content .= '<table>
            <tr>
                <th width="20">ID</th>
                <!--<th width="10"><input type="checkbox" class="ch_box_min" name="" id="cat_sel"><label for="cat_sel"></label></th>-->
                <th>Название</th>
                <th width="200">Тип</th>
                <th width="20">Статус</th>
                <th width="50">Действия</th>
            </tr>
            '.$propertiesContent.'
        </table>';

        $this->view->render('Категории товаров', $content);
    }





    /**
     * @name добавление и редактирование категории
     * ===========================================
     * @return void
     * @throws Exception
     */
    public function addProductAction(){

        $this->view->styles = ['css/addon/product.css'];
        $this->view->scripts = ['js/addon/product.js'];
        $this->view->plugins = ['select2', 'datepicker', 'fancybox'];

        $title = $h1 = 'Добавление товара';

        $ProductModel = new ProductModel();
        $CategoryModel = new CategoryModel();

        $Categories = $CategoryModel->getAll(true);
        $Properties = $ProductModel->getPropertiesAll(true);



        // Property
        $propertiesSelect = '<select name="" id="propertiesAll">
            <option>-- выбрать свойство --</option>';
        $propsOptions = [];
        if(!empty($Properties)){

            foreach ($Properties as $propertyTitle => $propertyRow) {

                $propertiesSelect .= '<option value="'.$propertyRow[0]["id"].'" data-property=\''.json_encode($propertyRow, JSON_UNESCAPED_UNICODE).'\'>'.$propertyTitle.'</option>';

                foreach ($propertyRow as $propItem) {

                    if(empty($propsOptions[$propertyTitle]))
                        $propsOptions[$propertyTitle] = '
                                            <option value="'.$propItem["vid"].'">'.$propItem["val"].'</option>';
                    else
                        $propsOptions[$propertyTitle] .= '
                                            <option value="'.$propItem["vid"].'">'.$propItem["val"].'</option>';
                }
            }
        }
        $propertiesSelect .= '</select> <a href="#" class="btn" id="addPropertiy">Добавить</a>';
        // Property END



        if(!empty($this->urls[3])){
            
            $id = intval($this->urls[3]);
            $Product = $ProductModel->get($id);

            $title = 'Редактирование товара';
            $h1 = 'Редактирование товара: <b>'.$Product["product"]["title"].'</b>';
        }

        // категории
        $categoryOptions = '';
        if(!empty($Categories)){
            $categoriesIsset = !empty($Product["product"]["category"]) ? explode(",", $Product["product"]["category"]) : [];
            foreach ($Categories as $row) {

                $selected = in_array($row["id"], $categoriesIsset) ? ' selected' : '';
                $categoryOptions .= '<option value="'.$row["id"].'"'.$selected.'>'.$row["title"].'</option>';
            }
        }
        
        // свойства
        $properties = '';
        if(!empty($Product["props"])){

            $propId = $Product["props"][0]["id"];

            foreach ($Product["props"] as $propKey => $row) {

                if($propKey == 0 || $propId != $row["id"]){

                    $closeProp = true;

                    $properties .= '<div class="prop">
                            <div class="prop_main" data-prop-id="'.$row["id"].'">
                                <div class="pr">
                                    <input type="hidden" name="prop['.$row["id"].'][pp_id][]" class="pp_id" value="'.$row["pp_id"].'">
                                    <label for="">'.$row["title"].': <a href="#" class="del_property"></a></label>
                                    <select class="property_name" name="prop['.$row["id"].'][id][]" data-prop-sel="'.$row["id_prop"].'">
                                        <option value="">-- не выбрано --</option>'.$propsOptions[$row["title"]].'
                                    </select>
                                </div>
                                <div>
                                    <label for="">Артикул</label>
                                    <input type="text" name="prop['.$row["id"].'][vendor][]" value="'.$row["vendor"].'" placeholder="Артикул">
                                </div>
                                <div>
                                    <label for="">Цена</label>
                                    <input type="number" name="prop['.$row["id"].'][price][]" min="0" step=".1" value="'.$row["price"].'" placeholder="Цена">
                                </div>
                                <div>
                                    <label for="">Кол-во</label>
                                    <input type="number" name="prop['.$row["id"].'][stock][]" min="0" step="1" value="'.$row["stock"].'" placeholder="Кол-во">
                                </div>
                                <a href="#" class="add_sub_property">+</a>
                            </div>
                            <div class="prop_subs">';

                } else{

                    $closeProp = false;

                            $properties .= '<div class="prop_sub">
                                    <div class="pr">
                                        <input type="hidden" name="prop['.$row["id"].'][pp_id][]" class="pp_id" value="'.$row["pp_id"].'">
                                        <select class="property_name" name="prop['.$row["id"].'][id][]" data-prop-sel="'.$row["id_prop"].'">
                                            <option value="">-- не выбрано --</option>'.$propsOptions[$row["title"]].'
                                        </select>
                                    </div>
                                    <input type="text" name="prop['.$row["id"].'][vendor][]" value="'.$row["vendor"].'" placeholder="Артикул">
                                    <input type="number" name="prop['.$row["id"].'][price][]" min="0" step=".1" value="'.$row["price"].'" placeholder="Цена">
                                    <input type="number" name="prop['.$row["id"].'][stock][]" min="0" step="1" value="'.$row["stock"].'" placeholder="Кол-во">
                                    <a href="#" class="remove_sub_property" data-a="ProductShop:deleteProperty='.$row["pp_id"].'">-</a>
                                </div>';
                }

                if(!$closeProp && (!empty($Product["props"][$propKey+1]["id"]) && $propId != $Product["props"][$propKey+1]["id"]) || count($Product["props"])-1 == $propKey)
                            $properties .= '
                            </div>
                        </div>';

                $propId = $row["id"];
            }
        }


        // изображения товара
        $images = '';
        if(!empty($Product["images"])){
            foreach ($Product["images"] as $image) {
                $thumb = !empty(CONFIG_SYSTEM["thumb"]) ? CONFIG_SYSTEM["home"].'uploads/products/'.str_replace('/', '/thumbs/', $image["src"]) : CONFIG_SYSTEM["home"].'uploads/products/'.$image["src"];
                $images .= '<div class="img_item">
                    <a href="'.CONFIG_SYSTEM["home"].'uploads/products/'.$image["src"].'" data-fancybox="gallery" data-caption="'.$image["alt"].'"><img src="'.$thumb.'" alt=""></a>
                    <a href="#editPhoto" class="edit_image open_modal" data-img-id="'.$image["id"].'"></a>
                    <a href="#" class="delete_image" data-a="ProductShop:deleteImage='.$image["id"].'&link='.$image["src"].'"></a>
                </div>';
            }
        }

        $content = '<h1>'.$h1.'</h1>';

        $content .= '<form action method="POST">
            <div class="tabs">
                <ul class="tabs_caption">
                    <li class="active">Товар</li>
                    <li>SEO</li>
                    <li>Свойства</li>
                </ul>
                <div class="tabs_content active">
                    <div class="dg dg_auto">
                        <div>
                            <div>
                                <label for="" class="rq">Название</label>
                                <input type="text" name="title" value="'.(!empty($Product["product"]["title"])?$Product["product"]["title"]:'').'" autocomplete="off">
                            </div>
                            <div>
                                <label for="">Артикул</label>
                                <input type="text" name="vendor" value="'.(!empty($Product["product"]["vendor"])?$Product["product"]["vendor"]:'').'" autocomplete="off">
                            </div>
                            <div>
                                <label for="">Категория</label>
                                <select name="category[]" id="categoryOptions" class="multipleSelect" multiple>
                                    '.$categoryOptions.'
                                </select>
                            </div>
                            <div>
                                <label for="">Дата публикации</label>
                                <input type="text" name="created" class="dateTime" value="'.(!empty($Product["product"]["created"])?date("d.m.Y H:i", $Product["product"]["created"]):'').'" autocomplete="off">
                            </div>
                        </div>
                        <div>
                            <div>
                                <div>
                                    <label for="" class="rq">Цена</label>
                                    <input type="number" name="price" min="0" value="'.(!empty($Product["product"]["price"])?$Product["product"]["price"]:'').'" autocomplete="off">
                                </div>
                                <div>
                                    <label for="">Скидка</label>
                                    <input type="text" name="sale" value="'.(!empty($Product["product"]["sale"])?$Product["product"]["sale"]:'').'" autocomplete="off">
                                </div>
                                <div>
                                    <label for="">На складе</label>
                                    <input type="number" name="stock" value="'.(!empty($Product["product"]["stock"])?$Product["product"]["stock"]:'').'" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>
                                <div class="tr">
                                    <input type="checkbox" name="status" id="p_status"'.(!empty($Product["product"]["status"])?' checked':'').' value="1"><label for="p_status">Активен</label>
                                </div>
                                <div>
                                    <label for="p_images" class="upload_files">
                                        <input type="file" name="images[]" id="p_images" multiple> выбрать изображения
                                    </label>
                                </div>
                            </div>
                            
                            <!-- изображения товара -->
                            <div id="product_images">
                                '.$images.'
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
                            <input type="text" name="url" placeholder="Только латинские символы без пробелов" value="'.(!empty($Product["product"]["url"])?$Product["product"]["url"]:'').'" autocomplete="off">
                        </div>
                        <div>
                            <label for="">Meta Title</label>
                            <input type="text" name="meta[title]" value="'.(!empty($Product["product"]["m_title"])?$Product["product"]["m_title"]:'').'" autocomplete="off">
                        </div>
                    </div>
                    <div class="dg dg_auto">
                        <div>
                            <label for="">Meta Description</label>
                            <textarea name="meta[description]" rows="3">'.(!empty($Product["product"]["m_description"])?$Product["product"]["m_description"]:'').'</textarea>
                        </div>
                    </div>
                </div>
                
                
                <!-- tab Свойства -->
                <div class="tabs_content">
                    <div class="properties_actions">
                        '.$propertiesSelect.'
                    </div>
                    <div id="properties_product">
                        '.$properties.'
                    </div>
                </div>
                
            </div>
            
            <input type="submit" class="btn" data-a="ProductShop" value="Сохранить">
            
        </form>';


        $content .= '<form action method="POST" class="modal_big" id="editPhoto">
            <div class="modal_title">Редактор фото</div>
            <div class="modal_body dg dg-2" id="photoEditor"></div>
            <div class="fx jc_c">
                <input type="submit" class="btn" data-a="ProductShop" value="Сохранить">&nbsp;&nbsp;
                <a href="#" class="btn cancel">Отмена</a>
            </div>
            <a href="#" class="close"></a>
        </form>';

        $this->view->render($title, $content);
    }





    /**
     * @name добавление и редактирование категории
     * ===========================================
     * @return void
     * @throws Exception
     */
    public function addCategoryAction(){

        $this->view->styles = ['css/addon/product.css'];

        $title = 'Добавление категории для товаров';

        $CategoryModel = new CategoryModel();
        $Categories = $CategoryModel->getAll(true);

        if($this->urls[4]){

            $id = intval($this->urls[4]);
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
                    <label for="icon" class="upload_files">
                        <input type="file" name="icon" id="icon"> выбрать изображение
                    </label>
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