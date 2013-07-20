<?php

/** INDEX */
$app->get('/route', $authenticate($app), function() use ($app){
    $user = current_user();
   $routes['routes'] =  $user->routes;
    $app->render('route/index.html', $routes);
});

/** SHOW */
$app->get('/route/(:id)', function($id) use ($app){
   $route['route'] = Route::find_by_id($id);
   $route['link'] = 'shareroute/'.$route['route']->identifier;
   $route['imagens'] = $route['route']->route_pictures;
   $app->render('route/show.html', $route);
});


/** FIND ROUTE by SHAREURL */
$app->get('/shareroute/:term', function($term) use ($app){
    $route['route'] = Route::find_by_identifier($term);
    if($route['route']){
        $app->redirect(get_root_url().'route/'.$route['route']->id);
    }else{
        echo 'A rota que você está procurando não existe !';   
    }
});

/** CREATE */
$app->post('/route', $authenticate($app), function () use ($app) {
    $user = current_user();
    $route = new Route();
    $imagem = new RoutePicture();
    $last_route = Route::last();
    $last_id = $last_route->id;
    $route->name  = $app->request()->post('nome');
    $route->identifier = $app->request()->post('identificador');
    $route->route_path = $app->request()->post('route_path');
    $route->description = $app->request()->post('descricao');
    $route->visibility = $app->request()->post('exibicao');
    $route->users_id = $user->id;

    if($route->save()){

        $pasta = "uploads_rotas/";

        foreach ($_FILES["img"]["error"] as $key => $error) {
            if($error == UPLOAD_ERR_OK){
                $tmp_name = $_FILES["img"]["tmp_name"][$key];
                $name = $_FILES["img"]["name"][$key];
                $uploadfile = $pasta . basename($name);

                if(move_uploaded_file($tmp_name, $uploadfile)){
                    $imagem->routes_id = $last_id+1;
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
        $app->redirect(get_root_url().'route');
    }else{
        $app->render('route/new.html', 'erro no insert');
    }
});

/** DELETE */
$app->get('/route/delete/(:id)', function($id) use ($app) {
 $route = Route::find_by_id($id);
 $route->delete();
 $app->redirect(get_root_url().'route');
});

/** NEW */
$app->get('/route/new/', $authenticate($app), function () use ($app) {
    $dados_requisicao['action'] = get_root_url().'route';
    $dados_requisicao['acao'] = "cadastrar";
    $app->render('route/new.html', $dados_requisicao);
});

/** EDIT */
$app->get('/route/edit/(:id)', $authenticate($app), function ($id) use ($app) {
    $dados_requisicao['route'] = Route::find_by_id($id);
    $dados_requisicao['action'] = get_root_url().'route/update/'.$id;
    $dados_requisicao['acao'] = "editar";
    $app->render('route/edit.html', $dados_requisicao);
});

/** UPDATE */
$app->put('/route/update/(:id)', function ($id) use ($app) {
    $route = Route::find_by_id($id);
    $imagem = new RoutePicture();
    $route->name  = $app->request()->post('nome');
    $route->identifier = $app->request()->post('identificador');
    $route->route_path = $app->request()->post('route_path');
    $route->description = $app->request()->post('descricao');
    $route->visibility = $app->request()->post('exibicao');
    $dados_requisicao['route'] = $route;
    $dados_requisicao['action'] = get_root_url().'route/update/'.$id;
    $dados_requisicao['acao'] = "editar";

    $pasta = "uploads_rotas/";

    foreach ($_FILES["img"]["error"] as $key => $error) {
        if($error == UPLOAD_ERR_OK){
            $tmp_name = $_FILES["img"]["tmp_name"][$key];
            $name = $_FILES["img"]["name"][$key];
            $uploadfile = $pasta . basename($name);

            if(move_uploaded_file($tmp_name, $uploadfile)){
                $imagem->routes_id = $Route->id;
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

    if($route->save()){
        $app->redirect(get_root_url().'route/'.$id);
    }else{
        $app->render('route/edit.html', $dados_requisicao);
    }
});


/** AUTOCOMPLETE Route (SET ROUTE) */ 
$app->get('/autocomplete_route/(:id)', function ($id) use ($app) {

    $app->response()->header('Content-Type', 'application/json;charset=utf-8');

    $route['route'] = Route::find_by_id($id);

    echo json_encode($route['route']->attributes());
});