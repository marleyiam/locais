
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
    $app->render('login/login.html');
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
    $app->render('login/login.html');
});

?>