<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\models\panel\PageModel;


class PostsController extends PanelController {


    public function newsAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }


    public function pagesAction(){

        $this->view->styles = ['css/addon/page.css'];
        $this->view->scripts = ['js/addon/page.js'];

        $content = '<div class="fx">
            <h1>Категории товаров</h1>
            <a href="/panel/posts/pages/add/" class="btn">Добавить</a>
        </div>';

        $PageModel = new PageModel();
        $Pages = $PageModel->getAll();

        if($Pages["pages"]){

            $pageContent = '';

            foreach ($Pages["pages"] as $row) {

                $status = $row["status"] ? ' checked' : '';

                $pageContent .= '<tr>
                    <td>'.$row["id"].'</td>
                    <td><a href="//'.CONFIG_SYSTEM["home"].'/'.$row["url"].'.html" target="_blank" class="ico_see"></a></td>
                    <td>
                        <a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/posts/pages/edit/'.$row["id"].'/">'.$row["title"].'</a>
                    </td>
                    <td><input type="checkbox" name="status['.$row["id"].']" class="ch_min status_page" data-id="'.$row["id"].'" id="status_'.$row["id"].'"'.$status.'><label for="status_'.$row["id"].'"></label></td>
                    <td>
                        <ul class="tc">
                            <li><a href="#" class="remove" data-a="Page:deletePage='.$row["id"].'"></a></li>
                        </ul>
                    </td>
                </tr>';
            }

        } else $pageContent = '<tr class="tc"><td colspan="5">Страниц нет</td></tr>';

        $content .= '<table>
            <tr>
                <th width="20">ID</th>
                <th width="10"><span class="ico_see"></span></th>
                <th>Название</th>
                <th width="20">Статус</th>
                <th width="50"></th>
            </tr>
            '.$pageContent.'
        </table>';

        $this->view->render('Категории товаров', $content);
    }
    
    
    


    public function addPageAction(){

        $this->view->styles = ['css/addon/page.css'];
        $this->view->scripts = ['js/addon/page.js'];
        $this->view->plugins = ['datepicker', 'fancybox'];

        $title = $h1 = 'Добавление страницы';

        $images = '';

        if(!empty($this->urls[4])){

            $id = intval($this->urls[4]);

            $PageModel = new PageModel();
            $Page = $PageModel->get($id);
            $Images = $PageModel->getImages($id);

            $title = 'Редактирование страницы: <b>'.$Page["title"].'</b>';

            // изображения страницы
            if(!empty($Images)){
                foreach ($Images as $image) {

                    $thumb = !empty(CONFIG_SYSTEM["thumb"]) ? '//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.str_replace('/', '/thumbs/', $image["src"]) : '//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.$image["src"];

                    $is_main = ($Page["poster"] == $image["id"]) ? ' is_main' : '';

                    $images .= '<div class="img_item" data-img-id="'.$image["id"].'">
                    <a href="//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.$image["src"].'" data-fancybox="gallery" data-caption="'.$image["alt"].'"><img src="'.$thumb.'" alt=""></a>
                    <a href="#" class="main_image'.$is_main.'" data-a="Page:setMainImage='.$image["id"].'"></a>
                    <a href="#editPhoto" class="edit_image open_modal" data-img-id="'.$image["id"].'"></a>
                    <a href="#" class="delete_image" data-a="Page:deleteImage='.$image["id"].'&link='.$image["src"].'"></a>
                </div>';
                }
            }
        }

        $content = '<h1>'.$h1.'</h1>';

        $pageStatus = ' checked';
        if(!empty($Page["id"])){
            if(empty($Page["status"])) $pageStatus = '';
        }

        $content .= '<form action method="POST">
            <div class="tabs">
                <ul class="tabs_caption">
                    <li class="active">Основное</li>
                    <li>SEO</li>
                </ul>
                <div class="tabs_content active">
                    <div class="dg dg_auto">
                        <div>
                            <div>
                                <label for="" class="rq">Название</label>
                                <input type="text" name="title" value="'.(!empty($Page["title"])?$Page["title"]:'').'" autocomplete="off">
                            </div>
                        </div>
                        <div>
                            <div>
                                <div>
                                    <label for="">Дата публикации</label>
                                    <input type="text" name="created" class="dateTime" value="'.(!empty($Page["created"])?date("d.m.Y H:i", $Page["created"]):'').'" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>
                                <div class="tr">
                                    <input type="checkbox" name="status" id="p_status"'.$pageStatus.' value="1"><label for="p_status">Активена</label>
                                </div>
                                <div>
                                    <label for="p_images" class="upload_files">
                                        <input type="file" name="images[]" id="p_images" multiple> выбрать изображения
                                    </label>
                                </div>
                            </div>
                            
                            <!-- изображения страницы -->
                            <div id="page_images">
                                '.$images.'
                            </div>
                        </div>
                    </div>
                    <p class="title_box hr_d">Описание</p>
                    <div>
                        <textarea name="content" id="page_content" rows="5">'.(!empty($Page["content"])?$Page["content"]:'').'</textarea>
                        <br>
                        <script>
                            let editor = new FroalaEditor("#page_content", {
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
                            <label for="">Meta Title</label>
                            <input type="text" name="meta[title]" value="'.(!empty($Page["m_title"])?$Page["m_title"]:'').'" autocomplete="off">
                        </div>
                        <div>
                            <label for="" class="pr">URL <span class="q"><i>Для поисковых систем</i></span></label>
                            <input type="text" name="url" placeholder="Только латинские символы без пробелов" value="'.(!empty($Page["url"])?$Page["url"]:'').'" autocomplete="off">
                        </div>
                    </div>
                    <div class="dg dg_auto">
                        <div>
                            <label for="">Meta Description</label>
                            <textarea name="meta[description]" rows="3">'.(!empty($Page["m_description"])?$Page["m_description"]:'').'</textarea>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <input type="hidden" name="page" value="1">
            
            <input type="submit" class="btn" data-a="Page" value="Сохранить">
            
        </form>';


        $content .= '<form action method="POST" class="modal_big" id="editPhoto">
            <div class="modal_title">Редактор фото</div>
            <div class="modal_body dg dg-2" id="photoEditor"></div>
            <div class="fx jc_c">
                <input type="submit" class="btn" data-a="Page" value="Сохранить">&nbsp;&nbsp;
                <a href="#" class="btn cancel">Отмена</a>
            </div>
            <a href="#" class="close"></a>
        </form>';

        $this->view->render($title, $content);
    }


    public function categoriesAction(){

        $content = '';

        /*$this->view->include('');
        $this->view->set('{}', $content);

        $this->view->setMain('{tag}', $this->view->get());*/

        $this->view->render('');
    }

}