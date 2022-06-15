<?php

return [

    // вывод ошибок
	"errors" => 0,

    // вести журнал ошибок
	"db_log" => 1,

    // помощь разработчику
	"dev_tools" => 1,

    // какому IP показывать ошибки независимо от настроек выше
	"dev" => ["127.0.0.1"],

	"home" => "nexshop",

	"ssl" => 1,

	"site_title" => "Celena CMS",

	"site_description" => "Новый движок для создания блогов, магазинов и многих других приложений.",

	"panel" => "panel",

    // ЧПУ: 1 - link
    // ЧПУ: 2 - ID-link
    // ЧПУ: 3 - /category/link
    // ЧПУ: 4 - /category/ID-link
    "seo_type" => 4,

    // концовка URL товара
    "seo_type_end" => ".html",

    // разделитель чпу
    "separator" => "&nbsp;&nbsp;&#10148;&nbsp;&nbsp;",

    // кол-во товаров в категории
	"count_in_cat" => 12,

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

    // подтверждение почты
	"email_confirm" => 0,

    // email админа
	"admin_email" => "masterz1zzz@gmail.com",

	"mail_method" => "mail",

    "noreply" => "noreply@kylaksizov.com",

    // SMTP
	"SMTPHost" => "smtp.mailtrap.io",
	"SMTPLogin" => "55aeba55b268e6",
	"SMTPPassword" => "077917546beb79",
	"SMTPSecure" => "ssl",
	"SMTPPort" => 2525,
	"SMTPFrom" => "info@kylaksizov.com",

	"version" => "0.0.1",

];