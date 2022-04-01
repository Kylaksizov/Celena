<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\models\panel\BrandModel;
use app\models\panel\CategoryModel;
use app\models\panel\ProductModel;
use app\models\panel\PropertyModel;
use Exception;


class ProductsController extends PanelController {


    /**
     * @name товары
     * ============
     * @return void
     * @throws Exception
     */
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




    /**
     * @name добавление и редактирование товара
     * ========================================
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

                if(!empty($propertyRow[0]["sep"]))
                    $propsOptions[$propertyTitle] = '<option value="sep" class="sep_field">ПРОИЗВОЛЬНОЕ ПОЛЕ</option>';
                
                $propertiesSelect .= '<option value="'.$propertyRow[0]["id"].'" data-category="'.$propertyRow[0]["cid"].'" data-display="'.$propertyRow[0]["option"].'" data-property=\''.json_encode($propertyRow, JSON_UNESCAPED_UNICODE).'\'>'.$propertyTitle.'</option>';

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
            $Brands = $Product["brands"];

            $title = $Product["product"]["title"];
            $h1 = 'Редактирование товара: <b>'.$Product["product"]["title"].'</b>';
            
        } else{
            
            $BrandModel = new BrandModel();
            $Brands = $BrandModel->getAll(true);
        }

        // категории
        $categoriesIsset = !empty($Product["product"]["category"]) ? explode(",", $Product["product"]["category"]) : [];
        $categoryOptions = '';
        if(!empty($Categories)){
            foreach ($Categories as $row) {

                $selected = in_array($row["id"], $categoriesIsset) ? ' selected' : '';
                $categoryOptions .= '<option value="'.$row["id"].'"'.$selected.'>'.$row["title"].'</option>';
            }
        }

        // бренды
        $brandsOptions = '<option value="">-- не выбрано --</option>';
        if(!empty($Brands)){
            foreach ($Brands as $row) {

                # TODO потом сделать
                $bgOption = /*(!empty($row["icon"])) ? ' style=\'background-image:url("'.CONFIG_SYSTEM["home"].'uploads/brands/'.$row["icon"].'");\'' : */'';

                $selected = (!empty($Product["product"]["brand"]) && $row["id"] == $Product["product"]["brand"]) ? ' selected' : '';
                $brandsOptions .= '<option value="'.$row["id"].'" data-brand-categories="'.$row["categories"].'"'.$bgOption.$selected.'>'.$row["name"].'</option>';
            }
        }

        // свойства
        $properties = '';
        if(!empty($Product["props"])){

            $propId = $Product["props"][0]["id"];

            foreach ($Product["props"] as $propKey => $row) {

                if($propId != $row["id"])
                    $properties .= '</div>
                        </div>';

                if($propKey == 0 || (!empty($row["id"]) && $propId != $row["id"])){

                    //$closeProp = true;

                    if($row["sep"] == ''){
                        $element = '<select class="property_name" name="prop['.$row["id"].'][id][]" data-prop-sel="'.$row["id_pv"].'">
                                        <option value="">-- не выбрано --</option>'.$propsOptions[$row["title"]].'
                                    </select>';
                    } else $element = '<input type="text" name="prop['.$row["id"].'][id][]" class="property_name" value="'.$row["sep"].'"><span class="callback_select"></span>';

                    $properties .= '
                        <div class="prop" data-prop-id="'.$row["id"].'">
                            <div class="prop_main" data-prop-id="'.$row["id"].'">
                                <div class="pr">
                                    <input type="hidden" name="prop['.$row["id"].'][pp_id][]" class="pp_id" value="'.$row["pp_id"].'">
                                    <label for="">'.$row["title"].': <a href="#" class="del_property"></a></label>
                                    '.$element.'
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
                                <a href="#" class="remove_sub_property">-</a>
                            </div>
                            <div class="prop_subs">';

                } else{

                    //$closeProp = false;

                    if($row["sep"] == ''){
                        $element = '<select class="property_name" name="prop['.$row["id"].'][id][]" data-prop-sel="'.$row["id_pv"].'">
                                            <option value="">-- не выбрано --</option>'.$propsOptions[$row["title"]].'
                                        </select>';
                    } else $element = '<input type="text" name="prop['.$row["id"].'][id][]" class="property_name" value="'.$row["sep"].'"><span class="callback_select"></span>';

                    $properties .= '
                                <div class="prop_sub">
                                    <div class="pr">
                                        <input type="hidden" name="prop['.$row["id"].'][pp_id][]" class="pp_id" value="'.$row["pp_id"].'">
                                        '.$element.'
                                    </div>
                                    <input type="text" name="prop['.$row["id"].'][vendor][]" value="'.$row["vendor"].'" placeholder="Артикул">
                                    <input type="number" name="prop['.$row["id"].'][price][]" min="0" step=".1" value="'.$row["price"].'" placeholder="Цена">
                                    <input type="number" name="prop['.$row["id"].'][stock][]" min="0" step="1" value="'.$row["stock"].'" placeholder="Кол-во">
                                    <a href="#" class="add_sub_property">+</a>
                                    <a href="#" class="remove_sub_property" data-a="ProductShop:deleteProperty='.$row["pp_id"].'">-</a>
                                </div>';
                }

                #TODO чето запутался я !!!!!!!!!!!!!!!!!!!!!
                /*if((!$closeProp && (!empty($Product["props"][$propKey+1]["id"]) && $propId != $Product["props"][$propKey+1]["id"]) || count($Product["props"])-1 == $propKey) || (!empty($row["id"]) && $propId != $row["id"]))
                    $properties .= '
                            </div>
                        </div>';*/

                if(count($Product["props"])-1 == $propKey)
                    $properties .= '</div>
                        </div>';

                $propId = $row["id"];
            }
        }

        /*print_r($properties);
        exit;*/


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
                                <label for="">Бренд</label>
                                <select name="brand" id="productBrand">
                                    '.$brandsOptions.'
                                </select>
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
                                <div>
                                    <label for="">Дата публикации</label>
                                    <input type="text" name="created" class="dateTime" value="'.(!empty($Product["product"]["created"])?date("d.m.Y H:i", $Product["product"]["created"]):'').'" autocomplete="off">
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
                
                <input type="hidden" name="product" value="1">
                
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
     * @name категории товара
     * ======================
     * @return void
     * @throws Exception
     */
    public function categoriesAction(){

        $this->view->styles = ['css/addon/product.css'];
        $this->view->scripts = ['js/addon/product.js'];

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
                    <td>
                        <a href="'.CONFIG_SYSTEM["home"].CONFIG_SYSTEM["panel"].'/products/categories/edit/'.$row["id"].'/">'.(!empty($row["icon"])?'<img src="'.CONFIG_SYSTEM["home"].'uploads/categories/'.$row["icon"].'" alt="">':'<span class="no_image"></span>').'</a>
                    </td>
                    <td><a href="'.CONFIG_SYSTEM["home"].CONFIG_SYSTEM["panel"].'/products/categories/edit/'.$row["id"].'/">'.$row["title"].'</a></td>
                    <td><input type="checkbox" name="status['.$row["id"].']" class="ch_min status_category" data-id="'.$row["id"].'" id="status_'.$row["id"].'"'.$status.'><label for="status_'.$row["id"].'"></label></td>
                    <td>
                        <ul class="tc">
                            <li><a href="#" class="remove" data-a="CategoryShop:deleteCategory='.$row["id"].'"></a></li>
                        </ul>
                    </td>
                </tr>';
            }

        } else $categoryContent = '<tr class="tc"><td colspan="6">Категорий нет</td></tr>';

        $content .= '<table>
            <tr>
                <th width="20">ID</th>
                <th width="30">Иконка</th>
                <!--<th width="10"><input type="checkbox" class="ch_box_min" name="" id="cat_sel"><label for="cat_sel"></label></th>-->
                <th>Название</th>
                <th width="20">Статус</th>
                <th width="50">Действия</th>
            </tr>
            '.$categoryContent.'
        </table>';

        $this->view->render('Категории товаров', $content);
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

        if(!empty($this->urls[4])){

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

        $icon = (!empty($Category["icon"]) && file_exists(ROOT . '/uploads/categories/'.$Category["icon"])) ? '<img src="'.CONFIG_SYSTEM["home"].'uploads/categories/'.$Category["icon"].'" alt="">' : '<span class="no_image"></span>';

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
            <input type="checkbox" name="status" class="ch_min status_product" id="category_status"'.(!empty($Category["status"])?' checked':'').'><label for="category_status">Активна</label>
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



    /**
     * @name категории товара
     * ======================
     * @return void
     * @throws Exception
     */
    public function brandsAction(){

        $this->view->styles = ['css/addon/product.css'];
        $this->view->scripts = ['js/addon/product.js'];

        $content = '<div class="fx">
            <h1>Бренды</h1>
            <a href="/panel/products/brands/add/" class="btn">Добавить</a>
        </div>';

        $BrandModel = new BrandModel();
        $Brands = $BrandModel->getAll();

        if($Brands){

            $categoryContent = '';

            foreach ($Brands["brands"] as $row) {

                $categoryContent .= '<tr>
                    <td>'.$row["id"].'</td>
                    <td>
                        <a href="'.CONFIG_SYSTEM["home"].CONFIG_SYSTEM["panel"].'/products/brands/edit/'.$row["id"].'/">'.(!empty($row["icon"])?'<img src="'.CONFIG_SYSTEM["home"].'uploads/brands/'.$row["icon"].'" alt="">':'<span class="no_image"></span>').'</a>
                    </td>
                    <td><a href="'.CONFIG_SYSTEM["home"].CONFIG_SYSTEM["panel"].'/products/brands/edit/'.$row["id"].'/">'.$row["name"].'</a></td>
                    <td>
                        <ul class="tc">
                            <li><a href="#" class="remove" data-a="Brand:deleteBrand='.$row["id"].'"></a></li>
                        </ul>
                    </td>
                </tr>';
            }

        } else $categoryContent = '<tr class="tc"><td colspan="6">Брендов нет</td></tr>';

        $content .= '<table>
            <tr>
                <th width="20">ID</th>
                <th width="30">Иконка</th>
                <th>Название</th>
                <th width="50">Действия</th>
            </tr>
            '.$categoryContent.'
        </table>';

        $this->view->render('Бренды', $content);
    }





    /**
     * @name добавление и редактирование категории
     * ===========================================
     * @return void
     * @throws Exception
     */
    public function addBrandAction(){

        $this->view->styles = ['css/addon/product.css'];
        $this->view->plugins = ['select2'];

        $title = 'Добавление бренда';

        $CategoryModel = new CategoryModel();
        $Categories = $CategoryModel->getAll(true);

        if(!empty($this->urls[4])){

            $id = intval($this->urls[4]);
            $BrandModel = new BrandModel();
            $Brand = $BrandModel->get($id);

            $title = $Brand["name"];
        }

        // родительская категория
        $categoryOptions = '';
        if(!empty($Categories)){
            $categoriesIsset = !empty($Brand["categories"]) ? explode(",", $Brand["categories"]) : [];
            foreach ($Categories as $row) {

                $selected = in_array($row["id"], $categoriesIsset) ? ' selected' : '';
                $categoryOptions .= '<option value="'.$row["id"].'"'.$selected.'>'.$row["title"].'</option>';
            }
        }

        $content = '<h1>'.$title.'</h1>';

        $icon = (!empty($Brand["icon"]) && file_exists(ROOT . '/uploads/brands/'.$Brand["icon"])) ? '<img src="'.CONFIG_SYSTEM["home"].'uploads/brands/'.$Brand["icon"].'" alt="">' : '<span class="no_image"></span>';

        $content .= '<form action method="POST" class="box_">
            <div class="dg dg_auto">
                <div>
                    <div class="brand_icon">
                        '.$icon.'
                    </div>
                    <label for="icon" class="upload_files">
                        <input type="file" name="icon" id="icon"> выбрать изображение
                    </label>
                </div>
                <div>
                    <label for="" class="rq">Название</label>
                    <input type="text" name="name" value="'.(!empty($Brand["name"])?$Brand["name"]:'').'" autocomplete="off">
                    <br>
                    <label for="">URL</label>
                    <input type="text" name="url" value="'.(!empty($Brand["url"])?$Brand["url"]:'').'" autocomplete="off">
                    <br>
                    <label for="">Привязка к категориям</label>
                    <select name="categories[]" class="multipleSelect" multiple>
                        '.$categoryOptions.'
                    </select>
                </div>
            </div>
            <input type="submit" class="btn" data-a="Brand" value="Сохранить">
        </form>';

        $this->view->render($title, $content);
    }


    /**
     * @name свойства товара
     * =====================
     * @return void
     * @throws Exception
     */
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

                $propertiesContent .= '<tr>
                    <td>'.$row["id"].'</td>
                    <td><a href="'.CONFIG_SYSTEM["home"].CONFIG_SYSTEM["panel"].'/products/properties/edit/'.$row["id"].'/">'.$row["title"].'</a></td>
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
                <th width="50">Действия</th>
            </tr>
            '.$propertiesContent.'
        </table>';

        $this->view->render('Категории товаров', $content);
    }





    /**
     * @name добавление свойства
     * =========================
     * @return void
     * @throws Exception
     */
    public function addPropertyAction(){

        $this->view->styles = ['css/addon/property.css'];
        $this->view->scripts = ['js/addon/property.js'];
        $this->view->plugins = ['select2'];

        $title = 'Добавление свойства';
        $content = '';

        $CategoryModel = new CategoryModel();
        $Categories = $CategoryModel->getAll(true);
        
        $pVals = '<div class="p_val">
            <input type="text" name="val[]" value="">
            <a href="#" class="add_val">+</a>
            <a href="#" class="remove_val">-</a>
        </div>';

        if(!empty($this->urls[4])){

            $id = intval($this->urls[4]);
            $PropertyModel = new PropertyModel();
            $Property = $PropertyModel->get($id);

            $title = 'Редактирование свойства для товаров: <b>'.$Property[0]["title"].'</b>';

            $pVals = '';
            foreach ($Property as $item) {
                $pVals .= '<div class="p_val">
                    <input type="hidden" name="id[]" value="'.$item["pv_id"].'">
                    <input type="text" name="val[]" value="'.$item["val"].'">
                    <a href="#" class="add_val">+</a>
                    <a href="#" class="remove_val">-</a>
                </div>';
            }
        }

        // родительская категория
        $categoryOptions = '';
        if(!empty($Categories)){

            $catIsset = !empty($Property[0]["cid"]) ? explode(",", $Property[0]["cid"]) : [];

            foreach ($Categories as $row) {

                $selected = in_array($row["id"], $catIsset) ? ' selected' : '';
                $categoryOptions .= '<option value="'.$row["id"].'"'.$selected.'>'.$row["title"].'</option>';
            }
        }

        $content .= '<h1>'.$title.'</h1>
        <form action method="POST" class="box_">
            <div class="dg dg_auto">
                <div>
                    <label for="" class="rq">Название свойства</label>
                    <input type="text" name="title" value="'.(!empty($Property[0]["title"])?$Property[0]["title"]:'').'" autocomplete="off">
                </div>
                <div>
                    <label for="" class="pr">URL для фильтра <span class="q"><i>Если указано, то по этому свойству будет возможность произвести фильтрацию товаров в указанных категориях соответственно.</i></span></label>
                    <input type="text" name="url" placeholder="Только латинские символы без пробелов" value="'.(!empty($Property[0]["url"])?$Property[0]["url"]:'').'" autocomplete="off">
                </div>
                <div>
                    <input type="checkbox" name="display" class="ch_min" id="display"'.(!empty($Property[0]["option"])?' checked':'').'><label for="display">Выводить сразу</label>
                    <br>
                    <br>
                    <input type="checkbox" name="sep" class="ch_min" id="sep"'.(!empty($Property[0]["sep"])?' checked':'').'><label for="sep">Разрешить произвольный вариант</label>
                </div>
            </div>
            <div class="dg dg_auto">
                <div>
                    <label for="" class="pr">Категории <span class="q"><i>Выберите категории, в котрых отображать это свойство.<br>Или оставьте пустым, для вывода для любых категорий.</i></span></label>
                    <select name="cid[]" class="multipleSelect" multiple>
                        '.$categoryOptions.'
                    </select>
                </div>
            </div>
            <p class="title_box hr_d">Значения</p>
            <div id="propertyVals">
                '.$pVals.'
            </div>
            
            <input type="submit" class="btn" data-a="PropertyShop" value="Сохранить">
        </form>';

        

        $this->view->render($title, $content);
    }

}