<?php

namespace app\core\system\install\steps\addon;


class FillBase{

    public static function fill($db, $PREFIX){


        $query = $db->prepare("INSERT INTO {$PREFIX}roles
                (name, rules)
            VALUES (?, ?)");
        $query->execute(['Администратор', null]);
        $query->execute(['Пользователь', null]);


        $query = $db->prepare("INSERT INTO `{$PREFIX}products`
            (`uid`, `title`, `m_title`, `m_description`, `content`, `category`, `vendor`, `brand`, `price`, `sale`, `stock`, `url`, `poster`, `created`, `last_modify`, `status`)
        VALUES
            (1, 'Ноутбук Acer Swift 1 SF114-34 (NX.A77EU.00S) Pure Silver', 'Ноутбук Acer Swift 1 SF114-34 (NX.A77EU.00S) Pure Silver - купить в Украине', 'Отличный ноутбук фирмы Aser, который гарантирует приятную работу. Кчество на высоте! Покупаем быстренько.', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad aperiam asperiores, autem deserunt dolore dolorum eaque eligendi excepturi, explicabo harum illo itaque minus modi saepe sequi tempore vitae voluptates. Atque incidunt inventore laborum optio?</p><p>A ab accusamus ad amet aperiam asperiores atque beatae commodi cum deleniti dolorem dolores et explicabo facere harum illo in ipsa iure laborum laudantium maiores minus molestias necessitatibus neque omnis perspiciatis porro provident, quaerat quas quo quos reiciendis sint unde velit veniam voluptate voluptatibus! Ab ad aliquid commodi dignissimos doloremque dolores ex iusto laudantium nesciunt, quidem quis quisquam, sequi vel? Corporis dolore ex fugiat incidunt neque.</p>', '2,1', '1794625', 1, '16999.00', '5%', 10, 'noutbuk-acer-swift-1-sf114-34-nxa77eu00s-pure-silver', 3, 1652973511, 1652981242, 1),
            (1, 'Ноутбук игровой Lenovo Legion5 15IMH05 (82AU00Q8RA) Phantom Black', 'Ноутбук игровой Lenovo Legion5 15IMH05 (82AU00Q8RA) Phantom Black купить', 'Купите Ноутбук игровой Lenovo Legion5 15IMH05 (82AU00Q8RA) Phantom Black у нас и радуйтесь!', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad aperiam asperiores, autem deserunt dolore dolorum eaque eligendi excepturi, explicabo harum illo itaque minus modi saepe sequi tempore vitae voluptates. Atque incidunt inventore laborum optio? A ab accusamus ad amet aperiam asperiores atque beatae commodi cum deleniti dolorem dolores et explicabo facere harum illo in ipsa iure laborum laudantium maiores minus molestias necessitatibus neque omnis perspiciatis porro provident, quaerat quas quo quos reiciendis sint unde velit veniam voluptate voluptatibus! Ab ad aliquid commodi dignissimos doloremque dolores ex iusto laudantium nesciunt, quidem quis quisquam, sequi vel? Corporis dolore ex fugiat incidunt neque.</p>', '2,1', '1875053', 2, '34999.00', '4000', 17, 'noutbuk-igrovoy-lenovo-legion5-15imh05-82au00q8ra-phantom-black', 6, 1652974080, 1652981228, 1),
            (1, 'Стиральная машина Samsung WW70R62LATWDUA', 'Стиральная машина Samsung WW70R62LATWDUA купить', 'Купить новую стиралку Samsung WW70R62LATWDUA', '<p id=\"isPasted\">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad aperiam asperiores, autem deserunt dolore dolorum eaque eligendi excepturi, explicabo harum illo itaque minus modi saepe sequi tempore vitae voluptates. Atque incidunt inventore laborum optio?</p><p>A ab accusamus ad amet aperiam asperiores atque beatae commodi cum deleniti dolorem dolores et explicabo facere harum illo in ipsa iure laborum laudantium maiores minus molestias necessitatibus neque omnis perspiciatis porro provident, quaerat quas quo quos reiciendis sint unde velit veniam voluptate voluptatibus! Ab ad aliquid commodi dignissimos doloremque dolores ex iusto laudantium nesciunt, quidem quis quisquam, sequi vel? Corporis dolore ex fugiat incidunt neque.</p>', '4,3', '1469028', 4, '20999.00', '1500', 5, 'stiralynaya-mashina-samsung-ww70r62latwdua', 16, 1652976276, 1652978236, 1),
            (1, 'Стиральная машина LG F2V9HS9T', 'Стиральная машина LG F2V9HS9T купить online', 'Купить класную стиралку LG F2V9HS9T', '<p id=\"isPasted\">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad aperiam asperiores, autem deserunt dolore dolorum eaque eligendi excepturi, explicabo harum illo itaque minus modi saepe sequi tempore vitae voluptates. Atque incidunt inventore laborum optio?</p><p><span id=\"isPasted\" style=\"color: rgb(65, 65, 65); font-family: Roboto, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: left; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial; display: inline !important; float: none;\">Ab ad aliquid commodi dignissimos doloremque dolores ex iusto laudantium nesciunt, quidem quis quisquam, sequi vel? Corporis dolore ex fugiat incidunt neque.</span></p><p>A ab accusamus ad amet aperiam asperiores atque beatae commodi cum deleniti dolorem dolores et explicabo facere harum illo in ipsa iure laborum laudantium maiores minus molestias necessitatibus neque omnis perspiciatis porro provident, quaerat quas quo quos reiciendis sint unde velit veniam voluptate voluptatibus! Ab ad aliquid commodi dignissimos doloremque dolores ex iusto laudantium nesciunt, quidem quis quisquam, sequi vel? Corporis dolore ex fugiat incidunt neque.</p>', '4,3', '1710049', 4, '20099.00', NULL, 3, 'stiralynaya-mashina-lg-f2v9hs9t', 19, 1652978409, 1652979009, 1)");
        $query->execute();


        $query = $db->prepare("INSERT INTO `{$PREFIX}products_cat`
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
        $query->execute();


        $query = $db->prepare("INSERT INTO `{$PREFIX}product_prop`
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
        $query->execute();


        $query = $db->prepare("INSERT INTO `{$PREFIX}properties`
            (`title`, `url`, `f_type`, `cid`, `option`, `sep`, `req_p`, `req`, `position`)
        VALUES
            ('Цвет', 'color', 2, NULL, 1, 0, 1, 1, 0),
            ('Гарантия', 'garanty', 1, '2,1', 1, 0, 1, 1, 0)");
        $query->execute();


        $query = $db->prepare("INSERT INTO `{$PREFIX}properties_v`
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
        $query->execute();


        $query = $db->prepare("INSERT INTO `{$PREFIX}brands`
            (`name`, `url`, `icon`, `categories`)
        VALUES
            ('Aser', 'aser', '1_1652972384775.svg', '1'),
            ('Lenovo', 'lenovo', '2_1652972441449.svg', '1'),
            ('Asus', 'asus', '3_1652972460518.svg', '1'),
            ('Samsung', 'samsung', '4_1652974766703.svg', '3')");
        $query->execute();


        $query = $db->prepare("INSERT INTO `{$PREFIX}categories`
            (`title`, `m_title`, `m_description`, `content`, `icon`, `url`, `pid`, `status`)
        VALUES
            ('Ноутбуки, планшеты и компьютерная техника', 'Ноутбуки, планшеты и компьютерная техника', 'Ноутбуки, планшеты и компьютерная техника - описание...', 'Описание для категории техники...', '1_1652972248247.webp', 'tech', NULL, 1),
            ('Ноутбуки', 'Ноутбуки для дома и офиса', 'Ноутбуки для дома и офиса - описание', '', '2_1652972581933.webp', 'noutbuki', 1, 1),
            ('Техника для дома', 'Купить технику для дома', 'У нас самая новейшая техника для вашего дома', 'Описание для техники для дома...', '3_1652974677219.webp', 'tehnika-dlya-doma', NULL, 1),
            ('Крупная техника', 'Техника крупная купить онлайн', 'Стиралки, холодильники и всякая техника', 'Описание для техники крупной', '4_1652976017789.webp', 'krupnaya-tehnika', 3, 1)");
        $query->execute();


        $query = $db->prepare("INSERT INTO `{$PREFIX}images`
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
        $query->execute();
    }
}