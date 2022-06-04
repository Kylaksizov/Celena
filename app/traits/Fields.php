<?php

namespace app\traits;

trait Fields {





    public static function get(){

        if(file_exists(CORE . '/data/fields.json'))
            $fields = file_get_contents(CORE . '/data/fields.json');

        else $fields = null;

        return $fields;
    }
}