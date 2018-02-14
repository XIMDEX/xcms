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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Models\ORM\PipeStatusOrm;

/**
 *
 * @brief Stores the single status for a PipeTransition
 *
 * Stores the single status for a PipeTransition, this class provides just a description for the status
 *
 */
class PipeStatus extends PipeStatusOrm
{
	public function getIdStatus($name)
	{
		$id = $this->find('id', 'Name = %s ', array($name), MONO);
		if ((count($id) == 1) && ($id [0] > 0)) {
		    
			return $id [0];
		}
		return false;
	}

	/**
	 * @param $idNode
	 * @return bool|null|string
	 */
	public function loadByIdNode($idNode)
	{
		$nodes = $this->find('id', 'id = %s', array($idNode), MONO);
		if (count($nodes) != 1) {
		    
			$this->messages->add(_('No se ha podido cargar el estado por su id de nodo'), MSG_TYPE_ERROR);
			Logger::error(sprintf("No se ha podido cargar el estado por su id de nodo, se solicitó el idNode %s", print_r($idNode, true)));
			return NULL;
		}

		parent::__construct($nodes[0]);
		return $this->get('id');
	}

	/**
	 * @param $name
	 * @return bool|null|string
	 */
	public function loadByName($name)
	{
		$nodes = $this->find('id', 'Name = %s', array($name), MONO);
		if (count($nodes) != 1) {
		    
			$this->messages->add(_('No se ha podido cargar el estado por su nombre de nodo'), MSG_TYPE_ERROR);
			Logger::error('No se ha podido cargar el estado por su nombre de nodo');
			return NULL;
		}

		parent::__construct($nodes[0]);
		return $this->get('id');
	}
}