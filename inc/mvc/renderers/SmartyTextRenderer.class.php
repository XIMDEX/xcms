<?php
/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */




include_once (XIMDEX_ROOT_PATH . '/extensions/smarty/libs/Smarty.class.php');

/** ****************** SMARTY_STRING_RENDERER ************************** */

class SmartyTextRenderer {

	 static function render($_text, $_parameters = NULL) {
	
		$smarty = new Smarty();
		$smarty->template_dir = SMARTY_TMP_PATH . '/templates';
		$smarty->compile_dir = SMARTY_TMP_PATH . '/templates_c';
		$smarty->cache_dir = SMARTY_TMP_PATH . '/cache';
		$smarty->config_dir = SMARTY_TMP_PATH . '/configs';
		
	
		$smarty->register_resource('text', array(
			 'smarty_resource_text_get_template',
			 'smarty_resource_text_get_timestamp',
			 'smarty_resource_text_get_secure',
			 'smarty_resource_text_get_trusted')
		);
		
		
		if(!empty($_parameters) ) {
			foreach ($_parameters as $key => $value) {
				$smarty->assign($key, $value);
			}
		}
		
		return $smarty->fetch("text:".$_text);
	}

}


/** ****************** STRING SOURCE FOR SMARTY ************************** */

function smarty_resource_text_get_template($tpl_name, &$tpl_source, &$smarty_obj) {
    $tpl_source = $tpl_name;
    return true;
}

function smarty_resource_text_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj) {
    $tpl_timestamp = time();
    return true;
}

function smarty_resource_text_get_secure($tpl_name, &$smarty_obj) {  return true; }
function smarty_resource_text_get_trusted($tpl_name, &$smarty_obj) {}

?>
