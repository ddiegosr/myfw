<?php

$app = new \Core\Router();

$app->get('/', function (){
    echo "Página Inicial";
});