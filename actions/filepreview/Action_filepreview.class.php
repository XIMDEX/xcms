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









class Action_filepreview extends ActionAbstract {
   // Main method: shows initial form
    function index () {
		$this->response->set('Cache-Control', 
			array('no-store, no-cache, must-revalidate', 'post-check=0, pre-check=0'));
		$this->response->set('Pragma', 'no-cache');
		
    	$idNode = $this->request->getParam('nodeid');
    	
    	$version = $this->request->getParam('version');
    	$subVersion = $this->request->getParam('sub_version');
    	
    	if (is_numeric($version) && is_numeric($subVersion)) {
    		$dataFactory = new DataFactory($idNode);
    		$selectedVersion = $dataFactory->getVersionId($version, $subVersion);
    	} else {
	    	$dataFactory = new DataFactory($idNode);
	    	$selectedVersion = $dataFactory->GetLastVersionId();
    	}
    	
    	$version = new Version($selectedVersion);
    	$hash = $version->get('File');
    	
		$values = array('id_node' => $idNode,
						'path' => Config::getValue('UrlRoot') . '/data/files/' . $hash);
		$this->render($values, null, 'default-3.0.tpl');
    }
}
?>
