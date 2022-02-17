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

        $content = date("d.m.Y H:i:s", time()) . " | " . $errors . '<br><span class="file_log_1">[1: '.$file_info[1]['file'].' -> '.$file_info[1]['line'].']</span><br>' . '<span class="file_log_2">[2: '.$file_info[2]['file'].' -> on line: '.$file_info[2]['line'].']</span>' . PHP_EOL.PHP_EOL;

        self::$countErrors++;
        self::$errors[] = $file_info[2]['file'].' -> on line: '.$file_info[2]['line'];

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