<?php

require 'Slim/Slim.php';
require 'vendor/php-activerecord/ActiveRecord.php';
require 'Twig/lib/Twig/Autoloader.php'; 
require 'Twig/lib/Twig/Environment.php'; 
require 'functions/functions.php';
require 'functions/Inflect.php';
require 'vendor/facebook/facebook.php';

date_default_timezone_set("America/Fortaleza");

$loader = Twig_Autoloader::register();
\Slim\Slim::registerAutoloader();
ActiveRecord\Config::initialize(function($cfg) {
    $cfg->set_model_directory('models');
    $cfg->set_connections(array(
        'development' => 'compute-1.amazonaws.com/d2ups3m8cpgdva'
    ));
});
//
#$app = new \Slim\Slim();
//Diz pro Slim usar os templates do Twig \o/
$app = new \Slim\Slim(array( 'view' => new \Slim\Extras\Views\Twig()));
$app->add(new \Slim\Middleware\SessionCookie(array('secret' => 'myappsecret')));
/*
$twig = new Twig_Environment($loader, array(
        'autoescape' => false,
    ));

$twig->addFilter('var_dump', new Twig_Filter_Function('var_dump'));
*/
 /*definicoes do FB APP*/
define('FACEBOOK_APP_ID',"");
define('FACEBOOK_SECRET',"");
define('REDIRECT_URI',"http://nameless-river-5051.heroku.app/locais_fotos");



//$app->notFound('ErrorFunction404');

           // $app->setEncryptedCookie('my_cookie',$post->password);
           // $app->redirect('profile/index.html');


/*
$authAdmin = function($role = 'member'){

    return function () use ($role){

        $app = Slim::getInstance('my_cookie');

        // Check for password in the cookie
        if($app->getEncryptedCookie('my_cookie',false) != 'YOUR_PASSWORD'){

            $app->redirect('/login');
        }
    };
};
*/

require 'auth.php';
require 'locals.php';
require 'realties.php';
require 'routes.php';
require 'users.php';
require 'friends.php';
require 'albums.php';
require 'favorites.php';

/** RUN APP */
$app->run();
