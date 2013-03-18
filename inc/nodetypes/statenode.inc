<?php

/******************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    							*
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 * @version $Revision: $                                                      *  
 *                                                                            *
 *                                                                            *
 ******************************************************************************/










if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

require_once(XIMDEX_ROOT_PATH . '/inc/pipeline/PipeStatus.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/pipeline/PipeTransition.class.php');
require_once(XIMDEX_ROOT_PATH . "/inc/nodetypes/root.inc");

class StateNode extends Root {

	function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = null, $description='', $idTransition=null) {
		$currentTransition = new PipeTransition($idTransition);
		if (!$currentTransition->get('id') > 0) {
			$this->messages->add(_('No se ha podido encontrar la transacción, consulte con su administrador'), MSG_TYPE_ERROR);
			$this->messages->mergeMessages($currentTransition->messages);
			return NULL;
		}

		$this->UpdatePath();
	}

	function DeleteNode() {

		$pipeStatus = new PipeStatus();
		$pipeStatus->loadByIdNode($this->nodeID);

		$pipeProcess = new PipeProcess();
		$pipeProcess->loadByName('workflow');

		$pipeProcess->removeStatus($pipeStatus->get('id'));
	}

	function RenameNode($name) {
		$pipeStatus = new PipeStatus();
		$pipeStatus->loadByIdNode($this->nodeID);
		$pipeStatus->set('Name', $name);
		$pipeStatus->update();
		$this->UpdatePath();
	}

	function CanDenyDeletion() {
		$pipeStatus = new PipeStatus();
		$pipeStatus->loadByIdNode($this->nodeID);

		$pipeProcess = new PipeProcess();
		$pipeProcess->loadByName('workflow');

		$idStatus = $pipeStatus->get('id');

		if ($pipeProcess->isStatusFirst($idStatus) || $pipeProcess->isStatusLast($idStatus)) {
			$this->messages->add(_('No se pueden eliminar los estados primero y último del workflow'), MSG_TYPE_ERROR);
			XMD_Log::warning('Imposible eliminar estado primero y último de workflow');
			return true;
		}

		return false;
	}

	function GetDependencies() {
		$sql ="SELECT DISTINCT IdNode FROM Nodes WHERE IdState='".$this->nodeID."'";
		$this->dbObj->Query($sql);

		$deps = array();

		while(!$this->dbObj->EOF) {
			$deps[] = $this->dbObj->row["IdNode"];
			$this->dbObj->Next();
		}
    	return $deps;
	}
}

?>
