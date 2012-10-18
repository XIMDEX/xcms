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



if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));
}

require_once(XIMDEX_ROOT_PATH . "/inc/patterns/Factory.class.php");
ModulesManager::file('/inc/model/XimNewsBulletins.php', 'ximNEWS');
require_once(XIMDEX_ROOT_PATH . '/inc/db/db.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/model/RelStrDocChannels.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/structureddocument.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Abstract_View.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Interface_View.class.php');

class View_SQL extends Abstract_View implements Interface_View {
	function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {
		
		$content = $this->retrieveContent($pointer);
		$version = new Version($idVersion);

		if (!($version->get('IdVersion') > 0)) {
			XMD_Log::error("Se ha cargado una versión incorrecta ($idVersion)");
			return NULL;
		}

		$node = new Node($version->get('IdNode'));

		if (!($node->get('IdNode') > 0)) {
			XMD_Log::error("El nodo que se está intentando convertir no existe: " . $version->get('IdNode'));
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
			case 'XimNewsNewLanguage':

				$deleteQuery='';
				$insertQuery='';
				//pass arguments by reference
				$this->generateQueryForColectorInfo($deleteQuery,$insertQuery,$nodeId);

				$deleteQuery .= "DELETE FROM XimNewsNews WHERE IdNew = $nodeId;
				DELETE FROM XimNewsCache WHERE IdNew = $nodeId;
				DELETE FROM RelStrDocChannels WHERE IdDoc=$nodeId;
				DELETE FROM RelNewsArea WHERE IdNew = $nodeId;
				DELETE FROM XimNewsAreas;";

				$condition = 'IdNew = %s ';
				$params = array('IdNew' => $nodeId);
				$insertQuery .= $this->makeInsertQuery('XimNewsNews', $condition, $params);
				$insertQuery .= $this->makeInsertQuery('RelNewsArea', $condition, $params);

				$condition ='';
				$params= array();
				$insertQuery .= $this->makeInsertQuery('XimNewsAreas', $condition, $params);


				$condition = 'IdDoc = %s';
				$params = array('IdDoc' => $nodeId);
				$insertQuery .= $this->makeInsertQuery('RelStrDocChannels', $condition, $params);

				$sql = $deleteQuery . $insertQuery;
				

				break;
			case 'XmlDocument':
				$st = new StructuredDocument($nodeId);
				$lang = $st->get('IdLanguage');
				
				//get the section for this xmldocument by fastTraverse
		

				$s = "select n.IdNode,n.Name n.IdParent from FastTraverse as f inner join Nodes as n on f.IdNode = n.IdNode";
				$s .= " where f.IdChild = $nodeId and n.IdNodeType = 5015;";
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
	 * Generate all info for news' colector
	 *
	 * @param String $deleteQuery
	 * @param String $insertQuery
	 * @param String $nodeId
	 */
	private function generateQueryForColectorInfo(&$deleteQuery,&$insertQuery,$nodeId){

		$deleteQuery .= "DELETE FROM RelNewsColector WHERE IdNew = $nodeId;";

		//Make the RelNewsColector for nodeId new
		$condition = 'IdNew = %s';
		$params = array('IdNew' => $nodeId);
		$insertQuery .= $this->makeInsertQuery('RelNewsColector', $condition, $params);


		$sql = "select IdColector from RelNewsColector where IdNew = $nodeId";
		$colectors = array();
		$i=0;
		$insertGlobalInfo=false;

		//Work with each colector for this news
		$dbObj = new DB();
		$dbObj->Query($sql);
		while(!$dbObj->EOF) {
			$colectors[$i] = $dbObj->GetValue("IdColector");
			$i++;
			$dbObj->Next();
		}
		
		$pvdArray = array();
		
		if (count($colectors)>0){
			foreach($colectors as $indice => $colectorId) {
				
				//Create the ximNewsCache if it's necessary
				$colector = new XimNewsColector($colectorId);
				$xslFile = $colector->get('XslFile');
				$pvdTemplate = $colector->get('IdTemplate');

				if (!in_array($pvdTemplate, $pvdArray)){
					$ximCache = new XimNewsCache();
					$df= new DataFactory($nodeId);
					$idVersion = $df->GetLastVersionId();
					if (!$ximCache->CreateCache($nodeId,$idVersion,$pvdTemplate,$xslFile)){
						XMD_Log::error("No se ha creado ximnewsCache para la noticia $nodeId");
					}
					
					//add pvd to the pvdArray
					array_push($pvdArray, $pvdTemplate);
				}

				$deleteQuery .= "DELETE FROM XimNewsColector WHERE IdColector = $colectorId;";
				$condition = 'IdColector = %s';
				$params = array('IdColector' => $colectorId);
				$insertQuery .= $this->makeInsertQuery('XimNewsColector', $condition, $params);

				if ($this->isNeedToSendInfoColector($colectorId)){
					//This info have to be insert one time only
					if (!$insertGlobalInfo){
						$deleteQuery .= "DELETE FROM Channels;DELETE FROM Languages;DELETE FROM Sections;";
						$insertQuery .= $this->makeInsertQuery('Channels', '', 'NULL');
						$insertQuery .= $this->makeInsertQuery('Languages', '', 'NULL');

						//Add the Relation between Sections and colectors,  nodetype = XimNewsSection
						$sql = "select IdNode, IdParent, Name from Nodes where IdNodeType = 5300";
						$dbObj = new DB();
						$dbObj->Query($sql);

						while(!$dbObj->EOF) {
							$IdSection = $dbObj->GetValue("IdNode");
							$IdParent = $dbObj->GetValue("IdParent");
							$Name = $dbObj->GetValue("Name");
							$insertQuery .= "INSERT INTO Sections values ($IdSection, $IdParent, '$Name');";

							$dbObj->Next();
						}
						$insertGlobalInfo = true;
					}

					//Create Insert Query
					$condition = 'IdColector = %s';
					$params = array('IdColector' => $colectorId);

					$sql = "select IdNode from Nodes as N inner join XimNewsColector as X on N.IdParent = X.IdXimlet where X.IdColector=$colectorId;";
					$dbObj = new DB();
					$dbObj->Query($sql);
					$idXimlet = 0;
					if ($dbObj->numRows > 0) {
						$idXimlet = $dbObj->GetValue("IdNode");
					}

					if ($idXimlet > 0){
						$condition = 'IdDoc = %s';
						$params = array('IdDoc' => $idXimlet);

						$deleteQuery .= "DELETE FROM RelStrDocChannels WHERE IdDoc = $idXimlet;";
						$insertQueryBefore = $this->makeInsertQuery('RelStrDocChannels', $condition, $params);
						$insertQuery .= str_replace($idXimlet,$colectorId,$insertQueryBefore);
					}
					
					//change the node properties for the colector
					$this->changeNodeProperties($colectorId);

				}else{
					XMD_Log::info("Is not sended the colector info for the new $nodeId in OTF");
				}
			}
		}else{
			XMD_Log::error("Don't found colectors for idNew $nodeId");
		}
	}
	/**
	 * Check the nodeProperty 'isInfoColectorOTFSended'
	 * This propertie can be 'true' or 'false'
	 *
	 * @param int $colectorID
	 * @return boolean sendInfo
	 */
	private function isNeedToSendInfoColector($colectorID){
		$isSended = $this->checkBooleanProperty('isInfoColectorOTFSended',$colectorID);

		if ($isSended){
			return false;
		}else{
			return true;
		}

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
	/**
	 * change the node properties for the colector
	 * isInfoColectorOTFSended = true
	 * forceSendInfoColectorOTF = false
	 *
	 * @param int $colectorId
	 */
	private function changeNodeProperties($colectorId){
		$node = new Node($colectorId);
		$node->setProperty('isInfoColectorOTFSended','true');
		unset($node);
	}

	public function makeInsertQuery($tableName, $condition, $params) {

		$insertQuery = '';

		$factory = new Factory(XIMDEX_ROOT_PATH . "/inc/model/orm/", $tableName);
		$object = $factory->instantiate("_ORM");

		if (!is_object($object)) {
			XMD_Log::error("Error, la clase de orm especificada no existe");
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
?>
