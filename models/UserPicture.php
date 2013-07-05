<?php
class UserPicture extends ActiveRecord\Model 
{
	   static $belongs_to = array(
	     array('users')
	   );
}
?>