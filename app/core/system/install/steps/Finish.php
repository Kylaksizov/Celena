<?php

namespace app\core\system\install\steps;

class Finish{


    public function __construct() {}
    public function __clone() {}

    public function indexAction(){

        $config = require CORE . "/data/config.php";

        return '<div class="system_installed">
            <a href="//celena.io/" id="celena_logo" target="_blank" title="Celena logo"></a>
            <br>
            <p>Спасибо за установку нашей CMS!</p>
            <br>
            <div class="fx jc_c">
                <a href="//'.$config["home"].'/panel/" class="btn">Перейти в панель</a>&nbsp;&nbsp;&nbsp;
                <a href="//'.$config["home"].'/" class="btn">Перейти на сайт</a>
            </div>
        </div>';
    }
}