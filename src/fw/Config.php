<?php

namespace MyFw;


class Config
{
    /**
     * Carrega o arquivo de configurações e retorna o array contido
     *
     * @return array
     */
    private static function loadConfigs(): array
    {
        $configs = require __DIR__ . "/../../app/config.php";
        return $configs;
    }

    /**
     * Retorna um array com os valores do indice de Database
     * presente no arquivo de configurações
     *
     * @return array
     */
    public static function Database(): array
    {
        $configs = self::loadConfigs();
        return $configs['database'];
    }
}