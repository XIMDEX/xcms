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


ModulesManager::file('/actions/workflow_forward/Action_workflow_forward.class.php');
/**
* Move a node to next state. 
* 
* If the node is not a structured document the next state will be publication.
*/
class Action_workflow_forward_advance extends Action_workflow_forward {


	/**
	 *
	 */
	protected function buildFlagsPublication($markEnd, $structure, $deepLevel, $force, $lastPublished){

		//Creating flags to publicate
		$flagsPublication = array(
			'markEnd' => $markEnd,
			'structure' => $structure,
			'deeplevel' => $deepLevel,
			'force' => $force,
			'recurrence' => false,
			'workflow' => true,
			'lastPublished' => $lastPublished
		);

		return $flagsPublication;
	}

}
