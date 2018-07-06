<?php
/**
 * Created by PhpStorm.
 * User: diego
 * Date: 06/07/18
 * Time: 01:23
 */

namespace MyFw;


class Config
{
    private static function loadConfigs()
    {
        $configs = require __DIR__ . "/../../app/config.php";
        return $configs;
    }

    public static function Database()
    {
        $configs = self::loadConfigs();
        return $configs['database'];
    }
}