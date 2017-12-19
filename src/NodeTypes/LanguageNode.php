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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\NodeTypes;

use description;

use NodeProperty;

use Ximdex\Models\Language;
use Ximdex\NodeTypes\Root;


/**
 * @brief Handles the languages in wich could be written the documents.
 */
class LanguageNode extends Root
{

	/**
	 *  Calls for add a row to Languages table.
	 * @param string name
	 * @param int parentID
	 * @param int nodeTypeID
	 * @param int stateID
	 * @param string isoname
	 * @param string description
	 * @param int enabled
	 * @return unknown
	 */
	function CreateNode($name = null, $parentID = null, $nodeTypeID = null, $stateID = null, $isoname = null, $description = null, $enabled = null)
	{
		$language = new Language();
		$result = $language->find('IdLanguage', 'IsoName = %s', array($isoname));
		if ($result) {
			$this->messages->add(_('The ISO code entered is already assigned to another language'), MSG_TYPE_ERROR);
			$this->parent->delete();
			return NULL;
		}
		$ret = $language->CreateLanguage($name, $isoname, $description, $enabled, $this->parent->get('IdNode'));
		$this->UpdatePath();
		return $ret;
	}

	/**
	 *  Deletes the Language and its dependencies.
	 * @return unknown
	 */
	function DeleteNode()
	{
		$language = new Language($this->parent->get('IdNode'));
		$language->DeleteLanguage();

		$nodeProperty = new NodeProperty();
		$nodeProperty->cleanUpPropertyValue('language', $this->parent->get('IdNode'));
	}

	/**
	 *  Calls to method for updating the Name on the database.
	 * @param string name
	 * @return unknown
	 */
	function RenameNode($name = null)
	{
		$lang = new Language($this->parent->get('IdNode'));
		$lang->SetName($name);
		$this->UpdatePath();
	}

	/**
	 *  Gets the documents which have been written in the language.
	 * @return array
	 */
	function GetDependencies()
	{
		$sql = "SELECT DISTINCT IdDoc FROM StructuredDocuments WHERE IdLanguage='" . $this->parent->get('IdNode') . "'";
		$this->dbObj->Query($sql);

		$deps = array();
		while (!$this->dbObj->EOF) {
			$deps[] = $this->dbObj->row["IdDoc"];
			$this->dbObj->Next();
		}
		return $deps;
	}
}
