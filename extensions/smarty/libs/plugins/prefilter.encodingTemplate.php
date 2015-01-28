<?php
/*
 * 
 * Smarty Plugin
 * -------------------------------------------
 * File:	prefilter.encodingTemplate.php
 * Type: 	prefilter
 * Name: 	encodingTemplate	
 * Purpose; encoding the template to the config value
 * ---------------------------------------------------------
 */
function smarty_prefilter_encodingTemplate($source, &$smarty)
{
	$displayEncoding = \App::getValue( 'displayEncoding');
	$sourceEncoded = \Ximdex\XML\Base::recodeSrc($source,$displayEncoding);
	
	return $sourceEncoded;
}
?>