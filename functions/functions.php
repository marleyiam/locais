<?php
/** função que exibe os objetos de uma forma mais "legível" =| */
function printer($obj){
	echo '<pre>';
	var_dump($obj);
	echo '</pre>';
}

/**função pra tratamento de execeção ¬¬ */
function exceptionHandler($exception) {

    // template de erros
	$traceline = "#%s %s(%s): %s(%s)";
	$msg = "PHP Fatal error:  Exception não capturada '%s' com a mensagem '%s' no %s:%s\nStack trace:\n%s\n  lançada %s na linha %s";

	$trace = $exception->getTrace();
	foreach ($trace as $key => $stackPoint) {
        // recupera os tipos dos params
		$trace[$key]['args'] = array_map('gettype', $trace[$key]['args']);
	}

    // constroi as linhas do stacktrace
	$result = array();
	foreach ($trace as $key => $stackPoint) {
		$result[] = sprintf(
			$traceline,
			$key,
			$stackPoint['file'],
			$stackPoint['line'],
			$stackPoint['function'],
			implode(', ', $stackPoint['args'])
			);
	}
    // stacktrace sempre termina com {main}
	$result[] = '#' . ++$key . ' {main}';

    // escreve as linhas no template
	$msg = sprintf(
		$msg,
		get_class($exception),
		$exception->getMessage(),
		$exception->getFile(),
		$exception->getLine(),
		implode("\n", $result),
		$exception->getFile(),
		$exception->getLine()
		);

    //echo $msg;
	printer($msg);
}

function get_root_url(){
	return substr($_SERVER['PHP_SELF'],0,-9);
}

function convertDate2String($data) {

		return date('F d, Y h:i:s A', strtotime($data));	
}

function lastIndexOf($string,$item){  
    $index = strpos(strrev($string),strrev($item));  
    if ($index){  
        $index = strlen($string)-strlen($item)-$index;  
        return $index;  
    }  
        else  
        return -1;  
}

function from_camel_case($input) {
  preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
  $ret = $matches[0];
  foreach ($ret as &$match) {
    $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
  }
  return implode('_', $ret);
}

function relation($entity,$relation,$find){
//$user['imagens'] = relation(array("Local",$user['locals']),"LocalPicture","identifier");
	$data = array();
	$findby = 'find_by_'.$find;
	$get_relation = Inflect::pluralize(strtolower(from_camel_case($relation)));

	foreach ($entity[1] as $key => $value) {	
		$data[$key] = $value->$get_relation;
	}

	return $data;
}

function get_nested_relation($obj,$relation){
	$data = array();

	foreach ($obj as $key => $value) {
	    $data[$key] = $value->$relation;
		//printer($value);
	}

	return $data;
}

function make_date_select($param){
	
	$months = array(1=> "Janeiro", "Fevereiro", "Março", 
	           "Abril", "Maio", "Junho", "Julho", "Agosto", 
	           "Setembro", "Outubro", "Novembro", "Dezembro"); 
	$days = array();
	$years = array();
	$q = 0;

	switch($param){
		case $param == 'day': 
			for($i = 1; $i<=31; $i++){
				$days[$i] =  $i;
			}
			return $days;
		break;
		case  $param == 'month':
			return $months;
		break;
		case $param == 'year': 
			for($i = 1920; $i<=2013; $i++){
				$q++;
				$years[$q] = $i;	
			 }
			 return $years;
		break;
		default;
			return 'ERROR';
		break;
	}
}


function current_user(){
	if(isset($_SESSION['user_id'])){
		return User::find_by_id($_SESSION['user_id']);
	}
}

function current_user_avatar(){
	return current_user()? current_user()->user_pictures : 'default-user-picture.png';
}

function getGlobals() {
    return array(
        'session'   => $_SESSION,
    ) ;
}

function get_last_id($entity){
    $last_user = $entity::last();
    $last_user_id = $last_user? $last_user->id : 0;
    return ($last_user_id+1);
}

function validateEmail($email) {
  return filter_var($email, FILTER_VALIDATE_EMAIL);
}

?>