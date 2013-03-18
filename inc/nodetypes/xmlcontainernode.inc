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










if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

include_once XIMDEX_ROOT_PATH . "/inc/nodetypes/foldernode.inc";
include_once XIMDEX_ROOT_PATH . "/inc/model/RelTemplateContainer.class.php";
include_once XIMDEX_ROOT_PATH . "/inc/model/language.inc";
include_once XIMDEX_ROOT_PATH . "/inc/model/structureddocument.inc";

class XmlContainerNode extends FolderNode
	{


	/// Renderiza el nodo
	function RenderizeNode()
		{
		return null;
		}

	function getVisualTemplate() {
		$query = sprintf("SELECT IdTemplate"
					. " FROM RelTemplateContainer WHERE IdContainer = %d",
					$this->nodeID);

	  	$this->dbObj->Query($query);

		if (!($this->dbObj->numRows)) {
			return false;
		}
		return $this->dbObj->GetValue('IdTemplate');

	}


	function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = null, $templateID = null, $aliasLangList = null, $channelList = null, $master = null,$dataChildren=null)
	{
		/**
		 * En master viene el idlanguage, entonces en el bucle que recorre los idiomas, cuando se llega al idioma que
		 * es el maestro se coge el id del nodo, después de creados todos los nodos, se establece el workflowmaster de
		 * los nodos esclavos (sigo sin sabe que diferencia hay entre esto y el targetlink)
		 */
		$nodeMaster =  NULL;
		if (is_array($aliasLangList)) {
		foreach ($aliasLangList as $IdLanguage => $alias)
			{

 			$xmldoc = new Node();

			$childrenNodeType = new NodeType();

			switch($this->nodeType->GetName())
				{
				case 'XimNewsBulletin':
					$childrenNodeType->SetByName('XimNewsBulletinLanguage');
					break;
				case 'XimNewsNew':
					$childrenNodeType->SetByName('XimNewsNewLanguage');
					break;
				case 'XmlContainer':
					$childrenNodeType->SetByName('XmlDocument');
					break;
				case 'XimletContainer':
					$childrenNodeType->SetByName('Ximlet');
					break;
				case 'XimPdfDocumentFolder':
					$childrenNodeType->SetByName('XimPdfDocumentLang');
					break;
				default:
					return;
				}

			if ($childrenNodeType->HasError())
				{
			 	$this->parent->SetError(1);
				return;
				}

			$lang = new Language($IdLanguage);
			if ($lang->HasError())
				{
				$this->parent->SetError(1);

				return;
				}

			$nameDoc = $name . "-id" . $lang->GetIsoName() ;

	 		$xmldoc->CreateNode($nameDoc, $this->nodeID, $childrenNodeType->GetID(),$stateID = null, $templateID, $IdLanguage, $alias, $channelList, $dataChildren);
			if(!$nodeMaster && $master && $master == $IdLanguage)
				{
				///echo "@@SET".$master.",".$xmldoc->GetID()."@@<br>";
				$nodeMaster = $xmldoc->GetID();
				}

			if ($xmldoc->HasError())
				{
			 	$this->parent->SetError(1);
				return;
				}
			}
		}
		if($nodeMaster) {
			//echo "@@PONIENDO MAESTRO ".$nodeMaster."@@<br>";
			$children = $this->parent->GetChildren();
			foreach($children as $childID)
				{
				if($childID != $nodeMaster) {
					//echo "@@PONIENDO MAESTRO A ".$childID."@@<br>";
					$child = new Node($childID);
					$child->SetWorkflowMaster($nodeMaster);
					$strDoc = new StructuredDocument($childID);
					$strDoc->SetSymLink($nodeMaster);
				}
			}
		}
                        $reltemplate = new RelTemplateContainer();
                        $reltemplate->createRel($templateID,$this->nodeID);

        $this->updatePath();
	}

        //Borrar un contenedor implica eliminar el registro en RelTemplateContainer y
        //lo registrado en Dependencies.
        function DeleteNode()
                {
                 $templatecontainer = new RelTemplateContainer();
                 $templatecontainer->deleteRel($this->nodeID);
                }


	function RenameNode($name = null) {
		if(!$name) return false;

 		$listaDocs = $this->parent->GetChildren();

		if (sizeof($listaDocs) > 0) {

			foreach ($listaDocs as $docID) {
				$node = new Node($docID);
				$strdoc = new StructuredDocument($docID);
				$langId = $strdoc->GetLanguage();
				$lang = new Language($langId);

				$nameDoc = $name . "-id" . $lang->GetIsoName() ;
				// cambio el nombre al nodo
				//echo "@@ renombrando ".$docID." @@";
				$node->RenameNode($nameDoc);
			}
		}

        $this->updatePath();
	}

	function GetLanguages()
		{
		$node	 = new Node($this->nodeID);
 		$docList = $node->GetChildren();

		if ($node->HasError())
			{
			$this->parent->SetError(1);
			return;
			}

		foreach ($docList as $docID)
			{
	 		// obtengo el lenguaje
			$strDoc = new StructuredDocument($docID);
			$langList[] = $strDoc->GetLanguage();
			}

		return $langList;
		}

	function GetChildByLang($langID)
		{
		$node	 = new Node($this->nodeID);
 		$docList = $node->GetChildren();

		if ($node->HasError())
			{
			$this->parent->SetError(1);
			return;
			}

		foreach ($docList as $docID)
			{
	 		// obtengo el lenguaje
			$strDoc = new StructuredDocument($docID);
			$docLang = $strDoc->GetLanguage();
			if($docLang == $langID)
				return $docID;
			}

		return null;
		}
	function ToXml($depth, & $files, $recurrence) {

		$xml = '';

		$query = sprintf("SELECT IdTemplate FROM `RelTemplateContainer`"
					. " WHERE IdContainer = %d",
					$this->parent->nodeID);
		$this->dbObj->Query($query);
		while (!$this->dbObj->EOF) {
			$idTemplate = $this->dbObj->GetValue('IdTemplate');
			if (!(int) $idTemplate > 0) continue;

			$template = new Node($idTemplate);
			$xml .= $template->ToXml($depth, $files, $recurrence);
			$this->dbObj->Next();
		}

		return $xml;


	}

}
?>
