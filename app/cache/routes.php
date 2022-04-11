<?php

return [

    'panel' => [

        '404/$' => ['controller' => 'NotFound'],

        'products/(page-[0-9]+/)?$' => ['controller' => 'products'],
        'products/(add/|edit/([0-9]+/)?)$' => ['controller' => 'products', 'action' => 'addProduct'],
        'products/categories/(page-[0-9]+/)?$' => ['controller' => 'products', 'action' => 'categories'],
        'products/categories/(add/|edit/([0-9]+/)?)$' => ['controller' => 'products', 'action' => 'addCategory'],
        'products/brands/(page-[0-9]+/)?$' => ['controller' => 'products', 'action' => 'brands'],
        'products/brands/(add/|edit/([0-9]+/)?)$' => ['controller' => 'products', 'action' => 'addBrand'],
        'products/properties/(page-[0-9]+/)?$' => ['controller' => 'products', 'action' => 'properties'],
        'products/properties/(add/|edit/([0-9]+/)?)$' => ['controller' => 'products', 'action' => 'addProperty'],

        'orders/(page-[0-9]+/)?$' => ['controller' => 'orders'],
        'orders/click/(page-[0-9]+/)?$' => ['controller' => 'orders', 'action' => 'click'],

        'posts/news/(page-[0-9]+/)?$' => ['controller' => 'posts', 'action' => 'news'],
        'posts/pages/(page-[0-9]+/)?$' => ['controller' => 'posts', 'action' => 'pages'],
        'posts/categories/(page-[0-9]+/)?$' => ['controller' => 'posts', 'action' => 'categories'],

        'users/(page-[0-9]+/)?$' => ['controller' => 'users'],
        'users/customer/(page-[0-9]+/)?$' => ['controller' => 'users', 'action' => 'customer'],
        'users/employee/(page-[0-9]+/)?$' => ['controller' => 'users', 'action' => 'employee'],
        'users/roles/(page-[0-9]+/)?$' => ['controller' => 'users', 'action' => 'roles'],

        'shop/(page-[0-9]+/)?$' => ['controller' => 'shop'],
        'shop/templates/(page-[0-9]+/)?$' => ['controller' => 'shop', 'action' => 'templates'],
        'shop/plugins/(page-[0-9]+/)?$' => ['controller' => 'shop', 'action' => 'plugins'],
        'shop/modules/(page-[0-9]+/)?$' => ['controller' => 'shop', 'action' => 'modules'],
        'shop/order-development/(page-[0-9]+/)?$' => ['controller' => 'shop', 'action' => 'orderDevelopment'],

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

    ],

    'web' => [

        '404/$' => ['controller' => 'NotFound'],
        '(.+?).html$' => ['controller' => 'product'],
        '(.+?)/$' => ['controller' => 'category'],

        'news/shop$' => [
            'controller' => 'news',
            'action' => 'shop'
        ],

    ]

];