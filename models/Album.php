<?php
class Album extends ActiveRecord\Model 
{
	static $has_many = array(
	     array('locals','foreign_key' => 'locals_id')
	   );
	
	static $belongs_to = array(
	  array('users','foreign_key' => 'users_id')
	);
}
?>