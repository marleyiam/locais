<?php
class User extends ActiveRecord\Model 
{
	static $has_many = array(
	    array('locals','foreign_key' => 'users_id'),
	    array('routes','foreign_key' => 'users_id')
	   );

	static $has_one = array(
		array('user_pictures','foreign_key' => 'users_id')
		);

	/*static $has_many = array(
	     array('friends','foreign_key' => 'firends_id')
	   );*/
}
?>