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

namespace Ximdex\NodeTypes;

use Ximdex\Models\Language;
use Ximdex\Models\NodeProperty;

/**
 * @brief Handles the languages in wich could be written the documents
 */
class LanguageNode extends Root
{
	/**
	 * Calls for add a row to Languages table
	 * 
	 * {@inheritDoc}
	 * @see \Ximdex\NodeTypes\Root::createNode()
	 */
	function createNode(string $name = null, int $parentID = null, int $nodeTypeID = null, int $stateID = null, string $isoname = null
	    , string $description = null, int $enabled = null)
	{
		$language = new Language();
		$result = $language->find('IdLanguage', 'IsoName = %s', array($isoname));
		if ($result) {
			$this->messages->add(_('The ISO code entered is already assigned to another language'), MSG_TYPE_ERROR);
			$this->parent->delete();
			return null;
		}
		$ret = $language->CreateLanguage($name, $isoname, $description, $enabled, $this->parent->get('IdNode'));
		$this->UpdatePath();
		return $ret;
	}

	/**
	 * Deletes the Language and its dependencies
	 * 
	 * {@inheritDoc}
	 * @see \Ximdex\NodeTypes\Root::deleteNode()
	 */
	public function deleteNode() : bool
	{
		$language = new Language($this->parent->get('IdNode'));
		$language->DeleteLanguage();
		$nodeProperty = new NodeProperty();
		$nodeProperty->cleanUpPropertyValue('language', $this->parent->get('IdNode'));
		return true;
	}

	/**
	 * {@inheritDoc}
	 * @see \Ximdex\NodeTypes\Root::renameNode()
	 */
	public function renameNode(string $name) : bool
	{
		$lang = new Language($this->parent->get('IdNode'));
		$lang->SetName($name);
		$this->updatePath();
		return true;
	}

	/**
	 * Gets the documents which have been written in the language
	 * 
	 * {@inheritDoc}
	 * @see \Ximdex\NodeTypes\Root::getDependencies()
	 */
	public function getDependencies() : array
	{
		$sql = "SELECT DISTINCT IdDoc FROM StructuredDocuments WHERE IdLanguage='" . $this->parent->get('IdNode') . "'";
		$this->dbObj->Query($sql);
		$deps = array();
		while (! $this->dbObj->EOF) {
			$deps[] = $this->dbObj->row["IdDoc"];
			$this->dbObj->Next();
		}
		return $deps;
	}
}
