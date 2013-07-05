<?php

/** FRIENDS */
$app->get('/user/friends', function() use ($app){
   $users['users'] = User::find('all');
    $app->render('user/index.html', $users);
});

/** VIEW PROFILE */
$app->get('/user/(:id)', function($id) use ($app){
   $user['user'] = User::find_by_id($id);

   $user['imagens'] = $user['user']->user_pictures;
   $app->render('user/show_profile.html', $user);
});

/** CREATE */
$app->post('/user', function () use ($app) {
    $user = new User();
    $user->name  = $app->request()->post('nome');
    $user->email = $app->request()->post('email');
    $user->city = $app->request()->post('cidade');
    $user->password = $app->request()->post('senha');
   
    if($user->save()){
        $app->redirect(get_root_url().'user/'.User::last()->id);
    }else{
        $app->render('/login', 'erro no insert');
    }
});

/** NEW */
$app->get('/user/new/', function () use ($app) {
    $dados_requisicao['action'] = get_root_url().'user';
    $dados_requisicao['acao'] = "cadastrar";
    $app->render('user/new.html', $dados_requisicao);
});

/** EDIT */
$app->get('/user/edit/(:id)', function ($id) use ($app) {
    $dados_requisicao['user'] = User::find_by_id($id);
    $dados_requisicao['action'] = get_root_url().'user/update/'.$id;
    $dados_requisicao['acao'] = "editar";
    $app->render('user/edit.html', $dados_requisicao);
});

/** UPDATE PROFILE */
$app->put('/user/update/(:id)', function ($id) use ($app) {
    $user = User::find_by_id($id);
    $imagem = new UserPicture();
    $user->name  = $app->request()->post('nome');
    $user->phone = $app->request()->post('telefone');
    $user->address = $app->request()->post('endereco');
    $user->city = $app->request()->post('cidade');
    $user->area = $app->request()->post('bairro');
    $user->uf = $app->request()->post('uf');
    $user->country = $app->request()->post('pais');
    $user->site = $app->request()->post('site');
    $user->aboutme = $app->request()->post('aboutme');
    $user->type = $app->request()->post('tipo');

    $dados_requisicao['user'] = $user;
    $dados_requisicao['action'] = get_root_url().'user/update/'.$id;
    $dados_requisicao['acao'] = "editar";

    if($user->save()){

        $pasta = "uploads_users/";

        foreach ($_FILES["img"]["error"] as $key => $error) {
            if($error == UPLOAD_ERR_OK){
                $tmp_name = $_FILES["img"]["tmp_name"][$key];
                $name = $_FILES["img"]["name"][$key];
                $uploadfile = $pasta . basename($name);

                if(move_uploaded_file($tmp_name, $uploadfile)){
                    $imagem->users_id = $user->id;
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
        $app->redirect(get_root_url().'user/'.$id);
    }else{
        $app->render('user/edit_profile.html', $dados_requisicao);
    }
});


/** FORM User */
$app->get('/form_user', function () use ($app) {

    $app->render('user/form_user.html');
});