<?php
# Plugin for input select options
# JMGG


function smarty_function_select($params, &$smarty) {
	
	$name = trim(isset($params['name']) ? $params['name'] : '');
	$humanReadableName = trim(isset($params['humanReadableName']) ? $params['humanReadableName'] : '');
	$values = isset($params['values']) ? $params['values'] : array();
	
	$options = array();
	foreach ($values as $key => $value) {
		$options[] = sprintf('<option value="%s">%s</option>', $key, $value);
	}
	$optionString = implode("\n\t\t", $options);
	
	echo <<<HEREDOC
	<label for="$name">$humanReadableName</label>
	<select name="$name" id="$name">
		$options
	</select>
HEREDOC;
}
?>