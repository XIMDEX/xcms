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


ModulesManager::file('/inc/db/db.inc');
ModulesManager::file('/inc/nodetypes/statenode.inc');
ModulesManager::file('/inc/workflow/Workflow.class.php');

class Action_checkstatus extends ActionAbstract {
	//Main method: shows initial form
    	function index () {
    		$idNode = $this->request->getParam('nodeid');
    		$node = new Node($idNode);
    		
		if (!$node->get('IdNode') > 0) {
    			$this->messages->add(_('Node could not be found'), MSG_TYPE_ERROR);
			$values = array('messages' => $node->messages->messages);
			$this->render($values, NULL, 'messages.tpl');
			return false;
 		}

		//we obtain the project and the server name to filter on the query.
		$server=new Node($idNode);
		$serverName=$server->GetNodeName();

		$project=new Node($server->getProject());
		$projectName=$project->GetNodeName();

 		$dbObj = new DB();
		$query = "SELECT n.IdState,n.IdNode,n.Path,n.Name,v1.Version, v1.SubVersion,v1.Date FROM Versions v1 INNER JOIN Nodes n USING (IdNode) WHERE n.IdNodetype in (5032,5039,5040,5041,5028) AND n.Path like '%".$projectName."%".$serverName."%' AND NOT v1.SubVersion=0 AND NOT EXISTS (select Idnode from Versions v2 where v2.IdNOde=v1.IdNOde and (v2.Version>v1.Version OR (v1.Version=v2.Version AND v2.SubVersion>v1.Subversion))) ORDER BY n.IdNode";

		$dbObj->query($query);
		$data = array();
		while(!$dbObj->EOF) {
			$data[$dbObj->getValue('IdState')][] = array(
						'Version' => $dbObj->getValue('Version'),
						'SubVersion' => $dbObj->getValue('SubVersion'),
						'Date' => date('d/m/Y H:i', $dbObj->getValue('Date')),
						'Name' => $dbObj->getValue('Name'),
						'Path' => $dbObj->getValue('Path'),
						'isLastVersion' => 'false');
			$dbObj->Next();
		}

		//creates abother array with all de states info.
		$states=array();
		$wf=new Workflow($idNode);
		$states=$wf->GetAllStates();

		foreach($states as $state){
			$ps=new PipeStatus($state);
			$count= isset($data[$state]) ? count($data[$state]) : 0;
			$statesFull[$state] = array(
					'stateName' =>$ps->get('Name'),
					'count' => $count
					);			
		}

		//ksort($data, SORT_NUMERIC);
		//$keys = array_keys($data);
		//$lastVersion = end($keys);

		//ksort($data[$lastVersion], SORT_NUMERIC);
		//$keys = array_keys($data[$lastVersion]);
		//$lastSubversion = end($keys);

		//$data[$lastVersion][$lastSubversion]['isLastVersion'] = 'true';

		$this->addJs('/actions/checkstatus/resources/js/index.js');
		$this->addCss('/actions/checkstatus/resources/css/index.css');

		$values = array('files' => $data,
				'statesFull'=> $statesFull,
	//			'isStructuredDocument' => $isStructuredDocument,
				'id_node' => $idNode,
	//			'node_type_name' => $node->nodeType->get('Name'),
	//			'channels' => $channels,
				'actionid' => $this->request->getParam('actionid')
				);

		$this->render($values, null, 'default-3.0.tpl');
	}

	function getPublicationQueue () {
		$frames = new ServerFrame();
		$values = array();
		$etag = null;
		$etag = $this->request->getParam('etag');
		$values['publications'] = $frames->getPublicationQueue();
		$this->sendJSON_cached($values, $etag);
	}

    /*function recover() {
    	$idNode = $this->request->getParam('nodeid');
    	$version = $this->request->getParam('version');
		$subVersion	= $this->request->getParam('subversion');

    	if (!is_null($version) && !is_null($subVersion)) {
			//If it is a recovery of a version, first we recover it and then we show the form
			$data = new DataFactory($idNode);
			$ret = $data->RecoverVersion($version, $subVersion);
			if ($ret === false) {
				$this->render(array(
					'messages' => array(array(
						'type' => $data->numErr,
						'message' => $data->msgErr
					)),
					'goback' => true
				));
				return;
			}
		}

    	$this->redirectTo('index');
    }*/

    /*function delete() {
    	$idNode = $this->request->getParam('nodeid');
    	$version = $this->request->getParam('version');
		$subVersion	= $this->request->getParam('subversion');
    	if (!is_null($version) && !is_null($subVersion)) {
			$data = new DataFactory($idNode);
			$ret = $data->DeleteSubversion($version,$subVersion);
			if ($ret === false) {
				$this->render(array(
					'messages' => array(array(
						'type' => $data->numErr,
						'message' => $data->msgErr
					)),
					'goback' => true
				));
				return;
			}
		}
    	$this->redirectTo('index');
    }*/
}
?>
