<?php
/*
 public function set_password($plaintext) {
      $this->encrypted_password = md5($plaintext);
    }
     printer($user['user']->attributes());
*/
     /*
     $slim->setCookie('takeOut', 1, '1 hour');
$slim->setEncryptedCookie('loyaltyCardNumber', 43252);
*/

/** FRIENDS */
$app->get('/user/friends', function() use ($app){
   $users['users'] = User::find('all');
    $app->render('user/index.html', $users);
});


/** VIEW PROFILE */
$app->get('/user', $authenticate($app), function() use ($app){
   $user['user'] = current_user();
   $user['locals'] = $user['user']->locals;
   $user['routes'] = $user['user']->routes;
   $user['imagens_routes'] = get_nested_relation($user['routes'],'route_pictures');
   $user['imagens_locals'] = get_nested_relation($user['locals'],'local_pictures');
   $user['avatar'] = $user['user']->user_pictures;

   $app->render('user/show_profile.html', $user);
});

/** USER PUBLIC PROFILE */
$app->get('/profile/(:id)', $authenticate($app), function($id) use ($app){
   $user['avatar'] = current_user()->user_pictures;
   $user['public_user'] = User::find_by_id($id);
   $user['public_locals'] = $user['public_user']->locals;
   $user['public_routes'] = $user['public_user']->routes;
   $user['public_imagens_routes'] = get_nested_relation($user['public_routes'],'route_pictures');
   $user['public_imagens_locals'] = get_nested_relation($user['public_locals'],'local_pictures');
   $user['public_avatar'] = $user['public_user']->user_pictures;

   $app->render('user/user_profile.html', $user);
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


/** EDIT PROFILE */
$app->get('/user/edit', $authenticate($app), function () use ($app) {
    $dados_requisicao['user'] = current_user();
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
$app->get('/user/config', $authenticate($app), function () use ($app) {
    $dados_requisicao['user'] = current_user();
    $dados_requisicao['avatar'] = $dados_requisicao['user']->user_pictures;

    $app->render('user/edit_config.html', $dados_requisicao);
});

/** UPDATE PROFILE */
$app->put('/user/update', function () use ($app) {
    $user = current_user();
    $imagem = new UserPicture();
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
        $app->redirect(get_root_url().'user');
    }else{
        $app->render('user/edit_profile.html', $dados_requisicao);
    }
});

/** UPDATE CONFIG */
$app->put('/user/update_config', function () use ($app) {
    $user = current_user();
    $dados_requisicao['user'] = $user;
    $equals = 0;

    $user->name  = $app->request()->post('nome');
    $user->alternative_email = $app->request()->post('submail');
    $user->address = $app->request()->post('endereco');
    $user->secret_question = $app->request()->post('question');
    $user->question_answer = $app->request()->post('answer');

    if($app->request()->post('password')!="" && $app->request()->post('npassword')!=""){
        $equals = ($app->request()->post('password') == $app->request()->post('npassword'));

        if($equals == 1){
            $user->password = $app->request()->post('password');
        }else{
            $app->render('user/edit_config.html', $dados_requisicao);
        }
    }

    if($user->save()){
        $app->redirect(get_root_url().'user');
    }else{
        $app->render('user/edit_config.html', $dados_requisicao);
    }
});

$app->get('/login', function() use ($app){
    $app->render('login/login.html');
});

$app->post('/user_login', function () use ($app) {
    $obj = (object) $app->request()->post();
    $email = $obj->email;
    //$password = md5($obj->password);
    $password = $obj->password;
    $user = User::find_by_email_and_password($email,$password);
    if($user){
        $_SESSION['user_id'] = $user->id;
        $app->redirect(get_root_url().'user');
        $app->flash("login_msg","Você está logado");
    }else{
        $app->redirect(get_root_url().'login');
        $app->flash("login_msg","Você está logado");
    }
});

/** AUTOCOMPLETE SEARCH USER AJAX*/
$app->get('/ajax_search_users', function () use ($app) {

    $app->response()->header('Content-Type', 'application/json;charset=utf-8');

    $arrterm = $app->request()->params();
    $term = $arrterm["term"];

    $res = array();
    $ress = array();
    $arr = array();
    $avatar = "";
    $users = User::find('all', array('conditions' => array("name LIKE ?", "%".$term."%")));

    foreach ($users as $key => $value) {
        $avatar = $value->user_pictures? $value->user_pictures->attributes() : 'default-user-picture.png';
        $res[$key] = $value->attributes();
        $ress[$key] = $res[$key]["name"]; 
        $arr[$key] = array("id" => $res[$key]["id"],"name" => $res[$key]["name"],
        "city" => $res[$key]["city"], "avatar" => $avatar);  
    }

    echo json_encode($arr);
});