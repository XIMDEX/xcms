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


ModulesManager::file('/inc/repository/nodeviews/View_FilterMacros.class.php');

class Action_filemapper extends ActionAbstract {
   // Main method: shows initial form
    function index () {
		if ($this->request->getParam('nodeid')) {
			$idNode = $this->request->getParam("nodeid");
                        $this->echoNode($idNode);
			
		}
    }

    public function nodeFromExpresion(){
    	if ($this->request->getParam('expresion')) {
    		$expresion = $this->request->getParam("expresion");
    		$filterMacro = new View_FilterMacros();
    		$res = $filterMacro->infererNodeAndChannel($expresion);
    		if ($res && is_array($res) && isset($res["idNode"])){
				$idNode = $res["idNode"];			
				$this->echoNode($idNode);		
			}
		}
    }

    private function echoNode($idNode){
    	$fileNode = new Node($idNode);
		$fileName = $fileNode->get('Name');
        $gmDate =  gmdate("D, d M Y H:i:s");
        $fileContent = $fileNode->GetContent();
        
        /// Expiration headers
        $this->response->set('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
        $this->response->set('Last-Modified', $gmDate . " GMT");
        $this->response->set('Cache-Control',
            array('no-store, no-cache, must-revalidate', 'post-check=0, pre-check=0'));
        $this->response->set('Pragma', 'no-cache');
        $this->response->set('ETag', md5($idNode.$gmDate));
        $this->response->set('Content-transfer-encoding', 'binary');
        $this->response->set('Content-type', 'octet/stream');
        $this->response->set('Content-Disposition', "attachment; filename=".$fileName);
        $this->response->set('Content-Length', strlen(strval($fileContent)));
        $this->response->sendHeaders();
        echo $fileContent;
    }
}
?>