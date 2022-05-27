<?php

namespace app\core\system\shop;

use app\models\panel\PluginModel;

class ShopController{


    CONST CELENA_URI = 'api.celena.io';


    public static function getPlugins(){

        $PluginsModel = new PluginModel();
        $MyIdsPlugins = $PluginsModel->getMyPluginsIds();

        $post = !empty($MyIdsPlugins) ? "myPlugins=".implode(",", $MyIdsPlugins) : "";
        return self::request("shop/plugins/", $post);
    }


    public static function getPlugin($id, $format = false){
        return self::request("shop/plugin/$id/", "", $format);
    }


    public static function installPlugin($id){
        return self::request("shop/install/plugin/$id/");
    }


    private static function request($method, $post = "", $format = false){

        /*$headers = array(
            "Accept: application/json",
            "Authorization: Bearer " . sha1("7"),
        );*/

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "authDev=".sha1("7").($post?"&".$post:"")."&format=$format");
        //curl_setopt($ch, CURLOPT_HEADER, true);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_URL, 'https://'.self::CELENA_URI.'/'.$method);

        $html = curl_exec($ch);
        //$document_info = curl_getinfo($ch);
        curl_close($ch);

        return $html;
    }

}