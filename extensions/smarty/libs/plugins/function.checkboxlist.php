<?php
# Plugin for input select options
# JMGG


function smarty_function_select($params, &$smarty) {
	
	$name = trim(isset($params['name']) ? $params['name'] : '');
	$humanReadableName = trim(isset($params['humanReadableName']) ? $params['humanReadableName'] : '');
	$values = isset($params['values']) ? $params['values'] : array();
	
	foreach ($values as $key => $value) {
		$newparams = array('name' => $name, 
			'humanReadableName' => $humanReadableName, 
			'key' => $key, 'value' => $value);
		
		smarty_function_checkbox($newparams, $smarty);		
	}
}
?>