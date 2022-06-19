<?php

namespace app\plugins\Celena\Shop;

use app\core\Base;
use app\core\interface\InitPlugin;
use app\core\System;
use app\traits\Log;

/**
 * @name Установка/включение/выключение/удаление/плагина
 * =====================================================
 */
class Init implements InitPlugin {


    // install...
    public function install()
    {

        // добавление таблиц
        Base::run("CREATE TABLE " . PREFIX . "properties_v (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `pid` int(11) NOT NULL COMMENT 'property_id',
            `val` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `def` tinyint(1) DEFAULT NULL COMMENT 'null - нет умолчания',
            `position` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            KEY `pid` (`pid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        Base::run("CREATE TABLE " . PREFIX . "properties (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `url` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'null - не участвует в фильтре',
            `f_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1-select, 2-checkbox, 3-radio',
            `cid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Categories ids. null - во всех категориях.',
            `option` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 - не выводить, 1-выводить сразу',
            `sep` tinyint(1) DEFAULT 0 COMMENT '0 - нельзя произвольный вариант, 1 можно',
            `req_p` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - не обязательно заполнять, 1 - обязательно',
            `req` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - не обязательно заполнять при покупке, 1 - обязательно',
            `position` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        Base::run("CREATE TABLE " . PREFIX . "product_prop (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `pid` int(11) NOT NULL DEFAULT 0 COMMENT 'ID товара',
            `id_p` int(11) NOT NULL COMMENT 'ID свойства',
            `id_pv` int(11) NOT NULL DEFAULT 0 COMMENT 'ID значения свойства',
            `sep` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'произвольное значение',
            `vendor` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'артикул',
            `price` decimal(10,2) NOT NULL DEFAULT 0.00,
            `pv` tinyint(1) DEFAULT NULL COMMENT 'price variant: null - новая цена, 0 - минус, 1 - плюс, 2 - -%, 3 - +%',
            `stock` int(11) DEFAULT NULL COMMENT 'null - без лимит',
            PRIMARY KEY (`id`),
            KEY `pid` (`pid`),
            KEY `id_p` (`id_p`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        Base::run("CREATE TABLE " . PREFIX . "products_cat (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `pid` int(11) NOT NULL,
            `cid` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `pid` (`pid`),
            KEY `cid` (`cid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        Base::run("CREATE TABLE " . PREFIX . "products (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `uid` int(11) NOT NULL,
            `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `m_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '''''',
            `m_description` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '''''',
            `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
            `category` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '''''',
            `vendor` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'артикул',
            `brand` int(11) DEFAULT NULL,
            `price` decimal(10,2) NOT NULL DEFAULT 0.00,
            `sale` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `stock` int(11) DEFAULT NULL COMMENT 'null - неограничено',
            `url` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
            `poster` int(11) NOT NULL DEFAULT 0,
            `created` int(11) NOT NULL,
            `last_modify` int(11) DEFAULT NULL,
            `status` tinyint(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`),
            KEY `uid` (`uid`),
            KEY `brand` (`brand`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        Base::run("CREATE TABLE " . PREFIX . "orders_status (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `color` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ffffff',
            `pos` int(11) NOT NULL DEFAULT 0 COMMENT 'position',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        Base::run("CREATE TABLE " . PREFIX . "orders_ex (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `oid` int(11) NOT NULL COMMENT 'order id',
            `pid` int(11) NOT NULL COMMENT 'product id',
            `count` int(10) NOT NULL DEFAULT 1,
            `props` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'properties ids',
            PRIMARY KEY (`id`),
            KEY `oid` (`oid`),
            KEY `pid` (`pid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        Base::run("CREATE TABLE " . PREFIX . "orders (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `uid` int(11) NOT NULL DEFAULT 0 COMMENT 'id того кто создал',
            `buyer_id` int(11) NOT NULL DEFAULT 0 COMMENT 'id покупателя',
            `order_id` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '''''',
            `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `tel` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `payment_id` int(11) NOT NULL DEFAULT 0,
            `prod_ids` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '''''',
            `total` decimal(10,2) NOT NULL DEFAULT 0.00,
            `comment` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '''''',
            `hash` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '''''',
            `created` int(11) NOT NULL,
            `paid` tinyint(1) NOT NULL DEFAULT 0,
            `status` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            KEY `uid` (`uid`),
            KEY `buyer_id` (`buyer_id`),
            KEY `order_id` (`order_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        Base::run("CREATE TABLE " . PREFIX . "brands (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
            `url` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `categories` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");


        // добавление данных в таблицу
        /*Base::run("INSERT INTO `".PREFIX."products`
            (`uid`, `title`, `m_title`, `m_description`, `content`, `category`, `vendor`, `brand`, `price`, `sale`, `stock`, `url`, `poster`, `created`, `last_modify`, `status`)
        VALUES
            (1, 'Ноутбук Acer Swift 1 SF114-34 (NX.A77EU.00S) Pure Silver', 'Ноутбук Acer Swift 1 SF114-34 (NX.A77EU.00S) Pure Silver - купить в Украине', 'Отличный ноутбук фирмы Aser, который гарантирует приятную работу. Кчество на высоте! Покупаем быстренько.', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad aperiam asperiores, autem deserunt dolore dolorum eaque eligendi excepturi, explicabo harum illo itaque minus modi saepe sequi tempore vitae voluptates. Atque incidunt inventore laborum optio?</p><p>A ab accusamus ad amet aperiam asperiores atque beatae commodi cum deleniti dolorem dolores et explicabo facere harum illo in ipsa iure laborum laudantium maiores minus molestias necessitatibus neque omnis perspiciatis porro provident, quaerat quas quo quos reiciendis sint unde velit veniam voluptate voluptatibus! Ab ad aliquid commodi dignissimos doloremque dolores ex iusto laudantium nesciunt, quidem quis quisquam, sequi vel? Corporis dolore ex fugiat incidunt neque.</p>', '2,1', '1794625', 1, '16999.00', '5%', 10, 'noutbuk-acer-swift-1-sf114-34-nxa77eu00s-pure-silver', 3, 1652973511, 1652981242, 1),
            (1, 'Ноутбук игровой Lenovo Legion5 15IMH05 (82AU00Q8RA) Phantom Black', 'Ноутбук игровой Lenovo Legion5 15IMH05 (82AU00Q8RA) Phantom Black купить', 'Купите Ноутбук игровой Lenovo Legion5 15IMH05 (82AU00Q8RA) Phantom Black у нас и радуйтесь!', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad aperiam asperiores, autem deserunt dolore dolorum eaque eligendi excepturi, explicabo harum illo itaque minus modi saepe sequi tempore vitae voluptates. Atque incidunt inventore laborum optio? A ab accusamus ad amet aperiam asperiores atque beatae commodi cum deleniti dolorem dolores et explicabo facere harum illo in ipsa iure laborum laudantium maiores minus molestias necessitatibus neque omnis perspiciatis porro provident, quaerat quas quo quos reiciendis sint unde velit veniam voluptate voluptatibus! Ab ad aliquid commodi dignissimos doloremque dolores ex iusto laudantium nesciunt, quidem quis quisquam, sequi vel? Corporis dolore ex fugiat incidunt neque.</p>', '2,1', '1875053', 2, '34999.00', '4000', 17, 'noutbuk-igrovoy-lenovo-legion5-15imh05-82au00q8ra-phantom-black', 6, 1652974080, 1652981228, 1),
            (1, 'Стиральная машина Samsung WW70R62LATWDUA', 'Стиральная машина Samsung WW70R62LATWDUA купить', 'Купить новую стиралку Samsung WW70R62LATWDUA', '<p id=\"isPasted\">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad aperiam asperiores, autem deserunt dolore dolorum eaque eligendi excepturi, explicabo harum illo itaque minus modi saepe sequi tempore vitae voluptates. Atque incidunt inventore laborum optio?</p><p>A ab accusamus ad amet aperiam asperiores atque beatae commodi cum deleniti dolorem dolores et explicabo facere harum illo in ipsa iure laborum laudantium maiores minus molestias necessitatibus neque omnis perspiciatis porro provident, quaerat quas quo quos reiciendis sint unde velit veniam voluptate voluptatibus! Ab ad aliquid commodi dignissimos doloremque dolores ex iusto laudantium nesciunt, quidem quis quisquam, sequi vel? Corporis dolore ex fugiat incidunt neque.</p>', '4,3', '1469028', 4, '20999.00', '1500', 5, 'stiralynaya-mashina-samsung-ww70r62latwdua', 16, 1652976276, 1652978236, 1),
            (1, 'Стиральная машина LG F2V9HS9T', 'Стиральная машина LG F2V9HS9T купить online', 'Купить класную стиралку LG F2V9HS9T', '<p id=\"isPasted\">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad aperiam asperiores, autem deserunt dolore dolorum eaque eligendi excepturi, explicabo harum illo itaque minus modi saepe sequi tempore vitae voluptates. Atque incidunt inventore laborum optio?</p><p><span id=\"isPasted\" style=\"color: rgb(65, 65, 65); font-family: Roboto, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: left; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial; display: inline !important; float: none;\">Ab ad aliquid commodi dignissimos doloremque dolores ex iusto laudantium nesciunt, quidem quis quisquam, sequi vel? Corporis dolore ex fugiat incidunt neque.</span></p><p>A ab accusamus ad amet aperiam asperiores atque beatae commodi cum deleniti dolorem dolores et explicabo facere harum illo in ipsa iure laborum laudantium maiores minus molestias necessitatibus neque omnis perspiciatis porro provident, quaerat quas quo quos reiciendis sint unde velit veniam voluptate voluptatibus! Ab ad aliquid commodi dignissimos doloremque dolores ex iusto laudantium nesciunt, quidem quis quisquam, sequi vel? Corporis dolore ex fugiat incidunt neque.</p>', '4,3', '1710049', 4, '20099.00', NULL, 3, 'stiralynaya-mashina-lg-f2v9hs9t', 19, 1652978409, 1652979009, 1)");


        Base::run("INSERT INTO `".PREFIX."products_cat`
            (`pid`, `cid`)
        VALUES
            (1, 2),
            (1, 1),
            (2, 2),
            (2, 1),
            (3, 4),
            (3, 3),
            (4, 4),
            (4, 3)");


        Base::run("INSERT INTO `".PREFIX."product_prop`
            (`pid`, `id_p`, `id_pv`, `sep`, `vendor`, `price`, `pv`, `stock`)
        VALUES
            (1, 2, 9, '', '', '0.00', NULL, NULL),
            (1, 1, 1, '', '', '0.00', NULL, 3),
            (1, 1, 2, '', '', '200.00', 1, 7),
            (2, 2, 6, '', '', '0.00', NULL, NULL),
            (2, 1, 3, '', '', '0.00', NULL, 10),
            (2, 1, 1, '', '', '200.00', 1, 3),
            (2, 1, 2, '', '', '350.00', 1, 4),
            (3, 1, 2, '', '', '0.00', NULL, NULL),
            (4, 1, 1, '', '', '0.00', NULL, NULL),
            (4, 1, 3, '', '', '0.00', NULL, NULL)");


        Base::run("INSERT INTO `".PREFIX."properties`
            (`title`, `url`, `f_type`, `cid`, `option`, `sep`, `req_p`, `req`, `position`)
        VALUES
            ('Цвет', 'color', 2, NULL, 1, 0, 1, 1, 0),
            ('Гарантия', 'garanty', 1, '2,1', 1, 0, 1, 1, 0)");


        Base::run("INSERT INTO `".PREFIX."properties_v`
            (`pid`, `val`, `def`, `position`)
        VALUES
            (1, 'Серый', NULL, 0),
            (1, 'Белый', 1, 0),
            (1, 'Черный', NULL, 0),
            (1, 'Цветной', NULL, 0),
            (2, '1 год', 1, 0),
            (2, '2 года', NULL, 0),
            (2, '3 года', NULL, 0),
            (2, '4 года', NULL, 0),
            (2, '5 лет', NULL, 0),
            (2, '10 лет', NULL, 0)");


        Base::run("INSERT INTO `".PREFIX."brands`
            (`name`, `url`, `icon`, `categories`)
        VALUES
            ('Aser', 'aser', '1_1652972384775.svg', '1'),
            ('Lenovo', 'lenovo', '2_1652972441449.svg', '1'),
            ('Asus', 'asus', '3_1652972460518.svg', '1'),
            ('Samsung', 'samsung', '4_1652974766703.svg', '3')");


        Base::run("INSERT INTO `".PREFIX."categories`
            (`title`, `m_title`, `m_description`, `content`, `icon`, `url`, `pid`, `status`)
        VALUES
            ('Ноутбуки, планшеты и компьютерная техника', 'Ноутбуки, планшеты и компьютерная техника', 'Ноутбуки, планшеты и компьютерная техника - описание...', 'Описание для категории техники...', '1_1652972248247.webp', 'tech', NULL, 1),
            ('Ноутбуки', 'Ноутбуки для дома и офиса', 'Ноутбуки для дома и офиса - описание', '', '2_1652972581933.webp', 'noutbuki', 1, 1),
            ('Техника для дома', 'Купить технику для дома', 'У нас самая новейшая техника для вашего дома', 'Описание для техники для дома...', '3_1652974677219.webp', 'tehnika-dlya-doma', NULL, 1),
            ('Крупная техника', 'Техника крупная купить онлайн', 'Стиралки, холодильники и всякая техника', 'Описание для техники крупной', '4_1652976017789.webp', 'krupnaya-tehnika', 3, 1)");


        Base::run("INSERT INTO `".PREFIX."images`
            (`itype`, `nid`, `src`, `alt`, `position`, `status`)
        VALUES
            (1, 1, '2022-05/1_1652973511_acer_swift_1_sf114-33_silver_01_3_1.webp', '', 0, NULL),
            (1, 1, '2022-05/1_1652973511_acer_swift_1_sf114-33_silver_03_3_1.webp', '', 0, NULL),
            (1, 1, '2022-05/1_1652973511_acer_swift_1_sf114-33_silver_04_3_1.webp', '', 0, NULL),
            (1, 1, '2022-05/1_1652973511_acer_swift_1_sf114-33_silver_05_3_1.webp', '', 0, NULL),
            (1, 1, '2022-05/1_1652973511_acer_swift_1_sf114-33_silver_08_3_1.webp', '', 0, NULL),
            (1, 2, '2022-05/2_1652974080_yqndd-eg.webp', '', 0, NULL),
            (1, 2, '2022-05/2_1652974080_6172ad22380b5395308786.webp', '', 0, NULL),
            (1, 2, '2022-05/2_1652974080_6172ad2350d5c907652907.webp', '', 0, NULL),
            (1, 2, '2022-05/2_1652974080_6172ad2465e4f988758904.webp', '', 0, NULL),
            (1, 2, '2022-05/2_1652974080_6172ad26a7208618948151.webp', '', 0, NULL),
            (1, 2, '2022-05/2_1652974080_6172ad2c6ca34928997843.webp', '', 0, NULL),
            (1, 3, '2022-05/3_1652976365_samsung_ww70r62latwdua.webp', '', 2, NULL),
            (1, 3, '2022-05/3_1652976365_samsung_ww70r62latwdua_2.webp', '', 3, NULL),
            (1, 3, '2022-05/3_1652976366_samsung_ww70r62latwdua_3.webp', '', 1, NULL),
            (1, 3, '2022-05/3_1652976366_samsung_ww70r62latwdua_4.webp', '', 4, NULL),
            (1, 3, '2022-05/3_1652976366_samsung_ww70r62latwdua_10.webp', '', 0, NULL),
            (1, 4, '2022-05/4_1652978993_f2v9hs9t_01_front_with_medals_and_logos_1_.webp', '', 1, NULL),
            (1, 4, '2022-05/4_1652978994_f2v9hs9t-07-dimensions-desktop_1_.webp', '', 2, NULL),
            (1, 4, '2022-05/4_1652978994_z01.webp', '', 0, NULL),
            (1, 4, '2022-05/4_1652979009_5fb69b1b090c8_f2v9hs9t_15_right_drawer_details.webp', '', 3, NULL),
            (1, 4, '2022-05/4_1652979009_5fb69b15af236_f2v9hs9t_06_right_door_open.webp', '', 4, NULL),
            (1, 4, '2022-05/4_1652979009_5fb69b16cea98_f2v9hs9t_08_right_low_perspective.webp', '', 5, NULL),
            (1, 4, '2022-05/4_1652979009_5fb69b19d6d7b_f2v9hs9t_13_front_top_drawer_details.webp', '', 6, NULL),
            (1, 4, '2022-05/4_1652979009_5fb69b1823623_f2v9hs9t_10_left_top_perspective_drawer_open.webp', '', 7, NULL)");


        Base::run("INSERT INTO `".PREFIX."orders`
            (`uid`, `buyer_id`, `order_id`, `name`, `email`, `tel`, `address`, `payment_id`, `prod_ids`, `total`, `comment`, `hash`, `created`, `paid`, `status`)
        VALUES
            (1, 1, '6T1652983373', 'Владимир', 'masterz1zzz@gmail.com', '+380952059675', '', 0, '2,3,4', '113594.00', 'Хочу все купить', 'e6778c02f4216afa154d9255fce728577b9d691c', 1652983373, 0, 0)");


        Base::run("INSERT INTO `".PREFIX."orders_ex`
            (`oid`, `pid`, `count`, `props`)
        VALUES
            (1, 2, 2, '4,7|4,7'),
            (1, 3, 1, '8'),
            (1, 4, 3, '9')");


        Base::run("INSERT INTO `".PREFIX."orders_status`
            (`name`, `color`, `pos`)
        VALUES
            ('Новый', 'ff2d2d', 1),
            ('Принят', '17d5d5', 2),
            ('У курьера', '906ee3', 3),
            ('Выполнен', '69d924', 4)");*/

        // добавление роутов
        self::addRoutes();

        return true;
    }


    // power on...
    public function powerOn()
    {
        self::addRoutes();
        return true;
    }


    // power off...
    public function powerOff()
    {
        self::deleteRoutes();
        return true;
    }


    // delete...
    public function delete()
    {

        // удаление таблиц
        Base::run("DROP TABLE IF EXISTS " . PREFIX . "brands");
        Base::run("DROP TABLE IF EXISTS " . PREFIX . "orders");
        Base::run("DROP TABLE IF EXISTS " . PREFIX . "orders_ex");
        Base::run("DROP TABLE IF EXISTS " . PREFIX . "orders_status");
        Base::run("DROP TABLE IF EXISTS " . PREFIX . "products");
        Base::run("DROP TABLE IF EXISTS " . PREFIX . "products_cat");
        Base::run("DROP TABLE IF EXISTS " . PREFIX . "product_prop");
        Base::run("DROP TABLE IF EXISTS " . PREFIX . "properties");
        Base::run("DROP TABLE IF EXISTS " . PREFIX . "properties_v");

        self::deleteRoutes();

        return true;
    }



    private function addRoutes(){

        // добавление роутов
        $resultAdd = System::addRoute([
            'panel' => [
                'products/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Products'],
                'products/(add/|edit/([0-9]+/)?)$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'addProduct'],
                'products/categories/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'categories'],
                'products/categories/(add/|edit/([0-9]+/)?)$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'addCategory'],
                'products/brands/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'brands'],
                'products/brands/(add/|edit/([0-9]+/)?)$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'addBrand'],
                'products/properties/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'properties'],
                'products/properties/(add/|edit/([0-9]+/)?)$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'addProperty'],
                'orders/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Orders'],
                'orders/[0-9]+/$' => ['controller' => 'plugins\Celena\Shop\Orders', 'action' => 'order'],
                'orders/click/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Orders', 'action' => 'click'],
                'settings/shop/$' => ['controller' => 'plugins\Celena\Shop\Settings'],
                'settings/promo-codes/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Settings', 'action' => 'promoCodes'],
                'settings/currency/$' => ['controller' => 'plugins\Celena\Shop\Settings', 'action' => 'currency'],
                'settings/payment-methods/$' => ['controller' => 'plugins\Celena\Shop\Settings', 'action' => 'paymentMethods'],
                'settings/delivery-methods/$' => ['controller' => 'plugins\Celena\Shop\Settings', 'action' => 'deliveryMethods'],
            ],
            'web' => [
                '(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Index'],
                '([a-z-/0-9]+).html$' => ['controller' => 'plugins\Celena\Shop\Product'],
                'search/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Search'],
                'cart/$' => ['controller' => 'plugins\Celena\Shop\Cart'],
                '(.+?)/$' => ['controller' => 'plugins\Celena\Shop\Category'],
            ]
        ], 0);

        if(!$resultAdd){
            Log::add('Не удалось добавить роуты при установке плагина', 2);
            return 'Не удалось добавить роуты';
        }
    }



    private function deleteRoutes(){

        // удаление роутов
        $resultRemoved = System::removeRoute([
            'panel' => [
                'products/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Products'],
                'products/(add/|edit/([0-9]+/)?)$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'addProduct'],
                'products/categories/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'categories'],
                'products/categories/(add/|edit/([0-9]+/)?)$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'addCategory'],
                'products/brands/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'brands'],
                'products/brands/(add/|edit/([0-9]+/)?)$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'addBrand'],
                'products/properties/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'properties'],
                'products/properties/(add/|edit/([0-9]+/)?)$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'addProperty'],
                'orders/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Orders'],
                'orders/[0-9]+/$' => ['controller' => 'plugins\Celena\Shop\Orders', 'action' => 'order'],
                'orders/click/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Orders', 'action' => 'click'],
                'settings/shop/$' => ['controller' => 'plugins\Celena\Shop\Settings'],
                'settings/promo-codes/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Settings', 'action' => 'promoCodes'],
                'settings/currency/$' => ['controller' => 'plugins\Celena\Shop\Settings', 'action' => 'currency'],
                'settings/payment-methods/$' => ['controller' => 'plugins\Celena\Shop\Settings', 'action' => 'paymentMethods'],
                'settings/delivery-methods/$' => ['controller' => 'plugins\Celena\Shop\Settings', 'action' => 'deliveryMethods'],
            ],
            'web' => [
                '(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Index'],
                '([a-z-/0-9]+).html$' => ['controller' => 'plugins\Celena\Shop\Product'],
                'search/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Search'],
                'cart/$' => ['controller' => 'plugins\Celena\Shop\Cart'],
                '(.+?)/$' => ['controller' => 'plugins\Celena\Shop\Category'],
            ]
        ]);

        if(!$resultRemoved){
            Log::add('Не удалось удалить роуты при удалении плагина', 2);
            return 'Не удалось удалить роуты';
        }
    }

}