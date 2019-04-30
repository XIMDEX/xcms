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

namespace Ximdex\Deps;

use Ximdex\Logger;
use Ximdex\Utils\Factory;

class DepsManager
{
    const SECTION_XIMLET = 'RelSectionXimlet';
    
    const STRDOC_XIMLET = 'RelStrdocXimlet';
    
    const STRDOC_TEMPLATE = 'RelStrdocTemplate';
    
    const NODE2ASSET = 'RelNode2Asset';
    
    const XML2XML = 'RelXml2Xml';
    
    const DOCFOLDER_TEMPLATESINC = 'RelDocumentFolderToTemplatesIncludeFile';

    /**
     * Returns the model object specified by "$tableName" name or NULL
     * 
     * @param string $rel
     * @param int $idSource
     * @param int $idTarget
     * @return bool
     */
    public function set(string $rel, int $idSource, int $idTarget) : bool
    {
        $object = $this->getModel($rel);
        if (! is_object($object)) {
            return false;
        }
        $res = $object->find(ALL, 'source = %s and target = %s', array($idSource, $idTarget));
        if (empty($res)) {
            $object->set('target', $idTarget);
            $object->set('source', $idSource);
            if (! $object->add()) {
                Logger::error('Inserting dependency');
                return false;
            }
        }
        return true;
    }

    /**
     * From a given target node returns its source node
     * 
     * @param string $rel
     * @param int $target
     * @return boolean|NULL|array
     */
    public function getByTarget(string $rel, int $target)
    {
        $object = $this->getModel($rel);
        if (! is_object($object)) {
            return false;
        }
        $result = $object->find('source', 'target = %s', array($target), MONO);
        return count($result) > 0 ? $result : null;
    }

    /**
     * From a given source node returns its target nodes
     * 
     * @param string $rel
     * @param string $source
     * @param array An array with Dependencies types ID that will be exclude in the search
     * @return boolean|array
     */
    public function getBySource(string $rel, string $source, array $exclude = [])
    {
        $object = $this->getModel($rel);
        if (! is_object($object)) {
            return false;
        }
        $sqlConditions = 'source = %s';
        if ($exclude) {
            $sqlConditions .= ' and target not in (select distinct idNodeDependent from Dependencies where idNodeMaster = ' 
                . $source . ' and (false';
            foreach ($exclude as $exclusionType) {
                $sqlConditions .= ' or DepType = ' . $exclusionType;
            }
            $sqlConditions .= '))';
        }
        $result = $object->find('target', $sqlConditions, array($source), MONO);
        return count($result) > 0 ? $result : array();
    }

    /**
     * Deletes a row in a relation table
     * 
     * @param string $rel
     * @param int $idSource
     * @param int $idTarget
     * @return boolean|int
     */
    public function delete(string $rel, int $idSource, int $idTarget)
    {
        $object = $this->getModel($rel);
        if (! is_object($object)) {
            return false;
        }
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
     * 
     * @param string $rel
     * @param int $idSource
     * @return boolean
     */
    public function deleteBySource(string $rel, int $idSource) : bool
    {
        $object = $this->getModel($rel);
        if (! is_object($object)) {
            return false;
        }
        $result = $object->find('id', 'source = %s', array($idSource), MONO);
        if (sizeof($result) > 0) {
            if ($object->deleteAll('id in (%s)', array(implode(', ', $result)), false) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Deletes all relations for a target node
     * 
     * @param string $rel
     * @param int $idTarget
     * @return boolean
     */
    public function deleteByTarget(string $rel, int $idTarget) : bool
    {
        $object = $this->getModel($rel);
        if (! is_object($object)) {
            return false;
        }
        $result = $object->find('id', 'target = %s', array($idTarget), MONO);
        if (sizeof($result) > 0) {
            if ($object->deleteAll('id in (%s)', array(implode(', ', $result)), false) === false) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Inserts a row in a relation table
     *
     * @param string $tableName
     * @param int $id
     * @return mixed
     */
    private function getModel(string $tableName, int $id = null)
    {
        $factory = new Factory(XIMDEX_ROOT_PATH . "/src/Models/", $tableName);
        $object = $factory->instantiate(null, [$id], '\Ximdex\Models');
        if (! is_object($object)) {
            Logger::error(sprintf("Can't instantiate a %s model", $tableName));
        }
        return $object;
    }
}
