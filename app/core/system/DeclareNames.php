<?php

namespace app\core\system;

class DeclareNames{

    const ROUTES = [

        'panel' => [

            '404/$' => ['controller' => 'NotFound'],
            'auth/$' => ['controller' => 'auth'],

            'news/(page-[0-9]+/)?$' => ['controller' => 'news'],
            'news/(add/|edit/([0-9]+/)?)$' => ['controller' => 'products', 'action' => 'addNews'],

            'categories/(page-[0-9]+/)?$' => ['controller' => 'products', 'action' => 'categories'],
            'categories/(add/|edit/([0-9]+/)?)$' => ['controller' => 'products', 'action' => 'addCategory'],

            'pages/(page-[0-9]+/)?$' => ['controller' => 'posts', 'action' => 'pages'],
            'posts/pages/(add/|edit/([0-9]+/)?)$' => ['controller' => 'posts', 'action' => 'addPage'],
            'posts/categories/(page-[0-9]+/)?$' => ['controller' => 'posts', 'action' => 'categories'],

            'users/(page-[0-9]+/)?$' => ['controller' => 'users'],
            'users/customer/(page-[0-9]+/)?$' => ['controller' => 'users', 'action' => 'customer'],
            'users/employee/(page-[0-9]+/)?$' => ['controller' => 'users', 'action' => 'employee'],
            'users/roles/(page-[0-9]+/)?$' => ['controller' => 'users', 'action' => 'roles'],

            'plugins/(page-[0-9]+/)?$' => ['controller' => 'plugins'],
            'plugin/[0-9]+/settings/$' => ['controller' => 'pluginSettings'],
            'modules/(page-[0-9]+/)?$' => ['controller' => 'modules'],
            'celenaShop/plugins/(page-[0-9]+/)?$' => ['controller' => 'celenaPlugins'],
            'celenaShop/order-development/(page-[0-9]+/)?$' => ['controller' => 'orderDevelopment'],

            'settings/$' => ['controller' => 'settings'],
            'settings/seo/$' => ['controller' => 'settings', 'action' => 'seo'],
            'settings/promo-codes/(page-[0-9]+/)?$' => ['controller' => 'settings', 'action' => 'promoCodes'],
            'settings/lang/$' => ['controller' => 'settings', 'action' => 'lang'],
            'settings/payment-methods/$' => ['controller' => 'settings', 'action' => 'paymentMethods'],
            'settings/delivery-methods/$' => ['controller' => 'settings', 'action' => 'deliveryMethods'],

            'system/logs/(page-[0-9]+/)?$' => ['controller' => 'System', 'action' => 'Logs'],
            'system/db-logs/(page-[0-9]+/)?$' => ['controller' => 'System', 'action' => 'DbLogs'],

            'support/(page-[0-9]+/)?$' => ['controller' => 'support'],

        ],

        'web' => [

            '(page-[0-9]+/)?$' => ['controller' => 'index'],
            '404/$' => ['controller' => 'NotFound'],
            '([a-z-0-9]+).html$' => ['controller' => 'page'],
            '([a-z-/0-9]+).html$' => ['controller' => 'post'],
            'search/(page-[0-9]+/)?$' => ['controller' => 'search'],
            '(.+?)/$' => ['controller' => 'category'],
        ]

    ];


    // будет дорабатываться
    const FOLDERS = [
        "app/"
    ];
}