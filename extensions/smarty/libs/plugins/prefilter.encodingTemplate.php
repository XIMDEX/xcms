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
	$displayEncoding = Config::getValue('displayEncoding');
	$sourceEncoded = XmlBase::recodeSrc($source,$displayEncoding);
	
	return $sourceEncoded;
}
?>