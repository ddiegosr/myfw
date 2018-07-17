<?php
/**
 * Created by PhpStorm.
 * User: diego
 * Date: 02/07/18
 * Time: 20:39
 */

namespace MyFw;


use PDO;
use PDOException;

class Database
{
    private static $instance = null;

    public static function getConn()
    {
        return self::connect();
    }

    private static function connect()
    {
        $configs = Config::Database();
        $options = [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$configs['charset']}"];
        $dsn = "mysql:host={$configs['host']};dbname={$configs['database']}";
        try {
            self::$instance = new PDO($dsn, $configs['username'], $configs['password'], $options);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die;
        }

        return self::$instance;
    }

}