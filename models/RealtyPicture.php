<?php
class RealtyPicture extends ActiveRecord\Model 
{
	   	static $belongs_to = array(
	     	array('realties', 'foreign_key' => 'realties_id')
	    );
}
?>