<?php

namespace app\core\system\install\steps;

class Step_1{


    public function __construct() {}
    public function __clone() {}




    public function postAction(){

        return 'next';
    }




    public function indexAction(){

        return '<form action method="POST">
            <h1>Проверка...</h1>
            <div class="licence_text">
                
            </div>
            <input type="submit" data-a="Step" class="btn" value="Далее">
        </form>';
    }
}