<?php

/** INDEX */
$app->get('/route', function() use ($app){
   $routes['routes'] = Route::find('all');
    $app->render('route/index.html', $routes);
});

/** SHOW */
$app->get('/route/(:id)', function($id) use ($app){
   $route['route'] = Route::find_by_id($id);

   $route['imagens'] = $route['route']->route_pictures;
   $app->render('route/show.html', $route);
});

/** CREATE */
$app->post('/route', function () use ($app) {
    $route = new Route();
    $imagem = new RoutePicture();
    $last_route = Route::last();
    $last_id = $last_route->id;
    $route->name  = $app->request()->post('nome');
    $route->identifier = $app->request()->post('identificador');
    $route->route_path = $app->request()->post('route_path');
    $route->description = $app->request()->post('descricao');
    $route->visibility = $app->request()->post('exibicao');

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
$app->get('/route/new/', function () use ($app) {
    $dados_requisicao['action'] = get_root_url().'route';
    $dados_requisicao['acao'] = "cadastrar";
    $app->render('route/new.html', $dados_requisicao);
});

/** EDIT */
$app->get('/route/edit/(:id)', function ($id) use ($app) {
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

/** SETROTA */
$app->get('/route/setrota/(:id)', function ($id) use ($app) {
    $route = Route::find_by_id($id);
    $coordenadas['lat'] = $route->lat;
    $coordenadas['lng'] = $route->lng;
    $app->response()->header('Content-Type', 'application/json;charset=utf-8');
    echo json_encode($coordenadas);
});


/** AUTOCOMPLETE Route */
$app->get('/autocomplete_route/(:id)', function ($id) use ($app) {

    $app->response()->header('Content-Type', 'application/json;charset=utf-8');

    $route['route'] = Route::find_by_id($id);

    echo json_encode($route['route']->attributes());
});

/** FORM Route */
$app->get('/form_route', function () use ($app) {

    $app->render('route/form_route.html');
});