<?php

return [

    'panel' => [
        '404/$' => ['controller' => 'NotFound'],
		'auth/$' => ['controller' => 'auth'],
		'posts/(page-[0-9]+/)?$' => ['controller' => 'posts'],
		'posts/(add/|edit/([0-9]+/)?)$' => ['controller' => 'posts', 'action' => 'add'],
		'pages/(page-[0-9]+/)?$' => ['controller' => 'pages'],
		'pages/(add/|edit/([0-9]+/)?)$' => ['controller' => 'pages', 'action' => 'add'],
		'category/(page-[0-9]+/)?$' => ['controller' => 'category'],
		'category/(add/|edit/([0-9]+/)?)$' => ['controller' => 'category', 'action' => 'add'],
		'users/(page-[0-9]+/)?$' => ['controller' => 'users'],
		'users/roles/(page-[0-9]+/)?$' => ['controller' => 'users', 'action' => 'roles'],
		'fields/$' => ['controller' => 'fields'],
		'fields/(add/|edit/(.+?/)?)$' => ['controller' => 'fields', 'action' => 'add'],
		'plugins/(page-[0-9]+/)?$' => ['controller' => 'plugins'],
		'plugin/[0-9]+/settings/$' => ['controller' => 'pluginSettings'],
		'modules/(page-[0-9]+/)?$' => ['controller' => 'modules'],
		'modules/(add/|edit/[0-9]+/)?$' => ['controller' => 'modules', 'action' => 'add'],
		'celenaShop/plugins/(page-[0-9]+/)?$' => ['controller' => 'celenaPlugins'],
		'celenaShop/templates/(page-[0-9]+/)?$' => ['controller' => 'celenaTemplates'],
		'celenaShop/order-development/(page-[0-9]+/)?$' => ['controller' => 'orderDevelopment'],
		'settings/$' => ['controller' => 'settings'],
		'settings/seo/$' => ['controller' => 'settings', 'action' => 'seo'],
		'settings/lang/$' => ['controller' => 'settings', 'action' => 'lang'],
		'system/routes/(page-[0-9]+/)?$' => ['controller' => 'System', 'action' => 'routes'],
		'system/logs/(page-[0-9]+/)?$' => ['controller' => 'System', 'action' => 'logs'],
		'system/db-logs/(page-[0-9]+/)?$' => ['controller' => 'System', 'action' => 'dbLogs'],
		'system/updates/$' => ['controller' => 'CelenaUpdates'],
		'support/(page-[0-9]+/)?$' => ['controller' => 'support'],
		'examplePlugin/$' => ['controller' => 'plugins\Celena\Example\Index'],
        
    ],
    
    'web' => [
        'example/$' => ['controller' => 'plugins\Celena\Example\Index'],
		'404/$' => ['controller' => 'NotFound'],
		'([a-z-0-9]+).html$' => ['controller' => 'page'],
		'(page-[0-9]+/)?$' => ['controller' => 'index'],
		'([a-z-/0-9]+).html$' => ['controller' => 'post'],
		'search/(page-[0-9]+/)?$' => ['controller' => 'search'],
		'(.+?)/$' => ['controller' => 'category'],
    ],
    
];