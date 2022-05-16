<?php

namespace app\core\system\install\steps;

class Finish{


    public function __construct() {}
    public function __clone() {}

    public function indexAction(){

        return 'Спасибо за установку нашей CMS!';
    }
}