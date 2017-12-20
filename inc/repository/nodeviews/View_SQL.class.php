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


use Ximdex\Logger;
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Models\StructuredDocument;
use Ximdex\Models\Version;


require_once(XIMDEX_ROOT_PATH . '/inc/model/RelStrDocChannels.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Abstract_View.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Interface_View.class.php');

class View_SQL extends Abstract_View implements Interface_View {
	function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {
		
		$content = $this->retrieveContent($pointer);
		$version = new Version($idVersion);

		if (!($version->get('IdVersion') > 0)) {
			Logger::error("Se ha cargado una versión incorrecta ($idVersion)");
			return NULL;
		}

		$node = new Node($version->get('IdNode'));

		if (!($node->get('IdNode') > 0)) {
			Logger::error("El nodo que se está intentando convertir no existe: " . $version->get('IdNode'));
			return NULL;
		}

		$content = $content . '<sql_content>' . $this->getSQLContent($node->get('IdNode'), $args) . '</sql_content>';

		return $this->storeTmpContent($content);
	}

	function getSQLContent($nodeId, $args) {
		$node = new Node($nodeId);
		$nodeType = new NodeType($node->get('IdNodeType'));
		$nodeTypeName = $nodeType->get('Name');

		$sql='';
		switch($nodeTypeName) {
			case 'XmlDocument':
				$st = new StructuredDocument($nodeId);
				$lang = $st->get('IdLanguage');
				
				//get the section for this xmldocument by fastTraverse
		

				$s = "select n.IdNode,n.Name n.IdParent from FastTraverse as f inner join Nodes as n on f.IdNode = n.IdNode";
				$s .= " where f.IdChild = $nodeId and n.IdNodeType = " . \Ximdex\NodeTypes\NodeType::SECTION;
				$result = $node->query($s);
				if (!is_null($result)){
					$idSection = $result[0]['IdNode'];
					$idParent  = $result[0]['IdParent'];
					$name = $result[0]['Name'];

					$sql = "insert into Sections values ($idSection, $idParent, $name);";
					$sql .= "insert into XimDocs values ($nodeId, $idSection, '".$node->get('Name')."',$lang);";
				}
		
				$sql .= $this->makeInsertQuery('RelStrDocChannels', 'IdDoc = %s', array('IdDoc' => $nodeId));	

				break;
			default:
				//Do nothing 
				break;
		}
		return $sql;
	}


	/**
	 * Check if a boolean property is true or false
	 *
	 * @param string $nodeProperty
	 * @param int $idNode
	 * @return boolean
	 */
	private function checkBooleanProperty($nodeProperty, $idNode){

		$n = new Node($idNode);
		$isProperty = $n->getProperty($nodeProperty);
		if(!((is_array($isProperty)) && ($isProperty[0]=="true"))){
			$isProperty = false;
		}else{
			$isProperty = true;
		}
		unset($n);

		return $isProperty;
	}


	public function makeInsertQuery($tableName, $condition, $params) {

		$insertQuery = '';

		$factory = new \Ximdex\Utils\Factory(XIMDEX_ROOT_PATH . "/inc/model/orm/", $tableName);
		$object = $factory->instantiate("_ORM");

		if (!is_object($object)) {
			Logger::error("Error, la clase de orm especificada no existe");
			return NULL;
		}

		$result = $object->find(ALL, $condition, $params, MULTI);

		if ($result != null){
			$object->returnQuery = true;
			foreach ($result as $data) {
				$object->loadFromArray($data);
				$query = $object->add();
				$insertQuery .= $query.";";
			}
		}

		return $insertQuery;
	}
}