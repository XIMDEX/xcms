<?php
# Plugin for input select options
# JMGG


function smarty_function_checkbox($params, &$smarty) {
	
	$name = trim(isset($params['name']) ? $params['name'] : '');
	$humanReadableName = trim(isset($params['humanReadableName']) ? $params['humanReadableName'] : '');
	$key = trim(isset($params['key']) ? $params['key'] : '');
	$value = isset($params['value']) ? $params['value'] : '';
	
	$checked = $value ? ' checked="checked"' : '';
	
	echo <<<HEREDOC
	<label for="{$name}_{$key}">$humanReadableName</label>
	<input type="checkbox" id="{$name}_{$key}" name="{$name}_{$key}"{$checked} />
HEREDOC;

}
?>