<?php
/** ADD TO FAVORITES */

$app->get('/favorite', $authenticate($app), function() use ($app){
  $user = current_user();
  $favorites['favorites'] =  $user->favorites;
  $app->render('favorite/index.html', $favorites);
});

$app->post('/add_to_favorite', function() use ($app){
    $user = current_user();
    $friend_id = 0;
    $f = $app->request()->params();

    $aproved = Friend::find("all", array(
        "conditions" => array('aproved = ? AND id_b = ? OR aproved = ? AND id_a = ? ',
        'TRUE', $user->id,'TRUE', $user->id)));

    $pendent = Friend::find("all", array(
        "conditions" => array('aproved = ? AND id_b = ? OR aproved = ? AND id_a = ? ',
        'FALSE', $user->id,'FALSE', $user->id)));

    $already_friends = array();
    $already_friends_ids = array();
    $already_requesters = array();
    $those_I_sent = array();
    $those_sentme = array();

    foreach ($aproved as $key => $value) {
        $already_friends[$key] = $value->attributes();

        if($user->id!=$already_friends[$key]['id_a']){
          $friend_id = $already_friends[$key]['id_a'];
        }else{
          $friend_id = $already_friends[$key]['id_b'];
        }
        $already_friends_ids[$key] = $friend_id;
    }


    if(in_array($f['to_user'],$already_friends_ids)){
      echo 'Vocês já são amigos !';
    }else if(!in_array($f['from_user'],$already_friends_ids)){

      foreach ($pendent as $key => $value) {
          $already_requesters[$key] = $value->attributes();

          if($user->id!=$already_requesters[$key]['id_a']){
            $those_sentme[$key] = $already_requesters[$key]['id_a'];
          }else{
            $those_I_sent[$key] = $already_requesters[$key]['id_b'];
          }
      }

      if(in_array($f['to_user'],$those_sentme)){
        echo "Esse usuário já solicitou sua amizade !";
      }else if(in_array($f['to_user'],$those_I_sent)){
        echo "Sua solicitação já foi enviada !";
      }else{

          $friends = new Friend();
          
          $friends->id_a = $f['from_user'];
          $friends->id_b = $f['to_user'];
          $friends->aproved = 'false';
          if($friends->save()){
               echo 'sua solicitação foi enviada !';
          }else{
               echo 'não foi possível realizar sua solicitação !';
          }
      }
    }
});

?>