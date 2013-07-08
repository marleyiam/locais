<?php

require 'Slim/Slim.php';
require 'vendor/php-activerecord/ActiveRecord.php';
require 'Twig/lib/Twig/Autoloader.php'; 
require 'functions/functions.php';
require 'functions/Inflect.php';

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


/** INDEX */
$app->get('/local', function() use ($app){
    //$session['user_id']
    $user = User::find_by_id(3);
    $locals['locals'] = $user->locals;

    //$locals['locals'] = Local::find('all');
    $app->render('local/index.html', $locals);
});

/** SHOW */
$app->get('/local/(:id)', function($id) use ($app){
   $local['local'] = Local::find_by_id($id);

   $local['imagens'] = $local['local']->local_pictures;
   $app->render('local/show.html', $local);
});

/** CREATE */
$app->post('/local', function () use ($app) {
    $local = new Local();
    $imagem = new LocalPicture();
    $last_local = Local::last();
    $last_id = $last_local->id;
    $local->name  = $app->request()->post('nome');
    $local->identifier = $app->request()->post('identificador');
    $local->address = $app->request()->post('endereco');
    $local->city = $app->request()->post('cidade');
    $local->lat = $app->request()->post('lat');
    $local->lng = $app->request()->post('lng');
    $local->description = $app->request()->post('descricao');
    $local->visibility = $app->request()->post('exibicao');
    $local->clone = $app->request()->post('clone');
    
    if($local->save()){

        $pasta = "uploads_locais/";

        foreach ($_FILES["img"]["error"] as $key => $error) {
            if($error == UPLOAD_ERR_OK){
                $tmp_name = $_FILES["img"]["tmp_name"][$key];
                $name = $_FILES["img"]["name"][$key];
                $uploadfile = $pasta . basename($name);

                if(move_uploaded_file($tmp_name, $uploadfile)){
                    $imagem->locals_id = $last_id+1;
                    $imagem->name = $name;
                    
                    echo "Rolou :" . $name . " e tal</br>";
                    chmod($uploadfile,0777);
                    if($imagem->save()){
                        echo 'salvo';
                    }else{
                        echo 'nao salvo';
                    }
                }else{
                    echo "Não Rolou :" . $name . " e pah</br>";
                }
            }
        }
        $app->redirect(get_root_url().'local');
    }else{
        $app->render('local/new.html', 'erro no insert');
    }
});

/** DELETE */
$app->get('/local/delete/(:id)', function($id) use ($app) {
 $local = Local::find_by_id($id);
 $local->delete();
 $app->redirect(get_root_url().'local');
});

/** NEW */
$app->get('/local/new/', function () use ($app) {
    $dados_requisicao['action'] = get_root_url().'local';
    $dados_requisicao['acao'] = "cadastrar";
    $app->render('local/new.html', $dados_requisicao);
});

/** EDIT */
$app->get('/local/edit/(:id)', function ($id) use ($app) {
    $dados_requisicao['local'] = Local::find_by_id($id);
    $dados_requisicao['action'] = get_root_url().'local/update/'.$id;
    $dados_requisicao['acao'] = "editar";
    $app->render('local/edit.html', $dados_requisicao);
});

/** UPDATE */
$app->put('/local/update/(:id)', function ($id) use ($app) {
    $local = Local::find_by_id($id);
    $imagem = new LocalPicture();
    $local->name  = $app->request()->put('nome');
    $local->identifier = $app->request()->put('identificador');
    $local->address = $app->request()->put('endereco');
    $local->city = $app->request()->put('cidade');
    $local->lat = $app->request()->put('lat');
    $local->lng = $app->request()->put('lng');
    $local->description = $app->request()->put('descricao');
    $local->visibility = $app->request()->put('exibicao');
    $local->clone = $app->request()->put('clone');
    $dados_requisicao['local'] = $local;
    $dados_requisicao['action'] = get_root_url().'local/update/'.$id;
    $dados_requisicao['acao'] = "editar";

    $pasta = "uploads_locais/";

    foreach ($_FILES["img"]["error"] as $key => $error) {
        if($error == UPLOAD_ERR_OK){
            $tmp_name = $_FILES["img"]["tmp_name"][$key];
            $name = $_FILES["img"]["name"][$key];
            $uploadfile = $pasta . basename($name);

            if(move_uploaded_file($tmp_name, $uploadfile)){
                $imagem->locals_id = $local->id;
                $imagem->name = $name;
                
                echo "Rolou :" . $name . " e tal</br>";
                chmod($uploadfile,0777);
                if($imagem->save()){
                    echo 'salvo';
                }else{
                    echo 'nao salvo';
                }
            }else{
                echo "Não Rolou :" . $name . " e pah</br>";
            }
        }
    }

    if($local->save()){
        $app->redirect(get_root_url().'local/'.$id);
    }else{
        $app->render('local/edit.html', $dados_requisicao);
    }
});

/** SETLOCAL */
$app->get('/local/setlocal/(:id)', function ($id) use ($app) {
    $local = Local::find_by_id($id);
    $coordenadas['lat'] = $local->lat;
    $coordenadas['lng'] = $local->lng;
    $app->response()->header('Content-Type', 'application/json;charset=utf-8');
    echo json_encode($coordenadas);
});


/** rota ROOT */
$app->get('/', function () use ($app){
       echo '<a href="local">Lista de Locais</a>';
       echo '</br>';
       echo '<a href="form_rota">Form Rota</a>';
       echo '</br>';
       echo '<a href="route">Lista de Rotas</a>';
       echo '</br>';
       echo '<a href="login">Login</a>';
});

/** AUTOCOMPLETE LOCAL */
$app->get('/autocomplete', function () use ($app) {

    $app->response()->header('Content-Type', 'application/json;charset=utf-8');
    //$local = Local::find_by_identifier($identifier);
    $res = array();
    $local['local'] = Local::find('all');
    /*foreach ($local['local'] as $key => $value) {
        $res[$key] = $value->to_json(
                array('only' => array('id', 'name', 'identifier', 'address', 'city', 'lat', 'lng','description','clone'))
            );
    }*/
    foreach ($local['local'] as $key => $value) {
        $res[$key] = $value->attributes();
    }
    echo json_encode($res);
});

/** AUTOCOMPLETE SEARCH LOCAL*/
$app->get('/search_locals', function () use ($app) {

    $app->response()->header('Content-Type', 'application/json;charset=utf-8');

    $arrterm = $app->request()->params();
    $term = $arrterm["term"];

    $res = array();
    $ress = array();
    $locals['locals'] = Local::find('all', array('conditions' => array("name LIKE ?
    OR identifier LIKE ? OR address LIKE ?
    OR city LIKE ?", "%".$term."%","%".$term."%","%".$term."%","%".$term."%")));
    $locals['results'] = count($locals['locals']);
    echo $locals['results'];
    printer($locals['locals']);
});

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


$app->map('/login', function () use ($app) {

            // Test for Post & make a cheap security check, to get avoid from bots
    if($app->request()->isPost() && sizeof($app->request()->post()) > 2)
    {
        // Don't forget to set the correct attributes in your form (name="user" + name="password")
        $post = (object)$app->request()->post();

        if(isset($post->user) && isset($post->passwort))
        {
            $app->setEncryptedCookie('my_cookie',$post->password);
            $app->redirect('profile/index.html');
        } 
        else
        {
            $app->redirect('/login');
        }
    }
            // render login
    $app->render('login/login.html');

})->via('GET','POST')->name('/login');



$authAdmin = function($role = 'member'){

    return function () use ($role){

        $app = Slim::getInstance('my_cookie');

        // Check for password in the cookie
        if($app->getEncryptedCookie('my_cookie',false) != 'YOUR_PASSWORD'){

            $app->redirect('/login');
        }
    };
};

$app->get('/admin', $authAdmin('admin'), function () use ($app) {

    $app->render('default.tpl', array
       (
           'siteTitle'   => $app->siteTitle,
           'pageTitle'   => 'Admin Control Panel',
           'mainTitle'   => 'Admin Control Panel',
           'subTemplate' => 'pages/admin.tpl'
       )
    );

})->name('admin');


require 'routes.php';
require 'users.php';
/** RUN APP */
$app->run();