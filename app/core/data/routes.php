<?php

return [

    '' => ['controller' => 'index',],
    '404/' => ['controller' => 'NotFound'],
    'balance/' => ['controller' => 'balance'],

    'leads/' => ['controller' => 'leads'],

    'action/login' => [
        'controller' => 'account',
        'action' => 'login'
    ],

    'news/shop' => [
        'controller' => 'news',
        'action' => 'shop'
    ],

    'board/' => [
        'controller' => 'plugins\Kylaksizov\Board\Index',
        'action' => 'index'
    ],

];