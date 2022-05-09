-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 09 2022 г., 13:48
-- Версия сервера: 10.3.29-MariaDB-log
-- Версия PHP: 7.4.21

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
(2, 'Lenovo', 'lenovo', '2_1648791048163.png', '13,12,11,10'),
(3, 'Apple', 'apple', '3_1648791211852.png', '12,11,10'),
(5, 'Cougar', 'cougar', '5_1648804887887.png', '9');

-- --------------------------------------------------------

--
-- Структура таблицы `nex_category`
--

CREATE TABLE `nex_category` (
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
-- Дамп данных таблицы `nex_category`
--

INSERT INTO `nex_category` (`id`, `title`, `m_title`, `m_description`, `content`, `icon`, `url`, `pid`, `status`) VALUES
(7, 'Техника', 'Тайтл техники', 'Дескрипшн техники', 'Это техника...', '7_1648563948492.jpg', 'tehnika', NULL, 1),
(8, 'Одежда', 'Тайтл одежды', 'Дескрипшн одежды', 'Описание одежды...', '8_1648563988203.jpg', 'odeghda', NULL, 1),
(9, 'Мебель', 'Тайтл мебели', 'Дескрипшн мебели', 'Описание мебели...', '9_1648564049068.jpg', 'mebely', NULL, 1),
(10, 'Телефоны', '', '', '', '10_1648564200044.png', 'telefony', NULL, 1),
(11, 'Планшеты', 'Тайтл для планшетов', 'Дескрипшн для планшетов', 'Это текст который можно вывести на странице категории', '11_1648882161908.png', 'planshety', 7, 1),
(12, 'Смарт-часы', '', '', '', '12_1648564512769.webp', 'smart-chasy', 7, 1),
(13, 'Фитнес-браслеты', '', '', '', '13_1648565883086.webp', 'fitnes-braslety', 12, 1),
(14, 'Телевизоры', '', '', '', '14_1648565910644.jpg', 'televizory', 7, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `nex_images`
--

CREATE TABLE `nex_images` (
  `id` int(11) NOT NULL,
  `itype` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - product, 2 - news',
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
(48, 1, 7, '2022-03/7_1648566416_img_0_22162_126_0webp.webp', '', 0, NULL),
(49, 1, 7, '2022-03/7_1648566417_img_0_22162_126_1webp.webp', '', 0, NULL),
(50, 1, 7, '2022-03/7_1648566417_img_0_22162_126_2webp.webp', '', 0, NULL),
(51, 1, 7, '2022-03/7_1648566417_img_0_22162_126_3webp.webp', '', 0, NULL),
(52, 1, 7, '2022-03/7_1648566418_img_0_22162_126_4webp.webp', '', 0, NULL),
(53, 1, 7, '2022-03/7_1648566418_img_0_22162_126_5webp.webp', '', 0, NULL),
(54, 1, 7, '2022-03/7_1648566419_img_0_22162_126_6webp.webp', '', 0, NULL),
(55, 1, 13, '2022-03/13_1648747355_ipad-air-202203-gallery-1jpg_3webp.webp', '', 0, NULL),
(56, 1, 13, '2022-03/13_1648747356_ipad-air-202203-gallery-3jpg_3webp.webp', '', 0, NULL),
(57, 1, 13, '2022-03/13_1648747356_ipad-air-select-202203jpg_3webp.webp', '', 0, NULL),
(58, 1, 13, '2022-03/13_1648747356_ipad-air-select-cell-purple-202203png_1webp.webp', '', 0, NULL),
(59, 1, 14, '2022-03/14_1648749249_nxn_-_2021-12-30t101145037jpegjpg.jpg', 'Это планшет', 0, NULL),
(60, 1, 14, '2022-03/14_1648749249_nxn_-_2021-12-30t101148721jpegjpg.jpg', 'Тоже планшет', 0, NULL),
(61, 1, 14, '2022-03/14_1648749249_nxn_-_2021-12-30t101338582jpegjpg.jpg', '', 0, NULL),
(62, 1, 14, '2022-03/14_1648749249_nxn_-_2021-12-30t101345960jpegjpg.jpg', '', 0, NULL),
(63, 1, 14, '2022-03/14_1648749249_nxn_-_2021-12-30t101350014jpegwebp.webp', '', 0, NULL),
(64, 1, 14, '2022-03/14_1648749249_nxn_-_2021-12-30t101353726jpegjpg.jpg', '', 0, NULL),
(65, 1, 14, '2022-03/14_1648749249_nxn_-_2021-12-30t101406964jpegwebp.webp', '', 0, NULL),
(66, 1, 14, '2022-03/14_1648749250_nxn_-_2021-12-30t101416023jpegjpg.jpg', '', 0, NULL),
(67, 1, 14, '2022-03/14_1648749250_nxn_-_2021-12-30t101419837jpegjpg.jpg', '', 0, NULL),
(68, 1, 14, '2022-03/14_1648749250_nxn_-_2021-12-30t101423833jpegjpg.jpg', '', 0, NULL),
(69, 1, 14, '2022-03/14_1648749250_nxn_-_2021-12-30t101431702jpegjpg.jpg', '', 0, NULL),
(70, 1, 15, '2022-04/15_1648806042__cougar_mars_120jpgjpg.jpg', '', 0, NULL),
(71, 1, 15, '2022-04/15_1648806042_1_2_5_1jpg.jpg', '', 0, NULL),
(72, 1, 15, '2022-04/15_1648806042_1_3_1_5jpg.jpg', '', 0, NULL),
(73, 1, 15, '2022-04/15_1648806042_1_5_1_4jpg.jpg', '', 0, NULL),
(74, 1, 15, '2022-04/15_1648806042_mars-lite-_2_jpg.jpg', '', 0, NULL),
(75, 1, 15, '2022-04/15_1648806043_mars-lite-_3_jpg.jpg', '', 0, NULL),
(76, 1, 15, '2022-04/15_1648806043_mars-lite-_5_jpg.jpg', '', 0, NULL),
(77, 1, 15, '2022-04/15_1648806043_mars-lite-_7_jpg.jpg', '', 0, NULL),
(78, 1, 15, '2022-04/15_1648806043_mars-lite-_8_jpg.jpg', '', 0, NULL),
(79, 1, 15, '2022-04/15_1648806043_mars-lite-_10_jpg.jpg', '', 0, NULL),
(80, 1, 15, '2022-04/15_1648806043_mars-lite-_12_webp.webp', '', 0, NULL),
(81, 1, 15, '2022-04/15_1648806043_mars-lite-_13_jpg.jpg', '', 0, NULL),
(82, 1, 17, '2022-04/17_1649487384_ipad_mini_q421_cellular_purple_pdp_image_position-7-ww_ru_1jpg.jpg', '', 4, NULL),
(83, 1, 17, '2022-04/17_1649487385_ipad_mini_q421_wi-fi_purple_pdp_image_position-1a-ww-ru_1-2jpg.jpg', 'Это планшет', 0, NULL),
(84, 1, 17, '2022-04/17_1649487385_ipad_mini_q421_wi-fi_purple_pdp_image_position-2-ww-ru_1-2jpg.jpg', 'Тоже самое', 1, NULL),
(85, 1, 17, '2022-04/17_1649487385_ipad_mini_q421_wi-fi_purple_pdp_image_position-3-ww-ru_1-2jpg.jpg', 'Камера', 2, NULL),
(86, 1, 17, '2022-04/17_1649487385_ipad_mini_q421_wi-fi_purple_pdp_image_position-4-ww_ru_1-2jpg.jpg', '', 5, NULL),
(87, 1, 17, '2022-04/17_1649487385_ipad_mini_q421_wi-fi_purple_pdp_image_position-5-ww_ru_1-2jpg.jpg', '', 3, NULL),
(88, 1, 17, '2022-04/17_1649487385_ipad_mini_q421_wi-fi_purple_pdp_image_position-6-ww-ru_1-2jpg.jpg', '', 6, NULL),
(89, 1, 17, '2022-04/17_1649487385_ipad_mini_q421_wi-fi_purple_pdp_image_position-8-ww-ru_1-2jpg.jpg', '', 7, NULL);

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
(8, 1, '127.0.0.1', 'admin/plugins/install/Kylaksizov/Example2/', 'Не удалось установить \'плагин\' <b>Example2</b> от разработчика <b>Kylaksizov</b>', 1644505830, 2);

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
(2, 1, 1, 'HC1651505978', 'Владимир', 'masterz1zzz@gmail.com', '+380952059675', '', 0, '13,17', '118301.50', '111', '0d3d1348dc80024ec2745d4141ac40fcaa71aa2c', 1651505978, 0, 2),
(3, 1, 1, '861651506024', 'Владимир', 'masterz1zzz@gmail.com', '+380952059675', '', 0, '13,17', '118301.50', '111', '500e02bb3e074a513a8d35dc1d878f0c64e2d06a', 1651506024, 0, 1),
(4, 1, 1, 'H51651506092', 'Владимир', 'masterz1zzz@gmail.com', '+380952059675', '', 0, '13,17', '118301.50', '111', '58af975521741579a19ce603b9258a6fbed7ff17', 1651506092, 0, 3),
(5, 1, 1, 'ZK1651506165', 'Кулаксизов Владимир Иванович', 'masterz1zzz@gmail.com', '+380952059675', '', 0, '13,17', '118301.50', '111', '44f38fce8b6166e6640a93d3ad1a62348734b8c7', 1651506165, 0, 4),
(6, 1, 1, 'JT1651506185', 'Владимир', 'masterz1zzz@gmail.com', '+380952059675', '', 0, '13,17', '118301.50', '111', '9ef59f6257ec0b45042b32eb74f48b3e45615286', 1651506185, 0, 0),
(7, 1, 1, '2X1651506197', 'Владимир', 'masterz1zzz@gmail.com', '+380952059675', '', 0, '13,17', '118301.50', '111', '1d6d1da06c169209898138fbbd717ee29104f3e2', 1651506197, 0, 5),
(8, 1, 1, 'ZO1651506227', 'Владимир', 'masterz1zzz@gmail.com', '+380952059675', '', 0, '13,17', '118301.50', '111', '825df5eeb73601aa02f3da799607a3013a785f25', 1651506227, 0, 4),
(9, 1, 1, 'MZ1651508900', 'Владимир', 'masterz1zzz@gmail.com', '+380952059675', '', 0, '13,17', '118301.50', '78ygthxdf', '57d8549584b787534d9fe69720eeedc688b4184d', 1651508900, 0, 1),
(10, 1, 1, 'ZK1651508931', 'Владимир', 'masterz1zzz@gmail.com', '+380952059675', '', 0, '13,17', '288051.50', '78ygthxdfzvs', 'b44321c54d35c0142b815eb15b2b611a275580af', 1651508931, 0, 2),
(11, 1, 1, '7T1651509034', 'Таня', 'masterz1zzz@gmail.com', '+380952059675', '', 0, '13,14,17', '199400.50', 'Мой коммент', '3e10b6512ddf520676cf20b4e11d04fc3a6024d7', 1651509034, 0, 3);

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
(1, 8, 13, 2, '50,55,51,53,54|50,55,51,52,53,54'),
(2, 8, 17, 3, '92,95,85|91,92,95,85,99|91,92,96,85,98,99'),
(3, 9, 13, 2, '50,55,51,53,54|50,55,51,52,53,54'),
(4, 9, 17, 3, '92,95,85|91,92,95,85,99|91,92,96,85,98,99'),
(5, 10, 13, 7, '50,55,51,53,54|50,55,51,52,53,54'),
(6, 10, 17, 3, '92,95,85|91,92,95,85,99|91,92,96,85,98,99'),
(7, 11, 13, 4, '50,55,51,53,54|50,55,51,52,53,54'),
(8, 11, 14, 1, '58,57'),
(9, 11, 17, 3, '92,95,85|91,92,95,85,99|91,92,96,85,98,99');

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
(7, 1, 'Фитнес-браслет MYKRONOZ ZeFit4 Black/Black', '', '', '', '13,7', '6370870', NULL, '1599.00', '10%', 10, 'fitnes-braslet-mykronoz-zefit4-black_black', 48, 1648566360, 1651316026, 1),
(13, 1, 'Планшет Apple iPad Air 10.9\'\' (5Gen) Cellular 256GB Purple', '', '', '', '11,7', 'IPAD3101', 3, '35000.00', '3%', 10, 'nazvanie-tovara', 58, 1648706180, 1648832262, 1),
(14, 1, 'Ноутбук Lenovo IdeaPad 3 15IGL05 (81WQ000RRA) Business Black', '', '', '', '7', '1932221', 2, '13199.00', NULL, 10, 'noutbuk-lenovo-ideapad-3-15igl05-81wq000rra-business-black', 59, 1648749249, 1648899344, 1),
(15, 1, 'Стол компьютерный Cougar Mars 120', '', '', '', '9', '1686482', 5, '8999.00', '300', 3, 'stol-kompyyuternyy-cougar-mars-120', 71, 1648806042, 1648832234, 1),
(17, 1, 'Планшет Apple iPad Mini 6 WiFi 64GB (MK7R3) Purple', '', '', '<p>Экран 12.9\" Liquid Retina XDR (2732x2048) емкостный MultiTouch / Apple M1 / RAM 8 ГБ / 128 ГБ встроенной памяти / Wi-Fi / Bluetooth 5.0 / основная двойная камера 12 Мп + 10 Мп, фронтальная - 12 Мп / iPadOS / 682 г / серый космос</p>', '11,7', '5211111', 3, '17000.50', '200', 10, 'planshet-apple-ipad-mini-6-wifi-64gb-mk7r3-purple', 83, 1649487384, 1651473468, 1);

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
(7, 15, 9),
(9, 14, 7),
(10, 13, 11),
(11, 13, 7),
(12, 7, 13),
(13, 7, 7),
(14, 17, 11),
(15, 17, 7);

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
(50, 13, 4, 0, 'Сим-карту можно', '', '0.00', NULL, NULL),
(51, 13, 1, 1, '', '', '0.00', NULL, NULL),
(52, 13, 1, 2, '', '', '0.00', NULL, NULL),
(53, 13, 1, 3, '', '', '0.00', NULL, NULL),
(54, 13, 1, 4, '', '', '0.00', NULL, NULL),
(55, 13, 3, 11, '', '', '0.00', NULL, NULL),
(56, 14, 4, 12, '', '', '0.00', NULL, NULL),
(57, 14, 1, 1, '', '', '0.00', NULL, NULL),
(58, 14, 3, 9, '', '', '0.00', NULL, NULL),
(60, 7, 2, 6, '', '', '3000.00', NULL, NULL),
(63, 7, 3, 11, '', '', '2000.00', NULL, NULL),
(66, 15, 3, 9, '', '', '0.00', NULL, NULL),
(69, 14, 2, 0, '', '', '0.00', NULL, NULL),
(70, 13, 2, 0, '', '', '0.00', NULL, NULL),
(82, 17, 4, 0, '', '', '0.00', NULL, NULL),
(84, 17, 2, 0, '', '', '0.00', NULL, NULL),
(85, 17, 1, 1, '', '', '200.00', 1, 2),
(87, 17, 3, 0, '', '', '0.00', NULL, NULL),
(91, 17, 4, 0, 'Вариант 1', 'АРТ1', '0.00', NULL, NULL),
(92, 17, 3, 9, '', '', '0.00', NULL, NULL),
(93, 17, 3, 10, '', '', '300.00', 1, NULL),
(94, 17, 3, 11, '', '', '500.00', 1, NULL),
(95, 17, 2, 5, '', 'HD1', '17000.00', NULL, 3),
(96, 17, 2, 7, '', '4K', '18000.00', NULL, 3),
(98, 17, 1, 19, '', '', '300.00', 1, 3),
(99, 17, 1, 3, '', '', '100.00', 1, NULL);

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
(1, 'Цвет', NULL, 3, '11,10', 1, 1, 1, 1, 0),
(2, 'Размер', NULL, 1, '14,11,10', 1, 1, 0, 0, 0),
(3, 'Гарантия', 'garantia', 1, NULL, 1, 0, 0, 0, 0),
(4, 'Произвольное свойство', 'proizvolynoe-svoystvo', 1, NULL, 1, 1, 0, 0, 0);

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
(1, 1, 'Белый', 1, 0),
(2, 1, 'Черный', NULL, 1),
(3, 1, 'Голубой', NULL, 2),
(4, 1, 'Золотой', NULL, 3),
(5, 2, 'HD', 1, 0),
(6, 2, 'FULL HD', NULL, 0),
(7, 2, '4K', NULL, 0),
(8, 2, '8K', NULL, 0),
(9, 3, '12 мес.', 1, 0),
(10, 3, '2 года', NULL, 0),
(11, 3, '3 года', NULL, 0),
(19, 1, 'Красный', NULL, 0);

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
-- Структура таблицы `nex_systems`
--

CREATE TABLE `nex_systems` (
  `id` int(11) NOT NULL,
  `s_type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Тип',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Название плагина или модуля',
  `menu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Меню для админки JSON',
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Конфиг для плагина',
  `status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_systems`
--

INSERT INTO `nex_systems` (`id`, `s_type`, `name`, `menu`, `config`, `status`) VALUES
(2, 'plugin', 'Kylaksizov/Example', '{\"Меню моего плагина\":{\"link\":\"#\",\"class\":\"ico_space\",\"icon\":\"\",\"submenu\":{\"Пункт меню 1\":\"#\",\"Пункт меню 2\":\"#\",\"Пункт меню 3\":\"#\"}}}', '111', 0),
(3, 'plugin', 'Kylaksizov/Example2', '{\"Меню моего плагина 2\":{\"link\":\"#\",\"class\":\"ico_space\",\"icon\":\"\",\"submenu\":{\"Пункт меню 1\":\"#\",\"Пункт меню 2\":\"#\",\"Пункт меню 3\":\"#\"}}}', '111', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `nex_users`
--

CREATE TABLE `nex_users` (
  `id` int(11) NOT NULL,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` tinyint(5) DEFAULT NULL,
  `ip` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `nex_users`
--

INSERT INTO `nex_users` (`id`, `name`, `email`, `password`, `role`, `ip`, `hash`, `created`, `status`) VALUES
(1, 'Владимир', 'masterz1zzz@gmail.com', '1db06802aa36e154921ad6212637afe3d2daaded', 1, '', 'eb88e995a8c9b81c0a74eba4893e65a5e9193a2f', 1622707821, 1),
(3, 'Татьяна', 'snegurochka@gmail.com', 'eb88e995a8c9b81c0a74eba4893e65a5e9193a2f', 2, '', 'eb88e995a8c9b81c0a74eba4893e65a5e9193a2c', 1643141853, 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `nex_brands`
--
ALTER TABLE `nex_brands`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `nex_category`
--
ALTER TABLE `nex_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`);

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
-- Индексы таблицы `nex_systems`
--
ALTER TABLE `nex_systems`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `nex_category`
--
ALTER TABLE `nex_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `nex_images`
--
ALTER TABLE `nex_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT для таблицы `nex_log`
--
ALTER TABLE `nex_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `nex_orders`
--
ALTER TABLE `nex_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `nex_orders_ex`
--
ALTER TABLE `nex_orders_ex`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `nex_orders_status`
--
ALTER TABLE `nex_orders_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `nex_products`
--
ALTER TABLE `nex_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT для таблицы `nex_products_cat`
--
ALTER TABLE `nex_products_cat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `nex_product_prop`
--
ALTER TABLE `nex_product_prop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT для таблицы `nex_properties`
--
ALTER TABLE `nex_properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `nex_properties_v`
--
ALTER TABLE `nex_properties_v`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `nex_roles`
--
ALTER TABLE `nex_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `nex_systems`
--
ALTER TABLE `nex_systems`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `nex_users`
--
ALTER TABLE `nex_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
