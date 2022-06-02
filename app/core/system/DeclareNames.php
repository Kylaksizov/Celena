<?php

namespace app\core\system;

class DeclareNames{

    const ROUTES = [

        'panel' => [

            '404/$' => ['controller' => 'NotFound'],
            'auth/$' => ['controller' => 'auth'],
            'posts/news/(page-[0-9]+/)?$' => ['controller' => 'posts', 'action' => 'news'],
            'posts/pages/(page-[0-9]+/)?$' => ['controller' => 'posts', 'action' => 'pages'],
            'posts/pages/(add/|edit/([0-9]+/)?)$' => ['controller' => 'posts', 'action' => 'addPage'],
            'posts/categories/(page-[0-9]+/)?$' => ['controller' => 'posts', 'action' => 'categories'],
            'users/(page-[0-9]+/)?$' => ['controller' => 'users'],
            'users/customer/(page-[0-9]+/)?$' => ['controller' => 'users', 'action' => 'customer'],
            'users/(page-[0-9]+/)?$' => ['controller' => 'users'],
            'users/roles/(page-[0-9]+/)?$' => ['controller' => 'users', 'action' => 'roles'],
            'plugins/(page-[0-9]+/)?$' => ['controller' => 'plugins'],
            'plugin/[0-9]+/settings/$' => ['controller' => 'pluginSettings'],
            'modules/(page-[0-9]+/)?$' => ['controller' => 'modules'],
            'modules/(add/|edit/[0-9]+/)?$' => ['controller' => 'modules', 'action' => 'add'],
            'celenaShop/plugins/(page-[0-9]+/)?$' => ['controller' => 'celenaPlugins'],
            'celenaShop/order-development/(page-[0-9]+/)?$' => ['controller' => 'orderDevelopment'],
            'settings/$' => ['controller' => 'settings'],
            'settings/seo/$' => ['controller' => 'settings', 'action' => 'seo'],
            'settings/lang/$' => ['controller' => 'settings', 'action' => 'lang'],
            'system/routes/(page-[0-9]+/)?$' => ['controller' => 'System', 'action' => 'routes'],
            'system/logs/(page-[0-9]+/)?$' => ['controller' => 'System', 'action' => 'logs'],
            'system/db-logs/(page-[0-9]+/)?$' => ['controller' => 'System', 'action' => 'dbLogs'],
            'system/updates/$' => ['controller' => 'CelenaUpdates'],
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