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

    // email админа
    "admin_email" => "masterz1zzz@gmail.com",

    "mail_method" => "mail",

    "noreply" => "noreply@kylaksizov.com",

    // SMTP
    "SMTPHost" => "mail.adm.tools",
    "SMTPLogin" => "info@kylaksizov.com",
    "SMTPPassword" => "не скажу",
    "SMTPSecure" => "ssl",
    "SMTPPort" => 465,
    "SMTPFrom" => "info@kylaksizov.com",

    "version" => "0.0.1",

];