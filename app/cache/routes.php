<?php

return [

    'panel' => [
        'products/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Products'],
		'products/(add/|edit/([0-9]+/)?)$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'addProduct'],
		'products/categories/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'categories'],
		'products/categories/(add/|edit/([0-9]+/)?)$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'addCategory'],
		'products/brands/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'brands'],
		'products/brands/(add/|edit/([0-9]+/)?)$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'addBrand'],
		'products/properties/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'properties'],
		'products/properties/(add/|edit/([0-9]+/)?)$' => ['controller' => 'plugins\Celena\Shop\Products', 'action' => 'addProperty'],
		'orders/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Orders'],
		'orders/[0-9]+/$' => ['controller' => 'plugins\Celena\Shop\Orders', 'action' => 'order'],
		'orders/click/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Orders', 'action' => 'click'],
		'settings/shop/$' => ['controller' => 'plugins\Celena\Shop\Settings'],
		'settings/promo-codes/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Settings', 'action' => 'promoCodes'],
		'settings/currency/$' => ['controller' => 'plugins\Celena\Shop\Settings', 'action' => 'currency'],
		'settings/payment-methods/$' => ['controller' => 'plugins\Celena\Shop\Settings', 'action' => 'paymentMethods'],
		'settings/delivery-methods/$' => ['controller' => 'plugins\Celena\Shop\Settings', 'action' => 'deliveryMethods'],
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
		'(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Index'],
		'([a-z-/0-9]+).html$' => ['controller' => 'plugins\Celena\Shop\Product'],
		'search/(page-[0-9]+/)?$' => ['controller' => 'plugins\Celena\Shop\Search'],
		'cart/$' => ['controller' => 'plugins\Celena\Shop\Cart'],
		'(.+?)/$' => ['controller' => 'plugins\Celena\Shop\Category'],
		'404/$' => ['controller' => 'NotFound'],
		'([a-z-0-9]+).html$' => ['controller' => 'page'],
    ],
    
];