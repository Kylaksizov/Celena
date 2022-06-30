<?php

namespace app\controllers\ajax\panel;

use app\core\System;

class Systems{

    public function index(){

        if(!empty($_POST["clear"])) self::clear($_POST["clear"]);
        die("info::error::Не понимаю!");
    }


    /**
     * @name очистка данных
     * ====================
     * @return void
     */
    private static function clear($action){

        if($action == "dbLogs"){

            if(file_exists(CORE . '/tmp/db_errors.txt'))
                unlink(CORE . '/tmp/db_errors.txt');
        }

        $script = '<script>
                $("#nex_logs").html("");
                $.server_say({say: "Очистил!", status: "success"});
            </script>';
        System::script($script);
    }

}