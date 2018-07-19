<?php

namespace MyFw;


use MyFw\exceptions\ControllerException;

class ControllerFactory
{
    /**
     * Retorna a instancia de um controller
     *
     * @param string $controllerName
     * @return mixed
     */
    public static function build(string $controllerName)
    {
        $controller = "\App\controllers\\{$controllerName}";
        if (!file_exists(__DIR__ . "/../../app/controllers/{$controllerName}.php")) {
            throw new ControllerException("O controller $controllerName não foi encontrado");
        }

        return new $controller();
    }
}