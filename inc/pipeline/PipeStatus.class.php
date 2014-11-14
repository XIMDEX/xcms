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



if (! defined ( 'XIMDEX_ROOT_PATH' )) {
	define ( 'XIMDEX_ROOT_PATH', realpath ( dirname ( __FILE__ ) . '/../../' ) );
}

require_once (XIMDEX_ROOT_PATH . '/inc/model/orm/PipeStatus_ORM.class.php');
/**
 * 
 * @brief Stores the single status for a PipeTransition
 * 
 * Stores the single status for a PipeTransition, this class provides just a description for the status
 *
 */
class PipeStatus extends PipeStatus_ORM {
	function getIdStatus($name) {
		$id = $this->find ( 'id', 'Name = %s ', array ($name ), MONO );
		if ((count ( $id ) == 1) && ($id [0] > 0)) {
			return $id [0];
		}
		return false;
	}
	
	function loadByIdNode($idNode) {
		$nodes = $this->find('id', 'IdNode = %s', array ($idNode ), MONO);
		if (count($nodes) != 1) {
			$this->messages->add(_('No se ha podido cargar el estado por su id de nodo'), MSG_TYPE_ERROR);
			XMD_Log::error(sprintf("No se ha podido cargar el estado por su id de nodo, se solicitó el idNode %s", print_r($idNode, true)));
			return NULL;
		}
		
		parent::GenericData($nodes[0]);
		return $this->get('id');
	}
	
	function loadByName($name) {
		$nodes = $this->find('id', 'Name = %s', array($name), MONO);
		if (count($nodes) != 1) {
			$this->messages->add(_('No se ha podido cargar el estado por su nombre de nodo'), MSG_TYPE_ERROR );
			XMD_Log::error('No se ha podido cargar el estado por su nombre de nodo');
			return NULL;
		}
		
		parent::GenericData($nodes[0]);
		return $this->get('id');
	}
}
?>