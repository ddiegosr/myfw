<?php

/**
 * Helper para debug.
 * Realiza um var_dump() no parametro $dump
 * e logo executa um die() para parar a aplicação naquele ponto
 *
 * @param $dump
 */
function dd($dump): void
{
    var_dump($dump);
    die();
}

/**
 * Renderiza uma view
 *
 * @param string $view
 * @param array $data
 * @throws ErrorException
 */
function view(string $view, array $data = []): void
{
    $viewObject = \MyFw\View::getInstance();
    $viewObject->render($view, $data);
}

/**
 * Registra uma função personalizada do usuário
 * para a Template Engine
 *
 * @param callable $functionName
 */
function view_register_function(Callable $functionName): void
{
    $viewObject = \MyFw\View::getInstance();
    $viewObject->registerFunction($functionName);
}

/**
 * Recupera variaveis do arquivo .env
 *
 * @param string $envName
 * @param string $default
 * @return string
 */
function env(string $envName, string $default): string
{
    $variables = [];
    $dotenv = new \Dotenv\Dotenv(__DIR__ . '/../../');

    foreach ($dotenv->load() as $var) {
        $pieces = explode('=', $var);
        $variables[$pieces[0]] = $pieces[1];
    }

    if (array_key_exists($envName, $variables)) {
        return $variables[$envName];
    }

    return $default;
}