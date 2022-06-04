<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\models\panel\CategoryModel;
use app\models\panel\PostModel;
use Exception;


class PostsController extends PanelController {


    /**
     * @name посты
     * ============
     * @return void
     * @throws Exception
     */
    public function indexAction(){

        $this->view->styles = ['css/post.css'];
        $this->view->scripts = ['js/post.js'];

        $content = '<div class="fx">
            <h1>Посты</h1>
            <a href="/'.CONFIG_SYSTEM["panel"].'/posts/add/" class="btn">Добавить</a>
        </div>';

        $PostModel = new PostModel();
        $CategoryModel = new CategoryModel();
        $Posts = $PostModel->getAll();
        $Categories = $CategoryModel->getAll(true);

        if(!empty($Posts["posts"])){

            $PostContent = '';

            foreach ($Posts["posts"] as $row) {

                $category = '';
                if(!empty($row["category"])){
                    $categoryArr = explode(",", $row["category"]);
                    $category = !empty($Categories[$categoryArr[0]]["title"]) ? $Categories[$categoryArr[0]]["title"].'...' : '';
                }

                $status = $row["status"] ? ' checked' : '';

                $PostContent .= '<tr>
                    <td>'.$row["id"].'</td>
                    <td>
                        <a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/posts/edit/'.$row["id"].'/">'.$row["title"].'</a>
                    </td>
                    <td>'.$category.'</td>
                    <td class="fs12">'.date("d.m.Y", $row["created"]).'</td>
                    <td><input type="checkbox" name="status['.$row["id"].']" class="ch_min status_post" data-id="'.$row["id"].'"  id="status_'.$row["id"].'"'.$status.'><label for="status_'.$row["id"].'"></label></td>
                    <td>
                        <ul class="tc">
                            <li><a href="#" class="remove" data-a="Post:deletePost='.$row["id"].'"></a></li>
                        </ul>
                    </td>
                </tr>';
            }

        } else $PostContent = '<tr class="tc"><td colspan="6">Постов нет</td></tr>';

        $content .= '<div class="">
            <table>
                <tr>
                    <th width="20">ID</th>
                    <th>Заголовок поста</th>
                    <th width="200">Категория</th>
                    <th width="130">Дата публикации</th>
                    <th width="30">Статус</th>
                    <th width="50"></th>
                </tr>
                '.$PostContent.'
            </table>
        </div>'.$Posts["pagination"];

        $this->view->render('Посты', $content);
    }




    /**
     * @name добавление и редактирование постов
     * ========================================
     * @return void
     * @throws Exception
     */
    public function addAction(){

        $this->view->styles = ['css/post.css'];
        $this->view->scripts = ['js/post.js'];
        $this->view->plugins = ['jquery-ui', 'select2', 'datepicker', 'fancybox'];

        $title = $h1 = 'Добавление поста';

        $PostModel = new PostModel();
        $CategoryModel = new CategoryModel();

        $Categories = $CategoryModel->getAll(true);



        if(!empty($this->urls[3])){

            $id = intval($this->urls[3]);
            $Post = $PostModel->get($id);

            $title = $Post["posts"]["title"];
            $h1 = 'Редактирование поста: <b>'.$Post["posts"]["title"].'</b>';

        }

        // категории
        $categoriesIsset = !empty($Post["posts"]["category"]) ? explode(",", $Post["posts"]["category"]) : [];
        $categoryOptions = '';
        if(!empty($Categories)){
            foreach ($Categories as $row) {

                $selected = in_array($row["id"], $categoriesIsset) ? ' selected' : '';
                $categoryOptions .= '<option value="'.$row["id"].'"'.$selected.'>'.$row["title"].'</option>';
            }
        }


        // изображения постов
        $images = '';
        if(!empty($Post["images"])){
            foreach ($Post["images"] as $image) {

                $thumb = !empty(CONFIG_SYSTEM["thumb"]) ? '//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.str_replace('/', '/thumbs/', $image["src"]) : '//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.$image["src"];

                $is_main = ($Post["posts"]["poster"] == $image["id"]) ? ' is_main' : '';

                $images .= '<div class="img_item" data-img-id="'.$image["id"].'">
                    <a href="//'.CONFIG_SYSTEM["home"].'/uploads/posts/'.$image["src"].'" data-fancybox="gallery" data-caption="'.$image["alt"].'"><img src="'.$thumb.'" alt=""></a>
                    <a href="#" class="main_image'.$is_main.'" data-a="Post:setMainImage='.$image["id"].'"></a>
                    <a href="#editPhoto" class="edit_image open_modal" data-img-id="'.$image["id"].'"></a>
                    <a href="#" class="delete_image" data-a="Post:deleteImage='.$image["id"].'&link='.$image["src"].'"></a>
                </div>';
            }
        }

        $content = '<h1>'.$h1.'</h1>';

        $PostStatus = ' checked';
        if(!empty($Post["posts"])){
            if(empty($Post["posts"]["status"])) $PostStatus = '';
        }

        $content .= '<form action method="POST">
            <div class="tabs">
                <ul class="tabs_caption">
                    <li class="active">Пост</li>
                    <li>SEO</li>
                </ul>
                <div class="tabs_content active">
                    <div class="dg dg_auto">
                        <div>
                            <div>
                                <label for="" class="rq">Название</label>
                                <input type="text" name="title" value="'.(!empty($Post["posts"]["title"])?$Post["posts"]["title"]:'').'" autocomplete="off">
                            </div>
                            <div>
                                <div>
                                    <label for="">Дата публикации</label>
                                    <input type="text" name="created" class="dateTime" value="'.(!empty($Post["posts"]["created"])?date("d.m.Y H:i", $Post["posts"]["created"]):'').'" autocomplete="off">
                                </div>
                            </div>
                            <div>
                                <label for="">Категория</label>
                                <select name="category[]" id="categoryOptions" class="multipleSelect" multiple>
                                    '.$categoryOptions.'
                                </select>
                            </div>
                        </div>
                        <div>
                            <div class="tr">
                                <label for="p_images" class="upload_files">
                                    <input type="file" name="images[]" id="p_images" multiple> выбрать изображения
                                </label>
                            </div>
                            <!-- изображения постов -->
                            <div id="post_images">
                                '.$images.'
                            </div>
                        </div>
                    </div>
                    <p class="title_box hr_d">Анонс</p>
                    <div>
                        <textarea name="short" id="post_short" rows="5">'.(!empty($Post["posts"]["short"])?$Post["posts"]["short"]:'').'</textarea>
                        <br>
                        <script>
                            let editor = new FroalaEditor("#post_short", {
                                inlineMode: true,
                                countCharacters: false
                            });
                        </script>
                    </div>
                    <p class="title_box hr_d">Описание</p>
                    <div>
                        <textarea name="content" id="post_content" rows="10">'.(!empty($Post["posts"]["content"])?$Post["posts"]["content"]:'').'</textarea>
                        <br>
                        <script>
                            let editor = new FroalaEditor("#post_content", {
                                inlineMode: true,
                                countCharacters: false
                            });
                        </script>
                    </div>
                    <input type="checkbox" name="status" id="p_status"'.$PostStatus.' value="1"><label for="p_status">Активно</label>
                </div>
                
                <!-- tab SEO -->
                <div class="tabs_content">
                    <div class="dg dg_auto">
                        <div>
                            <label for="" class="pr">URL <span class="q"><i>Для поисковых систем</i></span></label>
                            <input type="text" name="url" placeholder="Только латинские символы без пробелов" value="'.(!empty($Post["posts"]["url"])?$Post["posts"]["url"]:'').'" autocomplete="off">
                        </div>
                        <div>
                            <label for="">Meta Title</label>
                            <input type="text" name="meta[title]" value="'.(!empty($Post["posts"]["m_title"])?$Post["posts"]["m_title"]:'').'" autocomplete="off">
                        </div>
                    </div>
                    <div class="dg dg_auto">
                        <div>
                            <label for="">Meta Description</label>
                            <textarea name="meta[description]" rows="3">'.(!empty($Post["posts"]["m_description"])?$Post["posts"]["m_description"]:'').'</textarea>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="post" value="1">
                
            </div>
            
            <input type="submit" class="btn" data-a="Post" value="Сохранить">
            
        </form>';


        $content .= '<form action method="POST" class="modal_big" id="editPhoto">
            <div class="modal_title">Редактор фото</div>
            <div class="modal_body dg dg-2" id="photoEditor"></div>
            <div class="fx jc_c">
                <input type="submit" class="btn" data-a="posts" value="Сохранить">&nbsp;&nbsp;
                <a href="#" class="btn cancel">Отмена</a>
            </div>
            <a href="#" class="close"></a>
        </form>';

        $this->view->render($title, $content);
    }

}