<?php

$app = new \MyFw\Router();

$app->get('/', function (){
    view('welcome');
});