<?php
/**
 * @name Главный контроллер, который вызывается при старте любого контроллера
 */

namespace app\core;

use app\models\CommentsModel;
use app\models\panel\PluginModel;
use app\traits\panel\Notify;

abstract class PanelController{

    use Notify;

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
                "Новости" => [
                    "link" => "{panel}/posts/",
                    "class" => "ico_news",
                    "submenu" => [
                        "Добавить новость" => "{panel}/posts/add/",
                    ]
                ],
                "Страницы" => [
                    "link" => "{panel}/pages/",
                    "class" => "ico_pages",
                ],
                "Категории" => [
                    "link" => "{panel}/category/",
                    "class" => "ico_category",
                    "submenu" => [
                        "Добавить категорию" => "{panel}/category/add/",
                    ]
                ],
                "Пользователи" => [
                    "link" => "{panel}/users/",
                    "class" => "ico_users",
                    "submenu" => [
                        "Пользователи" => "{panel}/users/",
                        "Группы" => "{panel}/roles/",
                    ]
                ],
                "Комментарии" => [
                    "link" => "{panel}/comments/",
                    "class" => "ico_comments",
                ],
                "Настройки" => [
                    "link" => "{panel}/settings/",
                    "class" => "ico_settings",
                    "submenu" => [
                        "Общие настройки" => "{panel}/settings/",
                        "Дополнительные поля" => "{panel}/fields/",
                        "SEO" => "{panel}/settings/seo/",
                        "Карта сайта" => "{panel}/settings/sitemap/",
                        "Языки" => "{panel}/settings/lang/",
                    ]
                ]
            ];

            // tmp
            /*die(json_encode(["Меню моего плагина" => [
                "link" => "#",
                "class" => "ico_space",
                "icon" => "",
                "informer" => [
                    "name" => "settings",
                    "status" => 1, // 1-5
                    "num" => 5,
                ],
                "submenu" => [
                    "Пункт меню 1" => "#",
                    "Пункт меню 2" => "#",
                    "Пункт меню 3" => "#",
                ]
            ]], JSON_UNESCAPED_UNICODE));*/

            $PluginModel = new PluginModel();
            $PluginsInfo = $PluginModel->getPluginField('plugin_id, name, version, status');


            if(!empty($PluginsInfo)){

                $pluginsMenu = [];
                foreach ($PluginsInfo as $row) {

                    if(file_exists(APP . '/plugins/'.$row["name"].'/system.json')){

                        $PluginSystem = json_decode(file_get_contents(APP . '/plugins/'.$row["name"].'/system.json'), true);

                        $this->pluginsSystems[$row["name"]] = $PluginSystem;
                        $this->pluginsSystems[$row["name"]]["brandName"] = $row["name"];

                        if($row["status"] != 0){

                            //if(!empty($this->plugin->system->name) && $this->plugin->system->name == $row["name"]) define("PLUGIN", $row);

                            if(!empty($PluginSystem["editMenu"])){

                                foreach ($PluginSystem["editMenu"] as $menuName => $itemMenu) {

                                    if(!empty($itemMenu["link"])) $menu[$menuName]["link"] = $itemMenu["link"];
                                    if(!empty($itemMenu["submenu"])) $menu[$menuName]["submenu"] = array_merge($menu[$menuName]["submenu"], $itemMenu["submenu"]);
                                }
                            }
                        }

                        if($row["status"] == '1') $pluginsMenu = array_merge($pluginsMenu, $PluginSystem["menu"]);

                    } else{

                        die("Плагин <b>{$row["name"]}</b> отсутствует конфигурационный файл, который должен быть обязательно!");
                    }
                }

                # TODO хз, чето надо придумать ещё тут...
                $this->pluginsSystems = json_decode(json_encode($this->pluginsSystems));

                $menu = array_merge($pluginsMenu, $menu);
            }

            $addMenu = '';


            // Informers
            $informerSystem = 0;

            if(!empty($menu["Комментарии"])){

                $CommentsModel = new CommentsModel();
                $CounterComments = $CommentsModel->getCounter();

                if(!empty($CounterComments)) $menu["Комментарии"]["informer"] = [
                    "name" => "settings",
                    "status" => 3,
                    "num" => $CounterComments,
                ];
            }

            if(!CONFIG_SYSTEM["power"]){

                $menu["Настройки"]["informer"] = [
                    "name" => "settings",
                    "num" => '<img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMSIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbG5zOnN2Z2pzPSJodHRwOi8vc3ZnanMuY29tL3N2Z2pzIiB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgeD0iMCIgeT0iMCIgdmlld0JveD0iMCAwIDQ4OS44ODggNDg5Ljg4OCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTEyIDUxMiIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgY2xhc3M9IiI+PGc+DQo8ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPg0KCTxnPg0KCQk8cGF0aCBkPSJNMjUuMzgzLDI5MC41Yy03LjItNzcuNSwyNS45LTE0Ny43LDgwLjgtMTkyLjNjMjEuNC0xNy40LDUzLjQtMi41LDUzLjQsMjVsMCwwYzAsMTAuMS00LjgsMTkuNC0xMi42LDI1LjcgICAgYy0zOC45LDMxLjctNjIuMyw4MS43LTU2LjYsMTM2LjljNy40LDcxLjksNjUsMTMwLjEsMTM2LjgsMTM4LjFjOTMuNywxMC41LDE3My4zLTYyLjksMTczLjMtMTU0LjVjMC00OC42LTIyLjUtOTIuMS01Ny42LTEyMC42ICAgIGMtNy44LTYuMy0xMi41LTE1LjYtMTIuNS0yNS42bDAsMGMwLTI3LjIsMzEuNS00Mi42LDUyLjctMjUuNmM1MC4yLDQwLjUsODIuNCwxMDIuNCw4Mi40LDE3MS44YzAsMTI2LjktMTA3LjgsMjI5LjItMjM2LjcsMjE5LjkgICAgQzEyMi4xODMsNDgxLjgsMzUuMjgzLDM5Ni45LDI1LjM4MywyOTAuNXogTTI0NC44ODMsMGMtMTgsMC0zMi41LDE0LjYtMzIuNSwzMi41djE0OS43YzAsMTgsMTQuNiwzMi41LDMyLjUsMzIuNSAgICBzMzIuNS0xNC42LDMyLjUtMzIuNVYzMi41QzI3Ny4zODMsMTQuNiwyNjIuODgzLDAsMjQ0Ljg4MywweiIgZmlsbD0iI2Y3NTk1OSIgZGF0YS1vcmlnaW5hbD0iIzAwMDAwMCIgY2xhc3M9IiI+PC9wYXRoPg0KCTwvZz4NCjwvZz4NCjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+DQo8L2c+DQo8ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPg0KPC9nPg0KPGcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCjwvZz4NCjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+DQo8L2c+DQo8ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPg0KPC9nPg0KPGcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCjwvZz4NCjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+DQo8L2c+DQo8ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPg0KPC9nPg0KPGcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCjwvZz4NCjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+DQo8L2c+DQo8ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPg0KPC9nPg0KPGcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCjwvZz4NCjxnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+DQo8L2c+DQo8ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPg0KPC9nPg0KPGcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4NCjwvZz4NCjwvZz48L3N2Zz4=">',
                ];
            }

            $dbErrors = 0;
            if(file_exists(CORE . '/tmp/db_errors.txt') && filesize(CORE . '/tmp/db_errors.txt') > 5){
                $informerSystem++;
                $dbErrors = 1;
            }
            if(!empty(CONFIG_SYSTEM["system_update"])) $informerSystem = "♻️";
            // Informers END


            foreach ($menu as $titleMenu => $itemMenu) {

                if(!empty($itemMenu["informer"])){

                    $informerStatus = "";
                    if(!empty($itemMenu["informer"]["status"])){
                        switch ($itemMenu["informer"]["status"]){
                            case 1: $informerStatus = "informer_1"; break;
                            case 2: $informerStatus = "informer_2"; break;
                            case 3: $informerStatus = "informer_3"; break;
                            case 4: $informerStatus = "informer_4"; break;
                            case 5: $informerStatus = "informer_5"; break;
                        }
                    }

                    if(!is_numeric($itemMenu["informer"]["num"])) $informerStatus .= ' informer_symbol';

                    $titleMenu .= '<span data-informer="'.$itemMenu["informer"]["name"].'" class="'.$informerStatus.'">'.$itemMenu["informer"]["num"].'</span>';
                }

                $addMenu .= '<li><a href="'.$itemMenu["link"].'" class="'.$itemMenu["class"].'">'.$titleMenu.'</a>';

                if(!empty($itemMenu["submenu"])){

                    $addMenu .= '<ul>';
                    foreach ($itemMenu["submenu"] as $titleSubmenu => $submenu) {

                        if(is_array($submenu)){

                            if(!empty($submenu["informer"])){

                                $informerStatus = "";
                                if(!empty($submenu["informer"]["status"])){
                                    switch ($submenu["informer"]["status"]){
                                        case 1: $informerStatus = "informer_1"; break;
                                        case 2: $informerStatus = "informer_2"; break;
                                        case 3: $informerStatus = "informer_3"; break;
                                        case 4: $informerStatus = "informer_4"; break;
                                        case 5: $informerStatus = "informer_5"; break;
                                    }
                                }

                                if(!is_numeric($submenu["informer"]["num"])) $informerStatus .= ' informer_symbol';

                                $titleSubmenu .= '<span data-informer="'.$itemMenu["informer"]["name"].'" class="'.$informerStatus.'">'.$submenu["informer"]["num"].'</span>';
                            }

                            $addMenu .= '<li><a href="'.$submenu["link"].'" class="'.(!empty($submenu["class"])?$submenu["class"]:'').'">'.$titleSubmenu.'</a>';

                        } else $addMenu .= '<li><a href="'.$submenu.'">'.$titleSubmenu.'</a>';
                    }
                    $addMenu .= '</ul>';
                }

                $addMenu .= '</li>';
            }


            $menuResult = '<ul>
                <li id="home_link"><a href="{panel}/" class="ico_space">Рабочий стол</a></li>
                '.$addMenu.'
                <li>
                    <a href="{panel}/plugins/" class="ico_applications">Плагины и модули'.(!empty(CONFIG_SYSTEM["plugins_update"])?'<span class="update_icon"></span>':'').'</a>
                    <ul>
                        <li><a href="{panel}/plugins/">Плагины</a></li>
                        <li><a href="{panel}/modules/">Модули</a></li>
                        <li class="menu_shop"><a href="{panel}/celenaShop/plugins/">Магазин плагинов и модулей</a></li>
                        <li><a href="{panel}/celenaShop/order-development/">Заказать разработку</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{panel}/templates/" class="ico_template">Шаблоны</a>
                    <ul>
                        <li><a href="{panel}/templates/">Шаблоны</a></li>
                        <li class="menu_shop"><a href="{panel}/celenaShop/templates/">Магазин шаблонов</a></li>
                        <li><a href="{panel}/celenaShop/order-development/">Заказать разработку</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="ico_system">Система'.(!empty(CONFIG_SYSTEM["system_update"])?'<span class="update_icon"></span>':($informerSystem?'<span data-informer="system" class="informer_5">'.$informerSystem.'</span>':'')).'</a>
                    <ul>
                        <li><a href="{panel}/system/routes/">Роуты</a></li>
                        <li><a href="{panel}/system/logs/">Журнал логов</a></li>
                        <li><a href="{panel}/system/db-logs/">Ошибки базы'.($dbErrors?'<span data-informer="dbErrors" class="informer_symbol informer_5">⚠️</span>':'').'</a></li>
                        <li><a href="{panel}/system/updates/">Обновление <b>Celena</b>'.(!empty(CONFIG_SYSTEM["system_update"])?'<span class="update_icon"></span>':'').'</a></li>
                        <li><a href="{panel}/system/info/">Информация</a></li>
                    </ul>
                </li>
                <li><a href="{panel}/support/" class="ico_support">Поддержка</a></li>
            </ul>';

            $this->view->setMain('{menu}', $menuResult);


            //self::addNotify("Плагин", "Доступно обновление Celena 1.0.0");
            $Notify = self::getNotify();

            $notify = '<div id="notification">
                <a href="#" class="ico_notify" title="Уведомления"></a>
            </div>';

            if(!empty($Notify)){

                $newMessages = 0;
                $messages = '';
                foreach ($Notify as $row) {

                    if($row["see"] == 0) $newMessages++;
                    $newNotifyClass = ($row["see"] == 1) ?  'notify_see' : '';

                    $link = !empty($row["link"]) ? str_replace(['{home}', '{panel}'], [CONFIG_SYSTEM["home"], CONFIG_SYSTEM["panel"]], $row["link"]) : '';

                    $messages .= '<li class="'.$newNotifyClass.' notify_'.$row["status"].'">
                        <a href="#" data-s="Notify:see='.$row["id"].'&link='.$link.'">
                            <span class="new_lead">'.$row["title"].'</span>
                            <p>'.$row["message"].'</p>
                        </a>
                    </li>';
                }

                $notify = '<div id="notification">
                <a href="#" class="ico_notify" title="Уведомления">
                    '.(!empty($newMessages) ? '<span>'.$newMessages.'</span>' : '').'
                </a>
                <ul class="esc">'.$messages.'</ul>
                </div>';
            }

            $this->view->setMain('{notify}', $notify);

            unset($menu, $addMenu, $menuResult);
            /**
             * @name MENU END
             * ==============
             */

        } else{

            $this->view->load('PanelAuth');

        }


    }
}