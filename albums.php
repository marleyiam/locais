<?php

/** INDEX */
$app->get('/album', $authenticate($app), function() use ($app){
    $user = current_user();
    $albums['avatar'] = $user->user_pictures;
    $albums['albums'] =  $user->albums;
    $app->render('album/index.html', $albums);
});

/** SHOW */
$app->get('/album/(:id)', function($id) use ($app){
    $user = current_user();
    $album['avatar'] = $user->user_pictures;
    $album['album'] = Album::find_by_id($id);

    $app->render('album/show.html', $album);
});

/** CREATE */
$app->post('/album', $authenticate($app), function () use ($app) {
    $user = current_user();
    $album = new Album();
    $album->users_id = $user->id;
    $album->album_name = $app->request()->post('nome');

    if($album->save()){

        $app->redirect(get_root_url().'album');
    }else{
        $app->render('album/new.html', 'erro no insert');
    }
});

/** DELETE */
$app->get('/album/delete/(:id)', function($id) use ($app) {
    $app->response()->header('Content-Type', 'application/json;charset=utf-8');
    
    $album = Album::find_by_id($id);
    $success = false;
    try{
       $success = $album->delete();
    }catch(Exception $e){
   //var_dump($e->getMessage());
    }
    if($success){
        echo json_encode(array('msg'=>'Album excluído com sucesso !','status'=>$success));
    }else{
        echo json_encode(array('msg'=>'Para excluir o album você deve antes excluir todos os registros associados à ele !','status'=>$success));
    }
});

/** NEW */
$app->get('/album/new/', $authenticate($app), function () use ($app) {
    $user = current_user();
    $dados_requisicao['avatar'] = $user->user_pictures;
    $dados_requisicao['action'] = get_root_url().'album';
    $dados_requisicao['acao'] = "cadastrar";
    $dados_requisicao['locals'] = Local::find("all",array("conditions" => array("users_id = ?", current_user()->id)));
    $app->render('album/new.html', $dados_requisicao);
});

/** EDIT */
$app->get('/album/edit/(:id)', $authenticate($app), function ($id) use ($app) {
    $user = current_user();
    $dados_requisicao['avatar'] = $user->user_pictures;
    $dados_requisicao['album'] = Album::find_by_id($id);
    $dados_requisicao['action'] = get_root_url().'album/update/'.$id;
    $dados_requisicao['acao'] = "editar";
    $dados_requisicao['locals'] = Local::find("all",array("conditions" => array("users_id = ?", current_user()->id)));

    $dados_requisicao['realties'] = Realty::find("all",array("conditions" => array("users_id = ?", current_user()->id)));

    $dados_requisicao['routes'] = Route::find("all",array("conditions" => array("users_id = ?", current_user()->id)));

    /*
    $aproved = Friend::find("all", array(
     "conditions" => array('aproved = ? AND id_b = ? OR aproved = ? AND id_a = ? ','TRUE',$user['user']->id,'TRUE',$user['user']->id)));

     $join = 'LEFT JOIN users as u ON locals.users_id = u.id';
     $locals['locals'] = Local::find('all', 
     array('conditions' => array("name LIKE ?
     OR identifier LIKE ? OR address LIKE ?
     OR city LIKE ?", "%".$term."%","%".$term."%","%".$term."%","%".$term."%")));
     $locals['locals'] =  Local::all(array('joins' => array('users')));
     $locals['locals'] =  Local::all(array('joins' => $join));
     
    */
    $app->render('album/edit.html', $dados_requisicao);
});

/** UPDATE */
$app->put('/album/update/(:id)', function ($id) use ($app) {
    $album = Album::find_by_id($id);
    $album->album_name  = $app->request()->post('nome');

    $dados_requisicao['album'] = $album;
    $dados_requisicao['action'] = get_root_url().'album/update/'.$id;
    $dados_requisicao['acao'] = "editar";

    if($album->save()){
        $app->redirect(get_root_url().'album/'.$id);
    }else{
        $app->render('album/edit.html', $dados_requisicao);
    }
});

/** ADD lOCAL TO ALBUM*/
$app->post('/add_local_to_album', function() use ($app){
    
    $arrterm = $app->request()->params();
    $local_id = $arrterm["local_id"];
    $album_id = $arrterm["album_id"];
    $local = Local::find_by_id($local_id);
     
    //if($local->albums_id == $album_id){
    //    echo 'O local já está associado à um album !';
    //}else{
        if($local->update_attribute ("albums_id" ,$album_id)){
            echo 'O local foi adicionado ao seu album !';
        }else{
            echo 'O local não pode ser adicionado ao seu album !';
        }    
   // }

});

/** RMV lOCAL FROM ALBUM*/
$app->post('/del_local_of_album', function() use ($app){

    $arrterm = $app->request()->params();
    $local_id = $arrterm["local_id"];
    $local = Local::find_by_id($local_id);
    
    if($local->update_attribute ("albums_id" ,NULL)){
        echo 'O local foi removido do seu album !';
    }else{
        echo 'O local não pode ser removido ao seu album !';
    }
});


/** ADD REALTY TO ALBUM*/
$app->post('/add_realty_to_album', function() use ($app){
    
    $arrterm = $app->request()->params();
    $realty_id = $arrterm["realty_id"];
    $album_id = $arrterm["album_id"];
    $realty = Realty::find_by_id($realty_id);
     
    if($realty->update_attribute ("albums_id" ,$album_id)){
        echo 'O imóvel foi adicionado ao seu album !';
    }else{
        echo 'O imóvel não pode ser adicionado ao seu album !';
    }
});

/** RMV REALTY FROM ALBUM*/
$app->post('/del_realty_of_album', function() use ($app){

    $arrterm = $app->request()->params();
    $realty_id = $arrterm["realty_id"];
    $realty = Realty::find_by_id($realty_id);
     
    if($realty->update_attribute ("albums_id" ,NULL)){
        echo 'O imóvel foi removido do seu album !';
    }else{
        echo 'O imóvel não pode ser removido ao seu album !';
    }
});


/** ADD ROUTE TO ALBUM*/
$app->post('/add_route_to_album', function() use ($app){
    
    $arrterm = $app->request()->params();
    $route_id = $arrterm["route_id"];
    $album_id = $arrterm["album_id"];
    $route = Route::find_by_id($route_id);
     
    if($route->update_attribute ("albums_id" ,$album_id)){
        echo 'A rota foi adicionada ao seu album !';
    }else{
        echo 'A rota não pode ser adicionada ao seu album !';
    }
});

/** RMV ROUTE FROM ALBUM*/
$app->post('/del_route_of_album', function() use ($app){

    $arrterm = $app->request()->params();
    $route_id = $arrterm["route_id"];
    $route = Route::find_by_id($route_id);
     
    if($route->update_attribute ("albums_id" ,NULL)){
        echo 'A rota foi removida do seu album !';
    }else{
        echo 'A rota não pode ser removida do seu album !';
    }
});