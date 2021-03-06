<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

namespace Ximdex\Nodeviews;

use Ximdex\Logger;
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Models\StructuredDocument;
use Ximdex\Models\Version;

class ViewSQL extends AbstractView
{
    /**
     * {@inheritDoc}
     * @see \Ximdex\Nodeviews\AbstractView::transform()
     */
    public function transform(int $idVersion = null, string $content = null, array $args = null)
    {
		$version = new Version($idVersion);
		if (! $version->get('IdVersion')) {
			Logger::error("Se ha cargado una versión incorrecta ($idVersion)");
			return false;
		}
		$node = new Node($version->get('IdNode'));
		if (! $node->get('IdNode')) {
			Logger::error("El nodo que se está intentando convertir no existe: " . $version->get('IdNode'));
			return false;
		}
		$content = $content . '<sql_content>' . $this->getSQLContent($node->get('IdNode'), $args) . '</sql_content>';
		return $content;
	}

	private function getSQLContent(int $nodeId, array $args = null) : string
	{
		$node = new Node($nodeId);
		$nodeType = new NodeType($node->get('IdNodeType'));
		$nodeTypeName = $nodeType->get('Name');
		$sql = '';
		switch($nodeTypeName) {
			case 'XmlDocument':
				$st = new StructuredDocument($nodeId);
				$lang = $st->get('IdLanguage');
				
				// Get the section for this xmldocument by fastTraverse
				$s = "select n.IdNode,n.Name n.IdParent from FastTraverse as f inner join Nodes as n on f.IdNode = n.IdNode";
				$s .= " where f.IdChild = $nodeId and n.IdNodeType = " . \Ximdex\NodeTypes\NodeTypeConstants::SECTION;
				$result = $node->query($s);
				if (! is_null($result)){
					$idSection = $result[0]['IdNode'];
					$idParent  = $result[0]['IdParent'];
					$name = $result[0]['Name'];
					$sql = "insert into Sections values ($idSection, $idParent, $name);";
					$sql .= "insert into XimDocs values ($nodeId, $idSection, '".$node->get('Name')."',$lang);";
				}
				break;
			default:
			    
				// Do nothing 
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
	private function checkBooleanProperty(string $nodeProperty, int $idNode) : bool
	{
		$node = new Node($idNode);
		$isProperty = $node->getProperty($nodeProperty);
		if (! (is_array($isProperty) && $isProperty[0] == "true")) {
			$isProperty = false;
		} else {
			$isProperty = true;
		}
		return $isProperty;
	}

	public function makeInsertQuery(string $tableName, string $condition = null, array $params = []) : string
	{
		$insertQuery = '';
		$factory = new \Ximdex\Utils\Factory(XIMDEX_ROOT_PATH . "/src/Models/", $tableName);
		$object = $factory->instantiate(null, null, '\Ximdex\Models');
		if (! is_object($object)) {
			Logger::error("Error, la clase de orm especificada no existe");
			return null;
		}
		$result = $object->find(ALL, $condition, $params, MULTI);
		if ($result != null){
			$object->returnQuery = true;
			foreach ($result as $data) {
				$object->loadFromArray($data);
				$query = $object->add();
				$insertQuery .= $query . ";";
			}
		}
		return $insertQuery;
	}
}
