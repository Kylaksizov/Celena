<?php

return [

    'panel' => [

        '404/$' => ['controller' => 'NotFound'],

        'auth/$' => ['controller' => 'auth'],

        'products/(page-[0-9]+/)?$' => ['controller' => 'products'],
        'products/(add/|edit/([0-9]+/)?)$' => ['controller' => 'products', 'action' => 'addProduct'],
        'products/categories/(page-[0-9]+/)?$' => ['controller' => 'products', 'action' => 'categories'],
        'products/categories/(add/|edit/([0-9]+/)?)$' => ['controller' => 'products', 'action' => 'addCategory'],
        'products/brands/(page-[0-9]+/)?$' => ['controller' => 'products', 'action' => 'brands'],
        'products/brands/(add/|edit/([0-9]+/)?)$' => ['controller' => 'products', 'action' => 'addBrand'],
        'products/properties/(page-[0-9]+/)?$' => ['controller' => 'products', 'action' => 'properties'],
        'products/properties/(add/|edit/([0-9]+/)?)$' => ['controller' => 'products', 'action' => 'addProperty'],

        'orders/(page-[0-9]+/)?$' => ['controller' => 'orders'],
        'orders/[0-9]+/$' => ['controller' => 'orders', 'action' => 'order'],
        'orders/click/(page-[0-9]+/)?$' => ['controller' => 'orders', 'action' => 'click'],

        'posts/news/(page-[0-9]+/)?$' => ['controller' => 'posts', 'action' => 'news'],
        'posts/pages/(page-[0-9]+/)?$' => ['controller' => 'posts', 'action' => 'pages'],
        'posts/pages/(add/|edit/([0-9]+/)?)$' => ['controller' => 'posts', 'action' => 'addPage'],
        'posts/categories/(page-[0-9]+/)?$' => ['controller' => 'posts', 'action' => 'categories'],

        'users/(page-[0-9]+/)?$' => ['controller' => 'users'],
        'users/customer/(page-[0-9]+/)?$' => ['controller' => 'users', 'action' => 'customer'],
        'users/employee/(page-[0-9]+/)?$' => ['controller' => 'users', 'action' => 'employee'],
        'users/roles/(page-[0-9]+/)?$' => ['controller' => 'users', 'action' => 'roles'],

        'celena_shop/(page-[0-9]+/)?$' => ['controller' => 'shop'],
        'celena_shop/templates/(page-[0-9]+/)?$' => ['controller' => 'shop', 'action' => 'templates'],
        'celena_shop/plugins/(page-[0-9]+/)?$' => ['controller' => 'shop', 'action' => 'plugins'],
        'celena_shop/modules/(page-[0-9]+/)?$' => ['controller' => 'shop', 'action' => 'modules'],
        'celena_shop/order-development/(page-[0-9]+/)?$' => ['controller' => 'shop', 'action' => 'orderDevelopment'],

        'settings/$' => ['controller' => 'settings'],
        'settings/seo/$' => ['controller' => 'settings', 'action' => 'seo'],
        'settings/promo-codes/(page-[0-9]+/)?$' => ['controller' => 'settings', 'action' => 'promoCodes'],
        'settings/lang/$' => ['controller' => 'settings', 'action' => 'lang'],
        'settings/currency/$' => ['controller' => 'settings', 'action' => 'currency'],
        'settings/payment-methods/$' => ['controller' => 'settings', 'action' => 'paymentMethods'],
        'settings/delivery-methods/$' => ['controller' => 'settings', 'action' => 'deliveryMethods'],

        'system/logs/(page-[0-9]+/)?$' => ['controller' => 'System', 'action' => 'Logs'],
        'system/db-logs/(page-[0-9]+/)?$' => ['controller' => 'System', 'action' => 'DbLogs'],

        'support/(page-[0-9]+/)?$' => ['controller' => 'support'],

        'Kylaksizov/Example/$' => ['controller' => 'support'],

    ],

    'web' => [

        'kyl/$' => [
            'controller' => 'plugins\Kylaksizov\Example\Index',
            'action' => 'index'
        ],

        '(page-[0-9]+/)?$' => ['controller' => 'index'],
        '404/$' => ['controller' => 'NotFound'],
        '([a-z-0-9]+).html$' => ['controller' => 'page'],
        '([a-z-/0-9]+).html$' => ['controller' => 'product'],
        'cart/$' => ['controller' => 'cart'],
        'search/(page-[0-9]+/)?$' => ['controller' => 'search'],
        '(.+?)/$' => ['controller' => 'category'],

        'news/shop$' => [
            'controller' => 'news',
            'action' => 'shop'
        ],

    ]

];