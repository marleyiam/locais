<?php

require 'Slim/Slim.php';
require 'vendor/php-activerecord/ActiveRecord.php';
require 'Twig/lib/Twig/Autoloader.php'; 
require 'Twig/lib/Twig/Environment.php'; 
require 'functions/functions.php';

$loader = Twig_Autoloader::register();
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
$app->add(new \Slim\Middleware\SessionCookie(array('secret' => 'myappsecret')));
/*
$twig = new Twig_Environment($loader, array(
        'autoescape' => false,
    ));

$twig->addFilter('var_dump', new Twig_Filter_Function('var_dump'));
*/
$authenticate = function ($app) {
    return function () use ($app) {
        if (!isset($_SESSION['user_id'])) {
            $app->flash('error', 'Login required');
            $app->redirect(get_root_url().'login');
        }
    };
};

$app->hook('slim.before.dispatch', function() use ($app) { 
   $user = null;
   if (isset($_SESSION['user_id'])) {
      $user_id = $_SESSION['user_id'];
      $user = User::find_by_id($user_id);
   }
   $app->view()->setData('user', $user);
});

$app->get('/logout', function () use ($app) {
    unset($_SESSION['user_id']);
    $app->view()->setData('user', null);
    $app->render('login/login.html');
});

/** INDEX */ 
$app->get('/local', $authenticate($app), function() use ($app){
    $user = current_user();
    $locals['locals'] = $user->locals;
    $app->render('local/index.html', $locals);
});

/** SHOW */
$app->get('/local/(:id)', function($id) use ($app){
   $local['local'] = Local::find_by_id($id);

   $local['imagens'] = $local['local']->local_pictures;
   $app->render('local/show.html', $local);
});

/** CREATE */
$app->post('/local', $authenticate($app) , function () use ($app) {
    $user = current_user();
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
    $local->users_id = $user->id;
    
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
        //$app->redirect(get_root_url().'user');
        $app->redirect(get_root_url().'local/'.$id);
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
$app->get('/local/new/', $authenticate($app), function () use ($app) {
    $dados_requisicao['action'] = get_root_url().'local';
    $dados_requisicao['acao'] = "cadastrar";
    $app->render('local/new.html', $dados_requisicao);
});

/** EDIT */
$app->get('/local/edit/(:id)', $authenticate($app), function ($id) use ($app) {
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

    $arrterm = $app->request()->params();
    $term = $arrterm["term"];
    $users = array();
    $images = array();

    //$join = 'LEFT JOIN users as u ON locals.users_id = u.id';
    $locals['locals'] = Local::find('all', 
    array('conditions' => array("name LIKE ?
    OR identifier LIKE ? OR address LIKE ?
    OR city LIKE ?", "%".$term."%","%".$term."%","%".$term."%","%".$term."%")));
    //$locals['locals'] =  Local::all(array('joins' => array('users')));
    //$locals['locals'] =  Local::all(array('joins' => $join));
    foreach ($locals['locals'] as $key => $value) {
        $users[$key] = $value->users;
        $images[$key] = $value->local_pictures;
       
    }

    $locals['images'] = $images;
    $locals['users'] = $users;
    $locals['results'] = count($locals['locals']);
    //printer($images);exit;
    $app->render('user/search_for_locals.html', $locals);
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

require 'routes.php';
require 'users.php';
/** RUN APP */
$app->run();