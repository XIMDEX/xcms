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
 *  @version $Revision: 7740 $
 */



require_once(XIMDEX_ROOT_PATH . '/inc/xvfs/backends/XVFS_Backend_interface.class.php');

/**
*  @brief Abstract class for build the XVFS_Backend classes.
*/

abstract class XVFS_Backend implements Backend_XVFS_interface{

	/**
	 *  URI which identified the backend general properties.
	 */
	var $_uri;

	/**
	 *  base path from the backend.
	 */
	var $_be_base;

	/**
	 *  vfs mount point of the backend.
	 */
	var $_vfs_base;
	var $lastError;
	

	/**
	 * Constructor.
	 * Recibe como parametro un array con el punto de montaje, la ruta fisica
	 * del recurso y el tipo de backend.
	 * 
	 * @param array uri
	 */
	public function XVFS_Backend($uri) {
		$this->_uri = $uri;
		if (is_array($uri)) {
			if (isset($this->_uri['path'])) $this->_be_base = XVFS::normalizePath($this->_uri['path']);
			if (isset($this->_uri['vfspath'])) $this->_vfs_base = XVFS::normalizePath($this->_uri['vfspath']);
		}
	}

	// General Utils
	
	/**
	 * Devuelve el punto de montaje del backend
	 */
	public function getMountPoint() {
		return $this->_vfs_base;
	}
	
	/**
	 * Obtiene el path en el backend a partir de una ruta fisica
	 * 
	 * @param string path Ruta fisica
	 * @return string La ruta en el backend
	 */
	public function pathInBackend($path) {
//		$bpath = XVFS::normalizePath($path);
		$bpath = $path;
//		logdump(0, $bpath, "{$this->_be_base} - {$this->_vfs_base}");
		
		$be_base = str_replace('/', '\/', $this->_be_base);
	
		$bpath = preg_replace("/^($be_base){1}/", $this->_vfs_base, $bpath, 1);
//		logdump(1, $bpath);
		
		$bpath = XVFS::normalizePath($bpath);
//		logdump(2, $bpath);
		
		return $bpath;
	}
	
	/**
	 * Obtiene el path real de un recurso a partir del path en el backend
	 * 
	 * @param string bpath Path en el backend
	 * @return string
	 */
	public function realPath($bpath) {
		$path = XVFS::normalizePath($bpath);
		$vfs_base = $this->_vfs_base;
		$path = preg_replace("#^$vfs_base#", $this->_be_base . '/', $path, 1);
		$path = XVFS::normalizePath($path);
		return $path;
	}
}

?>
