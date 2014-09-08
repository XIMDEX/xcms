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



require_once(XIMDEX_ROOT_PATH . '/inc/mvc/renderers/AbstractRenderer.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');

/**
 * 
 * @brief Renderer to eval and execute a php script
 * 
 * Renderer to eval and execute a php script
 *
 */
class PHPRenderer extends AbstractRenderer {

	/**
	 * (non-PHPdoc)
	 * @see inc/mvc/renderers/AbstractRenderer#render($view)
	 */
	function render() {
		//tomamos todos los datos comunes a todos los renders
		parent::render();

		$template = $this->getTemplate();

		if (!isset($template)) {
			return NULL;
		}

		$parameters = $this->getParameters();

		$php_code = '';

		foreach ($parameters as $varName => $data) {
                        if(!is_numeric($data)){
                                $pos1=strpos($data,'"');
                                $pos2=strpos($data,"'");
                                if(($pos1===false) && ($pos2===false)){
                                        $data="'".$data."'";
                                }
                        }
                        $php_code .= "\$$varName = $data;";
                }		

		ob_start();
		eval($php_code);
		require($this->getTemplate());
		$content = ob_get_clean();

		return $content;
	}
}
?>