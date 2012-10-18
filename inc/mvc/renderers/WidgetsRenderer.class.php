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




include_once (XIMDEX_ROOT_PATH . '/inc/mvc/renderers/AbstractRenderer.class.php');
include_once (XIMDEX_ROOT_PATH . '/inc/widgets/Widget.class.php');
/**
 * 
 * @brief Renderer for the widget system
 * 
 * Renderer for the widget system 
 *
 */
class WidgetsRenderer extends AbstractRenderer {

	/**
	 * Constructor
	 * @param $fileName
	 * @return unknown_type
	 */
	public function __construct($fileName = NULL) {
		parent::__construct($fileName);
	}

	/**
	 * (non-PHPdoc)
	 * @see inc/mvc/renderers/AbstractRenderer#render($view)
	 */
	public function render($view = NULL) {
		parent::render(null);
		$params = $this->getParameters();
		return $this->process($view, $params);
    }

	/**
	 * Smarty prefilter plugin
	 * Identify widgets tags, for each tag replaces it with the associated template
	 * and includes dependencies (javascript, css, etc...)
	 * @param $source
	 * @param $params
	 * @return unknown_type
	 */
    public function process($source, $params) {

		$ret = Widget::process($source, $params);

		if ($ret === null) {
//			debug::log(' ===> NO WIDGET');
			return $source;
		}

//$ret['tpl'] = '';
//debug::log($ret['tpl']);
		return $ret['tpl'];
	}
}
?>
