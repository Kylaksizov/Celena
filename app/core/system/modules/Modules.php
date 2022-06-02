<?php

namespace app\core\system\modules;

use app\core\System;
use app\models\panel\ModuleModel;
use app\traits\Log;

/**
 * TMP
 */
class Modules{



    public static function initialize(){

        $ModuleModel = new ModuleModel();
        $Modules = $ModuleModel->getByInitialize();

        if(empty($Modules)) return null;

        // перебираем файлы
        foreach ($Modules as $filePath => $listActions) {

            if(file_exists(CORE . '/system/modules/originals/' . $filePath)) $originalFileContent = file_get_contents(CORE . '/system/modules/originals/' . $filePath);
            else{

                if(file_exists(APP . '/' . $filePath))
                    $originalFileContent = file_get_contents(APP . '/' . $filePath);

                else{

                    if($listActions[0]["action"] == 5){

                        self::createOriginalFile($filePath, $listActions[0]["replacecode"]);
                        continue;

                    } else{

                        Log::add('Файл <b>'.APP . '/' . $filePath.'</b> не найден для изменения');
                        return false;
                    }
                }
            }

            $resultFileContent = $originalFileContent;

            // перебираем список действий
            foreach ($listActions as $row) {

                switch ($row["action"]){

                    case '1': // найти и заменить

                        $resultFileContent = str_replace($row["searchcode"], $row["replacecode"], $resultFileContent);
                        break;

                    case '2': // найти и добавит выше

                        $resultFileContent = str_replace($row["searchcode"], $row["replacecode"].PHP_EOL.$row["searchcode"], $resultFileContent);
                        break;

                    case '3': // найти и добавить ниже

                        $resultFileContent = str_replace($row["searchcode"], $row["searchcode"].PHP_EOL.$row["replacecode"], $resultFileContent);
                        break;

                    case '4': // заменить весь файл

                        $resultFileContent = $row["replacecode"];
                        break;

                }
            }

            self::createOriginalFile('core/system/modules/originals/' . $filePath, $originalFileContent);
            self::createOriginalFile($filePath, $resultFileContent);

        }
    }




    public static function buildRoutes($oldRoutes, $newRoutes){

        $oldRoutes = json_decode($oldRoutes, true);
        $newRoutes = json_decode($newRoutes, true);
        $routePrepare = [
            'panel' => $oldRoutes["panel"]["url"],
            'web' => $oldRoutes["web"]["url"]
        ];

        // удаляем старые роуты данного модуля
        System::removeRoute($routePrepare);

        $panelRoutes = [];
        $panelRoutesEnd = [];
        $webRoutes = [];
        $webRoutesEnd = [];

        if(!empty($newRoutes["panel"]["url"])){
            foreach ($newRoutes["panel"]["url"] as $key => $url) {
                if(isset($newRoutes["panel"]["position"][$key])){
                    $panelRoutes["panel"][$url]['controller'] = $newRoutes["panel"]["controller"][$key];
                    if(!empty($newRoutes["panel"]["action"][$key]))
                        $panelRoutes["panel"][$url]['action'] = $newRoutes["panel"]["action"][$key];
                } else {
                    $panelRoutesEnd["panel"][$url]['controller'] = $newRoutes["panel"]["controller"][$key];
                    if(!empty($newRoutes["panel"]["action"][$key]))
                        $panelRoutesEnd["panel"][$url]['action'] = $newRoutes["panel"]["action"][$key];
                }
            }
        }

        if(!empty($newRoutes["web"]["url"])){
            foreach ($newRoutes["web"]["url"] as $key => $url) {
                if(isset($newRoutes["web"]["position"][$key])){
                    $webRoutes["web"][$url]['controller'] = $newRoutes["web"]["controller"][$key];
                    if(!empty($newRoutes["web"]["action"][$key]))
                        $webRoutes["web"][$url]['action'] = $newRoutes["web"]["action"][$key];
                } else {
                    $webRoutesEnd["web"][$url]['controller'] = $newRoutes["web"]["controller"][$key];
                    if(!empty($newRoutes["panel"]["action"][$key]))
                        $webRoutesEnd["web"][$url]['action'] = $newRoutes["web"]["action"][$key];
                }
            }
        }

        $routes = array_merge($panelRoutes, $webRoutes);
        $routesEnd = array_merge($panelRoutesEnd, $webRoutesEnd);

        if(!empty($routesEnd)) System::addRoute($routes);
        if(!empty($routes)) System::addRoute($routesEnd, false);
    }




    public static function package(){
        //$ModuleModel = new ModuleModel();
        //$Modules = $ModuleModel->getByPackage();
    }



    private static function createOriginalFile($filePath, $content){
        
        $filePath = explode("/", $filePath);
        $fileName = array_pop($filePath);

        $next = '';
        foreach ($filePath as $dir) {
            $next .= '/' . $dir;
            if(!file_exists(APP . $next)) @mkdir(APP . $next);
        }

        $fp = fopen(APP . $next . '/' . $fileName, "w");
        flock($fp, LOCK_EX);
        fwrite($fp, $content);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

}