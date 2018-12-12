<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\MVC\Render;

use Extensions;
use Smarty;
use Ximdex\Runtime\App;
use Xmd\Widgets\Widget;

require_once(APP_ROOT_PATH . Extensions::SMARTY);

/**
 * @brief Renderer for the compiling PHP template engine Smarty
 *
 * Renderer for the compiling PHP template engine Smarty
 */
class SmartyRenderer extends AbstractRenderer
{
	function render($view = NULL)
	{
		// Tomamos todos los datos comunes a todos los renders
		parent::render($view);
		$smarty = new Smarty();
        $smarty->setTemplateDir([\APP_ROOT_PATH, \XIMDEX_ROOT_PATH]);
		$smarty->setCompileDir(SMARTY_TMP_PATH . '/templates_c');
		$smarty->setCacheDir(SMARTY_TMP_PATH . '/cache');
		$smarty->setConfigDir(SMARTY_TMP_PATH . '/configs');
        $smarty->config_vars = get_defined_constants() + App::config();
        $smarty->registerPlugin("block","url", [$this, 'url']);
        $smarty->registerPlugin("block","ximdex", [$this, 'urlXimdex']);
        
		// Initialize NODE_PATH
		$smarty->assign("_NODE_PATH", "");
		
		// Default num_nodes: "1"
		$smarty->assign("num_nodes", 1);
		
		// Default theme: "ximdex_theme"
		$smarty->assign("theme", "ximdex_theme");
		// Assign extensions class
		$smarty->registerClass("EXTENSIONS", "Extensions");

		// Encode the template about the config value
		$smarty->autoload_filters = array('pre' => array('encodingTemplate'));
		$this->_set_params($smarty);
		return $smarty->fetch($this->_template);
	}

	public function url($params, $url, \Smarty_Internal_Template $template, & $repeat)
	{
	    if (! $repeat and isset($url)) {
            return App::getUrl($url);
        }
    }

    public function urlXimdex($params, $url, \Smarty_Internal_Template $template, & $repeat)
    {
        if (! $repeat and isset($url)) {
            return App::getXimdexUrl($url);
        }
    }

	private function _set_params(& $smarty)
	{
		//Debuggins smarty
		$debug_smarty = $this->get("debugsmarty");
		if ($debug_smarty != NULL) {
			$smarty->debugging = true; //Remove comment for debugging
		}
		$_method = $this->get('method');
		if (strpos($this->get('method'), ".tpl") === false) {
			$_method .= '.tpl';
        }
		$this->_set_controller_path($this->get('module'), $_method);

		// We send the parameters to smarty
		$_parameters = $this->getParameters();

		// We initialize params used in some actions
		if (!array_key_exists("history_value", $_parameters)) {
			$_parameters["history_value"] = 1;
		}
		if (!array_key_exists("goback", $_parameters)) {
			$_parameters["goback"] = false;
		}
		foreach ($_parameters as $key => $value) {
			$smarty->assign($key, $value);
		}
		$messages = $smarty->getTemplateVars('messages');
		$smarty->assign('messages_count', is_array($messages) ? count($messages) : 0);

	}
    
	private function _set_controller_path($module = NULL, $_method = NULL)
	{
		if (empty($module)) {
			$_ACTION_CONTROLLER = APP_ROOT_PATH . '/actions/' . $this->get('_ACTION_COMMAND') . '/template/Smarty/' . $_method;
			$this->set('_ACTION_CONTROLLER', $_ACTION_CONTROLLER);
		} else {
			$_ACTION_CONTROLLER = XIMDEX_ROOT_PATH . \Ximdex\Modules\Manager::path($module) . '/actions/' . $this->get('_ACTION_COMMAND') . '/template/Smarty/' . $_method;
			$this->set('_ACTION_CONTROLLER', $_ACTION_CONTROLLER);
		}
	}

	/**
	 * Smarty prefilter plugin
	 * Identify widgets tags, for each tag replaces it with the associated template
	 * and includes dependencies (javascript, css, etc...)
	 * 
	 * @param $source
	 * @param $smarty
	 */
	public function prefilter_widgets($source, &$smarty)
	{
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
			return $source;
		}
		$smarty->append('js_files', $ret['js_files'], true);
		$smarty->append('css_files', $ret['css_files'], true);
		return $ret['tpl'];
	}
}
