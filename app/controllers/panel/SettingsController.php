<?php


namespace app\controllers\panel;

use app\core\PanelController;
use app\core\System;


class SettingsController extends PanelController {


    public function indexAction(){

        $content = '<h1>Общие настройки</h1>';

        $templatesOptions = '<select name="config[template]">';

        $templates = scandir(ROOT . '/templates');
        foreach ($templates as $template) {

            if(
                is_dir(ROOT . '/templates/' . $template) &&
                $template != '.' &&
                $template != '..' &&
                $template != '_system' &&
                $template != '_plugins' &&
                $template != 'Panel' &&
                $template != 'PanelAuth'
            ){
                $selectedTemplate = CONFIG_SYSTEM["template"] == $template ? ' selected' : '';
                $templatesOptions .= '<option value="'.$template.'"'.$selectedTemplate.'>'.$template.'</option>';
            }
        }

        $templatesOptions .= '</select>';

        $content .= '<form action="" method="POST">
            <div class="tabs">
                <ul class="tabs_caption">
                    <li class="active">Общие настройки</li>
                    <li>Почта</li>
                    <li>Разработчикам</li>
                </ul>
                <div class="tabs_content active">
                    <div class="dg settings">
                        <div class="set_item">
                            <div>
                                <h3>Адрес сайта</h3>
                                <div class="setDescription">Указывать нужно без слешей. Например: <b>site.com</b></div>
                            </div>
                            <div>
                                <input type="text" name="config[home]" value="'.CONFIG_SYSTEM["home"].'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Адрес панели</h3>
                            </div>
                            <div>
                                <input type="text" name="config[panel]" value="'.CONFIG_SYSTEM["panel"].'">
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
                                <h3>Шаблон сайта</h3>
                            </div>
                            <div>
                                '.$templatesOptions.'
                            </div>
                        </div>
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
                                <input type="text" name="config[admin_email]" value="'.CONFIG_SYSTEM["admin_email"].'">
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
                                <input type="text" name="config[SMTPHost]" value="'.CONFIG_SYSTEM["SMTPHost"].'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>SMTP логин</h3>
                            </div>
                            <div>
                                <input type="text" name="config[SMTPLogin]" value="'.CONFIG_SYSTEM["SMTPLogin"].'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>SMTP пароль</h3>
                            </div>
                            <div>
                                <input type="text" name="config[SMTPPassword]" value="'.CONFIG_SYSTEM["SMTPPassword"].'">
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
                                <input type="text" name="config[SMTPPort]" value="'.CONFIG_SYSTEM["SMTPPort"].'">
                            </div>
                        </div>
                        <div class="set_item">
                            <div>
                                <h3>Отправлять от email</h3>
                            </div>
                            <div>
                                <input type="text" name="config[SMTPFrom]" value="'.CONFIG_SYSTEM["SMTPFrom"].'">
                            </div>
                        </div>
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
                    </div>
                </div>
                
            </div>
            
            <input type="submit" class="btn" data-a="Settings" value="Сохранить">
            
        </form>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Общие настройки', $content);
    }


    public function seoAction(){

        $content = '<h1>SEO</h1>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' SEO', $content);
    }


    public function promoCodesAction(){

        $content = '<h1>Промокоды</h1>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Промокоды', $content);
    }


    public function langAction(){

        $content = '<h1>Языки</h1>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Языки', $content);
    }


    public function currencyAction(){

        $content = '<h1>Валюта</h1>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Валюта', $content);
    }


    public function paymentMethodsAction(){

        $content = '<h1>Способы оплаты</h1>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Способы оплаты', $content);
    }


    public function deliveryMethodsAction(){

        $content = '<h1>Способы доставки</h1>';

        $this->view->render('Настройки '.CONFIG_SYSTEM["separator"].' Способы доставки', $content);
    }

}