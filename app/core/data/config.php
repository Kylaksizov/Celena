<?php

return [

    // вывод ошибок: 0 - выкл., 1 - ошибки будут показаны только разработчику, IP которого на настройку ниже
    "errors" => 1,

    "db_log" => 1,

    // IP разработчика
    "dev" => [
        "127.0.0.1",
        "93.79.238.129"
    ],

    "home" => "http://nexshop/",

    "panel" => "panel",

    // размер обрезки загружаемых изображений
    "origin_image" => "1500",

    // размер уменьшенной копии загружаемых изображений
    "thumb" => "300",

    // качество загружаемых изображений
    "quality_image" => "80",

    // качество уменьшенной копии
    "quality_thumb" => "80",

    // шаблон по умолчанию
    "template" => "Web",

    "auth" => [
        "googleClientId" => '956715554556-ckj2ju70102elqq6990k8rb3439qb28e.apps.googleusercontent.com',
        "googleClientSecret" => 'MQc0zaALnIANl2KEmJBDsPH2',
        "redirect_url" => 'https://nex.company/'
    ],


    "telegramToken" => "5045603422:AAF3be0tXSZXdPz_3kt0xOhfluSN0jcv4eU",


    // LIQ-PAY
    //"LiqPay_public_key" => "sandbox_i61178662755",
    "LiqPay_public_key" => "i15845909488",
    //"LiqPay_private_key" => "sandbox_LJQTJkT3Xh5M024SeMFHm9L5e4riRcaNo8rBYAnJ",
    "LiqPay_private_key" => "A31DuJRMAr5FcS6Eyi4v1VwwxBHEuODlioqpotYd",


    // email админа
    "noreply" => "noreply@nex.company",
    "admin_email" => "info@nex.company",

    // SMTP
    "SMTPHost" => "smtp.beget.com",
    "SMTPLogin" => "info@nex.company",
    "SMTPPassword" => "Kyls17*master",
    "SMTPSecure" => "ssl",
    "SMTPPort" => 465,
    "SMTPFrom" => "info@nex.company",

    "version" => "0.0.1",

];