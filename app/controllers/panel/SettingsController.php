<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\core\System;
use app\models\panel\PostModel;
use app\traits\SiteMap;


class SettingsController extends PanelController {

    use SiteMap;


    public function indexAction(){

        //$this->view->styles = ['css/settings.css'];
        $this->view->scripts = ['js/settings.js'];

        $content = '<h1>Общие настройки</h1>';

        $templatesOptions = '<select name="config[template]">';

        $templates = scandir(ROOT . '/templates');
        foreach ($templates as $template) {

            if(
                is_dir(ROOT . '/templates/' . $template) &&
                $template != '.' &&
                $template != '..' &&
                $template != 'system' &&
                $template != 'plugins' &&
                $template != 'Panel' &&
                $template != 'PanelAuth'
            ){
                $selectedTemplate = CONFIG_SYSTEM["template"] == $template ? ' selected' : '';
                $templatesOptions .= '<option value="'.$template.'"'.$selectedTemplate.'>'.$template.'</option>';
            }
        }
        $templatesOptions .= '</select>';

        $mainContentDisplay = !empty(CONFIG_SYSTEM["main_content"]) ? ' style="display:block;"' : ' style="display:none;"';


        $mainPageOptions = '<select name="config[main]" id="selectMain">
                <option value="1">Контент</option>
                <option value="2"'.(CONFIG_SYSTEM["main"]==2&&CONFIG_SYSTEM["main_content"]?' selected':'').'>Страница</option>
                <option value="3"'.(CONFIG_SYSTEM["main"]==3&&CONFIG_SYSTEM["main_content"]?' selected':'').'>TPL</option>
            </select>';

        $content .= '<form action="" method="POST">
            <div class="tabs">
                <ul class="tabs_caption">
                    <li class="active">Общие настройки</li>
                    <li>Изображения</li>
                    <li>Почта</li>
                    <li>Пользователи</li>
                    <li>Разработчикам</li>
                </ul>
                <div class="tabs_content active">
                    <div class="dg settings">
                        <div class="set_item">
                            <div>
                                <h3>Название сайта</h3>
                            </div>
                            <div>
                                <input type="text" name="config[site_title]" value="'.htmlspecialchars(CONFIG_SYSTEM["site_title"]).'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Описание сайта</h3>
                                <div class="setDescription">До 250 символов.</div>
                            </div>
                            <div>
                                <textarea name="config[site_description]">'.htmlspecialchars(CONFIG_SYSTEM["site_description"]).'</textarea>
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Краткое название для хлебных крошек</h3>
                            </div>
                            <div>
                                <input type="text" name="config[crumbs_title]" value="'.htmlspecialchars(CONFIG_SYSTEM["crumbs_title"]).'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Разделитель для хлебных крошек</h3>
                            </div>
                            <div>
                                <input type="text" name="config[separator]" value="'.htmlspecialchars(CONFIG_SYSTEM["separator"]).'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Адрес сайта</h3>
                                <div class="setDescription">Указывать нужно без слешей. Например: <b>site.com</b></div>
                            </div>
                            <div>
                                <input type="text" name="config[home]" value="'.htmlspecialchars(CONFIG_SYSTEM["home"]).'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Адрес панели</h3>
                            </div>
                            <div>
                                <input type="text" name="config[panel]" value="'.htmlspecialchars(CONFIG_SYSTEM["panel"]).'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>SSL протокол</h3>
                            </div>
                            <div>
                                <input type="checkbox" name="config[ssl]" value="1"'.System::check(CONFIG_SYSTEM["ssl"]).' id="ch_ssl">
                                <label for="ch_ssl"></label>
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Количество постов в категориях</h3>
                            </div>
                            <div>
                                <input type="text" name="config[count_in_cat]" value="'.htmlspecialchars(CONFIG_SYSTEM["count_in_cat"]).'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Комментарии на сайте</h3>
                                <div class="setDescription">Разрешить оставлять комментарии на сайте.</div>
                            </div>
                            <div>
                                <input type="checkbox" name="config[comments]" value="1"'.System::check(CONFIG_SYSTEM["comments"]).' id="ch_comments">
                                <label for="ch_comments"></label>
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Содержимое главной страницы</h3>
                                <div class="setDescription"><b>Контент</b> - стандартный вывод тега {CONTENT}.<br>
                                <b>Страница</b> - определенная <a href="/'.CONFIG_SYSTEM["panel"].'/pages/">страница</a>.<br>
                                <b>TPL</b> - файл формата tpl, созданный в шаблоне.</div>
                            </div>
                            <div>
                                '.$mainPageOptions.'
                                <input type="text" name="config[main_content]" id="main_content"'.$mainContentDisplay.' value="'.htmlspecialchars(CONFIG_SYSTEM["main_content"]).'" placeholder="Название страницы или tpl файла">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Шаблон сайта</h3>
                            </div>
                            <div>
                                '.$templatesOptions.'
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3><b>POWER</b></h3>
                                <div class="setDescription">Включить сайт? :D</div>
                            </div>
                            <div>
                                <input type="checkbox" name="config[power]" value="1"'.System::check(CONFIG_SYSTEM["power"]).' id="ch_power">
                                <label for="ch_power"></label>
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Текст при выключеном сайте</h3>
                                <div class="setDescription">Текст при выключеном сайте.</div>
                            </div>
                            <div>
                                <textarea name="config[power_text]" rows="3">'.htmlspecialchars(CONFIG_SYSTEM["power_text"]).'</textarea>
                            </div>
                        </div>
                        <!--:settings_main-->
                    </div>
                </div>
                
                <div class="tabs_content">
                    <div class="dg settings">
                        <div class="set_item">
                            <div>
                                <h3>До какого размера (в px) уменьшать загружаемые изображения</h3>
                                <div class="setDescription">Если оставить пустым, то будут загружаться оригинальные изображения.</div>
                            </div>
                            <div>
                                <input type="text" name="config[origin_image]" value="'.htmlspecialchars(CONFIG_SYSTEM["origin_image"]).'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Качество загружаемых изображений</h3>
                                <div class="setDescription">Если оставить пустым, качество будет оставаться исходным.</div>
                            </div>
                            <div>
                                <input type="text" name="config[quality_image]" value="'.htmlspecialchars(CONFIG_SYSTEM["quality_image"]).'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>До какого размера (в px) создавать уменьшенную копию изображений</h3>
                                <div class="setDescription">Если оставить пустым, уменьшеная копия создаваться не будет.</div>
                            </div>
                            <div>
                                <input type="text" name="config[thumb]" value="'.htmlspecialchars(CONFIG_SYSTEM["thumb"]).'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Качество уменьшенных копий изображений</h3>
                                <div class="setDescription">Если оставить пустым, качество будет оставаться исходным.</div>
                            </div>
                            <div>
                                <input type="text" name="config[quality_thumb]" value="'.htmlspecialchars(CONFIG_SYSTEM["quality_thumb"]).'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Создавать миниатюры при вставке изображений через редактор Quill</h3>
                                <div class="setDescription">Если включено, то будут создаваться уменьшенные копии изображений размерами заданными на два пункта выше.</div>
                            </div>
                            <div>
                                <input type="checkbox" name="config[quill_thumbs]" value="1"'.System::check(CONFIG_SYSTEM["quill_thumbs"]).' id="ch_quill_thumbs">
                                <label for="ch_quill_thumbs"></label>
                            </div>
                        </div>
                        <!--:settings_images-->
                    </div>
                </div>
                
                <!-- tab POST -->
                <div class="tabs_content">
                    <div class="dg settings">
                        <div class="set_item">
                            <div>
                                <h3>Email администратора</h3>
                            </div>
                            <div>
                                <input type="text" name="config[admin_email]" value="'.htmlspecialchars(CONFIG_SYSTEM["admin_email"]).'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Метод отправки</h3>
                            </div>
                            <div>
                                <select name="config[mail_method]">
                                    <option value="mail">PHP mail()</option>
                                    <option value="smtp"'.(CONFIG_SYSTEM["mail_method"] == 'smtp' ? ' selected' : '').'>SMTP</option>
                                </select>
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>SMTP хост</h3>
                            </div>
                            <div>
                                <input type="text" name="config[SMTPHost]" value="'.htmlspecialchars(CONFIG_SYSTEM["SMTPHost"]).'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>SMTP логин</h3>
                            </div>
                            <div>
                                <input type="text" name="config[SMTPLogin]" value="'.htmlspecialchars(CONFIG_SYSTEM["SMTPLogin"]).'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>SMTP пароль</h3>
                            </div>
                            <div>
                                <input type="text" name="config[SMTPPassword]" value="'.htmlspecialchars(CONFIG_SYSTEM["SMTPPassword"]).'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>SMTP шифрование</h3>
                            </div>
                            <div>
                                <select name="config[SMTPSecure]">
                                    <option value="tls">tls</option>
                                    <option value="ssl"'.(CONFIG_SYSTEM["SMTPSecure"] == 'ssl' ? ' selected' : '').'>ssl</option>
                                </select>
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>SMTP порт</h3>
                            </div>
                            <div>
                                <input type="text" name="config[SMTPPort]" value="'.htmlspecialchars(CONFIG_SYSTEM["SMTPPort"]).'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Отправлять от email</h3>
                            </div>
                            <div>
                                <input type="text" name="config[SMTPFrom]" value="'.htmlspecialchars(CONFIG_SYSTEM["SMTPFrom"]).'">
                            </div>
                        </div>
                        <!--:settings_post-->
                    </div>
                </div>
                
                
                <!-- tab users -->
                <div class="tabs_content">
                    <div class="dg settings">
                        <div class="set_item">
                            <div>
                                <h3>Отправлять email уведомление при регистрации</h3>
                                <div class="setDescription">После регистрации пользователь должен подтвердить свою почту перейдя по ссылке в письме.</div>
                            </div>
                            <div>
                                <input type="checkbox" name="config[email_confirm]" value="1"'.System::check(CONFIG_SYSTEM["email_confirm"]).' id="ch_email_confirm">
                                <label for="ch_email_confirm"></label>
                            </div>
                        </div>
                        <!--:settings_users-->
                    </div>
                </div>
                
                <!-- tab SEO -->
                <div class="tabs_content">
                    <div class="dg settings">
                        <div class="set_item">
                            <div>
                                <h3>Вывод ошибок</h3>
                                <div class="setDescription">Будет выводить все ошибки на странице.</div>
                            </div>
                            <div>
                                <input type="checkbox" name="config[errors]" value="1"'.System::check(CONFIG_SYSTEM["errors"]).' id="ch_errors">
                                <label for="ch_errors"></label>
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Вести журнал ошибок Базы Данных</h3>
                                <div class="setDescription">Будет выводить все ошибки на странице.</div>
                            </div>
                            <div>
                                <input type="checkbox" name="config[db_log]" value="1"'.System::check(CONFIG_SYSTEM["db_log"]).' id="ch_db_log">
                                <label for="ch_db_log"></label>
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Включить помошника</h3>
                                <div class="setDescription">При включении, будет выведены подсказки и вспомагательные элементы для разработки.</div>
                            </div>
                            <div>
                                <input type="checkbox" name="config[dev_tools]" value="1"'.System::check(CONFIG_SYSTEM["dev_tools"]).' id="ch_dev_tools">
                                <label for="ch_dev_tools"></label>
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>IP адреса для ошибок</h3>
                                <div class="setDescription">IP адреса (каждый с новой строки), которым будут видны ошибки при любом раскладе, независимо от настроек выше.</div>
                            </div>
                            <div>
                                <textarea name="config[dev]">'.implode("\r\n", CONFIG_SYSTEM["dev"]).'</textarea>
                            </div>
                        </div>
                        <!--:settings_dev-->
                    </div>
                </div>
                
            </div>
            
            <input type="submit" class="btn" data-a="Settings" value="Сохранить">
            
        </form>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Общие настройки', $content);
    }


    public function seoAction(){

        $content = '<h1>SEO</h1>
            <p>В разработке</p>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' SEO', $content);
    }


    public function sitemapAction(){

        $mapLink = file_exists(ROOT . '/uploads/system/sitemap.xml') ? '<a href="//'.CONFIG_SYSTEM["home"].'/sitemap.xml" target="_blank">Открыть карту сайта</a>' : '<p>Карта сайт ещё не создана!</p>';

        $content = '<h1>Карта сайта</h1>
            <div class="box_">
                '.$mapLink.'<br><br>
                <a href="#" class="btn" data-a="Settings:generateMap=1">Сгенерировать карту сайта</a>
            </div>';

        $PostModel = new PostModel();
        $Posts = $PostModel->getFromMap();
        self::generationMap($Posts);

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Карта сайта', $content);
    }


    public function langAction(){

        $content = '<h1>Языки</h1>
            <p>В разработке</p>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Языки', $content);
    }


    public function paymentMethodsAction(){

        $content = '<h1>Способы оплаты</h1>
            <p>В разработке</p>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Способы оплаты', $content);
    }

}