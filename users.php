<?php
/*
 public function set_password($plaintext) {
      $this->encrypted_password = md5($plaintext);
    }
     printer($user['user']->attributes());
*/

/** FRIENDS */
$app->get('/user/friends', function() use ($app){
   $users['users'] = User::find('all');
    $app->render('user/index.html', $users);
});


/** VIEW PROFILE */
$app->get('/user/(:id)', function($id) use ($app){
   $user['user'] = User::find_by_id($id);
   $user['locals'] = $user['user']->locals;
   $user['routes'] = $user['user']->routes;
   $user['imagens_routes'] = get_nested_relation($user['routes'],'route_pictures');
   $user['imagens_locals'] = get_nested_relation($user['locals'],'local_pictures');
   $user['avatar'] = $user['user']->user_pictures;

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

/** NEW *//*
$app->get('/user/new/', function () use ($app) {
    $dados_requisicao['action'] = get_root_url().'user';
    $dados_requisicao['acao'] = "cadastrar";
    $app->render('user/new.html', $dados_requisicao);
});
*/

/** EDIT PROFILE */
$app->get('/user/edit/(:id)', function ($id) use ($app) {
    $dados_requisicao['user'] = User::find_by_id($id);
    $dados_requisicao['avatar'] = $dados_requisicao['user']->user_pictures;

    $dados_requisicao['days'] = make_date_select('day');
    $dados_requisicao['months'] = make_date_select('month');
    $dados_requisicao['years'] = make_date_select('year');

    $birth_date = $dados_requisicao['user']->birthdate;
    $date = $birth_date->format('d-m-Y');
    $dados_requisicao['date'] = explode("-", $date);

    $app->render('user/edit_profile.html', $dados_requisicao);
});

/** EDIT CONFIG */
$app->get('/user/config/(:id)', function ($id) use ($app) {
    $dados_requisicao['user'] = User::find_by_id($id);
    $dados_requisicao['avatar'] = $dados_requisicao['user']->user_pictures;

    $app->render('user/edit_config.html', $dados_requisicao);
});

/** UPDATE PROFILE */
$app->put('/user/update/(:id)', function ($id) use ($app) {
    $user = User::find_by_id($id);
    $imagem = new UserPicture();
   // $user->name  = $app->request()->post('nome');
    $user->phone = $app->request()->post('telefone');
    $user->address = $app->request()->post('endereco');
    $user->city = $app->request()->post('cidade');
    $user->area = $app->request()->post('bairro');
    $user->uf = $app->request()->post('uf');
    $user->country = $app->request()->post('pais');
    $user->site = $app->request()->post('site');
    $user->aboutme = $app->request()->post('aboutme');
    $user->type = $app->request()->post('type');
    $day = $app->request()->post('day');
    $month = $app->request()->post('month');
    $year = $app->request()->post('year');
    $birth_date = $year.'-'.$month.'-'.$day;
    $user->birthdate = $birth_date;

    $dados_requisicao['user'] = $user;

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

/** UPDATE CONFIG */
$app->put('/user/update_config/(:id)', function ($id) use ($app) {
    $user = User::find_by_id($id);

    $user->name  = $app->request()->post('nome');
    $user->alternative_email = $app->request()->post('submail');
    $user->address = $app->request()->post('endereco');
    $user->secret_question = $app->request()->post('question');
    $user->question_answer = $app->request()->post('answer');
    $user->password = $app->request()->post('password');

    $dados_requisicao['user'] = $user;

    if($user->save()){
        $app->redirect(get_root_url().'user/'.$id);
    }else{
        $app->render('user/edit_config.html', $dados_requisicao);
    }
});


/** FORM User */
$app->get('/form_user', function () use ($app) {

    $app->render('user/form_user.html');
});