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
require_once XIMDEX_ROOT_PATH . '/inc/model/orm/Languages_ORM.class.php';

class Language extends Languages_ORM
{
	var $langID;
	var $dbObj;
	var $numErr;				// Error coe.
	var $msgErr;				// Error message.
	var $errorList= array(	// Class error list.
		1 => 'Language does not exist',
		2 => 'A language with this name already exists',
		3 => 'Arguments missing',
		4 => 'Database connection error',
		);

	//Constructor
	function Language($_params=null) {
		$this->errorList[1]=_('Language does not exist');
		$this->errorList[2]=_('A language with this name already exists');
		$this->errorList[3]=_('Arguments missing');
		$this->errorList[4]=_('Database connection error');
		parent::__construct($_params);
	}
	
	// Returns langID (class attribute)
	function GetID() {
		return $this->get('IdLanguage');
	}

	// Allows to change the langID without destroying and re-creating the object
	function SetID($id) {
		parent::GenericData($id);
		return $this->get('IdLanguage');
	}

	// Returns a list of all the existing idLanguages 
	function GetList($order = NULL)
	{
		$validDirs = array('ASC', 'DESC');
		$this->ClearError();
		$dbObj = new DB();
		$sql = "SELECT IdLanguage FROM Languages";
		if (!empty($order) && is_array($order) && isset($order['FIELD'])) {
			$sql .= sprintf(" ORDER BY %s %s", $order['FIELD'],
				isset($order['DIR']) && in_array($order['DIR'], $validDirs) ? $order['DIR'] : '');
		}
		$dbObj->Query($sql);
		if(!$dbObj->numErr) {
			while(!$dbObj->EOF) {
				$salida[] = $dbObj->GetValue("IdLanguage");
				$dbObj->Next();
			}
    		return !empty($salida) ? $salida : NULL;
		}
		else
			$this->SetError(4);
	}

	// Returns the language name
	function GetName()
	{
		return $this->get('Name');
	}


	function GetAllLanguages($order = NULL) {
		return $this->GetList($order);
	}

	function getLanguagesForNode($idNode) {

		$node = new Node($idNode);
		$languages = array();
		$langs = $node->getProperty('language');

		if (!is_array($langs)) {
			// Inherits the system properties
			$langs = array();
			$systemLanguages = $this->find('IdLanguage', 'Enabled = 1', null);
			if(!empty($systemLanguages) ) {
				foreach ($systemLanguages as $lang) {
					$langs[] = $lang['IdLanguage'];
				}
			}
		}

		if (count($langs) > 0) {
			foreach ($langs as $langId) {
				$lang = new Language($langId);
				$languages[] = array(
					'IdLanguage' => $langId,
					'Name' => $lang->get('Name')
				);
			}
		}

		return count($languages) > 0 ? $languages : null;
	}

	// Allows us to change the language name
	function SetName($name)
	{
		if (!($this->get('IdLanguage') > 0)) {
			$this->SetError(2, 'No Existe el idioma');
			return false;
		}
		$result = $this->set('Name', $name);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	// Returns the language iso name 
	function GetIsoName()
	{
		return $this->get('IsoName');
	}


	// Allows us to change the language iso name
	function SetIsoName($isoName)
	{
		if (!($this->get('IdLanguage') > 0)) {
			$this->SetError(2, 'No Existe el idioma');
			return false;
		}

		$result = $this->set('IsoName', $isoName);
		if (!$result) {
			return $this->update();
		}
		return false;
	}
	// Returns the language description
	function GetDescription()
	{
		$this->ClearError();
		if($this->get('IdLanguage') > 0) {
			$node = new Node($this->get('IdLanguage'));
			return $node->GetDescription();
		} else {
			$this->SetError(1);
		}
	}


	// Allows us to change the language description
	function SetDescription($description)
	{
		$this->ClearError();
		if($this->get('IdLanguage') > 0) {
			$node = new Node($this->get('IdLanguage'));
			return $node->SetDescription($description);
			}
		else
			$this->SetError(1);
	}


	// Searches a language by name
	function SetByName($name)
	{
		$this->ClearError();
		$dbObj = new DB();
		$query = sprintf("SELECT IdLanguage FROM Languages WHERE Name = %s", $dbObj->sqlEscapeString($name));
		$dbObj->Query($query);
		if ($dbObj->numRows)
			parent::GenericData($dbObj->GetValue("IdLanguage"));
		else
			$this->SetError(4);
	}

	// Searches a language by iso name
	function SetByIsoName($isoName)
		{
		$this->ClearError();
		$dbObj = new DB();
		$query = sprintf("SELECT IdLanguage FROM Languages WHERE IsoName = %s", $dbObj->sqlEscapeString($isoName));
		$dbObj->Query($query);
		if ($dbObj->numRows) {
			$idLanguage = $dbObj->GetValue("IdLanguage");
			parent::GenericData($idLanguage);
			return $idLanguage;
		} else {
			$this->SetError(4);
			return false;
		}
	}


	// Creates a new language and loads its ID in the object
	function CreateLanguage($name, $isoname, $description, $enabled, $nodeID=null)
	{

		if ($nodeID > 0) {
			$this->set('IdLanguage', $nodeID);
		}
		$this->set('Name', $name);
		$this->set('IsoName', $isoname);
		$this->set('Enabled', (int)!empty($enabled));
		$this->add();
		if ($this->get('IdLanguage') > 0) {
			$this->SetDescription($description);
		} else {
			$this->SetError(4) ;
		}

		return $nodeID;
	}

	// Deletes the current language
	function DeleteLanguage()
		{
		$this->ClearError();
		$dbObj = new DB();
		if(!is_null($this->get('IdLanguage')))
			{
				// Deleting from database
				$dbObj->Execute(sprintf("DELETE FROM Languages WHERE IdLanguage= %d", $this->get('IdLanguage')));
				if($dbObj->numErr)
					$this->SetError(4);
			}
		else
			$this->SetError(1);
		}

		function CanDenyDeletion()
		{
		$this->ClearError();
		$sql = sprintf("select count(*) AS total from StructuredDocuments where IdLanguage = %d", $this->get('IdLanguage'));
		$dbObj = new DB();
    	$dbObj->Query($sql);
		if($dbObj->numErr)
				$this->SetError(4);

		if ($dbObj->GetValue("total")== 0)
			 return true;
	  else
				return false;
		}


	function DocumentsWithLanguage($idlang) {
		$dbObj = new DB();
		$query = sprintf("SELECT IdDoc FROM StructuredDocuments WHERE IdLanguage = %d", $idlang );

		$dbObj->Query($query);
		if ($dbObj->numErr != 0) {
	 	$this->SetError(1);
	 	return null;
		}
		$arrayDocs = array();
		while(!$dbObj->EOF) {
			$arrayDocs[]=$dbObj->GetValue('IdDoc');
			$dbObj->Next();
		}

		return $arrayDocs;

	}

	function LanguageEnabled($idlang) {

		$dbObj = new DB();
		$query = sprintf("SELECT Enabled FROM Languages WHERE IdLanguage = %d", $idlang);
		$dbObj->Query($query);
		if ($dbObj->numErr != 0) {
	 	$this->SetError(1);
	 	return null;
		}
		return 	$dbObj->row["Enabled"];


	}


	function SetEnabled($enabled) {

		error_log('enabled->>>>>>>>>>>>>>>>>>>>>>>' . (int)$enabled);
		if (!($this->get('IdLanguage') > 0)) {
			$this->SetError(2, 'No Existe el idioma');
			return false;
		}

		$result = $this->set('Enabled', (int)$enabled);
		if ($result) {
			return $this->update();
		}
		return false;
	}


	/// Cleans class errors
	function ClearError() {
		$this->numErr = null;
		$this->msgErr = null;
	}

	/// Loads a class error
	function SetError($code){
		$this->numErr = $code;
		$this->msgErr = $this->errorList[$code];
	}

	// Returns true if the class had an error
	function HasError(){
		return ($this->numErr != null);
        }
}
?>
