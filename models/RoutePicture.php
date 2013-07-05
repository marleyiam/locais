<?php
class RoutePicture extends ActiveRecord\Model 
{
	   static $belongs_to = array(
	     array('routes')
	   );
}
?>