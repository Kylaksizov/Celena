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


    // получение инфы о новой версии системы
    public static function getUpdate($newVersion = false){
        return self::request("getUpdate/", $newVersion ? "checkNewVersion=".$newVersion : "");
    }


    // получение (версии) системы
    public static function getUpdateVersion(){
        return self::request("getUpdate/", "getVersion=1");
    }


    // установка обновления системы
    public static function installUpdate(){
        return self::request("update/install/");
    }


    // получение (версий) плагинов
    public static function getUpdatePlugins($plugins_ids){
        return self::request("getUpdatePlugins/", "plugins=$plugins_ids");
    }


    // установка обновления плагина
    public static function installUpdatePlugin($pluginId, $pluginVersion){
        return self::request("updatePlugin/", "plugin_id=$pluginId&pluginVersion=$pluginVersion");
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, "authDev=".sha1("7")."&host=".CONFIG_SYSTEM["home"]."&celenaVersion=".CONFIG_SYSTEM["version"].($post?"&".$post:"")."&php=".phpversion()."&format=$format");
        //curl_setopt($ch, CURLOPT_HEADER, true);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_URL, 'https://'.self::CELENA_URI.'/'.$method);

        $html = curl_exec($ch);
        //$document_info = curl_getinfo($ch);
        curl_close($ch);

        return $html;
    }

}