<?php

$app = new \Core\Router();

$app->get('/', function (){
    view('welcome');
});