<?php


$run = new Whoops\Run();
$handler = new \Whoops\Handler\PrettyPageHandler();

$run->pushHandler($handler);
$run->register();

require_once __DIR__ . "/../../app/routes.php";
$app->run();
