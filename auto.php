<?php
/*
require 'Slim/Slim.php';
require 'vendor/php-activerecord/ActiveRecord.php';
require 'Twig/lib/Twig/Autoloader.php'; 
require 'functions/functions.php';

Twig_Autoloader::register();
\Slim\Slim::registerAutoloader();
ActiveRecord\Config::initialize(function($cfg) {
    $cfg->set_model_directory('models');
    $cfg->set_connections(array(
        'development' => 'pgsql://postgres:150679@localhost/locals'
    ));
});

#$app = new \Slim\Slim();
//Diz pro Slim usar os templates do Twig \o/
$app = new \Slim\Slim(array( 'view' => new \Slim\Extras\Views\Twig()));


$app->get('/auto', function() use ($app) {
    echo 'auto';
});

$app->run();*/