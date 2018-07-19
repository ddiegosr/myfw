<?php

function dd($dump): void
{
    var_dump($dump);
    die();
}

function view(string $view, array $data = []): void
{
    $viewObject = \MyFw\View::getInstance();
    $viewObject->render($view, $data);
}

function view_register_function(Callable $functionName): void
{
    $viewObject = \MyFw\View::getInstance();
    $viewObject->registerFunction($functionName);
}

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