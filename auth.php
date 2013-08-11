
<?php


$authenticate = function ($app) {
    return function () use ($app) {
        if (!isset($_SESSION['user_id'])) {
            $app->flash('error', 'Login required');
            $app->redirect(get_root_url().'login');
        }
    };
};

$app->hook('slim.before.dispatch', function() use ($app) { 
   $user = null;
   if (isset($_SESSION['user_id'])) {
      $user_id = $_SESSION['user_id'];
      $user = User::find_by_id($user_id);
   }
   $app->view()->setData('user', $user);
});


$app->get('/login', function() use ($app){

  $facebook = new Facebook(array(
    'appId' => FACEBOOK_APP_ID,
    'secret' => FACEBOOK_SECRET,
    'cookie' => true,
  ));

  $fb_user_uid = $facebook->getUser();
  $fb_user = "";
  //printer($fb_user_uid);exit; 100002802537482

  $loginParams = array(
    'scope' => 'email, user_about_me, user_birthday, user_hometown',
    'display' => 'popup',
    'redirect_uri' => REDIRECT_URI.'/fb_login'
  );

  $logoutParams = array(
    'next'  =>  REDIRECT_URI.'/fb_logout'
  );

  if($fb_user_uid) {
      try {
          $user_profile = $facebook->api('/me','GET');
          //$fb_user['fb_user_data'] = $facebook->api('/me','GET');
          $fb_user['fb_logout'] = $facebook->getLogoutUrl($logoutParams);
      } catch(FacebookApiException $e) { 
          $fb_user['fb_login'] = $facebook->getLoginUrl($loginParams);
      }
  }else{
      //$app->flash("login_msg","Você ainda não está logado");
      $fb_user['fb_login'] = $facebook->getLoginUrl($loginParams);
  }

    $app->render('login/login.html',$fb_user);
});

$app->get('/fb_login', function() use ($app) {
 // printer($app->request()->get());
  $facebook = new Facebook(array(
    'appId' => FACEBOOK_APP_ID,
    'secret' => FACEBOOK_SECRET,
    'cookie' => true,
  ));
  $fb_user_uid = $facebook->getUser();

  if(isset($fb_user_uid )){ 
      $user_profile = $facebook->api('/me','GET');
      $user = new User();
      $user->name  = $user_profile['name'];
      $user->email = $user_profile['email'];
      $user->birthdate = $user_profile['birthday'];;
      $user->fb_uid =$user_profile['id'];;
     
      if($user->save()){
          $last_user = User::last();
          $last_user_id = $last_user? $last_user->id : 0;
          $_SESSION['user_id'] = $last_user_id;
          $app->flash("login_msg","Você está logado !!!");
          $app->redirect(get_root_url().'user');
      }else{
          $app->flash("login_msg","Não foi possível cadastra-lo !");
          $app->redirect('login');
      }
  }

});

$app->get('/fb_logout', function() use ($app) {
  $facebook = new Facebook(array(
    'appId' => FACEBOOK_APP_ID,
    'secret' => FACEBOOK_SECRET,
    'cookie' => true,
  ));
  $logoutParams = array(
    'next'  =>  REDIRECT_URI.'/fb_logout'
  );
  $fb_user['fb_login'] = $facebook->getLogoutUrl($logoutParams);
  $facebook->destroySession();
  $app->redirect('login',$fb_user);
});

$app->post('/user_login', function () use ($app) {
    $obj = (object) $app->request()->post();
    $email = $obj->email;
    $password = md5($obj->password);
    $user = User::find_by_email_and_password($email,$password);
    $msg = "";
    if($user){
        $_SESSION['user_id'] = $user->id;
        $app->redirect(get_root_url().'user');
        //$app->flash("login_msg","Você está logado");
    }else{
        //$app->flash("loginerror","Você NÂO está logado");
        $app->redirect(get_root_url().'login');
    }
});


$app->get('/logout', function () use ($app) {
    unset($_SESSION['user_id']);
    $app->view()->setData('user', null);

    $facebook = new Facebook(array(
      'appId' => FACEBOOK_APP_ID,
      'secret' => FACEBOOK_SECRET,
      'cookie' => true,
    ));
    $logoutParams = array(
      'next'  =>  REDIRECT_URI.'/fb_logout'
    );

    $fb_user['fb_login'] = $facebook->getLogoutUrl($logoutParams);
    $app->render('login/login.html',$fb_user);
});

$app->post('/check_email', function() use ($app){
  //$app->response()->header('Content-Type', 'application/json;charset=utf-8');
  $arr = $app->request()->params();
  //printer($arr);
  if(validateEmail($arr["email"])){
      $user = User::find_by_email($arr["email"]);
      if($user){
        echo 'Esse email já foi cadastrado e não está disponível !';
      }else{
        echo 'Esse email está disponível';
      }
  }else{
      echo "Esse endereço de email não é válido !";
  }

});

/** AJAX Change Password Request */
$app->post('/changepasswd', function() use ($app){
  $arr = $app->request()->params();
  $email = $arr['email'];
  $user = User::find_by_email($email);
  if($user){
      require_once 'lib/swift_required.php';

      $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
      ->setUsername('seu_login')
      ->setPassword('sua_senha');

      $name = $user->name;
      $sendto   = $email;
      $usermail = 'admin@mail.com';
      $token = sha1(uniqid($user->name, true));
      $ts = time();

      $content  = nl2br("
        Olá, recebemos uma solicitação sua para redefinição de sua senha,
        para realiza-la basta acessar o seguinte link:
        http://".get_host().get_root_url()."redefine_email?user=".base64_encode($user->email)."&token=".$token."&ts=".$ts."
        O link expirará dentro de 24Hs, ao fim desse prazo, caso deseje redefinir sua
        senha, será necessário realizar uma nova solicitação");

      $message = Swift_Message::newInstance();
      $message->setTo(array(
        $sendto => "Admin"
      ));
      $message->setSubject("Redefina sua senha");
      $message->setBody($content);
      $message->setFrom($usermail, $name);

      $mailer = Swift_Mailer::newInstance($transport);

      if($mailer->send($message,$failure)){
        echo 'O link para redefinição de senha foi enviado para o seu email!';
      }else{
        echo 'false';
        print_r($failures);
      }
  }else{
    echo "Esse endereço de email não pertence a um usuário cadastrado no sistema";
  }
});

/** REDEFINE PASSWORD */
$app->get('/redefine_email', function() use ($app){

  $arr = $app->request()->params();
  $u_email = base64_decode($arr['user']);
  $token = $arr['token'];
  $ts = $arr['ts'] + (60 * 60 * 24);
  $now = time();
  if($now < $ts){
    $agora = date("d/m/Y G:i:s", $now);
    $data_email = date("d/m/Y G:i:s", $ts);
    $user = User::find_by_email($u_email);
    if($user && is_sha1($token)){
      $_SESSION['user_id'] = $user->id;
      $dados_requisicao['user'] = $user;
      $dados_requisicao['avatar'] = $dados_requisicao['user']->user_pictures;
      $app->redirect(get_root_url().'user/config',$dados_requisicao);
      
    }else{
      echo "Error!";
      $app->redirect(get_root_url().'login');
    }

  }else{
    echo "O prazo de sua redefinição de senha excedeu o limite de 24Hs, será necessário
    solicitar a redefinição de senha novamente !";
    $app->redirect(get_root_url().'login');
  }
});


?>
