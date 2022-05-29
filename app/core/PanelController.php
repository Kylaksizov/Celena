<?php
/**
 * @name Главный контроллер, который вызывается при старте любого контроллера
 */

namespace app\core;

use app\models\panel\PluginModel;

abstract class PanelController{

    public $urls;
    public $plugin;
    public $pluginsSystems;
    public $route;
    public $view;
    public $ajax = false;



    /**
     * @param $route
     * @param $ajax
     */
    public function __construct($route, $ajax){

        $this->urls = $route["urls"];
        $this->plugin = $route["plugin"];
        $this->pluginsSystems = [];
        $this->ajax = $ajax;
        $this->route = $route;
        $this->view = new ViewPanel($route);

        if(USER && USER["role"] == '1'){

            $this->view->load('Panel');

            /**
             * @name MENU
             * ==========
             */
            $menu = [
                'Товары' => [
                    "link" => "{panel}/products/",
                    "class" => "ico_products",
                    "icon" => "",
                    "submenu" => [
                        "Товары" => "{panel}/products/",
                        "Категории" => "{panel}/products/categories/",
                        "Бренды" => "{panel}/products/brands/",
                        "Свойства" => "{panel}/products/properties/",
                    ]
                ],
                'Заказы и покупки <span data-informer="orders" class="informer_active">3</span>' => [
                    "link" => "{panel}/orders/",
                    "class" => "ico_cart",
                    "icon" => "",
                    "submenu" => [
                        "Заказы" => "{panel}/orders/",
                        "Быстрые заказы" => "{panel}/orders/click/",
                    ]
                ],
                'Новости и страницы' => [
                    "link" => "#",
                    "class" => "ico_news",
                    "icon" => "",
                    "submenu" => [
                        "Новости" => "{panel}/posts/news/",
                        "Страницы" => "{panel}/posts/pages/",
                        "Категории новостей" => "{panel}/posts/categories/",
                    ]
                ],
                'Пользователи' => [
                    "link" => "{panel}/users/",
                    "class" => "ico_users",
                    "icon" => "",
                    "submenu" => [
                        "Покупатели" => "{panel}/users/customer/",
                        "Сотрудники" => "{panel}/users/employee/",
                        "Группы" => "{panel}/users/roles/",
                    ]
                ]
            ];

            // tmp
            /*die(json_encode(["Меню моего плагина" => [
                "link" => "#",
                "class" => "ico_space",
                "icon" => "",
                "submenu" => [
                    "Пункт меню 1" => "#",
                    "Пункт меню 2" => "#",
                    "Пункт меню 3" => "#",
                ]
            ]], JSON_UNESCAPED_UNICODE));*/

            $PluginModel = new PluginModel();
            $PluginsInfo = $PluginModel->getPluginField('name, status');

            if(!empty($PluginsInfo)){

                $pluginsMenu = [];
                foreach ($PluginsInfo as $row) {

                    if(file_exists(APP . '/plugins/'.$row["name"].'/system.json')){

                        $PluginSystem = json_decode(file_get_contents(APP . '/plugins/'.$row["name"].'/system.json'), true);
                        $this->pluginsSystems[$row["name"]] = $PluginSystem;
                        $this->pluginsSystems[$row["name"]]["brandName"] = $row["name"];
                        if($row["status"] == '1') $pluginsMenu = array_merge($pluginsMenu, $PluginSystem["menu"]);

                    } else{

                        die("Плагин <b>{$row["name"]}</b> отсутствует конфигурационный файл, который должен быть обязательно!");
                    }
                }

                # TODO хз, чето надо придумать ещё тут...
                $this->pluginsSystems = json_decode(json_encode($this->pluginsSystems));

                $menu = array_merge($menu, $pluginsMenu);
            }

            $addMenu = '';

            foreach ($menu as $titleMenu => $itemMenu) {

                $addMenu .= '<li><a href="'.$itemMenu["link"].'" class="'.$itemMenu["class"].'">'.$titleMenu.'</a>';

                if(!empty($itemMenu["submenu"])){

                    $addMenu .= '<ul>';
                    foreach ($itemMenu["submenu"] as $titleSubmenu => $link) {

                        $addMenu .= '<li><a href="'.$link.'">'.$titleSubmenu.'</a>';
                    }
                    $addMenu .= '</ul>';
                }

                $addMenu .= '</li>';
            }


            $menuResult = '<ul>
                <li id="home_link"><a href="{panel}/" class="ico_space">Рабочий стол</a></li>
                '.$addMenu.'
                <li>
                    <a href="{panel}/plugins/" class="ico_applications">Плагины и модули</a>
                    <ul>
                        <li><a href="{panel}/plugins/">Плагины</a></li>
                        <li><a href="{panel}/modules/">Модули</a></li>
                        <li class="menu_shop"><a href="{panel}/celenaShop/plugins/">Магазин плагинов и модулей</a></li>
                        <li><a href="{panel}/celenaShop/order-development/">Заказать разработку</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{panel}/celena_shop/templates/" class="ico_template">Шаблоны</a>
                    <ul>
                        <li><a href="{panel}/celena_shop/templates/">Шаблоны</a></li>
                        <li><a href="{panel}/celena_shop/order-development/">Заказать разработку</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{panel}/settings/" class="ico_settings">Настройки</a>
                    <ul>
                        <li><a href="{panel}/settings/">Общие настройки</a></li>
                        <li><a href="{panel}/settings/seo/">SEO</a></li>
                        <li><a href="{panel}/settings/promo-codes/">Промокоды</a></li>
                        <li><a href="{panel}/settings/lang/">Языки</a></li>
                        <li><a href="{panel}/settings/currency/">Валюта</a></li>
                        <li><a href="{panel}/settings/payment-methods/">Способы оплаты</a></li>
                        <li><a href="{panel}/settings/delivery-methods/">Способы доставки</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="ico_system">Система</a>
                    <ul>
                        <li><a href="{panel}/system/routes/">Роуты</a></li>
                        <li><a href="{panel}/system/logs/">Журнал логов</a></li>
                        <li><a href="{panel}/system/db-logs/">Ошибки базы</a></li>
                        <li><a href="{panel}/system/updates/">Обновление <b>Celena</b></a></li>
                    </ul>
                </li>
                <li><a href="{panel}/support/" class="ico_support">Поддержка</a></li>
            </ul>';

            $this->view->setMain('{menu}', $menuResult);
            /**
             * @name MENU END
             * ==============
             */

        } else{

            $this->view->load('PanelAuth');

        }


    }
}