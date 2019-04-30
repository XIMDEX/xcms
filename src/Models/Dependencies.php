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

use Ximdex\Runtime\DataFactory;
use Ximdex\Logger;
use Ximdex\Models\ORM\DependenciesOrm;

class Dependencies extends DependenciesOrm
{
    const ASSET = "asset";
    
    const CHANNEL = "channel";
    
    const LANGUAGE = "language";
    
    const SCHEMA = "schema";
    
    const SYMLINK = "symlink";
    
    const TEMPLATE = "template";
    
    const XIMLET = "ximlet";
    
    const XML = "xml";
    
    const XIMLINK = "ximlink";

    /**
     * It will be something like
     * "css" => 1
     * "assets" => 2
     * "ximlets" => 3
     * It means, the key is the dependence name,
     * and the value is the ID.
     * 
     * @var $deptypes array
     */
    private static $depTypes = null;

    public function __construct()
    {
        if (! self::$depTypes) {
            self::buildDepTypes();
        }
        parent::__construct();
    }

    /**
     * Build static property deptypes
     */
    private static function buildDepTypes()
    {
        Logger::debug("Building DepTypes array");
        $dependenceTypes = new DependenceTypes();
        $result = $dependenceTypes->findAll();
        foreach ($result as $dependenceType) {
            $idType = $dependenceType["IdDepType"];
            $depType = strtolower($dependenceType["Type"]);
            self::$depTypes[$depType] = $idType;
        }
    }

    /**
     * Get IdDepType from a dependence type name
     * 
     * @param string $depType Searched name
     * @return string Id for the deptype if it exists. Otherwise return false
     */
    public function getDepTypeId(string $depType)
    {
        // If the array it doesn't exist, load it
        if (! self::$depTypes || ! is_array(self::$depTypes)) {
            self::buildDepTypes();
        }
        if (is_numeric($depType)) {
            return $this->getDepTypeName($depType) ? $depType : false;
        }
        $depType = strtolower($depType);
        
        // If don't found this deptype, reload the array
        // This deptype could be added after
        if (! array_key_exists($depType, self::$depTypes)) {
            self::buildDepTypes();
        }

        // Return the array value or false
        return array_key_exists($depType, self::$depTypes) ? self::$depTypes[$depType] : false;
    }

    public function getDepTypeName(int $idDepType)
    {
        // If the array it doesn't exist, load it
        if (! self::$depTypes || ! is_array(self::$depTypes)) {
            self::buildDepTypes();
        }

        // IdDepType have to be a number
        if (! is_numeric($idDepType)) {
            return false;
        }
        if (! is_array(self::$depTypes)) {
            return false;
        }

        // If don't found this deptype id, reload the array
        // This deptype could be added after
        if (! in_array($idDepType, self::$depTypes)) {
            self::buildDepTypes();
        }

        // Return the array key or false
        return in_array($idDepType, self::$depTypes) ? array_search($idDepType, self::$depTypes) : false;
    }

    public function insertDependence(int $master, int $dependent, string $type, int $version = null)
    {
        $type = $this->getDepTypeId($type);
        if (! $type) {
            return false;
        }
        $this->set('IdNodeMaster', $master);
        $this->set('IdNodeDependent', $dependent);
        $this->set('DepType', $type);
        if (isset($version)) {
            $this->set('version', $version);
        } else {
            $dataFactory = new DataFactory($master);
            $idVersion = $dataFactory->getLastVersion();
            $this->set('version', $idVersion);
        }
        if (! $this->existsDependence($master, $dependent, $type, $version)) {
            return $this->add();
        }
        return false;
    }

    /**
     * Deletes all the dependencies (ancestor) of this node ("free")
     * 
     * @param int $nodeID
     */
    public function deleteDependentNode(int $nodeID)
    {
        $this->deleteAll("IdNodeDependent = %s", array($nodeID));
    }

    /**
     * Delete all dependencies for a master node and an given type
     * 
     * @param int $nodeID
     * @param string $type
     */
    public function deleteMasterNodeAndType(int $nodeID, string $type)
    {
        $type = $this->getDepTypeId($type);

        // Deletes all the dependencies (ancestor) of this node ("free")
        $this->deleteAll("IdNodeMaster = %s AND DepType = %s", array($nodeID, $type));
    }

    /**
     * Deletes all the dependencies (ancestor) of this node ("free")
     * 
     * @param int $nodeID
     * @param int $version
     */
    public function deleteDependenciesByDependentAndVersion(int $nodeID, int $version)
    {
        // Last versions should be deleted
        $this->deleteAll("IdNodeDependent = %s and version = %s", array($nodeID, $version));
    }
    
    public function deleteByMasterAndVersion(int $nodeID, int $version)
    {
        // Deletes all the dependencies (ancestor) of this node ("free")
        // Last versions should be deleted
        $this->deleteAll("IdNodeMaster = %s and version= %s", array($nodeID, $version));
    }

    public function deleteDependenciesByDependentAndType(int $nodeID, string $type)
    {
        $type = $this->getDepTypeId($type);
        $this->deleteAll("IdNodeDependent = %s and DepType= %s", array($nodeID, $type));
    }

    /**
     * Get all the nodes which are dependant of the current one
     * 
     * @param int $nodeID
     * @return array|boolean
     */
    public function getDependenciesDependentNode(int $nodeID)
    {
        return $this->find("IdNodeMaster", "IdNodeDependent = %s", array($nodeID), MONO);
    }

    /**
     * Gets all the nodes which are pointing the the current one with dependencies of  kind $type
     * 
     * @param int $nodeID
     * @return array|boolean
     */
    public function getDependenciesMasterNode(int $nodeID)
    {
        return $this->find("IdNodeMaster", "IdNodeMaster = %s", array($nodeID), MONO);
    }

    public function getDependenciesMasterByType(int $nodeID, string $type, $version = null)
    {
        $type = $this->getDepTypeId($type);
        
        // Gets all the nodes which are pointing the the current one with dependencies of kind $type
        $condition = "IdNodeMaster = %s AND DepType = %s";
        $values = array($nodeID, $type);
        if (! is_null($version)) {
            $condition .= " AND version = %s";
            $values[] = $version;
        }
        $result = $this->find("IdNodeDependent", $condition, $values, MONO);
        return is_array($result) ? array_unique($result) : false;
    }

    public function getMastersByType(int $nodeID, string $type, int $version = null)
    {
        $type = $this->getDepTypeId($type);
        $condition = "IdNodeDependent = %s AND DepType = %s";
        $values = array($nodeID, $type);
        if (! is_null($version)) {
            $condition .= " AND version = %s";
            $values[] = $version;
        }
        $result = $this->find("IdNodeMaster", $condition, $values, MONO);
        return is_array($result) ? array_unique($result) : false;
    }

    public function existsDependence(int $master, int $dependent, string $type, int $version = null)
    {
        $type = $this->getDepTypeId($type);
        
        // Gets all the dependencies (ancestor) of this node
        $condition = "IdNodeMaster = %s AND IdNodeDependent = %s and DepType = %s and version = %s";
        $values = array($master, $dependent, $type, (int) $version);
        return $this->count($condition, $values);
    }

    public function deleteByMasterAndDependent(int $nodeMaster, int $nodeDependent, string $type)
    {
        $type = $this->getDepTypeId($type);
        if (is_null($nodeMaster) || is_null($nodeDependent) || is_null($type)) {
            Logger::info('Params required');
            return false;
        }
        $condition = "IdNodeDependent = %s AND IdNodeMaster = %s AND  DepType = %s";
        $values = array($nodeMaster, $nodeDependent, $type);
        return $this->deleteAll($condition, $values);
    }
}
