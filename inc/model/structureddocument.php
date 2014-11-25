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


if (!defined('XIMDEX_ROOT_PATH')) define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../..');
require_once XIMDEX_ROOT_PATH . '/inc/model/orm/StructuredDocuments_ORM.class.php';
require_once(XIMDEX_ROOT_PATH . "/inc/parsers/ParsingDependences.class.php" );
require_once(XIMDEX_ROOT_PATH . "/inc/parsers/ParsingRng.class.php");

class StructuredDocument extends StructuredDocuments_ORM
{
	var $ID;
	var $flagErr;
	var $numErr;
	var $msgErr;

	var $errorList= array(
		1 => 'Error while connecting with the database',
		2 => 'The structured document does not exist',
		3 => 'Not implemented yet',
		4 => 'A document cannot be linked to itself'
		);

	function StructuredDocument($docID = null)
	{
 		$this->ID = $docID;
		$this->flagErr = FALSE;
		$this->autoCleanErr = TRUE;
		parent::GenericData($docID);
	}

	// Devuelve un array con los ids de todos los structured documents del sistema.
	// return array of idDoc
	function GetAllStructuredDocuments() {
		$sql = "SELECT idDoc FROM StructuredDocuments";
		$dbObj = new DB();
		$dbObj->Query($sql);
		if ($dbObj->numErr != 0) {
		 	$this->SetError(1);
		 	return null;
		}
		while (!$dbObj->EOF) {
	 	$salida[] = $dbObj->row["idDoc"];
			$dbObj->Next();
		}
 	return $salida;
	}

	//	Devuelve el id del structure document actual.
	function GetID() {
 		return $this->get('IdDoc');
	}

	// Cambia el id del structure document actual.
	//  return int (status)
	function SetID($docID) {
		StructuredDocument::GenericData($docID);
		if (!($this->get('IdDoc') > 0)) {
			$this->SetError(2);
			return null;
		}
		return $this->get('IdDoc');
	}

	// Devuelve el nombre del structure document actual.
	// return string(name)
	function GetName()
	{
	 	return $this->get("Name");
	}

	// Cambia el nombre del structure document actual.
	// return int (status)
	function SetName($name) {
		if (!($this->get('IdDoc') > 0)) {
			$this->SetError(2);
			return false;
		}

		$result = $this->set('Name', $name);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	// Devuelve el creador del structure document actual.
	// return string (idcreator)
	function GetCreator () {
	 	return $this->get("IdCreator");
	}

	// Cambia el creador del structure document actual.
	// return int (status)
	function SetCreator($IdCreator) {
		if (!($this->get('IdDoc') > 0)) {
			$this->SetError(2);
			return false;
		}

		$result = $this->set('IdCreator', $IdCreator);
		if ($result) {
			return $this->update();
		}
		return false;
	}


	// Devuelve el lenguaje del structure document actual.
	// return int (IdLanguage)
	function GetLanguage () {
	 	return $this->get("IdLanguage");
	}

	// Cambia el lenguaje del structure document actual.
	// return int (status)
	function SetLanguage($IdLanguage) {
		if (!($this->get('IdDoc') > 0)) {
			$this->SetError(2);
			return false;
		}

		$result = $this->set('IdLanguage', $IdLanguage);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	function GetDocumentType () {
	 	return $this->get("IdTemplate");
	}

	function SetDocumentType($templateID)
	{
		if (!($this->get('IdDoc') > 0)) {
			$this->SetError(2);
			return false;
		}

		$result = $this->set('IdTemplate', $templateID);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	function GetSymLink()
	{
 		return $this->get("TargetLink");
	}

	function SetSymLink($docID) {
		if (!($this->get('IdDoc') >= 0)) {
			$this->SetError(2);
			return false;
		}

		if($docID != $this->get('IdDoc')) {
			$result = $this->set('TargetLink', $docID);
			if ($result) {
				$this->update();
			}

			$dependencies=new dependencies();
			$dependencies->insertDependence($docID,$this->get('IdDoc'),'SYMLINK',$this->GetLastVersion());
			return true;
			}
		else
			$this->SetError(4);
		return false;
	}

	function ClearSymLink()
	{
		if (!($this->get('IdDoc') > 0)) {
			$this->SetError(2);
			return false;
		}

		$result = $this->set('TargetLink', '');
		if ($result) {

			$result = $this->update();
			$this->SetContent($this->GetContent());

			// Elimina la dependencia
			$dependencies=new dependencies();
			$dependencies->DeleteTypeDependenciesNode($this->get('IdDoc'), 'SYMLINK');

			return $result;
		}
		return false;

	}

	// Devuelve el contenido xml del structure document actual.
	// return string (Content)
	function GetContent($version=null,$subversion=null)
		{
		$targetLink =  $this->GetSymLink();
		if($targetLink) {
			$target			= new StructuredDocument($targetLink);
			$targetContent	= $target->GetContent();
			$targetLang		= $target->GetLanguage();
			$targetContent	= preg_replace('/ a_enlaceid([A-Za-z0-9|\_]+)\s*=\s*\"([^\"]+)\"/ei' ,  "' a_enlaceid\\1=\"'.\$this->UpdateLinkParseLink($targetLang , '\\2').'\"'", $targetContent);
			$targetContent	= preg_replace('/ a_import_enlaceid([A-Za-z0-9|\_]+)\s*=\s*\"([^\"]+)\"/ei' ,  "' a_import_enlaceid\\1=\"'.\$this->UpdateLinkParseLink($targetLang , '\\2').'\"'", $targetContent);
			$targetContent	= preg_replace('/<url>\s*([^\<]+)\s*<\/url>/ei' ,  "'<url>'.\$this->UpdateLinkParseLink($targetLang , '\\1').'</url>'", $targetContent);
			return $targetContent;
		}

		$data = new DataFactory($this->get('IdDoc'));
		$content = $data->GetContent($version,$subversion);
		return $content;
		}

	function UpdateLinkParseLink($sourceLang, $linkID)
	{
		$pos=strpos($linkID,",");
		if ($pos!=FALSE) {
			$linkID=substr($linkID,0,$pos);
		}

		$node = new Node($linkID);
		if (($node->get('IdNode') > 0) && ($node->nodeType->get('Name') != "XmlDocument")) {
			return $linkID;
		}
		$linkDoc = new StructuredDocument($linkID);
		if($linkDoc->GetLanguage() != $sourceLang)
			return $linkID;

		$node->SetID($node->GetParent());
		if($node->nodeType->get('Name') != "XmlContainer")
			return $linkID;

		$sibling = $node->class->GetChildByLang($this->GetLanguage());

		if($sibling)
			return $sibling;
		else
			return $linkID;
	}

	/**
	 *
	 * @param string $content
	 * @param boolean $commitNode
	 */
	function SetContent($content, $commitNode = NULL) {

		$symLinks = $this->find('IdDoc', 'TargetLink = %s', array($this->get('IdDoc')), MONO);

		// Repetimos para todos los nodos que son enlaces simbolicos a este
		if(!empty($symLinks)) {
			foreach($symLinks as $link) {
				$node = new Node($link);
				$node->RenderizeNode();
			}
		}

		// refrescamos la fecha de Actualizacion del nodo
		$this->SetUpdateDate();

		// Y el contenido
		$data = new DataFactory($this->get('IdDoc'));
		$data->SetContent($content, NULL, NULL, $commitNode);
		$idVersion = $data->GetLastVersionId();

		// set dependencies

		ParsingDependences::GetAll($this->get('IdDoc'), $content, $idVersion);

		// Renderizamos el nodo para reflejar los cambios
		$node = new Node($this->get('IdDoc'));
		$node->RenderizeNode();
	}


	// Devuelve el timestamp de creacion del structure document actual.
	// return string (CreationDate)
	function GetCreationDate()
	{
		return $this->get("CreationDate");
	}

	// Devuelve el timestamp de modificacion del structure document actual.
	// return string (UpdateDate)
	function GetUpdateDate()
	{
		return $this->get("UpdateDate");
	}

	// Cambia el UpdateDate del structure document actual.
	// return int (status)
	function SetUpdateDate() {
		if (!($this->get('IdDoc') > 0)) {
			$this->SetError(2);
			return false;
		}

		$result = $this->set('UpdateDate', date('Y/m/d H:i:s'));
		if ($result) {
			return $this->update();
		}
		return false;
	}

	// Adds a channel to the current document
	function AddChannel($IdChannel) {
		$sqls = sprintf("INSERT INTO RelStrDocChannels (IdDoc, IdChannel)"
	 		. " VALUES (%d, %d)", $this->get('IdDoc'), $IdChannel);
	 	$dbObj = new DB();
   		$dbObj->Execute($sqls);
		if ($dbObj->numErr != 0) {
	 	$this->SetError(1);
		}
	}

	// Checks if the current document has the given channel
	function HasChannel($idChannel) {
		$sql = sprintf("SELECT COUNT(*) as total FROM RelStrDocChannels"
				. " WHERE IdDoc = %d"
				. " AND IdChannel = %d", $this->get('IdDoc'), $idChannel);
	 	$dbObj = new DB();
		$dbObj->Query($sql);
		if ($dbObj->numErr != 0)
	 	$this->SetError(1);
 		return $dbObj->GetValue("total");
	}

	function GetChannels() {
		$sql = sprintf("SELECT DISTINCT(IdChannel) FROM RelStrDocChannels" .
				" WHERE IdDoc = %d", $this->get('IdDoc'));
	 	$dbObj = new DB();
		$dbObj->Query($sql);
		if ($dbObj->numErr != 0) {
		 	$this->SetError(1);
		 	return null;
		}
		$salida = NULL;
		while (!$dbObj->EOF) {
	 	$salida[] = $dbObj->getValue("IdChannel");
			$dbObj->Next();
		}
 	return $salida;
	}

	// Delete the given channel for the current node
	function DeleteChannel($idChannel) {
		$sqls = sprintf("DELETE FROM RelStrDocChannels WHERE IdDoc = %d"
	 		. " AND IdChannel = %d", $this->get('IdDoc'), $idChannel);
	 	$dbObj = new DB();
   		$dbObj->Execute($sqls);
		if ($dbObj->numErr != 0) {
	 		$this->SetError(1);
		}
	}

	function DeleteChannels() {
		$dbObj = new DB();
		$sqls = sprintf("DELETE FROM RelStrDocChannels WHERE IdDoc = %d", $this->get('IdDoc'));
   		$dbObj->Execute($sqls);
		if ($dbObj->numErr != 0) {
	 		$this->SetError(1);
		}
	}

 	function add() {
 		$this->CreateNewStrDoc($this->get('IdDoc'), $this->get('Name'), $this->get('IdCreator'),
 				$this->get('CreationDate'), $this->get('UpdateDate'), $this->get('IdLanguage'),
 				$this->get('IdTemplate'));
 	}
	// Crea un nuevo structure document y carga su id en el docID de la clase.
	// return docID - lo carga como atributo
	function CreateNewStrDoc($docID , $name, $IdCreator, $IdLanguage, $templateID, $IdChannelList, $content = '')
	{

		$this->set('Name', $name);
		$this->set('IdCreator', $IdCreator);
		$now = date('Y/m/d H:i:s');
		$this->set('CreationDate', $now);
		$this->set('UpdateDate', $now);
		$this->set('IdLanguage', $IdLanguage);
		$this->set('IdTemplate', $templateID);
		if ((int)$docID > 0) {
			$this->set('IdDoc', $docID);
		}
		$result = parent::add();

		if ($this->get('IdDoc') > 0) {
			if ($IdChannelList) foreach ($IdChannelList as $idChannel) {
	 			$dbObj = new DB();
				$sql = sprintf("INSERT INTO RelStrDocChannels (IdDoc, IdChannel) "
					 	. " VALUES (%d, %d)", $this->get('IdDoc'), $idChannel);
				$dbObj->Execute($sql);

				if ($dbObj->numErr) {
	 				$this->SetError(1);
				}
			}
			$this->ID = $docID;

			/// Guardamos su contenido
			$this->SetContent($content);
		} else {
	 		$this->SetError(1);
		}
	}

	function delete() {
		$this->DeleteStrDoc();
	}
	// Elimina el structure document actual.
	// return int (status)
	function DeleteStrDoc() {
		parent::delete();
		$sql = sprintf("DELETE FROM RelStrDocChannels WHERE idDoc = " . $this->get('IdDoc'));
		$dbObj = new DB();
   		$dbObj->Execute($sql);
		if ($dbObj->numErr) {
			$this->SetError(1);
			return;
		}

		$this->ID = null;
	}

	function GetLastVersion()
	{
		$sql = sprintf("select max(Version) as UltimaVersion from Versions where IdNode=%d",$this->get('IdDoc'));
		$dbObj = new DB();
		$dbObj->Query($sql);
		if ($dbObj->numErr != 0)
		{
			$this->SetError(1);
		} else {
			$salida = NULL;
			while (!$dbObj->EOF) {
	 			$salida = $dbObj->GetValue("UltimaVersion");
				$dbObj->Next();
			}
			return $salida;
		}
		return NULL;
	}

	function isximletlink() {

		$sql= sprintf("select IdNodeDependent from Dependencies WHERE IdNodeMaster = %d and DepType='LINK'",
					$this->get('IdDoc'));
		$dbObj = new DB();
		$dbObj->Query($sql);
		if ($dbObj->numErr != 0) {
			$this->SetError(1);
		} else {
			$salida = NULL;
			while (!$dbObj->EOF) {
	 			$links[] = $dbObj->GetValue("IdNodeDependent");
				$dbObj->Next();
			}

			if (is_array($links)) foreach ($links as $link) {
				$node_ximlet=new Node($link);
				$node_type=new NodeType($node_ximlet->GetNodeType());

				if ($node_type->GetName()=='Ximlet') {
					$salida[]=$link;
				}

			}
			return $salida;
		}
		return NULL;
	}

	function ximletLinks($ximletID, $nodeID) {

		$sql=sprintf("SELECT IdNodeMaster FROM Dependencies"
				. " WHERE IdNodeDependent= %d AND DepType='LINK' AND IdNodeMaster!= %d", $ximletID, $nodeID);
		$dbObj = new DB();
		$dbObj->Query($sql);
		if ($dbObj->numErr != 0) {
			$this->SetError(1);
		} else {
			$link = NULL;
			while (!$dbObj->EOF) {
	 			$link[] = $dbObj->GetValue("IdNodeMaster");
				$dbObj->Next();
			}
			return $link;
		}
		return NULL;
	}



	//  limpia el ultimo error
	function ClearError() {
		$this->flagErr = FALSE;
	}

	function SetAutoCleanOn() {
 	$this->autoCleanErr = TRUE;
	}
	function SetAutoCleanOff() {
 	$this->autoCleanErr = FALSE;
	}

	/// Carga un error en la clase
	function SetError($code) {
 	$this->flagErr = TRUE;
		$this->numErr = $code;
		$this->msgErr = $this->errorList[$code];
	}

	// devuelve true si en la clase se ha producido un error
	function HasError() {
 	$aux = $this->flagErr;
		if ($this->autoCleanErr)
			$this->ClearError();
		return $aux;
	}

}
