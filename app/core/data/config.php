<?php

return [

    // вывод ошибок
	"errors" => 1,

    // вести журнал ошибок
	"db_log" => 1,

    // помощь разработчику
	"dev_tools" => 1,

    // какому IP показывать ошибки независимо от настроек выше
	"dev" => ["127.0.0.1"],

	"home" => "nexshop",

	"main" => 1,

	"main_content" => "",

	"power" => 1,

	"power_text" => "Сайт находится на реконструкции.",

	"ssl" => 0,

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

    // уменьшенные копии через редактор quill
	"quill_thumbs" => 1,

    // разрешить комментарии
	"comments" => 1,

    // шаблон по умолчанию
	"template" => "CelenaShop",

    // подтверждение почты
	"email_confirm" => 0,

    // email админа
	"admin_email" => "masterz1zzz@gmail.com",

	"mail_method" => "mail",

    "noreply" => "noreply@celena.io",

    // SMTP
	"SMTPHost" => "",
	"SMTPLogin" => "",
	"SMTPPassword" => "",
	"SMTPSecure" => "ssl",
	"SMTPPort" => 2525,
	"SMTPFrom" => "",

	"version" => "0.1.3",

];