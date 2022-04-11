<?php

namespace app\core;


use Exception;
use PDO;
use PDOException;
use PDOStatement;

class Base {

    public static $connection  = false;
    public static $countSql    = 0;
    public static $queries     = [];
    public static $countErrors = 0;
    public static $errors      = [];


    protected static $instance = null;

    public function __construct() {}
    public function __clone() {}

    public static function instance($conf = null){

        if (self::$instance === null || $conf !== null){

            $db_conf = include ROOT . '/app/core/data/db_config.php';

            // если гость
            //if(empty($db_conf)) $db_conf = require_once ROOT . '/core/data/db_config.php';

            !empty($db_conf["timezone"]) ? date_default_timezone_set($db_conf["timezone"]) : date_default_timezone_set("Europe/Kiev");

            if(!defined("PREFIX")) define("PREFIX", $db_conf["PREFIX"]);

            $opt  = array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => TRUE,
            );
            $dsn = 'mysql:host=' . $db_conf["DB_HOST"] . ';dbname=' . $db_conf["DB_NAME"];
            try{
                self::$instance = new PDO($dsn, $db_conf["DB_USER"], $db_conf["DB_PASSWORD"], $opt);
                self::$instance -> exec("set names utf8");
                self::$connection = true;
            } catch(PDOException $e){
                self::Error($e -> getMessage());
            }
        }
        return self::$instance;
    }


    /**
     * @param $sql
     * @param array $args
     *
     * @return bool|PDOStatement
     * @throws Exception
     * =========================
     * @name выполнение запросов
     */
    public static function run($sql, $args = array()){
        try{

            if(!is_array($args)){
                throw New Exception("Параметр \$args ($args) не является массивом | line: " . __LINE__ . " in " . __FILE__);
            } else{

                if(!empty(CONFIG_SYSTEM["db_log"])) $query_start = microtime(true);
                self::$queries[] = $sql; // add sql request
                self::$countSql++; // add +1 for counter
                $stmt = self::instance() -> prepare($sql);
                $stmt -> execute($args);

                if(!empty(CONFIG_SYSTEM["db_log"])){
                    $result_query = round(microtime(true) - intval($query_start), 2);
                }

                return $stmt;
            }

        } catch(PDOException $e){
            self::Error($e -> getMessage());
        }
    }




    /**
     * @return bool|string
     * ========================
     * @name get last insert id
     */
    public static function lastInsertId(){
        $lastId = self::instance()->lastInsertId();
        return (ctype_digit($lastId) && $lastId != 0) ? $lastId : false;
    }


    /**
     * @param $errors
     * ==============================
     * @name Записываем ошибки в файл
     */
    static function Error($errors){

        $file_info = debug_backtrace();

        $relPath_1 = strstr($file_info[1]['file'], "app\\");
        $relPath_2 = strstr($file_info[2]['file'], "app\\");
        $relPath_3 = strstr($file_info[3]['file'], "app\\");
        $relPath_4 = strstr($file_info[4]['file'], "app\\");
        $relPath_5 = strstr($file_info[5]['file'], "app\\");

        $content = date("d.m.Y H:i:s", time()) . " | " . $errors . '<br><span class="file_log">1: '.$relPath_1.' &#10148; '.$file_info[1]['line'].'</span><br>' . '<span class="file_log">2: '.$relPath_2.' &#10148; '.$file_info[2]['line'].'</span>';

        if($relPath_3)
            $content .= '<br>' . '<span class="file_log">3: '.$relPath_3.' &#10148; '.$file_info[3]['line'].'</span>';

        if($relPath_4)
            $content .= '<br>' . '<span class="file_log">4: '.$relPath_4.' &#10148; '.$file_info[4]['line'].'</span>';

        if($relPath_5)
            $content .= '<br>' . '<span class="file_log">5: '.$relPath_5.' &#10148; '.$file_info[5]['line'].'</span>';

        $content .= PHP_EOL.PHP_EOL;

        self::$countErrors++;
        self::$errors[] = $relPath_2.' &#10148; '.$file_info[2]['line'];

        $file =  ROOT . "/app/core/tmp/db_errors.txt";
        if(file_exists($file)){ // если файл существует
            if(filesize($file) > 5242880){ // если файл больше N кб (5 Мб)
                $fp = fopen($file, "w");
                fwrite($fp, $content);
            }
        }
        $fp = fopen($file, "a");
        fwrite($fp, $content);
        fclose($fp);
    }

    function __destruct(){
        self::$instance = null;
    }



    public static function log(){

        return json_decode(json_encode([
            "connection" => self::$connection,
            "countQuery" => self::$countSql,
            "queries" => self::$queries,
            "countErrors" => self::$countErrors,
            "errors" => self::$errors
        ]));
    }

}