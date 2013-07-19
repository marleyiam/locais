<?php

/** INDEX */
$app->get('/album', $authenticate($app), function() use ($app){
    $user = current_user();
    $albums['albums'] =  $user->albums;
    $app->render('album/index.html', $albums);
});

/** SHOW */
$app->get('/album/(:id)', function($id) use ($app){
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
 $album = Album::find_by_id($id);
 $album->delete();
 $app->redirect(get_root_url().'album');
});

/** NEW */
$app->get('/album/new/', $authenticate($app), function () use ($app) {
    $dados_requisicao['action'] = get_root_url().'album';
    $dados_requisicao['acao'] = "cadastrar";
    $dados_requisicao['locals'] = Local::find("all",array("conditions" => array("users_id = ?", current_user()->id)));
    $app->render('album/new.html', $dados_requisicao);
});

/** EDIT */
$app->get('/album/edit/(:id)', $authenticate($app), function ($id) use ($app) {
    $dados_requisicao['album'] = Album::find_by_id($id);
    $dados_requisicao['action'] = get_root_url().'album/update/'.$id;
    $dados_requisicao['acao'] = "editar";
    $dados_requisicao['locals'] = Local::find("all",array("conditions" => array("users_id = ?", current_user()->id)));
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

/** ADD lOCAL to ALBUM*/
$app->post('/add_local_to_album', function() use ($app){
    
    $arrterm = $app->request()->params();
    $local_id = $arrterm["local_id"];
    $album_id = $arrterm["album_id"];
    $local = Local::find_by_id($local_id);
     
    if($local->update_attribute ("albums_id" ,$album_id)){
        echo 'O local foi adicionado ao seu album !';
    }else{
        echo 'O local não pode ser adicionado ao seu album !';
    }
});

/** RMV lOCAL from ALBUM*/
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