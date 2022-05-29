<?php

namespace app\traits;

trait Functions{


    /**
     * @name генерация кодов
     * =====================
     * @description подходит для генерации промокодов и подобных вещей...
     * =====================
     * @example generationCode(кол-во символов, true - верхний регистр | false - разный регистр, кол-во символов, после которых идет разделение на дефис);
     * =====================
     * @param $countChar
     * @param $uppercase
     * @param $divider
     * @return string
     */
    public static function generationCode($countChar = 16, $uppercase = true, $divider = false){
        $chars = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if(!$uppercase) $chars .= 'abcdefghijklmmnopqrstuvwxyz';
        $hashpromo = '';
        $j = 0;
        for($ichars = 0; $ichars < $countChar; ++$ichars) {
            $random = str_shuffle($chars);
            $t = '';
            if($divider != false && $j == $divider){
                $t = '-';
                $j = 0;
            }
            $hashpromo .= $t . $random[0];
            if($j != $divider) $j++;
        }
        return $hashpromo;
    }



    /**
     * @name генерация паролей
     * =======================
     * @description для генерации паролей
     * =======================
     * @example generationPassword(кол-во символов);
     * =======================
     * @param $number
     * @return string
     */
    public static function generationPassword($number = 8){
        $arr = array('a','b','c','d','e','f',
            'g','h','i','j','k','l',
            'm','n','o','p','r','s',
            't','u','v','x','y','z',
            'A','B','C','D','E','F',
            'G','H','I','J','K','L',
            'M','N','O','P','R','S',
            'T','U','V','X','Y','Z',
            '1','2','3','4','5','6',
            '7','8','9','0','.',',',
            '(',')','[',']','!','?',
            '&','^','%','@','*','$',
            '<','>','/','|','+','-',
            '{','}','`','~');
        // Генерируем пароль
        $pass = "";
        for($i = 0; $i < $number; $i++) {
            // Вычисляем случайный индекс массива
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }
        return $pass;
    }



    /**
     * @name привязка атрибута к input[type="checkbox"]
     * ================================================
     * @param $data
     * @return string
     */
    public static function check($data){
        if($data) return ' checked';
        else return '';
    }

}