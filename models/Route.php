<?php
class Route extends ActiveRecord\Model 
{
	static $has_many = array(
	     array('route_pictures','foreign_key' => 'routes_id')
	   );
}
?>