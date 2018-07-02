<?php

function dd($dump): void
{
    var_dump($dump);
    die();
}

function view(string $view, array $data = []): void
{
    $viewObject = \Core\View::getInstance();
    $viewObject->render($view, $data);
}

function view_register_function(Callable $functionName): void{
    $viewObject = \Core\View::getInstance();
    $viewObject->registerFunction($functionName);
}