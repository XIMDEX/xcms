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



require_once(XIMDEX_ROOT_PATH . '/inc/model/RelSectionXimlet.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/patterns/Factory.class.php');

class DepsManager {

	/**
	 *
	 * @var string
	 */
	const SECTION_XIMLET = 'RelSectionXimlet';
	/**
	 *
	 * @var string
	 */
	const STRDOC_XIMLET = 'RelStrdocXimlet';
	/**
	 *
	 * @var string
	 */
	const BULLETIN_XIMLET = 'RelBulletinXimlet';
	/**
	 *
	 * @var string
	 */
	const STRDOC_NODE = 'RelStrdocNode';
	/**
	 *
	 * @var string
	 */
	const STRDOC_TEMPLATE = 'RelStrdocTemplate';
	/**
	 *
	 * @var string
	 */
	const STRDOC_ASSET = 'RelStrdocAsset';
	/**
	 *
	 * @var string
	 */
	const STRDOC_CSS = 'RelStrdocCss';
	/**
	 *
	 * @var string
	 */
	const STRDOC_SCRIPT = 'RelStrdocScript';
	/**
	 *
	 * @var string
	 */
	const STRDOC_STRUCTURE = 'RelStrdocStructure';

	/**
	 * Returns the model object specified by "$tableName" name or NULL
	 * @param string $tableName
	 * @return object
	 */
	private function getModel($tableName, $id = NULL) {
		$factory = new Factory(XIMDEX_ROOT_PATH . "/inc/model/", $tableName);
		$object = $factory->instantiate(NULL, $id);

		if (!is_object($object)) {
			XMD_Log::error(sprintf("Can't instantiate a %s model", $tableName));
		}
		return $object;
	}
	/**
	 * Inserts a row in a relation table
	 * @param const $rel
	 * @param int $idSource
	 * @param int $idTarget
	 * @return true / false
	 */
	function set($rel, $idSource, $idTarget) {
		$res=array();
		$object = $this->getModel($rel);
		if (!is_object($object)) return false;

		$res=$object->find(ALL,'source = %s and target = %s',array($idSource,$idTarget));

		if(empty($res)){
			$object->set('target', $idTarget);
			$object->set('source', $idSource);

			if (!$object->add()) {
				XMD_Log::error('Inserting dependency');
				return false;
			}
		}
		else{
			
		}

		return true;
	}

	/**
	 * From a given target node returns its source nodes
	 * @param const $rel
	 * @param int $idTarget
	 * @return array / NULL
	 */
	function getByTarget($rel, $target) {

		$object = $this->getModel($rel);
		if (!is_object($object)) return false;

		$result = $object->find('source', 'target = %s', array($target), MONO);

		return sizeof($result) > 0 ? $result : NULL;
	}

	/**
	 * From a given source node returns its target nodes
	 * @param const $rel
	 * @param int $idSource
	 * @return array / NULL
	 */
	function getBySource($rel, $source) {
		$object = $this->getModel($rel);
		if (!is_object($object)) return false;
		$result = $object->find('target', 'source = %s', array($source), MONO);
		return sizeof($result) > 0 ? $result : NULL;
	}

	/**
	 * Deletes a row in a relation table
	 * @param const $rel
	 * @param int $idSource
	 * @param int $idTarget
	 * @return true / false
	 */
	function delete($rel, $idSource, $idTarget) {

		$object = $this->getModel($rel);
		if (!is_object($object)) return false;

		$object->set('target', $idTarget);
		$object->set('source', $idSource);

		$result = $object->find('id', 'source = %s AND target = %s', array($idSource, $idTarget), MONO);

		if (sizeof($result) != 1) {
			XMD_Log::error('IN query');
			return false;
		}
		$objectLoaded = $this->getModel($rel, $result[0]);
		return $objectLoaded->delete();
	}

	/**
	 * Deletes all relations for a source node
	 * @param const $rel
	 * @param int $idSource
	 * @return true / false
	 */
	function deleteBySource($rel, $idSource) {

		$object = $this->getModel($rel);
		if (!is_object($object)) return false;

		$result = $object->find('id', 'source = %s', array($idSource), MONO);

		if (sizeof($result) > 0) {
			$object->deleteAll('id in (%s)', array(implode(', ', $result)), false);
		}

		return true;
	}

	/**
	 * Deletes all relations for a target node
	 * @param const $rel
	 * @param int $idTarget
	 * @return true / false
	 */
	function deleteByTarget($rel, $idTarget) {

		$object = $this->getModel($rel);
		if (!is_object($object)) return false;

		$result = $object->find('id', 'target = %s', array($idTarget), MONO);

		if (sizeof($result) > 0) {
			$object->deleteAll('id in (%s)', array(implode(', ', $result)), false);
		}

		return true;
	}
}
?>
