<?php

namespace app\_classes;

class Chart{


    public static function start(){

        $inc = '';
        if(!defined('CHART')) {

            define('CHART', true);

            $inc .= '<script src="/app/libs/charts/amcharts5/index.js"></script>
<script src="/app/libs/charts/amcharts5/themes/Material.js"></script>
<script src="/app/libs/charts/amcharts5/locales/ru_RU.js"></script>
<script src="/app/libs/charts/amcharts5/themes/Animated.js"></script>
<script src="/app/libs/charts/amcharts5/xy.js"></script>';

        }

        return $inc;
    }


    public static function init($moules = []){

        $inc = self::start();

        if(!empty($moules)){

            foreach ($moules as $moule) {
                $inc .= '<script src="/app/libs/charts/amcharts5/'.$moule.'"></script>';
            }
        }

        /*if(!defined('CHART')) {

            define('CHART', true);

            $inc .= '<script src="/templates/_plugins/amcharts5/index.js"></script>
<script src="/templates/_plugins/amcharts5/themes/Material.js"></script>';

            if(!empty($moules)){

                foreach ($moules as $moule) {
                    $inc .= '<script src="/templates/_plugins/amcharts5/'.$moule.'"></script>';
                }
            }

            $inc .= '
<script src="/templates/_plugins/amcharts5/locales/ru_RU.js"></script>
<script src="/templates/_plugins/amcharts5/themes/Animated.js"></script>';

        } else{

            if(!empty($moules)){

                foreach ($moules as $moule) {
                    $inc .= '<script src="/templates/_plugins/amcharts5/'.$moule.'"></script>';
                }
            }
        }*/

        return $inc;

    }


}