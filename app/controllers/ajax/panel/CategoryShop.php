<?php

namespace app\controllers\ajax\panel;

use app\core\System;

class CategoryShop{

    public function index(){
        
        echo "<pre>---";
        print_r($_POST);
        print_r($_FILES);
        echo "</pre>";
        exit;


        //define('AJAX', '123');
        $res = '123';

        /*$script = '<script>
            $.server_say({say: "Запись удалена!", status: "success"});
        </script>';

        System::script($script);*/

        return '155';
    }

}