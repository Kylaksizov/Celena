<?php

namespace app\controllers;

use app\core\Controller;
use app\core\View;
use app\traits\SiteMap;


class SiteMapController extends Controller {


    public function indexAction(){

        $SiteMap = SiteMap::get();

        // если страница есть
        if($SiteMap){

            header('Content-Type: text/xml');
            die($SiteMap);

        } else{

            header("Location: ".CONFIG_SYSTEM["home"]."/404/");
            View::errorCode(404);
        }
    }

}