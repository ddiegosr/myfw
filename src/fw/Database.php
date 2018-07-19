<?php

namespace MyFw;


use MyFw\exceptions\DatabaseException;
use PDO;
use PDOException;

class Database
{
    private static $instance = null;

    /**
     * Retorna uma instância da classe PDO
     * no padrão Singleton
     *
     * @return PDO
     * @throws DatabaseException
     */
    public static function getConn(): PDO
    {
        return self::connect();
    }

    /**
     * Conecta ao banco de dados com padrão Singleton
     *
     * @return PDO
     * @throws DatabaseException
     */
    private static function connect(): PDO
    {
        $configs = Config::Database();
        $options = [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$configs['charset']}"];
        $dsn = "mysql:host={$configs['host']};dbname={$configs['database']}";
        try {
            self::$instance = new PDO($dsn, $configs['username'], $configs['password'], $options);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        return self::$instance;
    }

    /**
     * Previne que a classe seja instância
     * com o operador new de fora dessa classe
     */
    private function __construct()
    {
    }

    /**
     * Previne a clonagem dessa instância da classe
     */
    private function __clone()
    {
    }

    /**
     * Previne a desserialização da instância dessa classe
     */
    private function __wakeup()
    {
    }

}