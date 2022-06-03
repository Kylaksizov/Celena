<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\models\panel\CategoryModel;
use app\models\panel\NewsModel;
use Exception;


class NewsController extends PanelController {


    /**
     * @name новости
     * =============
     * @return void
     * @throws Exception
     */
    public function indexAction(){

        $this->view->styles = ['css/news.css'];
        $this->view->scripts = ['js/news.js'];

        $content = '<div class="fx">
            <h1>Новости</h1>
            <a href="/'.CONFIG_SYSTEM["panel"].'/news/add/" class="btn">Добавить</a>
        </div>';

        $NewsModel = new NewsModel();
        $CategoryModel = new CategoryModel();
        $News = $NewsModel->getAll();
        $Categories = $CategoryModel->getAll(true);

        if(!empty($News["news"])){

            $newsContent = '';

            foreach ($News["news"] as $row) {

                $category = '';
                if(!empty($row["category"])){
                    $categoryArr = explode(",", $row["category"]);
                    $category = !empty($Categories[$categoryArr[0]]["title"]) ? $Categories[$categoryArr[0]]["title"].'...' : '';
                }

                $status = $row["status"] ? ' checked' : '';

                $newsContent .= '<tr>
                    <td>'.$row["id"].'</td>
                    <td>
                        <a href="//'.CONFIG_SYSTEM["home"].'/'.CONFIG_SYSTEM["panel"].'/news/edit/'.$row["id"].'/">'.$row["title"].'</a>
                        <span class="br_min">'.$category.'</span>
                    </td>
                    <td class="fs12">'.date("d.m.Y H:i", $row["created"]).'</td>
                    <td><input type="checkbox" name="status['.$row["id"].']" class="ch_min status_news" data-id="'.$row["id"].'"  id="status_'.$row["id"].'"'.$status.'><label for="status_'.$row["id"].'"></label></td>
                    <td>
                        <ul class="tc">
                            <li><a href="#" class="remove" data-a="News:deleteNews='.$row["id"].'"></a></li>
                        </ul>
                    </td>
                </tr>';
            }

        } else $newsContent = '<tr class="tc"><td colspan="6">Новостей нет</td></tr>';

        $content .= '<div class="">
            <table>
                <tr>
                    <th width="20">#</th>
                    <th>Наименование<span class="br_min">категория</span></th>
                    <th width="130">Дата публикации</th>
                    <th width="30">Статус</th>
                    <th width="50"></th>
                </tr>
                '.$newsContent.'
            </table>
        </div>';

        $this->view->render('Новости', $content);
    }




    /**
     * @name добавление и редактирование новости
     * =========================================
     * @return void
     * @throws Exception
     */
    public function addAction(){

        $this->view->styles = ['css/news.css'];
        $this->view->scripts = ['js/news.js'];
        $this->view->plugins = ['jquery-ui', 'select2', 'datepicker', 'fancybox'];

        $title = $h1 = 'Добавление новости';

        $NewsModel = new NewsModel();
        $CategoryModel = new CategoryModel();

        $Categories = $CategoryModel->getAll(true);



        if(!empty($this->urls[3])){

            $id = intval($this->urls[3]);
            $News = $NewsModel->get($id);

            $title = $News["news"]["title"];
            $h1 = 'Редактирование новости: <b>'.$News["news"]["title"].'</b>';

        }

        // категории
        $categoriesIsset = !empty($News["news"]["category"]) ? explode(",", $News["news"]["category"]) : [];
        $categoryOptions = '';
        if(!empty($Categories)){
            foreach ($Categories as $row) {

                $selected = in_array($row["id"], $categoriesIsset) ? ' selected' : '';
                $categoryOptions .= '<option value="'.$row["id"].'"'.$selected.'>'.$row["title"].'</option>';
            }
        }


        // изображения новости
        $images = '';
        if(!empty($News["images"])){
            foreach ($News["images"] as $image) {

                $thumb = !empty(CONFIG_SYSTEM["thumb"]) ? '//'.CONFIG_SYSTEM["home"].'/uploads/news/'.str_replace('/', '/thumbs/', $image["src"]) : '//'.CONFIG_SYSTEM["home"].'/uploads/news/'.$image["src"];

                $is_main = ($News["news"]["poster"] == $image["id"]) ? ' is_main' : '';

                $images .= '<div class="img_item" data-img-id="'.$image["id"].'">
                    <a href="//'.CONFIG_SYSTEM["home"].'/uploads/news/'.$image["src"].'" data-fancybox="gallery" data-caption="'.$image["alt"].'"><img src="'.$thumb.'" alt=""></a>
                    <a href="#" class="main_image'.$is_main.'" data-a="News:setMainImage='.$image["id"].'"></a>
                    <a href="#editPhoto" class="edit_image open_modal" data-img-id="'.$image["id"].'"></a>
                    <a href="#" class="delete_image" data-a="News:deleteImage='.$image["id"].'&link='.$image["src"].'"></a>
                </div>';
            }
        }

        $content = '<h1>'.$h1.'</h1>';

        $newsStatus = ' checked';
        if(!empty($News["news"])){
            if(empty($News["news"]["status"])) $newsStatus = '';
        }

        $content .= '<form action method="POST">
            <div class="tabs">
                <ul class="tabs_caption">
                    <li class="active">Новость</li>
                    <li>SEO</li>
                </ul>
                <div class="tabs_content active">
                    <div class="dg dg_auto">
                        <div>
                            <div>
                                <label for="" class="rq">Название</label>
                                <input type="text" name="title" value="'.(!empty($News["news"]["title"])?$News["news"]["title"]:'').'" autocomplete="off">
                            </div>
                            <div>
                                <div>
                                    <label for="">Дата публикации</label>
                                    <input type="text" name="created" class="dateTime" value="'.(!empty($News["news"]["created"])?date("d.m.Y H:i", $News["news"]["created"]):'').'" autocomplete="off">
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
                            <!-- изображения новости -->
                            <div id="news_images">
                                '.$images.'
                            </div>
                        </div>
                    </div>
                    <p class="title_box hr_d">Анонс</p>
                    <div>
                        <textarea name="short" id="news_short" rows="5">'.(!empty($News["news"]["short"])?$News["news"]["short"]:'').'</textarea>
                        <br>
                        <script>
                            let editor = new FroalaEditor("#news_short", {
                                inlineMode: true,
                                countCharacters: false
                            });
                        </script>
                    </div>
                    <p class="title_box hr_d">Описание</p>
                    <div>
                        <textarea name="content" id="news_content" rows="10">'.(!empty($News["news"]["content"])?$News["news"]["content"]:'').'</textarea>
                        <br>
                        <script>
                            let editor = new FroalaEditor("#news_content", {
                                inlineMode: true,
                                countCharacters: false
                            });
                        </script>
                    </div>
                    <input type="checkbox" name="status" id="p_status"'.$newsStatus.' value="1"><label for="p_status">Активно</label>
                </div>
                
                <!-- tab SEO -->
                <div class="tabs_content">
                    <div class="dg dg_auto">
                        <div>
                            <label for="" class="pr">URL <span class="q"><i>Для поисковых систем</i></span></label>
                            <input type="text" name="url" placeholder="Только латинские символы без пробелов" value="'.(!empty($News["news"]["url"])?$News["news"]["url"]:'').'" autocomplete="off">
                        </div>
                        <div>
                            <label for="">Meta Title</label>
                            <input type="text" name="meta[title]" value="'.(!empty($News["news"]["m_title"])?$News["news"]["m_title"]:'').'" autocomplete="off">
                        </div>
                    </div>
                    <div class="dg dg_auto">
                        <div>
                            <label for="">Meta Description</label>
                            <textarea name="meta[description]" rows="3">'.(!empty($News["news"]["m_description"])?$News["news"]["m_description"]:'').'</textarea>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="news" value="1">
                
            </div>
            
            <input type="submit" class="btn" data-a="News" value="Сохранить">
            
        </form>';


        $content .= '<form action method="POST" class="modal_big" id="editPhoto">
            <div class="modal_title">Редактор фото</div>
            <div class="modal_body dg dg-2" id="photoEditor"></div>
            <div class="fx jc_c">
                <input type="submit" class="btn" data-a="News" value="Сохранить">&nbsp;&nbsp;
                <a href="#" class="btn cancel">Отмена</a>
            </div>
            <a href="#" class="close"></a>
        </form>';

        $this->view->render($title, $content);
    }

}