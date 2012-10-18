<?php
# Plugin for input select options
# JMGG


function smarty_function_input_text($params, &$smarty) {
	
	$name = trim(isset($params['name']) ? $params['name'] : '');
	$humanReadableName = trim(isset($params['human_readable_name']) ? $params['human_readable_name'] : '');
	$value = isset($params['value']) ? $params['value'] : '';
	
	echo <<<HEREDOC
	<label for="{$name}">$humanReadableName</label>
	<input type="text" id="{$name}" name="{$name}" value="$value" class="large" />
HEREDOC;

}
?>