
<?php

/** INDEX */ 
$app->get('/local', $authenticate($app), function() use ($app){
    $user = current_user();
    $locals['avatar'] = $user->user_pictures;
    $locals['locals'] = $user->locals;
    $app->render('local/index.html', $locals);
});

/** SHOW */
$app->get('/local/(:id)', function($id) use ($app){
   $local['local'] = Local::find_by_id($id);
   $user = current_user();
   $local['avatar'] = $user->user_pictures;
   $local['imagens'] = $local['local']->local_pictures;
   $local['link'] = 'sharelocal/'.$local['local']->identifier;
   $app->render('local/show.html', $local);
});

/** FIND LOCAL by SHAREURL */
$app->get('/sharelocal/:term', function($term) use ($app){
    $local['local'] = Local::find_by_identifier($term);
    $user = current_user();
    $local['avatar'] = $user->user_pictures;
    if($local['local']){
        $app->redirect(get_root_url().'local/'.$local['local']->id);
    }else{
        echo 'O local que você está procurando não existe !';   
    }
});


/** CREATE */
$app->post('/local', $authenticate($app) , function () use ($app) {
    $user = current_user();
    $local = new Local();
    $imagem = new LocalPicture();
    $last_local = Local::last();
    $last_id = $last_local? $last_local->id : 0;
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

        //$app->redirect(get_root_url().'local');
        $app->redirect(get_root_url().'local');
    }else{
        $app->render('local/new.html', 'erro no insert');
    }
});

/** DELETE */
$app->get('/local/delete/(:id)', function($id) use ($app) {
    $local = Local::find_by_id($id);
    $success = false;
    try{
        $success = $local->delete();
    }catch(Exception $e){
    }
    if($success){
        echo 'Local excluído com sucesso !';
    }else{
        echo 'Para excluir o local você deve antes excluir todas as imagens associados à ele !';
    }
});

/** NEW */
$app->get('/local/new/', $authenticate($app), function () use ($app) {
    $user = current_user();
    $dados_requisicao['avatar'] = $user->user_pictures;
    $dados_requisicao['action'] = get_root_url().'local';
    $dados_requisicao['acao'] = "cadastrar";
    $app->render('local/new.html', $dados_requisicao);
});

/** EDIT */
$app->get('/local/edit/(:id)', $authenticate($app), function ($id) use ($app) {
    $user = current_user();
    $dados_requisicao['avatar'] = $user->user_pictures;
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
    $app->response()->header('Content-Type', 'application/json;charset=utf-8');
    $local = Local::find_by_id($id);
    $coordenadas['lat'] = $local->lat;
    $coordenadas['lng'] = $local->lng;
    echo json_encode($coordenadas);
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
$app->get('/search_for_locals', function () use ($app) {

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
    $app->render('user/search_for_locals.html', $locals);
});

?>