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



require_once XIMDEX_ROOT_PATH . '/inc/xvfs/XVFS_Entity.class.php';

/**
*  @brief Represents XVFS files as entities.
*/

class XVFS_Entity_File extends XVFS_Entity {
	
	/**
	 * Constructor. Necesita como parametro el path en el backend.
	 * 
	 * @param string bpath Backend path
	 */
	function XVFS_Entity_File($bpath=null) {
		
		parent::XVFS_Entity($bpath);
    	
		// Si el nodo existe y no es un fichero se limpia la estructura
		if ($this->get('exists') && !$this->get('isfile')) $this->clear();
		// Si no existe indico que es un fichero
		if (!$this->get('exists')) $this->set('isfile', true);
	}
	
}

?>
