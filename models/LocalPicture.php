<?php
class LocalPicture extends ActiveRecord\Model 
{
	   static $belongs_to = array(
	     array('locals')
	   );
}
?>