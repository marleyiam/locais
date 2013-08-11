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
        'development' => 'pgsql://vxfhhbrgubvffj:aZDz158BPmc7ph_jD29vOBK8tK@ec2-54-235-173-50.compute-1.amazonaws.com/d2ups3m8cpgdva'
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
define('FACEBOOK_APP_ID',"475085362501750");
define('FACEBOOK_SECRET',"d07dfae4e722f45fd92072069e4e0c59");
define('REDIRECT_URI',"http://localhost/locais_fotos");



//$app->notFound('ErrorFunction404');

/** AUTOCOMPLETE SEARCH LOCAL AJAX*/
/*$app->get('/search_locals', function () use ($app) {

    $app->response()->header('Content-Type', 'application/json;charset=utf-8');

    $arrterm = $app->request()->params();
    $term = $arrterm["term"];

    $res = array();
    $ress = array();
    $locals = Local::find('all', array('conditions' => array("name LIKE ?
    OR identifier LIKE ? OR address LIKE ?
    OR city LIKE ?", "%".$term."%","%".$term."%","%".$term."%","%".$term."%")));

    foreach ($locals as $key => $value) {
        $res[$key] = $value->attributes();
        $ress[$key] = $res[$key]["name"];   
    }

    echo json_encode($ress);
});*/



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
