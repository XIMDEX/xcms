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

ModulesManager::file('/inc/Tags.inc', 'ximTAGS');
ModulesManager::file('/inc/RelTagsNodes.inc', 'ximTAGS');
ModulesManager::file('/services/Xowl/OntologyService.class.php');

class Action_setmetadata extends ActionAbstract {

	function index() {
   		$this->addCss('/xmd/style/jquery/ximdex_theme/widgets/tagsinput/tagsinput.css');
   		$this->addCss('/inc/widgets/select/css/ximdex.select.css');
   		$this->addJs('/inc/widgets/select/js/ximdex.select.js');
		$this->addJs('/actions/setmetadata/resources/js/setmetadata.js','ximTAGS');
		$this->addCss('/actions/setmetadata/resources/css/setmetadata.css','ximTAGS');

	 	$idNode	= (int) $this->request->getParam("nodeid");
		$actionID = (int) $this->request->getParam("actionid");
		$params = $this->request->getParam("params");
		$tags = new Tag();
		$max=$tags->getMaxValue();		

		$cloud_tags = array();
		$cTags = $tags->getTags();
		
        if(count($cTags)>0){
		    foreach ($cTags as $tag) {
  			    $array = array(
  					"IdTag"=>(int)$tag["IdTag"],
  					"Name"=>utf8_encode($tag["Name"]),
  					"IdNamespace"=>(int)$tag["IdNamespace"]
			    );
  			    $cloud_tags[] = $array;
  		    }
        }

	 	$values = array(
	 		'cloud_tags' => json_encode($cloud_tags),
			'max_value' => $max[0][0],
			'id_node' => $idNode,
			'go_method' => 'save_metadata',
			'nodeUrl' => Config::getValue('UrlRoot')."/xmd/loadaction.php?actionid=$actionID&nodeid=$idNode",
			'namespaces' => json_encode($this->getAllNamespaces())
		);

	 	//Get the actual tags of the document
	 	$relTags = new RelTagsNodes();
	 	$values["tags"] = json_encode($relTags->getTags($idNode));
	 	//error_log(print_r($relTags->getTags($idNode)));
	 	$node = new Node($idNode);
	 	$values["isStructuredDocument"] = $node->nodeType->get('IsStructuredDocument');

		$this->render($values, 'index', 'default-3.0.tpl');
  	}

	/**
	*<p>Get Xowl related terms from content. It's just for structuredDocument </p>
	*/
	public function getRelatedTagsFromContent(){

		$idNode	= (int) $this->request->getParam("nodeid");
		$result = array();
		$node = new Node($idNode);
		$result = array();
		if ($node->nodeType->get('IsStructuredDocument')){
			$content = $node->GetContent();
			$result = $this->getRelatedTags($content);
		}
		$this->sendJson($result);
	}

	/**
	*<p>Get a json string with related terms from $content param</p>
	*@param $content string with text to search terms.
	*@return false if error, a json string otherwise.
	*/
	private function getRelatedTags($content){
				
		$ontologyService = new OntologyService("semantic");
		return $ontologyService->suggest($content);
	}

	private function getAllNamespaces(){
  		$result = array();
  		//Load from Xowl Service
  		$namespacesArray = OntologyService::getAllNamespaces();
  		//For every namespace build an array. This will be a json object
  		foreach ($namespacesArray as $namespace) {
  			$array = array(
  					"id"=>$namespace->get("idNamespace"),
  					"type"=>$namespace->get("type"),
  					"isSemantic"=>$namespace->get("isSemantic"),
  					"nemo"=>$namespace->get("nemo"),
  					"category"=>$namespace->get("category"),
  					"uri"=>$namespace->get("uri")
				);

  			$result[] = $array;
  		}
  		return $result;		
	}
  	
	/**
	*<p>Return all ontolgyTypes and mnemo from Namespaces table</p>
	*<p>The syntax for the json returned is: </p>
	*<code>   {"nemo1":{
	*			type:"type1",
	*			isSemantic:"isSemantic"
	*		 },
	*	     ...
	*	}
	*</code>
  	*/
  	function loadAllNamespaces(){
  		//Sending json from result array
		$this->sendJSON($this->getAllNamespaces());
  	}

  	function save_metadata() {
   		$idNode	= (int) $this->request->getParam("nodeid");

   		$tags = new RelTagsNodes();
   		$previous_tags = $tags->getTags($idNode);


   		$request_content = file_get_contents("php://input");
		$data = json_decode($request_content);
   		if (array_key_exists('tags', $data)){
   			$tags->saveAll($data->tags, $idNode, $previous_tags);
   		}
		$this->messages->add(_("All the tags have been properly associated."), MSG_TYPE_NOTICE);
		$values = array(
			'messages' => $this->messages->messages,
		);

		$this->sendJSON($values);
 	}

	public function getLocalOntology(){
	   $ontologyName = $this->request->getParam("ontologyName");
	   $format = $this->request->getParam("inputFormat");
	   if (!$format)
	       $format = "json";
		
       $ontologyPath = Config::GetValue("AppRoot")."/modules/ximTAGS/ontologies/{$format}/{$ontologyName}";
	   $content = "";
	   if (file_exists($ontologyPath)){
			$content = FsUtils::file_get_contents($ontologyPath);
	   }
	
	   header('Content-type: application/json');
	   print ($content);
	   exit();
	}
}

?>
