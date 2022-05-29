-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 27 2022 г., 22:46
-- Версия сервера: 10.3.29-MariaDB-log
-- Версия PHP: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `nexshop`
--

-- --------------------------------------------------------

--
-- Структура таблицы `nex_brands`
--

CREATE TABLE `nex_brands` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `categories` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_brands`
--

INSERT INTO `nex_brands` (`id`, `name`, `url`, `icon`, `categories`) VALUES
(1, 'Aser', 'aser', '1_1652972384775.svg', '1'),
(2, 'Lenovo', 'lenovo', '2_1652972441449.svg', '1'),
(3, 'Asus', 'asus', '3_1652972460518.svg', '1'),
(4, 'Samsung', 'samsung', '4_1652974766703.svg', '3');

-- --------------------------------------------------------

--
-- Структура таблицы `nex_categories`
--

CREATE TABLE `nex_categories` (
  `id` int(11) NOT NULL,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `m_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `m_description` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pid` int(11) DEFAULT NULL COMMENT 'parent id',
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_categories`
--

INSERT INTO `nex_categories` (`id`, `title`, `m_title`, `m_description`, `content`, `icon`, `url`, `pid`, `status`) VALUES
(1, 'Ноутбуки, планшеты и компьютерная техника', 'Ноутбуки, планшеты и компьютерная техника', 'Ноутбуки, планшеты и компьютерная техника - описание...', 'Описание для категории техники...', '1_1652972248247.webp', 'tech', NULL, 1),
(2, 'Ноутбуки', 'Ноутбуки для дома и офиса', 'Ноутбуки для дома и офиса - описание', '', '2_1652972581933.webp', 'noutbuki', 1, 1),
(3, 'Техника для дома', 'Купить технику для дома', 'У нас самая новейшая техника для вашего дома', 'Описание для техники для дома...', '3_1652974677219.webp', 'tehnika-dlya-doma', NULL, 1),
(4, 'Крупная техника', 'Техника крупная купить онлайн', 'Стиралки, холодильники и всякая техника', 'Описание для техники крупной', '4_1652976017789.webp', 'krupnaya-tehnika', 3, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `nex_example`
--

CREATE TABLE `nex_example` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Комментарий...'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_example`
--

INSERT INTO `nex_example` (`id`, `name`) VALUES
(1, 'Это тестовая таблица, можно удалить...'),
(2, 'Это тестовая таблица, можно удалить...'),
(3, 'Это тестовая таблица, можно удалить...'),
(4, 'Это тестовая таблица, можно удалить...');

-- --------------------------------------------------------

--
-- Структура таблицы `nex_images`
--

CREATE TABLE `nex_images` (
  `id` int(11) NOT NULL,
  `itype` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - product, 2 - news, 3 - page',
  `nid` int(11) NOT NULL DEFAULT 0 COMMENT 'id product or news...',
  `src` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` tinyint(10) NOT NULL DEFAULT 0,
  `status` tinyint(1) DEFAULT NULL COMMENT '1 - main image'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_images`
--

INSERT INTO `nex_images` (`id`, `itype`, `nid`, `src`, `alt`, `position`, `status`) VALUES
(1, 1, 1, '2022-05/1_1652973511_acer_swift_1_sf114-33_silver_01_3_1.webp', '', 0, NULL),
(2, 1, 1, '2022-05/1_1652973511_acer_swift_1_sf114-33_silver_03_3_1.webp', '', 0, NULL),
(3, 1, 1, '2022-05/1_1652973511_acer_swift_1_sf114-33_silver_04_3_1.webp', '', 0, NULL),
(4, 1, 1, '2022-05/1_1652973511_acer_swift_1_sf114-33_silver_05_3_1.webp', '', 0, NULL),
(5, 1, 1, '2022-05/1_1652973511_acer_swift_1_sf114-33_silver_08_3_1.webp', '', 0, NULL),
(6, 1, 2, '2022-05/2_1652974080_yqndd-eg.webp', '', 0, NULL),
(7, 1, 2, '2022-05/2_1652974080_6172ad22380b5395308786.webp', '', 0, NULL),
(8, 1, 2, '2022-05/2_1652974080_6172ad2350d5c907652907.webp', '', 0, NULL),
(9, 1, 2, '2022-05/2_1652974080_6172ad2465e4f988758904.webp', '', 0, NULL),
(10, 1, 2, '2022-05/2_1652974080_6172ad26a7208618948151.webp', '', 0, NULL),
(11, 1, 2, '2022-05/2_1652974080_6172ad2c6ca34928997843.webp', '', 0, NULL),
(17, 1, 4, '2022-05/4_1652978993_f2v9hs9t_01_front_with_medals_and_logos_1_.webp', '', 1, NULL),
(18, 1, 4, '2022-05/4_1652978994_f2v9hs9t-07-dimensions-desktop_1_.webp', '', 2, NULL),
(19, 1, 4, '2022-05/4_1652978994_z01.webp', '', 0, NULL),
(20, 1, 4, '2022-05/4_1652979009_5fb69b1b090c8_f2v9hs9t_15_right_drawer_details.webp', '', 3, NULL),
(21, 1, 4, '2022-05/4_1652979009_5fb69b15af236_f2v9hs9t_06_right_door_open.webp', '', 4, NULL),
(22, 1, 4, '2022-05/4_1652979009_5fb69b16cea98_f2v9hs9t_08_right_low_perspective.webp', '', 5, NULL),
(23, 1, 4, '2022-05/4_1652979009_5fb69b19d6d7b_f2v9hs9t_13_front_top_drawer_details.webp', '', 6, NULL),
(24, 1, 4, '2022-05/4_1652979009_5fb69b1823623_f2v9hs9t_10_left_top_perspective_drawer_open.webp', '', 7, NULL),
(30, 3, 1, '2022-05/1_1653210069_ad.jpg', '', 0, NULL),
(31, 3, 1, '2022-05/1_1653210070_logo.png', '', 0, NULL),
(32, 3, 4, '2022-05/4_1653210198_slide.jpg', '', 0, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `nex_log`
--

CREATE TABLE `nex_log` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL COMMENT 'User ID',
  `ip` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `log` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - default, 1 - success, 2 - error'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_log`
--

INSERT INTO `nex_log` (`id`, `uid`, `ip`, `url`, `log`, `created`, `status`) VALUES
(1, 1, '127.0.0.1', 'admin/plugins/install/Kylaksizov/Example2/', 'Не удалось установить плагин <b>Example2</b> от разработчика <b>Kylaksizov</b>', 1644488551, 2),
(2, 1, '127.0.0.1', 'admin/plugins/install/Kylaksizov/Example2/', 'Не удалось установить плагин <b>Example2</b> от разработчика <b>Kylaksizov</b>', 1644488552, 2),
(3, 1, '127.0.0.1', 'admin/plugins/install/Kylaksizov/Example/', 'Установлен плагин <b>Example</b> от разработчика <b>Kylaksizov</b>', 1644488552, 1),
(4, 1, '127.0.0.1', 'admin/plugins/install/Kylaksizov/Example/', 'Установлен \"плагин\" <b>Example</b> от разработчика <b>Kylaksizov</b>', 1644488585, 1),
(5, 1, '127.0.0.1', 'admin/plugins/install/Kylaksizov/Example2/', 'Не удалось установить \'плагин\' <b>Example2</b> от разработчика <b>Kylaksizov</b>', 1644488586, 2),
(6, 1, '127.0.0.1', 'admin/plugins/install/Kylaksizov/Example2/', 'Попытка подобрать пароль', 1644498660, 3),
(7, 1, '127.0.0.1', 'admin/plugins/install/Kylaksizov/Example2/', 'Не удалось установить \'плагин\' <b>Example2</b> от разработчика <b>Kylaksizov</b>', 1644498968, 2),
(8, 1, '127.0.0.1', 'admin/plugins/install/Kylaksizov/Example2/', 'Не удалось установить \'плагин\' <b>Example2</b> от разработчика <b>Kylaksizov</b>', 1644505830, 2),
(9, NULL, '127.0.0.1', '', 'Не удалось зарегистрировать пользователя!<br>Почта: ', 1653128942, 0),
(10, NULL, '127.0.0.1', '/', 'Не удалось зарегистрировать пользователя!<br>Почта: ', 1653129026, 0),
(11, NULL, '127.0.0.1', '/', 'Не удалось зарегистрировать пользователя!<br>Почта: ', 1653129059, 0),
(12, NULL, '127.0.0.1', '/', 'Не удалось зарегистрировать пользователя!<br>Почта: info@kylaksizov.com', 1653129805, 2),
(13, NULL, '127.0.0.1', '/', 'Не удалось зарегистрировать пользователя!<br>Почта: info@kylaksizov.com', 1653129812, 2),
(14, NULL, '127.0.0.1', '/', 'Не удалось зарегистрировать пользователя!<br>Почта: info@kylaksizov.com', 1653129846, 2),
(15, 1, '127.0.0.1', 'panel/users/', 'Пользователь <b>Владимир</b> удален!', 1653190021, 1),
(16, 1, '127.0.0.1', 'panel/plugins/install/', 'Не удалось установить \'плагин\' <b></b> от разработчика <b></b>', 1653294216, 2),
(17, 1, '127.0.0.1', 'panel/plugins/install/Kylaksizov/Example/', 'Установлен \"плагин\" <b>Example</b> от разработчика <b>Kylaksizov</b>', 1653294271, 1),
(18, 1, '127.0.0.1', 'panel/plugins/install/Kylaksizov/Example/', 'Установлен \"плагин\" <b>Example</b> от разработчика <b>Kylaksizov</b>', 1653294622, 1),
(19, 1, '127.0.0.1', 'panel/plugins/install/Kylaksizov/Example/', 'Установлен \"плагин\" <b>Example</b> от разработчика <b>Kylaksizov</b>', 1653295141, 1),
(20, 1, '127.0.0.1', 'panel/plugins/install/Kylaksizov/Example/', 'Установлен \"плагин\" <b>Example</b> от разработчика <b>Kylaksizov</b>', 1653295421, 1),
(21, 1, '127.0.0.1', 'panel/plugins/install/Kylaksizov/Example/', 'Установлен \"плагин\" <b>Example</b> от разработчика <b>Kylaksizov</b>', 1653297859, 1),
(22, 1, '127.0.0.1', 'panel/plugins/install/Kylaksizov/Example/', 'Установлен \"плагин\" <b>Example</b> от разработчика <b>Kylaksizov</b>', 1653366775, 1),
(23, 1, '127.0.0.1', 'panel/plugins/install/Kylaksizov/Example/', 'Установлен \"плагин\" <b>Example</b> от разработчика <b>Kylaksizov</b>', 1653397145, 1),
(24, 1, '127.0.0.1', 'panel/plugins/install/Kylaksizov/Example/', 'Установлен \"плагин\" <b>Example</b> от разработчика <b>Kylaksizov</b>', 1653446735, 1),
(25, 1, '127.0.0.1', 'panel/celenaShop/plugins/', 'Отсутствует метод установки в плагине <b>Kylaksizov/Test</b>! Обратитесь к разработчику плагина.', 1653626198, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `nex_orders`
--

CREATE TABLE `nex_orders` (
  `id` int(11) NOT NULL,
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
  `status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_orders`
--

INSERT INTO `nex_orders` (`id`, `uid`, `buyer_id`, `order_id`, `name`, `email`, `tel`, `address`, `payment_id`, `prod_ids`, `total`, `comment`, `hash`, `created`, `paid`, `status`) VALUES
(1, 1, 1, '6T1652983373', 'Владимир', 'masterz1zzz@gmail.com', '+380952059675', '', 0, '2,3,4', '113594.00', 'Хочу все купить', 'e6778c02f4216afa154d9255fce728577b9d691c', 1652983373, 0, 0),
(2, 12, 12, 'NK1653188217', 'Владимир', 'info@kylaksizov.com', '908979876', '', 0, '3', '20999.00', '', 'b3fbb8aff4073256706f110c6d0690a85635e872', 1653188217, 0, 0),
(3, 1, 1, 'E41653537447', 'Владимир', 'masterz1zzz@gmail.com', '+380952059675', '', 0, '4', '40198.00', '', '3f70eaecaebd1e5cee805fce674c1bbed24c5627', 1653537447, 0, 0),
(4, 1, 1, '9B1653537471', 'Владимир', 'masterz1zzz@gmail.com', '+380952059675', '', 0, '2', '16149.00', '', '9c0855c8a43b4e59a13fba9e23ab2885c66526da', 1653537471, 0, 0),
(5, 1, 1, 'KN1653537495', 'Владимир', 'masterz1zzz@gmail.com', '+380952059675', '', 0, '2', '16149.00', '', 'b836006f99487645502b3b19f959f87c45f5917b', 1653537495, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `nex_orders_ex`
--

CREATE TABLE `nex_orders_ex` (
  `id` int(11) NOT NULL,
  `oid` int(11) NOT NULL COMMENT 'order id',
  `pid` int(11) NOT NULL COMMENT 'product id',
  `count` int(10) NOT NULL DEFAULT 1,
  `props` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'properties ids'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_orders_ex`
--

INSERT INTO `nex_orders_ex` (`id`, `oid`, `pid`, `count`, `props`) VALUES
(1, 1, 2, 2, '4,7|4,7'),
(2, 1, 3, 1, '8'),
(3, 1, 4, 3, '9'),
(4, 2, 3, 1, '8'),
(5, 3, 4, 2, '9'),
(6, 4, 2, 1, '4,7'),
(7, 5, 2, 1, '4,7');

-- --------------------------------------------------------

--
-- Структура таблицы `nex_orders_status`
--

CREATE TABLE `nex_orders_status` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `color` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ffffff',
  `pos` int(11) NOT NULL DEFAULT 0 COMMENT 'position'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_orders_status`
--

INSERT INTO `nex_orders_status` (`id`, `name`, `color`, `pos`) VALUES
(0, 'Новый', 'ff2d2d', 1),
(1, 'Принят', '17d5d5', 2),
(2, 'У курьера', '906ee3', 3),
(3, 'Выполнен', '69d924', 4);

-- --------------------------------------------------------

--
-- Структура таблицы `nex_pages`
--

CREATE TABLE `nex_pages` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `m_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `m_description` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `poster` int(11) NOT NULL DEFAULT 0,
  `url` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_pages`
--

INSERT INTO `nex_pages` (`id`, `uid`, `title`, `m_title`, `m_description`, `content`, `poster`, `url`, `created`, `status`) VALUES
(1, 1, 'Первая статья', 'Надо поменять местами', 'Описание', '<p>Что-то написано</p>', 28, 'pervaya-statyya', 1653208380, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `nex_plugins`
--

CREATE TABLE `nex_plugins` (
  `id` int(11) NOT NULL,
  `plugin_id` int(11) NOT NULL COMMENT 'id plugin in system server',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Название плагина или модуля',
  `version` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0.0.1',
  `hashfile` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_plugins`
--

INSERT INTO `nex_plugins` (`id`, `plugin_id`, `name`, `version`, `hashfile`, `status`) VALUES
(6, 1, 'Celena/Example', '1.0.0', '1_e9c2f8ecd03cade656eecf90fb3c9f833b9c7bab', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `nex_products`
--

CREATE TABLE `nex_products` (
  `id` int(11) NOT NULL,
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
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_products`
--

INSERT INTO `nex_products` (`id`, `uid`, `title`, `m_title`, `m_description`, `content`, `category`, `vendor`, `brand`, `price`, `sale`, `stock`, `url`, `poster`, `created`, `last_modify`, `status`) VALUES
(1, 1, 'Ноутбук Acer Swift 1 SF114-34 (NX.A77EU.00S) Pure Silver', 'Ноутбук Acer Swift 1 SF114-34 (NX.A77EU.00S) Pure Silver - купить в Украине', 'Отличный ноутбук фирмы Aser, который гарантирует приятную работу. Кчество на высоте! Покупаем быстренько.', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad aperiam asperiores, autem deserunt dolore dolorum eaque eligendi excepturi, explicabo harum illo itaque minus modi saepe sequi tempore vitae voluptates. Atque incidunt inventore laborum optio?</p><p>A ab accusamus ad amet aperiam asperiores atque beatae commodi cum deleniti dolorem dolores et explicabo facere harum illo in ipsa iure laborum laudantium maiores minus molestias necessitatibus neque omnis perspiciatis porro provident, quaerat quas quo quos reiciendis sint unde velit veniam voluptate voluptatibus! Ab ad aliquid commodi dignissimos doloremque dolores ex iusto laudantium nesciunt, quidem quis quisquam, sequi vel? Corporis dolore ex fugiat incidunt neque.</p>', '2,1', '1794625', 1, '16999.00', '5%', 10, 'noutbuk-acer-swift-1-sf114-34-nxa77eu00s-pure-silver', 3, 1652973511, 1652981242, 1),
(2, 1, 'Ноутбук игровой Lenovo Legion5 15IMH05 (82AU00Q8RA) Phantom Black', 'Ноутбук игровой Lenovo Legion5 15IMH05 (82AU00Q8RA) Phantom Black купить', 'Купите Ноутбук игровой Lenovo Legion5 15IMH05 (82AU00Q8RA) Phantom Black у нас и радуйтесь!', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad aperiam asperiores, autem deserunt dolore dolorum eaque eligendi excepturi, explicabo harum illo itaque minus modi saepe sequi tempore vitae voluptates. Atque incidunt inventore laborum optio? A ab accusamus ad amet aperiam asperiores atque beatae commodi cum deleniti dolorem dolores et explicabo facere harum illo in ipsa iure laborum laudantium maiores minus molestias necessitatibus neque omnis perspiciatis porro provident, quaerat quas quo quos reiciendis sint unde velit veniam voluptate voluptatibus! Ab ad aliquid commodi dignissimos doloremque dolores ex iusto laudantium nesciunt, quidem quis quisquam, sequi vel? Corporis dolore ex fugiat incidunt neque.</p>', '2,1', '1875053', 2, '34999.00', '4000', 17, 'noutbuk-igrovoy-lenovo-legion5-15imh05-82au00q8ra-phantom-black', 6, 1652974080, 1652981228, 1),
(4, 1, 'Стиральная машина LG F2V9HS9T', 'Стиральная машина LG F2V9HS9T купить online', 'Купить класную стиралку LG F2V9HS9T', '<p id=\"isPasted\">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad aperiam asperiores, autem deserunt dolore dolorum eaque eligendi excepturi, explicabo harum illo itaque minus modi saepe sequi tempore vitae voluptates. Atque incidunt inventore laborum optio?</p><p><span id=\"isPasted\" style=\"color: rgb(65, 65, 65); font-family: Roboto, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: left; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255); text-decoration-thickness: initial; text-decoration-style: initial; text-decoration-color: initial; display: inline !important; float: none;\">Ab ad aliquid commodi dignissimos doloremque dolores ex iusto laudantium nesciunt, quidem quis quisquam, sequi vel? Corporis dolore ex fugiat incidunt neque.</span></p><p>A ab accusamus ad amet aperiam asperiores atque beatae commodi cum deleniti dolorem dolores et explicabo facere harum illo in ipsa iure laborum laudantium maiores minus molestias necessitatibus neque omnis perspiciatis porro provident, quaerat quas quo quos reiciendis sint unde velit veniam voluptate voluptatibus! Ab ad aliquid commodi dignissimos doloremque dolores ex iusto laudantium nesciunt, quidem quis quisquam, sequi vel? Corporis dolore ex fugiat incidunt neque.</p>', '4,3', '1710049', 4, '20099.00', NULL, 3, 'stiralynaya-mashina-lg-f2v9hs9t', 19, 1652978409, 1652979009, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `nex_products_cat`
--

CREATE TABLE `nex_products_cat` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `cid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_products_cat`
--

INSERT INTO `nex_products_cat` (`id`, `pid`, `cid`) VALUES
(1, 1, 2),
(2, 1, 1),
(3, 2, 2),
(4, 2, 1),
(7, 4, 4),
(8, 4, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `nex_product_prop`
--

CREATE TABLE `nex_product_prop` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT 0 COMMENT 'ID товара',
  `id_p` int(11) NOT NULL COMMENT 'ID свойства',
  `id_pv` int(11) NOT NULL DEFAULT 0 COMMENT 'ID значения свойства',
  `sep` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'произвольное значение',
  `vendor` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'артикул',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pv` tinyint(1) DEFAULT NULL COMMENT 'price variant: null - новая цена, 0 - минус, 1 - плюс, 2 - -%, 3 - +%',
  `stock` int(11) DEFAULT NULL COMMENT 'null - без лимит'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_product_prop`
--

INSERT INTO `nex_product_prop` (`id`, `pid`, `id_p`, `id_pv`, `sep`, `vendor`, `price`, `pv`, `stock`) VALUES
(1, 1, 2, 9, '', '', '0.00', NULL, NULL),
(2, 1, 1, 1, '', '', '0.00', NULL, 3),
(3, 1, 1, 2, '', '', '200.00', 1, 7),
(4, 2, 2, 6, '', '', '0.00', NULL, NULL),
(5, 2, 1, 3, '', '', '0.00', NULL, 10),
(6, 2, 1, 1, '', '', '200.00', 1, 3),
(7, 2, 1, 2, '', '', '350.00', 1, 4),
(9, 4, 1, 1, '', '', '0.00', NULL, NULL),
(10, 4, 1, 3, '', '', '0.00', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `nex_properties`
--

CREATE TABLE `nex_properties` (
  `id` int(11) NOT NULL,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'null - не участвует в фильтре',
  `f_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1-select, 2-checkbox, 3-radio',
  `cid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Categories ids. null - во всех категориях.',
  `option` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 - не выводить, 1-выводить сразу',
  `sep` tinyint(1) DEFAULT 0 COMMENT '0 - нельзя произвольный вариант, 1 можно',
  `req_p` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - не обязательно заполнять, 1 - обязательно',
  `req` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - не обязательно заполнять при покупке, 1 - обязательно',
  `position` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_properties`
--

INSERT INTO `nex_properties` (`id`, `title`, `url`, `f_type`, `cid`, `option`, `sep`, `req_p`, `req`, `position`) VALUES
(1, 'Цвет', 'color', 2, NULL, 1, 0, 1, 1, 0),
(2, 'Гарантия', 'garanty', 1, '2,1', 1, 0, 1, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `nex_properties_v`
--

CREATE TABLE `nex_properties_v` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL COMMENT 'property_id',
  `val` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `def` tinyint(1) DEFAULT NULL COMMENT 'null - нет умолчания',
  `position` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_properties_v`
--

INSERT INTO `nex_properties_v` (`id`, `pid`, `val`, `def`, `position`) VALUES
(1, 1, 'Серый', NULL, 0),
(2, 1, 'Белый', 1, 0),
(3, 1, 'Черный', NULL, 0),
(4, 1, 'Цветной', NULL, 0),
(5, 2, '1 год', 1, 0),
(6, 2, '2 года', NULL, 0),
(7, 2, '3 года', NULL, 0),
(8, 2, '4 года', NULL, 0),
(9, 2, '5 лет', NULL, 0),
(10, 2, '10 лет', NULL, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `nex_roles`
--

CREATE TABLE `nex_roles` (
  `id` int(11) NOT NULL,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_roles`
--

INSERT INTO `nex_roles` (`id`, `name`, `rules`) VALUES
(1, 'Администратор', NULL),
(2, 'Пользователь', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `nex_users`
--

CREATE TABLE `nex_users` (
  `id` int(11) NOT NULL,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `role` tinyint(5) DEFAULT NULL,
  `ip` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_users`
--

INSERT INTO `nex_users` (`id`, `name`, `email`, `password`, `avatar`, `role`, `ip`, `hash`, `created`, `status`) VALUES
(1, 'Владимир', 'masterz1zzz@gmail.com', '1db06802aa36e154921ad6212637afe3d2daaded', '', 1, '', 'eb88e995a8c9b81c0a74eba4893e65a5e9193a2f', 1622707821, 1),
(3, 'Татьяна', 'snegurochka@gmail.com', 'eb88e995a8c9b81c0a74eba4893e65a5e9193a2f', '', 2, '', 'eb88e995a8c9b81c0a74eba4893e65a5e9193a2c', 1643141853, 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `nex_brands`
--
ALTER TABLE `nex_brands`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `nex_categories`
--
ALTER TABLE `nex_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`);

--
-- Индексы таблицы `nex_example`
--
ALTER TABLE `nex_example`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `nex_images`
--
ALTER TABLE `nex_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nid` (`nid`);

--
-- Индексы таблицы `nex_log`
--
ALTER TABLE `nex_log`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `nex_orders`
--
ALTER TABLE `nex_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `status` (`status`);

--
-- Индексы таблицы `nex_orders_ex`
--
ALTER TABLE `nex_orders_ex`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `nex_orders_status`
--
ALTER TABLE `nex_orders_status`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `nex_pages`
--
ALTER TABLE `nex_pages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `nex_plugins`
--
ALTER TABLE `nex_plugins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plugin_id` (`plugin_id`);

--
-- Индексы таблицы `nex_products`
--
ALTER TABLE `nex_products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `nex_products_cat`
--
ALTER TABLE `nex_products_cat`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `nex_product_prop`
--
ALTER TABLE `nex_product_prop`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `nex_properties`
--
ALTER TABLE `nex_properties`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `nex_properties_v`
--
ALTER TABLE `nex_properties_v`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`);

--
-- Индексы таблицы `nex_roles`
--
ALTER TABLE `nex_roles`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `nex_users`
--
ALTER TABLE `nex_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `hash` (`hash`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `nex_brands`
--
ALTER TABLE `nex_brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `nex_categories`
--
ALTER TABLE `nex_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `nex_example`
--
ALTER TABLE `nex_example`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `nex_images`
--
ALTER TABLE `nex_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT для таблицы `nex_log`
--
ALTER TABLE `nex_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT для таблицы `nex_orders`
--
ALTER TABLE `nex_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `nex_orders_ex`
--
ALTER TABLE `nex_orders_ex`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `nex_orders_status`
--
ALTER TABLE `nex_orders_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `nex_pages`
--
ALTER TABLE `nex_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `nex_plugins`
--
ALTER TABLE `nex_plugins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `nex_products`
--
ALTER TABLE `nex_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `nex_products_cat`
--
ALTER TABLE `nex_products_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `nex_product_prop`
--
ALTER TABLE `nex_product_prop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `nex_properties`
--
ALTER TABLE `nex_properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `nex_properties_v`
--
ALTER TABLE `nex_properties_v`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `nex_roles`
--
ALTER TABLE `nex_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `nex_users`
--
ALTER TABLE `nex_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
