<?php
class User extends ActiveRecord\Model 
{
	//fk_local_users_id
	static $has_many = array(
	     array('locals','foreign_key' => 'locals_id'),
	     array('routes','foreign_key' => 'routes_id'),
	     array('user_pictures','foreign_key' => 'users_id')
	   );

	/*static $has_many = array(
	     array('friends','foreign_key' => 'firends_id')
	   );*/
}
?>