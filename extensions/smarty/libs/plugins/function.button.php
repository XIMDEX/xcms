<?php

function smarty_function_button($params, &$smarty) {

	$label = trim(isset($params['label']) ? $params['label'] : '');
	$class = trim(isset($params['class']) ? $params['class'] : '');
	$onclick = trim(isset($params['onclick']) ? $params['onclick'] : '');
	$id = trim(isset($params['id'] ) ? $params['id'] : '');
	$message = trim(isset($params['message']) ? $params['message'] : '');
	$type = strtolower(trim(isset($params['type']) ? $params['type'] : ''));
	$history = strtolower(trim(isset($params['history']) ? $params['history'] : '1'));

	if("" != $message && null != $message ) {
		//gettext de message
		$message = _($message);
	}

	if("" != $label && null != $label ) {
		//gettext de label
		$label = _($label);
	}


	// By default the button will be a submit button
	if (!in_array($type, array('submit', 'goback', 'close', 'reset'))) {
		$type = 'submit';
	}

	$class = explode(' ', trim($class . ' ui-state-default ui-corner-all button'));
	$class[] = "$type-button";

	$sButton = '<a href="#" id="%s" onclick="this.blur(); %s" class="%s"><span>%s</span></a>';

	// If it is not a submit button we don't want the validate class (see formManager)
	if ($type != 'submit') {
		$class = array_diff($class, array('validate'));
	}

	// Reset button renders an input[type=reset] field
	if ($type == 'reset') {
		$sButton = '<input id="%s" onclick="this.blur(); %s" type="reset" class="reset-button %s" value="%s" />';
	}

	// IMPORTANT: Shouldn't relay on the parameter "label" just because the used language.
	// IMPORTANT: The "Volver" condition should be deleted.
	if ($type == 'goback' || $params['label'] == 'Volver') {

		$sUrl = '';

		$history = !is_numeric($history)
			? '1'
			: $history < 1
				? '1'
				: $history;

		if ($history > 1) {
			$sUrl .= sprintf(
				'<div class="history-value" style="display: none;">%s</div>',
				$history
			);
		}

		$sUrl .= sprintf(
			'<div class="index" style="display: none">%s/xmd/loadaction.php?method=index&action=%s&nodeid=%s</div>',
			$smarty->getTemplateVars('_URL_ROOT'),
			$smarty->getTemplateVars('action'),
			$smarty->getTemplateVars('nodeid')
		);
		$sButton = "$sUrl\n$sButton";
	}

	$button = sprintf($sButton, $id, $onclick, implode(' ', $class), _($label));
	echo $button;

	if (!empty($message)) {
		echo sprintf('<input type="text" class="submit_message ui-helper-hidden" value="%s">', $message);
	}
}
?>
