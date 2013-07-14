<?php
class User extends ActiveRecord\Model 
{
	/*
	array('locals','foreign_key' => 'users_id','conditions' => array('visibility = ?', 'public')),
	*/
	static $has_many = array(
	    array('locals','foreign_key' => 'users_id'),
	    array('routes','foreign_key' => 'users_id'),
	    array('friends','foreign_key' => 'id_a')
	   );

	static $has_one = array(
		array('user_pictures','foreign_key' => 'users_id')
		);
}
?>