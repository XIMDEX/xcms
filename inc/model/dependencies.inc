<?php

/*********************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    			    	 *
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	    	 *
 *                                                                            					    	 *
 *  This program is free software: you can redistribute it and/or modify        	 *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      	 *
 *  (at your option) any later version.                                       			 *
 *                                                                            						 *
 *  This program is distributed in the hope that it will be useful,           		 *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the *
 *  GNU Affero General Public License for more details.                       		 *
 *                                                                            						 *
 * See the Affero GNU General Public License for more details.                	 *
 * You should have received a copy of the Affero GNU General Public License  *
 * version 3 along with Ximdex (see LICENSE).                                 		 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       		 *
 *                                                                            						 *
 * @version $Revision: $                                                      				 *
 *                                                                            						 *
 *                                                                            						 *
 *********************************************************************************/







if (!defined('XIMDEX_ROOT_PATH')) define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../..');
require_once XIMDEX_ROOT_PATH . '/inc/model/orm/Dependencies_ORM.class.php';

include_once (XIMDEX_ROOT_PATH."/inc/xml/XmlBase.class.php");
include_once (XIMDEX_ROOT_PATH."/inc/xml/XML.class.php");


class dependencies extends Dependencies_ORM{

	/**
	 * Construnctor
	 * @return unknown_type
	 */
	function dependencies()	{
		parent::GenericData();
	}

	/**
	 *
	 * @param $nodoMaestro
	 * @param $nodoDependiente
	 * @param $tipo
	 * @param $version
	 * @return unknown_type
	 */
	function InsertaDependencia($nodoMaestro,$nodoDependiente,$tipo,$version = NULL) {
		$this->set('IdNodeMaster', $nodoMaestro);
		$this->set('IdNodeDependent', $nodoDependiente);
		$this->set('DepType', $tipo);
		if(!empty($version) ) {
			$this->set('version', $version);
		}else {
			$node = new Node($nodoMaestro);
			$this->set('version', $node->getVersion() );
		}

		if (!$this->ExisteDependencia($nodoMaestro,$nodoDependiente,$tipo,$version)) {
			return $this->add();
		}
		return false;
	}

	/**
	 *
	 * @param $nodeID
	 * @return unknown_type
	 */
	function BorrarNodoDependiente($nodeID) {
		//Deletes all the dependencies (ancestor) of this node ("free")
		$dbObj = new DB();
		$sql= sprintf("DELETE FROM Dependencies WHERE IdNodeDependent= %d", $nodeID);
		$dbObj->Execute($sql);
	}

	/**
	 *
	 * @param $nodeID
	 * @param $version
	 * @return unknown_type
	 */
	function BorrarDependenciasNodoDependiente($nodeID,$version) {
		//Deletes all the dependencies (ancestor) of this node ("free")
		//Last versions should be deleted
		$dbObj = new DB();
		$sql=sprintf("DELETE from Dependencies WHERE IdNodeDependent = %d and version= %d", $nodeID, $version);
		$dbObj->Execute($sql);
	}

	/**
	 *
	 * @param $nodeID
	 * @param $tipo
	 * @return unknown_type
	 */
	function BorrarTipoDependenciasNodo($nodeID,$tipo) {
		$dbObj = new DB();
		$sql=sprintf("DELETE from Dependencies WHERE IdNodeDependent = %d and DepType= %s", $nodeID, $dbObj->sqlEscapeString($tipo));
		$dbObj->Execute($sql);

	}

	/**
	 *
	 * @param $nodeID
	 * @return unknown_type
	 */
	function SacaDependenciasNodoDependiente($nodeID) {
		//Get all the nodes which are dependant of the current one

		$dbObj = new DB();
		$sql=sprintf("SELECT IdNodeMaster from Dependencies WHERE IdNodoDependent= %d", $nodeID);
		$dbObj->Query($sql);

		if ($dbObj->numRows==0) {
			return 0;
		}

		if (!$dbObj->numErr) {
			$arrayNodes = array();
			while(!$dbObj->EOF)
			{
				$arrayNodes[]=$dbObj->GetValue("IdNodeMaster");
				$dbObj->Next();
			}
			return $arrayNodes;
		}
		else {
			return 0;
		}
	}

	/**
	 *
	 * @param $nodeID
	 * @param $type
	 * @param $version
	 * @return unknown_type
	 */
	function GetDependenciesMasterByType($nodeID, $type, $version = NULL) {

		// Gets all the nodes which are pointing the the current one with dependencies of  kind $type

		$dbObj = new DB();

		if (is_null($version)) {
			$sql = sprintf("SELECT DISTINCT(IdNodeDependent) from Dependencies WHERE IdNodeMaster = %d
				AND DepType=%s", $nodeID, $dbObj->sqlEscapeString($type));

		} else {
			$sql = sprintf("SELECT DISTINCT(IdNodeDependent) from Dependencies WHERE IdNodeMaster = %d
				AND DepType = %s AND version = %s", $nodeID, $dbObj->sqlEscapeString($type), $version);

		}

		$dbObj->Query($sql);


		if ($dbObj->numRows==0) {
			return NULL;
		}
		else{

			$arrayNodes = array();
			while(!$dbObj->EOF)
			{
				$arrayNodes[]=$dbObj->GetValue("IdNodeDependent");
				$dbObj->Next();
			}
			return $arrayNodes;
		}
	}

	/**
	 *
	 * @param $nodeID
	 * @param $type
	 * @param $version
	 * @return unknown_type
	 */
	function GetMastersByType($nodeID, $type, $version = NULL) {

		// Gets all the nodes which are pointing the the current one with dependencies of  kind $type

		$dbObj = new DB();

		if (is_null($version)) {
			$sql = sprintf("SELECT DISTINCT(IdNodeMaster) from Dependencies WHERE IdNodeDependent = %d
				AND DepType = %s",$nodeID, $dbObj->sqlEscapeString($type));
		} else {
			$sql = sprintf("SELECT DISTINCT(IdNodeMaster) from Dependencies WHERE IdNodeDependent = %d
				AND DepType = %s AND version = %s", $nodeID, $dbObj->sqlEscapeString($type), $version);
		}

		$dbObj->Query($sql);

		if ($dbObj->numRows==0) {
			return NULL;
		} else {

			$arrayNodes = array();
			while(!$dbObj->EOF) {
				$arrayNodes[]=$dbObj->GetValue("IdNodeMaster");
				$dbObj->Next();
			}
			return $arrayNodes;
		}
	}

	/**
	 *
	 * @param $nodoMaestro
	 * @param $nodoDependiente
	 * @param $tipo
	 * @param $version
	 * @return unknown_type
	 */
	function ExisteDependencia($nodoMaestro,$nodoDependiente,$tipo,$version) {
		//Gets all the dependencies (ancestor) of this node

		$dbObj = new DB();
		$sql=sprintf("SELECT IdDep from Dependencies WHERE IdNodeMaster = %d AND IdNodeDependent = %d and DepType = %s and version = %d",
		$nodoMaestro, $nodoDependiente, $dbObj->sqlEscapeString($tipo), $version);

		$dbObj->Query($sql);


		if ($dbObj->numRows==0) {
			return 0;
		} else {
			return 1;
		}
	}

	/**
	 * Gets all tags from nodeId and searchs its associated xslt
	 *
	 * @param string $content
	 * @param int $docId
	 * @return array
	 */
	public static function getXslDependencies($content, $docId) {

		$node = new Node($docId);

		$section = new Node($node->GetSection());
		$ptdFolderSection = new Node($section->GetChildByName('ximptd'));

		$project = new Node($node->GetProject());
		$ptdFolderProject = new Node($project->GetChildByName('ximptd'));

		$nodeType = new NodeType();
		$nodeType->SetByName('XslTemplate');
		$nodeTypeXsl = $nodeType->get('IdNodeType');

		// Gets document tags

		$domDoc = new DOMDocument();
		$domDoc->validateOnParse = true;

		$domDoc->loadXML(XmlBase::recodeSrc("<docxap>$content</docxap>", XML::UTF8));

		$xpath = new DOMXPath($domDoc);

		$nodeList = $xpath->query('//*');

		// Searchs xslt nodes

		$xslDependencies = array();

		foreach ($nodeList as $element) {

			$tagName = $element->nodeName;

			$xsltId = $ptdFolderSection->GetChildByName($tagName . '.xsl');

			// If not found in section it searchs in project

			if (!($xsltId > 0)) {
				$xsltId = $ptdFolderProject->GetChildByName($tagName . '.xsl');
			}

			if ($xsltId > 0) {
				$xslDependencies[] = $xsltId;
			}
		}

		return $xslDependencies;
	}

	/**
	 * Deletes all dependencies between nodes for a given type
	 * @param int nodeMaster
	 * @param int nodeDependent
	 * @param string type
	 * @return true / false
	 */
	function deleteByMasterAndDependent($nodeMaster, $nodeDependent, $type) {

		if (is_null($nodeMaster) || is_null($nodeDependent) || is_null($type)) {
			XMD_Log::info('Params required');
			return false;
		}

		$dbObj = new DB();

		$sql = "DELETE from Dependencies WHERE IdNodeDependent = $nodeDependent AND IdNodeMaster = $nodeMaster
			AND  DepType = '$type'";

		$result = $dbObj->Execute($sql);

		if (!($result > 0)) {
			XMD_Log::info(_('No dependencies deleted'));
			return false;
		}

		return true;
	}
}
?>
