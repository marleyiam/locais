<?php

/*
$slim->setCookie('takeOut', 1, '1 hour');
$slim->setEncryptedCookie('loyaltyCardNumber', 43252);
*/

/** rota ROOT */
$app->get('/', function () use ($app){
  if (!current_user()) {
    $app->redirect('login');
  }else{
     $app->redirect(get_root_url().'user');
  }
});

/** VIEW PROFILE */
$app->get('/user', $authenticate($app), function() use ($app){
   $user['user'] = current_user();
   $user['friends_relations'] = $user['user']->friends;
   $relation_id = 0;
   $arr = array();
   $friends = array();
   $friends_avatars = array();
   $aproved = Friend::find("all", array(
    "conditions" => array('aproved = ? AND id_b = ? OR aproved = ? AND id_a = ? ','TRUE',$user['user']->id,'TRUE',$user['user']->id)));

   $pendent = Friend::find("all", array(
    "conditions" => array('aproved = ? AND id_b = ?','FALSE',$user['user']->id)));

   $user['friends_quantity'] = count($aproved);
   $user['friends_requests_quantity'] = count($pendent);

   foreach ($aproved as $key => $value) {
        $arr[$key] = $value->attributes();

        if($user['user']->id!=$arr[$key]['id_a']){
          $relation_id = $arr[$key]['id_a'];
        }else{
          $relation_id = $arr[$key]['id_b'];
        }

        $friends[$key] = User::find_by_id($relation_id);
        $friends_avatars[$key] = $friends[$key]->user_pictures;
   }

   $user['friends'] = $friends;
   $user['friends_avatars'] = $friends_avatars;
   //printer(count($user['friends_avatars']));exit;

   $user['locals'] = $user['user']->locals;
   $user['routes'] = $user['user']->routes;

   $array_locals_albums = array();
   $imagens_locals_albums = array();

   $albums = $user['user']->albums;
   $user['albums'] = $albums;

   foreach ($user['albums'] as $key => $value) {
     $array_locals_albums[$key] = Local::find('all', array('conditions' => array('albums_id = ?',$value->id)));
   }

   foreach ($array_locals_albums as $key => $value) {
      $imagens_locals_albums[$key] = get_nested_relation($value,'local_pictures');
   }
 
   $user['locals_albums'] = $array_locals_albums;
   $user['imagens_routes'] = get_nested_relation($user['routes'],'route_pictures');
   $user['imagens_locals'] = get_nested_relation($user['locals'],'local_pictures');
   $user['imagens_locals_albums'] = $imagens_locals_albums;
  
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

/** CREATE LOGIN USER */ 
$app->post('/user', function () use ($app) {
    $user = new User();
    $user->name  = $app->request()->post('nome');
    $user->email = $app->request()->post('email');
    $user->city = $app->request()->post('cidade');
    $user->password = md5($app->request()->post('senha'));
    
    if($user->save()){
        $last_user = User::last();
        $last_user_id = $last_user? $last_user->id : 0;
        $_SESSION['user_id'] = $last_user_id;
        $app->redirect(get_root_url().'user');
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
    
    if($birth_date){
        $date = $birth_date->format('d-m-Y');
        $dados_requisicao['date'] = explode("-", $date);
    }

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