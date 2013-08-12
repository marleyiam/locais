
<?php

/** INDEX */ 
$app->get('/realty', $authenticate($app), function() use ($app){
    $user = current_user();
    $realties['avatar'] = $user->user_pictures;
    $realties['realties'] = $user->realties;
    $app->render('realty/index.html', $realties);
});

/** SHOW */
$app->get('/realty/(:id)', function($id) use ($app){
    $user = current_user();
    $realty['avatar'] = $user->user_pictures;
    $realty['realty'] = Realty::find_by_id($id);
    $realty['link'] = 'sharerealty/'.$realty['realty']->identifier;
    $realty['imagens'] = $realty['realty']->realty_pictures;
    $app->render('realty/show.html', $realty);
});

/** FIND REALTY by SHAREURL */
$app->get('/sharerealty/:term', function($term) use ($app){
    $user = current_user();
    $realty['avatar'] = $user->user_pictures;
    $realty['realty'] = Realty::find_by_identifier($term);
    if($realty['realty']){
        $app->redirect(get_root_url().'realty/'.$realty['realty']->id);
    }else{
        echo 'O imóvel que você está procurando não existe !';   
    }
});

/** CREATE */
$app->post('/realty', $authenticate($app) , function () use ($app) {
    $user = current_user();
    $realty = new Realty();
    $imagem = new RealtyPicture();
    $last_local = Realty::last();
    $last_id = $last_local? $last_local->id : 0;
    $realty->name  = $app->request()->post('nome');
    $realty->identifier = $app->request()->post('identificador');
    $realty->address = $app->request()->post('endereco');
    $realty->city = $app->request()->post('cidade');
    $realty->lat = $app->request()->post('lat');
    $realty->lng = $app->request()->post('lng');
    $realty->description = $app->request()->post('descricao');
    $realty->visibility = $app->request()->post('exibicao');

    $realty->rooms = $app->request()->post('quartos');
    $realty->area = $app->request()->post('area');
    $realty->price = $app->request()->post('preco');
    $realty->trading = $app->request()->post('negocio');
    $realty->contacts = $app->request()->post('contatos');

    $realty->clone = $app->request()->post('clone');
    $realty->users_id = $user->id;
    
    if($realty->save()){

        $pasta = "uploads_realties/";

        foreach ($_FILES["img"]["error"] as $key => $error) {
            if($error == UPLOAD_ERR_OK){
                $tmp_name = $_FILES["img"]["tmp_name"][$key];
                $name = $_FILES["img"]["name"][$key];
                $uploadfile = $pasta . basename($name);

                if(move_uploaded_file($tmp_name, $uploadfile)){
                    $imagem->realties_id = $last_id+1;
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

        $app->redirect(get_root_url().'realty');
    }else{
        $app->render('realty/new.html', 'erro no insert');
    }
});

/** DELETE */
$app->get('/realty/delete/(:id)', function($id) use ($app) {
 $realty = Realty::find_by_id($id);
 $realty->delete();
 $app->redirect(get_root_url().'realty');
});

/** NEW */
$app->get('/realty/new/', $authenticate($app), function () use ($app) {
    $user = current_user();
    $dados_requisicao['avatar'] = $user->user_pictures;
    $dados_requisicao['action'] = get_root_url().'realty';
    $dados_requisicao['acao'] = "cadastrar";
    $app->render('realty/new.html', $dados_requisicao);
});

/** EDIT */
$app->get('/realty/edit/(:id)', $authenticate($app), function ($id) use ($app) {
    $user = current_user();
    $dados_requisicao['avatar'] = $user->user_pictures;
    $dados_requisicao['realty'] = Realty::find_by_id($id);
    $dados_requisicao['action'] = get_root_url().'realty/update/'.$id;
    $dados_requisicao['acao'] = "editar";
    $app->render('realty/edit.html', $dados_requisicao);
});

/** UPDATE */
$app->put('/realty/update/(:id)', function ($id) use ($app) {
    $realty = Realty::find_by_id($id);
    $imagem = new RealtyPicture();
    $realty->name  = $app->request()->put('nome');
    $realty->identifier = $app->request()->put('identificador');
    $realty->address = $app->request()->put('endereco');
    $realty->city = $app->request()->put('cidade');
    $realty->lat = $app->request()->put('lat');
    $realty->lng = $app->request()->put('lng');
    $realty->description = $app->request()->put('descricao');
    $realty->visibility = $app->request()->put('exibicao');

    $realty->rooms = $app->request()->post('quartos');
    $realty->area = $app->request()->post('area');
    $realty->price = $app->request()->post('preco');
    $realty->trading = $app->request()->post('negocio');
    $realty->contacts = $app->request()->post('contatos');

    $realty->clone = $app->request()->put('clone');
    $dados_requisicao['realty'] = $realty;
    $dados_requisicao['action'] = get_root_url().'realty/update/'.$id;
    $dados_requisicao['acao'] = "editar";

    $pasta = "uploads_realties/";

    foreach ($_FILES["img"]["error"] as $key => $error) {
        if($error == UPLOAD_ERR_OK){
            $tmp_name = $_FILES["img"]["tmp_name"][$key];
            $name = $_FILES["img"]["name"][$key];
            $uploadfile = $pasta . basename($name);

            if(move_uploaded_file($tmp_name, $uploadfile)){
                $imagem->realties_id = $realty->id;
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

    if($realty->save()){
        $app->redirect(get_root_url().'realty/'.$id);
    }else{
        $app->render('realty/edit.html', $dados_requisicao);
    }
});

/** SETLOCAL */
$app->get('/realty/setlocal/(:id)', function ($id) use ($app) {
    $app->response()->header('Content-Type', 'application/json;charset=utf-8');
    $realty = Realty::find_by_id($id);
    $coordenadas['lat'] = $realty->lat;
    $coordenadas['lng'] = $realty->lng;
    echo json_encode($coordenadas);
});

?>