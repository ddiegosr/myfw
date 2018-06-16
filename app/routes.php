<?php

$app = new \Core\Router();

$app->get('/', function (){
    (new \Core\View())->render('home');
});