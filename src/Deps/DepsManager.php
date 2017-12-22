<?php
namespace Ximdex\Deps;

use Ximdex\Logger;
use Ximdex\Utils\Factory;

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
//

class DepsManager
{

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


    const NODE2ASSET = 'RelNode2Asset';

    const XML2XML = 'RelXml2Xml';
    
    const DOCFOLDER_TEMPLATESINC = 'RelDocumentFolderToTemplatesIncludeFile';

    /**
     * Returns the model object specified by "$tableName" name or NULL
     * @param $rel
     * @param $idSource
     * @param $idTarget
     * @return bool
     */
    function set($rel, $idSource, $idTarget)
    {
        $object = $this->getModel($rel);
        if (!is_object($object)) return false;

        $res = $object->find(ALL, 'source = %s and target = %s', array($idSource, $idTarget));

        if (empty($res)) {
            $object->set('target', $idTarget);
            $object->set('source', $idSource);

            if (!$object->add()) {
                Logger::error('Inserting dependency');
                return false;
            }
        } else {

        }

        return true;
    }

    /**
     * Inserts a row in a relation table
     * @param $tableName
     * @param null $id
     * @return mixed
     */
    private function getModel($tableName, $id = NULL)
    {
        $factory = new Factory(XIMDEX_ROOT_PATH . "/src/Models/", $tableName);
        $object = $factory->instantiate(NULL, $id, '\Ximdex\Models');

        if (!is_object($object)) {
            Logger::error(sprintf("Can't instantiate a %s model", $tableName));
        }
        return $object;
    }

    /**
     * From a given target node returns its source node
     * @param $rel
     * @param $target
     * @return bool|null
     */
    function getByTarget($rel, $target)
    {
        $object = $this->getModel($rel);
        if (!is_object($object)) return false;

        $result = $object->find('source', 'target = %s', array($target), MONO);

        return count($result) > 0 ? $result : NULL;
    }

    /**
     * From a given source node returns its target nodes
     * @param $rel
     * @param $source
     * @return array|bool
     */
    function getBySource($rel, $source)
    {
        $object = $this->getModel($rel);
        if (!is_object($object)) return false;
        $result = $object->find('target', 'source = %s', array($source), MONO);
        return count($result) > 0 ? $result : array();
    }

    /**
     * Deletes a row in a relation table
     * @param $rel
     * @param $idSource
     * @param $idTarget
     * @return bool
     */
    function delete($rel, $idSource, $idTarget)
    {
        $object = $this->getModel($rel);
        if (!is_object($object)) return false;

        $object->set('target', $idTarget);
        $object->set('source', $idSource);

        $result = $object->find('id', 'source = %s AND target = %s', array($idSource, $idTarget), MONO);

        if (sizeof($result) != 1) {
            Logger::error('IN query');
            return false;
        }
        $objectLoaded = $this->getModel($rel, $result[0]);
        return $objectLoaded->delete();
    }

    /**
     * Deletes all relations for a source node
     * @param $rel
     * @param $idSource
     * @return bool
     */
    function deleteBySource($rel, $idSource)
    {
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
     * @param $rel
     * @param $idTarget
     * @return bool
     */
    function deleteByTarget($rel, $idTarget)
    {
        $object = $this->getModel($rel);
        if (!is_object($object)) return false;

        $result = $object->find('id', 'target = %s', array($idTarget), MONO);

        if (sizeof($result) > 0) {
            $object->deleteAll('id in (%s)', array(implode(', ', $result)), false);
        }

        return true;
    }
}