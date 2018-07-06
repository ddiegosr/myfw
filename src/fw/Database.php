<?php
/**
 * Created by PhpStorm.
 * User: diego
 * Date: 02/07/18
 * Time: 20:39
 */

namespace MyFw;


use PDO;

class Database
{
    private static $instance;

    public static function conn(){
        $configs = Config::Database();
        if (self::$instance == null){
            $options = [ PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$configs['charset']}"];
            $dsn = "mysql:host={$configs['host']};dbname={$configs['database']}";
            $pdo = new PDO($dsn, $configs['username'], $configs['password'], $options);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            self::$instance = $pdo;
        }

        return self::$instance;
    }
}