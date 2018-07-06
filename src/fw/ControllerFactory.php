<?php

namespace MyFw;


class ControllerFactory
{
    /**
     * Retorna a instancia de um controller
     *
     * @param string $controllerName
     * @return mixed
     * @throws \Exception
     */
    public static function build(string $controllerName)
    {
        $controller = "\App\controllers\\{$controllerName}";
        if (!file_exists(__DIR__ . "/../app/controllers/{$controllerName}.php")) {
            throw new \Exception("O controller $controllerName não foi encontrado");
        }

        return new $controller();
    }
}