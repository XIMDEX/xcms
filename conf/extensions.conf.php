<?php

ModulesManager::file('/inc/persistence/Config.class.php');

class Extensions {
	const JQUERY_PATH   	 = "/extensions/jquery";
	const JQUERY   		 	 = "/extensions/jquery/jquery-1.8.3.min.js";
	const JQUERY_UI		 	 = "/extensions/jquery/jquery-ui-1.9.1.custom.min.js";
	const SMARTY    	  	 = "/extensions/smarty/libs/Smarty.class.php";
	const PHPSECLIB			 = "/extensions/phpseclib";


	 /**
		if function is called then we retrieve  urlboot + constant
		@param String $_func  attribute name. e.g: Extensions::smarty() -> smarty
		@param Array $_args ignored
		@return UrlRoot+attribute value
	  */
	  public static function __callStatic($_func, $_args) {
			$const = strtoupper($_func);

			$urlRoot = Config::getValue('UrlRoot');

			$value = constant(sprintf('%s::%s', "Extensions", $const)); 
			return $urlRoot.$value;
	  }

}

?>
