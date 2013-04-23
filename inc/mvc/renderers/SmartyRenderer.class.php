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


ModulesManager::file( '/conf/extensions.conf.php');
include_once (XIMDEX_ROOT_PATH . '/inc/mvc/renderers/AbstractRenderer.class.php');
require_once (XIMDEX_ROOT_PATH . Extensions::SMARTY);
include_once (XIMDEX_ROOT_PATH . '/inc/widgets/Widget.class.php');


/**
 *
 * @brief Renderer for the compiling PHP template engine Smarty
 *
 * Renderer for the compiling PHP template engine Smarty
 */
class SmartyRenderer extends AbstractRenderer {

	/**
	 * Constructor
	 * @return unknown_type
	 */
	function SmartyRenderer() {
		$diplayEncoding = Config::getValue('displayEncoding');
		parent::__construct();
	}

	/**
	 * (non-PHPdoc)
	 * @see inc/mvc/renderers/AbstractRenderer#render($view)
	 */
	function render($view = NULL) {

		//tomamos todos los datos comunes a todos los renders
		parent::render($view);

		$smarty = new Smarty();
		$smarty->setTemplateDir(SMARTY_TMP_PATH . '/templates');
		$smarty->setCompileDir(SMARTY_TMP_PATH . '/templates_c');
		$smarty->setCacheDir(SMARTY_TMP_PATH . '/cache');
		$smarty->setConfigDir(SMARTY_TMP_PATH . '/configs');

		//Remove whitespaces
//  		$smarty->autoload_filters = array('output' => array('trimwhitespace'));

		//Initialize NDOE_PATH
		$smarty->assign("_NODE_PATH", "");
		//default num_nodes: "1"
		$smarty->assign("num_nodes", 1);
		//default theme: "ximdex_theme"
		$smarty->assign("theme", "ximdex_theme");
		//Assign extensions class 
		$smarty->registerClass("EXTENSIONS", "Extensions");

		//Encode the template about the config value
		$smarty->autoload_filters = array('pre' => array('encodingTemplate'));
//		$smarty->autoload_filters = array('pre' => array('encodingTemplate', 'widgets'));
//		$smarty->register_prefilter(array(&$this, 'prefilter_widgets'));
		$this->_set_params($smarty);

//		XMD_Log::debug("MVC::SmartyRenderer display '".$_ACTION_CONTROLLER."' template " );
		return $smarty->fetch($this->_template);
    }

	/**
	 *
	 * @param $smarty
	 * @return unknown_type
	 */
	private function _set_params(& $smarty) {

		//Debuggins smarty
		$debug_smarty = $this->get("debugsmarty");
		if($debug_smarty != NULL ) {
	 		$smarty->debugging = true; //Remove comment for debugging
		}

		if(strpos($this->get('method'), ".tpl") === false)
			$_method = $this->get('method').".tpl";
		else
			$_method = $this->get('method');


		$this->_set_controller_path($this->get('module'),$_method );

		//pasamos los parámetros a smarty
		$_parameters = $this->getParameters();

		//we initialize params used in some actions
		if(!array_key_exists("history_value", $_parameters) ) { $_parameters["history_value"] = 1; }
		if(!array_key_exists("goback", $_parameters) ) { $_parameters["goback"] = false; }

		foreach ($_parameters as $key => $value) {
			$smarty->assign($key, $value);
		}

		$messages = $smarty->getTemplateVars('messages');
		$smarty->assign('messages_count', count($messages));

	}

	/**
	 *
	 * @param $module
	 * @param $_method
	 * @return unknown_type
	 */
	private function _set_controller_path($module = NULL, $_method = NULL) {

		if (empty($module)) {
			$_ACTION_CONTROLLER = XIMDEX_ROOT_PATH . '/actions/' .$this->get('_ACTION_COMMAND'). '/template/Smarty/' . $_method;
			$this->set('_ACTION_CONTROLLER', $_ACTION_CONTROLLER );
		} else {
			$_ACTION_CONTROLLER = XIMDEX_ROOT_PATH .ModulesManager::path($module).'/actions/' . $this->get('_ACTION_COMMAND') . '/template/Smarty/' . $_method;
			$this->set('_ACTION_CONTROLLER', $_ACTION_CONTROLLER );

		}

	}

	/**
	 * Smarty prefilter plugin
	 * Identify widgets tags, for each tag replaces it with the associated template
	 * and includes dependencies (javascript, css, etc...)
	 * @param $source
	 * @param $smarty
	 * @return unknown_type
	 */
	public function prefilter_widgets($source, &$smarty) {

		$smarty->clear_compiled_tpl();

		// Be sure to include the JS global vars
		$js_files = $smarty->get_template_vars('js_files');
		foreach ($js_files as $js) {
			if (strpos($js, 'vars_js.php') !== false) {
				$js_files = array($js);
				break;
			}
		}

		$params = $smarty->get_template_vars();
		$params['js_files'] = $js_files;
		$ret = Widget::process($source, $params);

		if ($ret === null) {
//			debug::log(' ===> NO WIDGET');
			return $source;
		}

		$smarty->append('js_files', $ret['js_files'], true);
		$smarty->append('css_files', $ret['css_files'], true);

		return $ret['tpl'];
	}
}
?>
