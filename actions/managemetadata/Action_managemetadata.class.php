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



/**
 * Manage metadata action. 
 *
 */
class Action_managemetadata extends ActionAbstract {

	/**
	 * Main function
	 *
	 * Load the manage metadata form.
	 *
	 * Request params:
	 * 
	 * * nodeid
	 * 
	 */
	public function index() {

		//Load css and js resources for action form.
		$this->addJs('/actions/manageproperties/resources/js/index.js');

		$nodeId = $this->request->getParam('nodeid');
		
		$values = array(
			'nodeid' => $nodeId,
			'go_method' => 'update_metadata'
		);

		$this->render($values, '', 'default-3.0.tpl');
	}

	/**
	 * Save the results from the form
	 */
	public function save_metadata() {

		# Add some code here
		
	}


}
?>
