<?php

require_once __DIR__ . "/../../app/routes.php";

$run = new Whoops\Run();
$handler = new \Whoops\Handler\PrettyPageHandler();

$run->pushHandler($handler);
$run->register();

$app->run();