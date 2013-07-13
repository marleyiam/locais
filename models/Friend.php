<?php
class Friend extends ActiveRecord\Model 
{
	static $belongs_to = array(
	  array('users', 'foreign_key' => 'id_a')
	);
}
?>