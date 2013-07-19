<?php
class Realty extends ActiveRecord\Model 
{
	static $has_many = array(
	     array('realty_pictures','foreign_key' => 'realties_id')
	   );
	
	static $belongs_to = array(
	  array('users','foreign_key' => 'users_id')
	);
}
?>