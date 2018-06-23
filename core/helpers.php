<?php

function dd($dump): void
{
    var_dump($dump);
    die();
}

function view(string $view, array $data = []): void
{
    (new \Core\View())->render($view, $data);
}