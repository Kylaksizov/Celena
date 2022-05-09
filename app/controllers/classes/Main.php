<?php

namespace app\controllers\classes;

class Main{


    public static function scanTags($e){

        preg_match_all('/\{products\s+(category=\"(.+?)\")?\s+(template=\"(.+?)\")?\s+(limit=\"(.+?)\")?\}/is', $e->view->include[$e->route["controller"]], $products);

        $e->view->set($products[0][0], 'Есть контакт');
    }

}