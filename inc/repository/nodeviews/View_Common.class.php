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




require_once(XIMDEX_ROOT_PATH . '/inc/model/Versions.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/dependencies/LinksManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Abstract_View.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Interface_View.class.php');

class View_Common extends Abstract_View implements Interface_View {
	
	private $_filePath;
	
	function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {
		
		if(!$this->_setFilePath($idVersion, $args))
			return NULL;
		
		if (!is_file($this->_filePath)) {
			XMD_Log::error('VIEW COMMON: Se ha solicitado cargar un archivo inexistente. FilePath: ' . $this->_filePath);
			return NULL;
		}

		if (!array_key_exists('REPLACEMACROS', $args)) {
			return $pointer;
		}

		// Replaces macros in content
		$content = $this->retrieveContent($this->_filePath);

		
		$linksManager = new LinksManager();
		$content = $linksManager->removeDotDot($content);
		$content = $linksManager->removePathTo($content);

		return $this->storeTmpContent($content);
	}
	
	private function _setFilePath ($idVersion = NULL, $args = array()) {
		
		if(!is_null($idVersion)) {
			$version = new Version($idVersion);
			$file = $version->get('File');
			$this->_filePath = XIMDEX_ROOT_PATH . Config::getValue('FileRoot') .'/'. $file;
		} else {
			// Retrieves Params:
			if (array_key_exists('FILEPATH', $args)) {
				$this->_filePath = $args['FILEPATH'];
			}
			// Check Params:
			if (!isset($this->_filePath) || $this->_filePath == "") {
				XMD_Log::error('VIEW COMMON: No se ha especificado la version ni el path del fichero correspondiente al nodo ' . $args['NODENAME'] . ' que quiere renderizar');
				return NULL;
			}
		}
		
		return true;
	}

}
?>
