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

        $filesTurnOriginal = [];

        // перебираем файлы
        foreach ($Modules as $filePath => $listActions) {

            #TODO нужно наверное исправить запрос в БД, так как идет пустой массив, если модуль не имеет действий над файлами
            if(empty($filePath)) continue;

            if(file_exists(CORE . '/system/modules/originals/' . $filePath)) $originalFileContent = file_get_contents(CORE . '/system/modules/originals/' . $filePath);
            else{

                if(file_exists(ROOT . '/' . $filePath))
                    $originalFileContent = file_get_contents(ROOT . '/' . $filePath);

                else{

                    if($listActions[0]["action"] == 5){

                        self::createDirFile($filePath, $listActions[0]["replacecode"]);
                        continue;

                    } else{

                        Log::add('Файл <b>'.$filePath.'</b> не найден для изменения');
                        return false;
                    }
                }
            }

            $resultFileContent = $originalFileContent;

            // перебираем список действий
            foreach ($listActions as $row) {

                $filesTurnOriginal[CORE . '/system/modules/originals/' . $filePath][] = $row["status"];

                if($row["status"] == '0') continue;

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

            if(!empty($filePath)){
                self::createDirFile('app/core/system/modules/originals/' . $filePath, $originalFileContent);
                self::createDirFile($filePath, $resultFileContent);
            }
        }

        // перебираем файлы, которые нужно поставить в оригинал
        foreach ($filesTurnOriginal as $filePath => $arr) {
            if(array_search('1', $arr) === false){
                $originalPath = explode("system/modules/originals/", $filePath);
                if(file_exists($filePath)) copy($filePath, ROOT . '/' . $originalPath[1]);
                unlink($filePath);
            }
        }
    }




    public static function buildRoutes($newRoutes, $oldRoutes = []){

        $newRoutes = json_decode($newRoutes, true);

        if(!empty($oldRoutes)){

            $oldRoutes = json_decode($oldRoutes, true);
            $routePrepare = [];
            if(!empty($oldRoutes["panel"]["url"])) $routePrepare["panel"] = $oldRoutes["panel"]["url"];
            if(!empty($oldRoutes["web"]["url"])) $routePrepare["web"] = $oldRoutes["web"]["url"];

            // удаляем старые роуты данного модуля
            System::removeRoute($routePrepare);
        }

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

        if(!empty($routes)) System::addRoute($routes);
        if(!empty($routesEnd)) System::addRoute($routesEnd, false);
    }




    public static function package(){
        //$ModuleModel = new ModuleModel();
        //$Modules = $ModuleModel->getByPackage();
    }



    private static function createDirFile($filePath, $content){

        $filePath = explode("/", $filePath);
        $fileName = array_pop($filePath);

        $next = '';
        foreach ($filePath as $dir) {
            $next .= '/' . $dir;
            if(!file_exists(ROOT . $next)){
                mkdir(ROOT . $next);
            }
        }

        $fp = fopen(ROOT . $next . '/' . $fileName, "w");
        flock($fp, LOCK_EX);
        fwrite($fp, $content);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

}