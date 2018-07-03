<?php
/**
 * Created by PhpStorm.
 * User: diego
 * Date: 02/07/18
 * Time: 20:39
 */

namespace Core;


use PDO;

class DB
{
    private static $instance;

    public static function conn(){
        if (self::$instance == null){
            $options = [ PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'];
            $pdo = new PDO("mysql:host=localhost;dbname=myfw", "local", "root", $options);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            self::$instance = $pdo;
        }

        return self::$instance;
    }
}