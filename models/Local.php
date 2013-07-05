<?php
class Local extends ActiveRecord\Model 
{
	static $has_many = array(
	     //array('local_pictures')
	     //array('local_pictures', 'through' => 'LocalPicture')
	     //array('pictures', 'class_name' => 'LocalPicture')
	     //array('pictures', 'select' => 'id, name, locals_id')
	     array('local_pictures','foreign_key' => 'locals_id')
	   );


}
?>