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











class Action_preview extends ActionAbstract {
   // Main method: shows initial form
    function index () {
      	$idNode		= (int) $this->request->getParam("nodeid");
		$params = $this->request->getParam("params");


		$node = new Node($idNode);
		$data = new DataFactory($idNode);
		$version = $data->GetLastVersion();
		$subVersion=$data->GetLastSubVersion($version);
		setlocale (LC_TIME, "es_ES"); 
		$date = strftime("%a, %d/%m/%G %R", $data->GetDate($version,$subVersion)); 
		$user = new User($data->GetUserID($version,$subVersion)); 
		$userName = $user->GetRealName();


		if (($node->nodeType->GetName()!='TextFile') 
			&& ($node->nodeType->GetName()!='ImageFile') 
			&& ($node->nodeType->GetName()!='BinaryFile') 
			&& ($node->nodeType->GetName()!='NodeHt')) {
			$titulo_canal="canal";
		}else {
			$titulo_canal = '';
		}

		$doc = new StructuredDocument($idNode);
		$channelList = $doc->GetChannels();
		$nod = new Node();
		$_channels = array();
		if(count($channelList)) {
			foreach($channelList as $channel) {
				$nod->SetID($channel);
				$_channels[] = array("Id" => $channel, "Name" =>$nod->GetNodeName() );
			}
		}

		$queryManager = new QueryManager();
		$this->addJs('/actions/preview/resources/js/preview.js');
		$values = array(
			'id_node' => $idNode,
			'params' => $params,
			'version' => $version,
			'subversion' => $subVersion,
			'titulo_canal' => $titulo_canal,
			'nameNodeType' => $node->nodeType->GetName(),
			'date' => $date,
			'user_name' => $userName,
			'channels' => $_channels,
			"nodeURL" => $queryManager->getPage() . $queryManager->build(),
			"go_method" => "preview",
		);

		$this->render($values, null, 'default-3.0.tpl');
    }

	function preview() {
      	$idNode		= (int) $this->request->getParam("nodeid");
		$params = $this->request->getParam("params");
		
		if (!is_null($this->request->getParam("version")) 
			&& !is_null($this->request->getParam("subVersion")) 
			&& is_null($this->request->getParam("delete"))) 	{ 
			// If it is a recovery of a version, first it recovers it and then shows the form
			$version	= $this->request->getParam("version");
			$subVersion	= $this->request->getParam("subVersion");
			$data		= new DataFactory($idNode);
			$data->RecoverVersion($version,$subVersion);
			$this->messages->add(_("Se ha recuperado corréctamente el archivo"), MSG_TYPE_NOTICE);

		}
		elseif (!is_null($this->request->getParam("delete"))) {
			$version	= $this->request->getParam("version");
			$subVersion	= $this->request->getParam("subVersion");
			$data		= new DataFactory($idNode);
			$data->DeleteSubversion($version,$subVersion);
			$this->messages->add(_("Se ha eliminado corréctamente el archivo"), MSG_TYPE_NOTICE);
		}

		$queryManager = new QueryManager();
		$values = array(
			'messages' => $this->messages->messages,
			'id_node' => $idNode,
			'params' => $params,
			"nodeURL" => $queryManager->getPage() . $queryManager->build() 
		);

		$this->render($values);
	}
}
?>
