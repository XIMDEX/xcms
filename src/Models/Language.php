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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\Models;

use Ximdex\Models\ORM\LanguagesOrm;

class Language extends LanguagesOrm
{
	public $langID;
	
	public $dbObj;
	
	public $numErr;
	
	public $msgErr;
	
	public $errorList = array
	(
		1 => 'Language does not exist',
		2 => 'A language with this name already exists',
		3 => 'Arguments missing',
		4 => 'Database connection error'
	);

	/**
	 * Returns langID (class attribute)
	 * 
	 * @return int
	 */
	public function getID()
	{
		return (int) $this->get('IdLanguage');
	}

	/**
	 * Allows to change the langID without destroying and re-creating the object
	 * 
	 * @param int $id
	 * @return boolean|string
	 */
	public function setID(int $id)
	{
		parent::__construct($id);
		return $this->get('IdLanguage');
	}

	/**
	 * Returns a list of all the existing idLanguages
	 * 
	 * @param array $order
	 * @return NULL|array|boolean
	 */
	public function GetList(array $order = null)
	{
		$validDirs = array('ASC', 'DESC');
		$this->ClearError();
		$dbObj = new \Ximdex\Runtime\Db();
		$sql = "SELECT IdLanguage FROM Languages";
		if (! empty($order) && is_array($order) && isset($order['FIELD'])) {
			$sql .= sprintf(" ORDER BY %s %s", $order['FIELD'], isset($order['DIR']) && in_array($order['DIR'], $validDirs) ? $order['DIR'] : '');
		}
		$dbObj->Query($sql);
		if (! $dbObj->numErr) {
		    $salida = [];
			while (!$dbObj->EOF) {
				$salida[] = $dbObj->GetValue("IdLanguage");
				$dbObj->Next();
			}
			return (! empty($salida)) ? $salida : null;
		}
        $this->setError(4);
        return false;
	}

	/**
	 * Returns the language name
	 * 
	 * @return boolean|string
	 */
	public function getName()
	{
		return $this->get('Name');
	}
	
	public function getLanguagesForNode(int $idNode)
	{
		$node = new Node($idNode);
		$languages = array();
		$langs = $node->getProperty('language');
		if (! is_array($langs)) {
		    
			// Inherits the system properties
			$langs = array();
			$systemLanguages = $this->find('IdLanguage', 'Enabled = 1', null);
			if (! empty($systemLanguages)) {
				foreach ($systemLanguages as $lang) {
					$langs[] = $lang['IdLanguage'];
				}
			}
		}
		if (count($langs) > 0) {
			foreach ($langs as $langId) {
				$lang = new Language($langId);
				$languages[] = array
				(
					'IdLanguage' => $langId,
					'Name' => $lang->get('Name')
				);
			}
		}
		return (count($languages) > 0) ? $languages : null;
	}

	/**
	 * Allows us to change the language name
	 * 
	 * @param string $name
	 * @return boolean|string|NULL|number
	 */
	public function setName(string $name)
	{
		if (! $this->get('IdLanguage')) {
			$this->setError(2, 'Language does not exist');
			return false;
		}
		$result = $this->set('Name', $name);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 * Returns the language iso name
	 * 
	 * @return boolean|string
	 */
	public function getIsoName()
	{
		return $this->get('IsoName');
	}


	/**
	 * Allows us to change the language iso name
	 * 
	 * @param string $isoName
	 * @return boolean|string|NULL|number
	 */
	public function SetIsoName(string $isoName)
	{
		if (! $this->get('IdLanguage')) {
			$this->setError(2, 'Language does not exist');
			return false;
		}
		$result = $this->set('IsoName', $isoName);
		if (! $result) {
			return $this->update();
		}
		return false;
	}

	/**
	 * Returns the language description
	 * 
	 * @return boolean|string
	 */
	public function getDescription()
	{
		$this->clearError();
		if ($this->get('IdLanguage')) {
			$node = new Node($this->get('IdLanguage'));
			return $node->getDescription();
		}
		$this->setError(1);
		return false;
	}

	/**
	 * Allows us to change the language description
	 * 
	 * @param string $description
	 * @return boolean|number|NULL|string
	 */
	public function setDescription(string $description)
	{
		$this->clearError();
		if ($this->get('IdLanguage') > 0) {
			$node = new Node($this->get('IdLanguage'));
			return $node->SetDescription($description);
		}
		$this->setError(1);
		return false;
	}

	/**
	 * Searches a language by name
	 * 
	 * @param string $name
	 * @return boolean
	 * @deprecated
	 */
	public function setByName(string $name)
	{
		$this->clearError();
		$dbObj = new \Ximdex\Runtime\Db();
		$query = sprintf("SELECT IdLanguage FROM Languages WHERE Name = %s", $dbObj->sqlEscapeString($name));
		$dbObj->query($query);
		if ($dbObj->numRows) {
			parent::__construct($dbObj->getValue("IdLanguage"));
			return true;
		}
		$this->setError(4);
		return false;
	}

	/**
	 * Searches a language by iso name
	 * 
	 * @param string $isoName
	 * @return boolean|string|NULL
	 * @deprecated
	 */
	public function setByIsoName(string $isoName)
	{
		$this->ClearError();
		$dbObj = new \Ximdex\Runtime\Db();
		$query = sprintf("SELECT IdLanguage FROM Languages WHERE IsoName = %s", $dbObj->sqlEscapeString($isoName));
		$dbObj->query($query);
		if ($dbObj->numRows) {
			$idLanguage = $dbObj->getValue("IdLanguage");
			parent::__construct($idLanguage);
			return $idLanguage;
		}
		$this->setError(4);
		return false;
	}

	/**
	 * Creates a new language and loads its ID in the object
	 * 
	 * @param string $name
	 * @param string $isoname
	 * @param string $description
	 * @param bool $enabled
	 * @param int $nodeID
	 * @return int|boolean
	 */
	public function createLanguage(string $name, string $isoname, string $description, bool $enabled, int $nodeID = null)
	{
		if ($nodeID) {
			$this->set('IdLanguage', $nodeID);
		}
		$this->set('Name', $name);
		$this->set('IsoName', $isoname);
		$this->set('Enabled', (int) ! empty($enabled));
		$this->add();
		if ($this->get('IdLanguage')) {
			$this->setDescription($description);
		} else {
			$this->setError(4);
			return false;
		}
		return $nodeID;
	}

	/**
	 * Deletes the current language
	 * 
	 * @return boolean
	 */
	public function deleteLanguage()
	{
		$this->clearError();
		$dbObj = new \Ximdex\Runtime\Db();
		if (! is_null($this->get('IdLanguage'))) {
		    
			// Deleting from database
			$dbObj->Execute(sprintf("DELETE FROM Languages WHERE IdLanguage= %d", $this->get('IdLanguage')));
			if ($dbObj->numErr) {
				$this->setError(4);
				return false;
			}
		} else {
			$this->setError(1);
			return false;
		}
		return true;
	}

	public function canDenyDeletion()
	{
		$this->clearError();
		$sql = sprintf("select count(*) AS total from StructuredDocuments where IdLanguage = %d", $this->get('IdLanguage'));
		$dbObj = new \Ximdex\Runtime\Db();
		$dbObj->query($sql);
		if ($dbObj->numErr) {
			$this->setError(4);
			return false;
		}
		if ($dbObj->getValue("total") == 0) {
			return true;
		}
        return false;
	}

	public function languageEnabled(int $idlang)
	{
		$dbObj = new \Ximdex\Runtime\Db();
		$query = sprintf("SELECT Enabled FROM Languages WHERE IdLanguage = %d", $idlang);
		$dbObj->query($query);
		if ($dbObj->numErr != 0) {
			$this->setError(1);
			return null;
		}
		if (isset($dbObj->row["Enabled"])) {
		    return $dbObj->row["Enabled"];
		}
		return null;
	}

	public function setEnabled(bool $enabled)
	{
		if (! $this->get('IdLanguage')) {
			$this->setError(2, 'Language does not exist');
			return false;
		}
		$result = $this->set('Enabled', (int) $enabled);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 * Cleans class errors
	 */
	public function clearError()
	{
		$this->numErr = null;
		$this->msgErr = null;
	}

	/**
	 * Loads a class error
	 * 
	 * @param int $code
	 */
	public function setError(int $code)
	{
		$this->numErr = $code;
		$this->msgErr = $this->errorList[$code];
	}

	/**
	 * Returns true if the class had an error
	 * 
	 * @return boolean
	 */
	public function hasError()
	{
		return ($this->numErr != null);
	}
}
