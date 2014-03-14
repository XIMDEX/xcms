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



require_once (XIMDEX_ROOT_PATH . '/inc/widgets/Widget_Abstract.class.php');
ModulesManager::file('/services/Xowl/OntologyService.class.php');
ModulesManager::file('/inc/RelTagsNodes.inc', 'ximTAGS');

class Widget_tagsinput extends Widget_Abstract {

	public function __construct() {
		if(! ModulesManager::isEnabled("ximTAGS") ) {
			$this->setEnable(false);
		}

		parent::__construct();
	}

	public function process($params) {


		if("true" == $params["initialize"]) {
			$relTags = new RelTagsNodes();
			$params["tags"] = json_encode($relTags->getTags($params["_enviroment"]["id_node"]));
		}

		$node = new Node($params["_enviroment"]["id_node"]);
		$params["isStructuredDocument"] = $node->nodeType->get('IsStructuredDocument');

		if(array_key_exists("editor", $params ) ) {
			$this->setTemplate("tagsinput_editor");
		}

		$params['namespaces'] = json_encode($this->getAllNamespaces());


		return parent::process($params);
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

}

?>
