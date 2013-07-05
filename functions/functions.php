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

?>