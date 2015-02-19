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

use Ximdex\Logger;


if (!defined('XIMDEX_ROOT_PATH')) define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../..');
require_once XIMDEX_ROOT_PATH . '/inc/model/orm/Dependencies_ORM.class.php';
require_once XIMDEX_ROOT_PATH . '/inc/model/DependenceTypes.class.php';


class Dependencies extends Dependencies_ORM
{

    const ASSET = "asset";
    const CHANNEL = "channel";
    const LANGUAGE = "language";
    const SCHEMA = "schema";
    const SYMLINK = "symlink";
    const TEMPLATE = "template";
    const XIMLET = "ximlet";
    const XML ="xml";
    const XIMLINK = "ximlink";


    /**
     * @var deptypes array
     * It will be something like
     * "css" => 1
     * "assets" => 2
     * "ximlets" => 3
     * It means, the key is the dependence name,
     * and the value is the ID.
     */
    private static $depTypes = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (!self::$depTypes) {
            self::buildDepTypes();
        }
        parent::GenericData();
    }

    /**
     * Build static property deptypes
     */
    private static function buildDepTypes()
    {

        Logger::info("Building DepTypes array.");
        $dependenceTypes = new DependenceTypes();
        $result = $dependenceTypes->findAll();
        foreach ($result as $dependenceType) {
            $idType = $dependenceType["IdDepType"];
            $depType = strtolower($dependenceType["Type"]);
            self::$depTypes[$depType] = $idType;
        }
    }

    /**
     * Get IdDepType from a dependence type name.
     * @param $depType String Searched name
     * @return string Id for the deptype if it exists.
     * Otherwise return false.
     */
    public function getDepTypeId($depType)
    {

        //If the array it doesn't exist, load it.
        if (!self::$depTypes || !is_array(self::$depTypes)) {
            self::buildDepTypes();
        }

        if (is_numeric($depType)) {
            return $this->getDepTypeName($depType) ? $depType : false;
        }
        $depType = strtolower($depType);
        //If don't found this deptype, reload the array
        //This deptype could be added after.
        if (!array_key_exists($depType, self::$depTypes)) {
            self::buildDepTypes();
        }

        //Return the array value or false.
        return array_key_exists($depType, self::$depTypes) ? self::$depTypes[$depType] : false;
    }

    /**
     * @param $idDepType
     * @return bool|mixed
     */
    public function getDepTypeName($idDepType)
    {

        //If the array it doesn't exist, load it.
        if (!self::$depTypes || !is_array(self::$depTypes)) {
            self::buildDepTypes();
        }

        //IdDepType have to be a number
        if (!is_numeric($idDepType)) {
            return false;
        }

        if (!is_array(self::$depTypes)) {
            return false;
        }

        //If don't found this deptype id, reload the array
        //This deptype could be added after.
        if (!in_array($idDepType, self::$depTypes)) {
            self::buildDepTypes();
        }

        //Return the array key or false.
        return in_array($idDepType, self::$depTypes) ? array_search($idDepType, self::$depTypes) : false;
    }

    /**
     *
     * @param $master
     * @param $dependent
     * @param $type
     * @param $version
     * @return unknown_type
     */
    public function insertDependence($master, $dependent, $type, $version = null)
    {

        $type = $this->getDepTypeId($type);

        if (!$type) {
            return false;
        }

        $this->set('IdNodeMaster', $master);
        $this->set('IdNodeDependent', $dependent);
        $this->set('DepType', $type);
        if (!empty($version)) {
            $this->set('version', $version);
        } else {
            $dataFactory = new DataFactory($master);
            $idVersion = $dataFactory->GetLastVersion();
            $this->set('version', $idVersion);
        }

        if (!$this->existsDependence($master, $dependent, $type, $version)) {
            return $this->add();
        }
        return false;
    }

    /**
     * Delete all dependencies for a dependent node.
     * @param $nodeID
     * @return unknown_type
     */
    public function deleteDependentNode($nodeID)
    {
        //Deletes all the dependencies (ancestor) of this node ("free")
        $this->deleteAll("IdNodeDependent= %s", array($nodeID));
    }

    /**
     * Delete all dependencies for a master node and an given type.
     * @param int $nodeID
     * @param int /String $type
     * @return unknown_type
     */
    function deleteMasterNodeandType($nodeID, $type)
    {
        $type = $this->getDepTypeId($type);

        //Deletes all the dependencies (ancestor) of this node ("free")
        $this->deleteAll("IdNodeMaster=%s AND DepType=%s", array($nodeID, $type));
    }

    /**
     * Delete all dependencies for a dependent node in a given version.
     * @param $nodeID
     * @param $version
     * @return unknown_type
     */
    function deleteDependenciesByDependentAndVersion($nodeID, $version)
    {
        //Deletes all the dependencies (ancestor) of this node ("free")
        //Last versions should be deleted
        $this->deleteAll("IdNodeDependent = %s and version= %s", array($nodeID, $version));
    }

    function deleteByMasterAndVersion($nodeID, $version)
    {
        //Deletes all the dependencies (ancestor) of this node ("free")
        //Last versions should be deleted
        $this->deleteAll("IdNodeMaster = %s and version= %s", array($nodeID, $version));
    }

    /**
     * Delete all dependencies for a dependent node with a given deptype.
     * @param $nodeID
     * @param $type
     * @return unknown_type
     */
    function deleteDependenciesByDependentAndType($nodeID, $type)
    {
        $type = $this->getDepTypeId($type);
        $this->deleteAll("IdNodeDependent = %s and DepType= %s", array($nodeID, $type));
    }

    /**
     *
     * @param $nodeID
     * @return unknown_type
     */
    function getDependenciesDependentNode($nodeID)
    {

        //Get all the nodes which are dependant of the current one
        return $this->find("IdNodeMaster", "IdNodeDependent= %s", array($nodeID), MONO);
    }

    /**
     *
     * @param $nodeID
     * @return unknown_type
     */
    function getDependenciesMasterNode($nodeID)
    {
        // Gets all the nodes which are pointing the the current one with dependencies of  kind $type
        return $this->find("IdNodeMaster", "IdNodeMaster= %s", array($nodeID), MONO);
    }


    /**
     *
     * @param $nodeID
     * @param $type
     * @param $version
     * @return unknown_type
     */
    function getDependenciesMasterByType($nodeID, $type, $version = null)
    {

        $type = $this->getDepTypeId($type);
        // Gets all the nodes which are pointing the the current one with dependencies of  kind $type
        $condition = "IdNodeMaster = %s AND DepType=%s";
        $values = array($nodeID, $type);

        if (!is_null($version)) {
            $condition .= " AND version = %s";
            $values[] = $version;
        }

        $result = $this->find("IdNodeDependent", $condition, $values, MONO);
        return is_array($result) ? array_unique($result) : false;
    }

    /**
     *
     * @param $nodeID
     * @param $type
     * @param $version
     * @return unknown_type
     */
    function getMastersByType($nodeID, $type, $version = null)
    {
        $type = $this->getDepTypeId($type);

        $condition = "IdNodeDependent = %s AND DepType=%s";
        $values = array($nodeID, $type);

        if (!is_null($version)) {
            $condition .= " AND version = %s";
            $values[] = $version;
        }

        $result = $this->find("IdNodeMaster", $condition, $values, MONO);
        return is_array($result) ? array_unique($result) : false;
    }

    /**
     *
     * @param $master
     * @param $dependent
     * @param $type
     * @param $version
     * @return unknown_type
     */
    function existsDependence($master, $dependent, $type, $version)
    {
        $type = $this->getDepTypeId($type);
        //Gets all the dependencies (ancestor) of this node

        $condition = "IdNodeMaster = %s AND IdNodeDependent = %s and DepType = %s and version = %s";
        $values = array($master, $dependent, $type, $version);
        return $this->count($condition, $values);
    }

    /**
     * Deletes all dependencies between nodes for a given type
     * @param int nodeMaster
     * @param int nodeDependent
     * @param string type
     * @return true / false
     */
    function deleteByMasterAndDependent($nodeMaster, $nodeDependent, $type)
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

