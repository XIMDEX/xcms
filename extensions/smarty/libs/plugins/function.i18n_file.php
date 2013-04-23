<?php

require_once(XIMDEX_ROOT_PATH . "/inc/persistence/XSession.class.php");

function smarty_function_i18n_file($params, &$smarty)
{ 
  XSession::check();

  //Fichero a traducir
  $file = trim(isset($params['file']) ? $params['file']:null);
  //Idioma principal en el que queremos traducir el archivo
  $_lang = trim(isset($params['lang'] ) ? $params['lang'] : null);
  //true | false, especifica si se concatenará la url base al fichero, a la hora de devolverlo multiidioma
  $_url = trim(isset($params['url'] ) ? Config::getValue('UrlRoot') : null);
  //El valor por defecto a devolver en caso de que no se haya encontrado ningun fichero válido
  $_default = trim(isset($params['default'] ) ? $params['default']: null);

	if($file == null) return null;
	$_file = null;

	//Comprobamos si existe el archivo para el idioma pasado
	if($_lang != null ) {
		$_file = str_replace("[LANG]", $_lang, $file);
		if(file_exists(XIMDEX_ROOT_PATH.$_file) )
			return $_url.$_file;
	}

	//Si no existe archivo asociado al idioma pasado, se comprueba con el idioma del sistema
	$_lang = XSession::get('default_lang');
	if($_lang != null ) {
		$_file = str_replace("[LANG]", $_lang, $file);
		if(file_exists(XIMDEX_ROOT_PATH.$_file) )
			return $_url.$_file;
	}

	//Si no existe para el idioma por defecto, en este caso(y por ahora) el 'es'
	$_lang = 'es';
	if($_lang != null ) {
		$_file = str_replace("[LANG]", $_lang, $file);
		if(file_exists(XIMDEX_ROOT_PATH.$_file) )
			return $_url.$_file;
	}

	return $_default;
}
?>
