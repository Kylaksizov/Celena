<?php

namespace app\core\system\install;

use app\core\System;

class Install{



    public $post = false;





    /**
     * @name treatment
     * ===============
     */
    public function __construct(){

        // POST
        if(!empty($_POST["ajax"])){
            if(!empty($_POST["params"])) mb_parse_str($_POST["params"], $_POST);
            else die("info::error::Видимо Вы что-то не пропустили!");
            $this->post = $_POST;
        }


        $step = !empty($_GET["step"]) ? 'Step_'.intval($_GET["step"]) : 'Start';

        $stepClass = 'app\core\system\install\steps\\'.$step;

        if(class_exists($stepClass)){

            $controller = new $stepClass();

            // if - AJAX POST
            if($this->post && method_exists($stepClass, 'postAction')){

                $result = $controller->postAction();

                if($result == 'next'){

                    $result = '<script>
                        
                    </script>';
                }

                System::script($result);

            } else if(method_exists($stepClass, 'indexAction')){

                $content = $controller->indexAction();

                self::render($content);

            } else{

                die("Не найден контроллер установки для этапа № ".$step." !");
            }
        }
    }






    /**
     * @name display
     * =============
     * @param $content
     * @return void
     */
    public function render($content){

        echo '<!doctype html>
        <html lang="en">
        <head>
            <head>
                <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                <meta name="apple-mobile-web-app-capable" content="yes">
                <meta name="apple-mobile-web-app-status-bar-style" content="default">
        
                <link rel="shortcut icon" href="{THEME}/img/favicon.ico">
                <link rel="stylesheet" href="/app/core/system/install/css/style.css">
        
                <script type="text/javascript" src="/templates/_system/js/jquery.min.js"></script>
                <script type="text/javascript" src="/templates/_system/js/panel.js"></script>
                <script type="text/javascript" src="/app/core/system/install/js/script.js"></script>
            </head>
        </head>
        <body>
        
            <div id="content">
                '.$content.'
            </div>
        
            <div id="main_answer_server"></div>
            
        </body>
        </html>';
    }

}