<?php
/** ADD USER */
$app->post('/add_user', function() use ($app){
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

   //echo  Friend::table()->last_sql;

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


/** FRIENDSHIP REQUESTS */
$app->get('/requests', $authenticate($app), function() use ($app){
    $user['user'] = current_user();
    $user['avatar'] = current_user_avatar();
    $arr = array();
    $requesters = array();
    $requesters_avatars = array();
    $friend_relation_ids = array();

    $pendent = Friend::find("all", array(
     "conditions" => array('aproved = ? AND id_b = ?','FALSE',$user['user']->id)));


    foreach ($pendent as $key => $value) {
         $arrr[$key] = $value->attributes();
         $friend_relation_ids[$key] = $arrr[$key]['id'];
         $requesters[$key] = User::find_by_id($arrr[$key]['id_a']);
         $requesters_avatars[$key] = $requesters[$key]->user_pictures;
    }

    $user['requesters'] = $requesters;
    $user['requesters_avatars'] = $requesters_avatars;
    $user['friend_relation_ids'] = $friend_relation_ids;

    $app->render('user/requests.html', $user);
});


/** CONFIRM FRIEND */
$app->post('/requests/confirm', function() use ($app){
      $f = $app->request()->params();
      $friend = Friend::find_by_id($f['id']);
      if($friend->update_attribute ("aproved" ,"TRUE")){
            echo "Vocês agora são amigos !!!";
      }else{
            echo "Não foi possível confirma esse pedido de amizade !";
      }
});

/** DENY FRIEND */
$app->post('/requests/deny', function() use ($app){
      $f = $app->request()->params();
      $friend = Friend::find_by_id($f['id']);

      if($friend->delete()){
          echo "Vocês negou essa solicitação de amizade !!!";
      }else{
          echo "Vocês não são amigos, mas a solicitação ainda existe";
      }
});


?>