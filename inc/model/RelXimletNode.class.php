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
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../..');
}

require_once XIMDEX_ROOT_PATH . '/inc/model/orm/RelXimletNode_ORM.class.php';

class RelXimletNode extends RelXimletNode_ORM {
	function getXimletForLanguage($idSection, $idLanguage) {
		$ximlet = $this->find('IdXimLetNode', 'IdSectionNode = %s', array($idSection), MONO);
		if (!count($ximlet) > 0) {
			return NULL;
		}
		$languageXimlets = array();
		foreach ($ximlet as $idXimlet) {
			$node = new Node($idXimlet);
			$idLanguageXimlet = $node->class->getChildByLang($idLanguage);
			if ($idLanguageXimlet > 0) {
				$languageXimlets[] = $idLanguageXimlet;
			}
		}
		return !empty($languageXimlets) ? $languageXimlets : NULL;
	}
	
	/**
	 * Gets all sections associateds to a given ximlet
	 *
	 * @param int idXimlet
	 * @return array / NULL
	 */

	function getSections($idXimletLanguage) {
		$node = new Node($idXimletLanguage);
		$idXimlet = $node->get('IdParent');

		$result = $this->find('IdSectionNode', 'IdXimletNode = %s', array($idXimlet), MULTI);

		if (!(count($result) > 0)) {
			return NULL;
		}

		foreach ($result as $resultData) {
			$sections[] = $resultData['IdSectionNode'];
		}

		return $sections;
	}

	/**
	 * Deletes an asocciation ximlet-node
	 * @param int idXimletLanguage
	 * @param int idSection
	 * @return true / false
	 */

	function delete($idXimletLanguage, $idSection) {

		if (is_null($idXimletLanguage) || is_null($idSection)) {
			XMD_Log::error('Params section and ximlet are mandaries');
			return false;
		}

		$node = new Node($idXimletLanguage);
		$idXimlet = $node->get('IdParent');

		$result = $this->find('IdRel', 'IdXimletNode = %s AND IdSectionNode = %s', array($idXimlet, $idSection), MONO);

		if (!(count($result) > 0)) {
			return false;
		}

		parent::GenericData($result[0]);

		if (!is_null(parent::delete())) {
			return true;
		}

		return false;
	}

	/**
	 * Deletes all asocciations by a section node
	 * @param int idSection
	 * @return true / false
	 */

	function deleteBySection($idSection) {
		if (is_null($idSection)) {
			XMD_Log::error('Param section not found');
			return false;
		}

		$result = $this->find('IdRel', 'IdSectionNode = %s', array($idSection), MULTI);

		if (!(count($result) > 0)) {
			XMD_Log::info("Section $idSection hasnt any ximlet dependency");
			return false;
		}

		foreach ($result as $resultData) {
			parent::GenericData($resultData['IdRel']);		
			parent::delete();
		}

		return true;
	}
}
?>
