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

require_once(XIMDEX_ROOT_PATH . '/inc/pipeline/Pipeline.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/pipeline/PipeProcess.class.php');
require_once(XIMDEX_ROOT_PATH . "/inc/nodetypes/root.inc");

class workflow_process extends Root {

	function CreateNode($name = null, $parentID = null, $nodeTypeID = null) {
		$pipeline = new Pipeline();
		$pipeline->set('Pipeline', $name);
		$pipeline->set('IdNode', $this->parent->get('IdNode'));
		$pipeline->add();

		$pipeline->initialize();

		if ($pipeline->messages->count() > 0) {
			$this->parent->messages->mergeMessages($pipeline->messages->messages);
		}

		$this->UpdatePath();
	}

	function DeleteNode() {
		$pipeline = new Pipeline();
		$result = $pipeline->loadByIdNode($this->parent->get('IdNode'));
		if (!$result) {
			$this->parent->messages->mergeMessages($pipeline->messages->messages);
			return false;
		}

		return $pipeline->delete();
	}

	function RenameNode($name = null) {
		$pipeline = new Pipeline();
		$result = $pipeline->loadByIdNode($this->parent->get('IdNode'));
		if (!$result) {
			$this->parent->messages->mergeMessages($pipeline->messages->messages);
		}

		$pipeline->set('Name', $name);
		$ret = $pipeline->update();
		$this->UpdatePath();

		return $ret;
	}

	function GetDependencies() {
		$pipeline = new Pipeline();
		$result = $pipeline->loadByIdNode($this->parent->get('IdNode'));
		if (!$result) {
			$this->parent->messages->mergeMessages($pipeline->messages->messages);
		}

		$allStatus = array();
		while ($process = $pipeline->processes->next()) {
			while ($transition = $process->transitions->next()) {
				$allStatus[] = $transition->get('IdStatusFrom');
				$allStatus[] = $transition->get('IdStatusTo');
			}
		}

		$node = new Node();
		$dependencies = array();
		foreach ($allStatus as $idStatus) {
			$result = $node->find('IdNode', 'IdStatus = %s', array($idStatus));
			array_merge($dependencies, $result);
		}

		return $dependencies;
	}
}

?>
