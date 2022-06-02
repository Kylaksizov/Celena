<?php

namespace app\core\system\install\steps;

use app\_classes\Functions;
use app\core\system\install\steps\addon\FillBase;
use Exception;
use PDO;
use PDOException;
use PDOStatement;

class Step_2{


    public function __construct() {}
    public function __clone() {}



    public function postAction(){

        $DB_HOST     = !empty($_POST["db"]["host"])     ? $_POST["db"]["host"]     : die("info::error::Заполните все поля!");
        $DB_NAME     = !empty($_POST["db"]["name"])     ? $_POST["db"]["name"]     : die("info::error::Заполните все поля!");
        $DB_USER     = !empty($_POST["db"]["user"])     ? $_POST["db"]["user"]     : die("info::error::Заполните все поля!");
        $DB_PASSWORD = !empty($_POST["db"]["password"]) ? $_POST["db"]["password"] : '';
        $PREFIX      = !empty($_POST["db"]["prefix"])   ? $_POST["db"]["prefix"]   : die("info::error::Заполните все поля!");

        $PANEL_NAME      = !empty($_POST["panel"]["name"])      ? $_POST["panel"]["name"]      : die("info::error::Заполните все поля!");
        $PANEL_EMAIL     = !empty($_POST["panel"]["email"])     ? $_POST["panel"]["email"]     : die("info::error::Заполните все поля!");
        $PANEL_PASSWORD  = !empty($_POST["panel"]["password"])  ? $_POST["panel"]["password"]  : die("info::error::Заполните все поля!");
        $PANEL_PASSWORD2 = !empty($_POST["panel"]["password2"]) ? $_POST["panel"]["password2"] : die("info::error::Заполните все поля!");
        $PANEL_HOME      = !empty($_POST["panel"]["home"])      ? $_POST["panel"]["home"]      : die("info::error::Заполните все поля!");

        $PANEL_NAME = trim(htmlspecialchars(stripslashes($PANEL_NAME)));

        if(!filter_var($PANEL_EMAIL, FILTER_VALIDATE_EMAIL)) die("info::error::Указанный не действительный Email!");
        if($PANEL_PASSWORD != $PANEL_PASSWORD2) die("info::error::Пароли не совпадают!");


        $opt  = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => TRUE,
        );
        $dsn = 'mysql:host=' . $DB_HOST . ';dbname=' . $DB_NAME;
        try{
            $db = new PDO($dsn, $DB_USER, $DB_PASSWORD, $opt);
            $db -> exec("set names utf8");
        } catch(PDOException $e){
            die("info::error::Не удалось соединится с базой!");
        }

        // удаляем все таблицы если есть
        $db->exec("DROP TABLE IF EXISTS {$PREFIX}categories");
        $db->exec("DROP TABLE IF EXISTS {$PREFIX}images");
        $db->exec("DROP TABLE IF EXISTS {$PREFIX}log");
        $db->exec("DROP TABLE IF EXISTS {$PREFIX}roles");
        $db->exec("DROP TABLE IF EXISTS {$PREFIX}plugins");
        $db->exec("DROP TABLE IF EXISTS {$PREFIX}modules");
        $db->exec("DROP TABLE IF EXISTS {$PREFIX}users");

        $query = $db->prepare("CREATE TABLE `{$PREFIX}users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
            `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
            `password` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
            `role` tinyint(5) DEFAULT NULL,
            `ip` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
            `hash` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `created` int(11) NOT NULL,
            `status` tinyint(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`),
            UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $query->execute();

        $query = $db->prepare("CREATE TABLE `{$PREFIX}plugins` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `plugin_id` int(11) NOT NULL,
            `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Название плагина или модуля',
            `version` varchar(13) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0.0.1' COMMENT 'Версия плагина',
            `menu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT NULL COMMENT 'Меню для админки JSON',
            `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Конфиг для плагина',
            `hashfile` varchar(75) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '',
            `status` tinyint(1) DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `plugin_id` (`plugin_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $query->execute();

        $query = $db->prepare("CREATE TABLE `{$PREFIX}modules` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `module_id` INT(11) NULL DEFAULT NULL COMMENT 'null-не из магазина',
            `name` VARCHAR(50) NOT NULL,
            `descr` VARCHAR(300) NOT NULL,
            `version` VARCHAR(15) NOT NULL,
            `cv` VARCHAR(15) NULL DEFAULT NULL COMMENT 'celena version? null - любая',
            `poster` VARCHAR(30) NULL DEFAULT NULL COMMENT 'null - без постера',
            `base_install` TEXT NOT NULL,
            `base_update` TEXT NOT NULL,
            `base_on` TEXT NOT NULL,
            `base_off` TEXT NOT NULL,
            `base_del` TEXT NOT NULL,
            `routes` TEXT NOT NULL,
            `comment` TEXT NOT NULL ,  `status` TINYINT(1) NOT NULL DEFAULT '0',
            PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $query->execute();

        $query = $db->prepare("CREATE TABLE `{$PREFIX}modules_ex` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `mid` INT(11) NOT NULL COMMENT 'module id',
            `searchcode` TEXT NOT NULL,
            `replacecode` TEXT NOT NULL,
            `filepath` VARCHAR(255) NOT NULL,
            `action` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '1 - замена, 2 - добавить выше, 3 - добавить ниже, 4 - замена файла, 5 - новый файл',
            PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $query->execute();

        $query = $db->prepare("CREATE TABLE `{$PREFIX}roles` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
            `rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $query->execute();

        $query = $db->prepare("CREATE TABLE `{$PREFIX}log` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `uid` int(11) DEFAULT NULL COMMENT 'User ID',
            `ip` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
            `url` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `log` text COLLATE utf8mb4_unicode_ci NOT NULL,
            `created` int(11) NOT NULL,
            `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - default, 1 - success, 2 - error',
            PRIMARY KEY (`id`),
            KEY `uid` (`uid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $query->execute();

        $query = $db->prepare("CREATE TABLE `{$PREFIX}images` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `itype` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - product, 2 - news',
            `nid` int(11) NOT NULL DEFAULT 0 COMMENT 'id product or news...',
            `src` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `alt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `position` tinyint(10) NOT NULL DEFAULT 0,
            `status` tinyint(1) DEFAULT NULL COMMENT '1 - main image',
            PRIMARY KEY (`id`),
            KEY `nid` (`nid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $query->execute();

        $query = $db->prepare("CREATE TABLE `{$PREFIX}categories` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
            `m_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `m_description` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
            `icon` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `url` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
            `pid` int(11) DEFAULT NULL COMMENT 'parent id',
            `status` tinyint(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $query->execute();

        $query = $db->prepare("CREATE TABLE `{$PREFIX}pages` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `uid` INT(11) NOT NULL,
            `title` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `m_title` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `m_description` VARCHAR(300) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `content` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
            `url` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
            `created` INT(11) NOT NULL,
            `status` TINYINT(1) NOT NULL DEFAULT '1',
            PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $query->execute();



        # ===========
        # FILL TABLES
        # ===========
        $query = $db->prepare("INSERT INTO {$PREFIX}users
                (name, email, password, role, ip, hash, created, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $PANEL_PASSWORD = sha1(md5($PANEL_PASSWORD).':NEX');
        $query->execute([$PANEL_NAME, $PANEL_EMAIL, $PANEL_PASSWORD, 1, $_SERVER["REMOTE_ADDR"], sha1(Functions::generationCode()), time(), 1]);

        FillBase::fill($db, $PREFIX);
        # ===============
        # FILL TABLES END
        # ===============


        // create db config
        $db_config = '<?php

return [
    "DB_HOST" => "'.$DB_HOST.'",
    "DB_NAME" => "'.$DB_NAME.'",
    "DB_USER" => "'.$DB_USER.'",
    "DB_PASSWORD" => "'.$DB_PASSWORD.'",
    "PREFIX" => "'.$PREFIX.'"
];';

        $fp = fopen(CORE . "/data/db_config.php", "w");
        flock($fp, LOCK_EX);
        fwrite($fp, $db_config);
        flock($fp, LOCK_UN);
        fclose($fp);

        // create config file
        $fp = fopen(CORE . "/data/config.php", "w");
        flock($fp, LOCK_EX);
        fwrite($fp, self::configFile($PANEL_HOME, $PANEL_EMAIL));
        flock($fp, LOCK_UN);
        fclose($fp);
        
        return 'next';
    }




    public function indexAction(){

        return '<form action method="POST">
            <h1><a href="//celena.io/" id="celena_logo" target="_blank" title="Celena logo"></a> Доступы</h1>
            <div class="dg access">
                <div>
                    <h3 class="title_hr">База данных</h3>
                    <div>
                        <label for="">Хост</label>
                        <input type="text" name="db[host]" value="localhost" placeholder="localhost">
                    </div>
                    <div>
                        <label for="">Имя базы данных</label>
                        <input type="text" name="db[name]">
                    </div>
                    <div>
                        <label for="">Пользователь (логин)</label>
                        <input type="text" name="db[user]">
                    </div>
                    <div>
                        <label for="">Пароль</label>
                        <input type="text" name="db[password]">
                    </div>
                    <div>
                        <label for="">Префикс к таблицам</label>
                        <input type="text" name="db[prefix]" value="sel_">
                    </div>
                </div>
                <div></div>
                <div>
                    <h3 class="title_hr">Доступ к панели управления</h3>
                    <div>
                        <label for="">Имя (логин)</label>
                        <input type="text" name="panel[name]">
                    </div>
                    <div>
                        <label for="">Email (логин)</label>
                        <input type="text" name="panel[email]">
                    </div>
                    <div>
                        <div class="fx">
                            <label for="">Пароль</label>
                            <div class="fx">
                                <div id="passwordHelper"></div>
                                <a href="#" class="generatePassword"></a>
                            </div>
                        </div>
                        <input type="password" name="panel[password]" id="password">
                    </div>
                    <div>
                        <label for="">Повторите пароль</label>
                        <input type="password" name="panel[password2]" id="password2">
                    </div>
                    <div>
                        <label for="">Адрес сайта (без протокода и слешей)</label>
                        <input type="text" name="panel[home]" value="'.$_SERVER["HTTP_HOST"].'">
                    </div>
                </div>
            </div>
            <input type="submit" data-a="Step" class="btn" id="createAccess" value="Далее">
        </form>';
    }


    private function configFile($home, $email){

        return '<?php

return [

    // вывод ошибок
	"errors" => 1,

    // вести журнал ошибок
	"db_log" => 1,

    // помощь разработчику
	"dev_tools" => 1,

    // какому IP показывать ошибки независимо от настроек выше
	"dev" => ["127.0.0.1"],

	"home" => "'.$home.'",

	"ssl" => 1,

	"site_title" => "Celena CMS",

	"site_description" => "Новый движок для создания блогов, магазинов и многих других приложений.",

	"panel" => "panel",

    // ЧПУ: 1 - link
    // ЧПУ: 2 - ID-link
    // ЧПУ: 3 - /category/link
    // ЧПУ: 4 - /category/ID-link
    "seo_type" => 3,

    // концовка URL товара
    "seo_type_end" => ".html",

    // разделитель чпу
    "separator" => " &#10148; ",

    // знак валюты
	"currency" => "$",

    // тип номера заказа
	"cart_id" => "int",

    // копейки
    "penny" => true,

    // знаков в ID товара
	"str_pad_id" => 6,

    // знаков в артикуле товара
    "str_pad_vendor" => 6,

    // количество товаров в категориях
	"count_prod_by_cat" => 5,

    // размер обрезки загружаемых изображений
	"origin_image" => 1500,

    // размер уменьшенной копии загружаемых изображений
	"thumb" => 300,

    // качество загружаемых изображений
	"quality_image" => 80,

    // качество уменьшенной копии
	"quality_thumb" => 80,

    // шаблон по умолчанию
	"template" => "Web",

    // перекидывать на страницу после оформления заказа
	"after_cart" => "/",

    // подтверждение почты
	"email_confirm" => 1,

    // email админа
	"admin_email" => "'.$email.'",

	"mail_method" => "mail",

    "noreply" => "noreply@kylaksizov.com",

    // SMTP
	"SMTPHost" => "",
	"SMTPLogin" => "",
	"SMTPPassword" => "",
	"SMTPSecure" => "ssl",
	"SMTPPort" => 465,
	"SMTPFrom" => "'.$email.'",

    "version" => "0.0.1",

];';

    }
}