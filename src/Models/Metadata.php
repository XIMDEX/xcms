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

use DateTime;
use Ximdex\Runtime\App;
use Ximdex\Runtime\Session;
use Ximdex\Data\GenericData;
use Ximdex\Properties\InheritedPropertiesManager;
use Ximdex\Properties\InheritableProperty;

class Metadata extends GenericData
{
    const META_FILES = ['image', 'file', 'link'];
    
    const META_TYPES = ['integer', 'float', 'text', 'boolean', 'date', 'array', 'image', 'link', 'file'];
    
    const TYPE_DATE = 'date';

    const TYPE_IMAGE = 'image';

    public $_idField = 'idMetadata';
    
    public $_table = 'Metadata';
    
    public $_metaData = array
    (
        'idMetadata' => array('type' => 'int(12)', 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'name' => array('type' => 'varchar(255)', 'not_null' => 'true'),
        'defaultValue' => array('type' => 'text', 'not_null' => 'false'),
        'type' => array('type' => 'varchar(255)', 'not_null' => 'true')
    );
    
    public $_uniqueConstraints = array(
        'name' => array('name'),
    );
    
    public $_indexes = array('idMetadata');
    
    public $idMetadata;
    
    public $name;
    
    public $defaultValue;
    
    public $type = 'text';
    
    public $schemeId;

    public function getMetadataSchemesAndGroups(bool $withValuesCount = false): array
    {
        $query = sprintf('SELECT MetadataGroup.idMetadataGroup, MetadataGroup.name as groupName,
            MetadataScheme.name as sectionName, MetadataScheme.idMetadataScheme 
            FROM MetadataScheme 
            JOIN MetadataGroup ON MetadataScheme.idMetadataScheme = MetadataGroup.idMetadataScheme 
            ORDER BY MetadataScheme.name, MetadataGroup.name');
        $dbObj = new \Ximdex\Runtime\Db();
        if ($dbObj->query($query) === false) {
            throw new \Exception('Query error in metadata scheme retrieve operation');
        }
        $returnArray = array();
        while (! $dbObj->EOF) {
            $idSection = $dbObj->getValue('idMetadataScheme');
            $group = [
                'id' => $dbObj->getValue('idMetadataGroup'),
                'name' => $dbObj->getValue('groupName')
            ];
            if (! isset($returnArray[$idSection])) {
                $returnArray[$idSection] = [
                    'id' => $dbObj->getValue('idMetadataScheme'), 
                    'name' => $dbObj->getValue('sectionName'), 
                    'groups' => []
                ];
            }
            $group['metadata'] = $this->getMetadataByMetagroup($group['id'], $withValuesCount);
            $returnArray[$idSection]['groups'][$group['id']] = $group;
            $dbObj->next();
        }
        return $returnArray;
    }
    
    public function getMetadataSchemeAndGroupByNodeType(int $idNodeType, int $nodeId = null): array
    {
        if (! $idNodeType) {
            throw new \Exception(_('No node type ID given to obtain metadata schemes'));
        }
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf('SELECT MetadataGroup.idMetadataGroup, MetadataGroup.name as groupName,
              MetadataScheme.name as sectionName, MetadataScheme.idMetadataScheme
              FROM RelMetadataSchemeNodeType
              JOIN MetadataScheme ON MetadataScheme.idMetadataScheme = RelMetadataSchemeNodeType.idMetadataScheme
              JOIN MetadataGroup ON MetadataScheme.idMetadataScheme = MetadataGroup.idMetadataScheme
              WHERE idNodeType = %s
              AND (
                SELECT COUNT(*) 
                FROM RelMetadataGroupMetadata 
                WHERE RelMetadataGroupMetadata.idMetadataGroup = MetadataGroup.idMetadataGroup 
                    AND RelMetadataGroupMetadata.enabled) > 0', $idNodeType);
        if ($nodeId) {
            
            // Load inheritable properties for this node in order to obtain the applied metadata schemes in its section
            $propMng = new InheritedPropertiesManager();
            $values = $propMng->getValues($nodeId, true, [InheritableProperty::METADATA_SCHEME]);
            if (! $values) {
                return [];
            }
            $inheritedGroups = [];
            foreach ($values[InheritableProperty::METADATA_SCHEME] as $group) {
                $inheritedGroups[] = $group['Id'];
            }
            if ($inheritedGroups) {
                $query .= ' AND MetadataScheme.idMetadataScheme IN (' . implode(', ', $inheritedGroups) . ')';
            }
        }
        $query .= ' ORDER BY MetadataScheme.name, MetadataGroup.name';
        if ($dbObj->query($query) === false) {
            throw new \Exception('Query error in metadata scheme retrieve operation');
        }
        $returnArray = array();
        while (! $dbObj->EOF) {
            $idSection = $dbObj->getValue('idMetadataScheme');
            $group = [
                'id' => $dbObj->getValue('idMetadataGroup'),
                'name' => $dbObj->getValue('groupName')
            ];
            if (! isset($returnArray[$idSection])) {
                $returnArray[$idSection] = [
                    'groups' => [],
                    'name' => $dbObj->getValue('sectionName')
                ];
            }
            if (! is_null($nodeId)) {
                $metadata = $this->getMetadataByMetagroupAndNodeId($group['id'], $nodeId);
                $group['metadata'] = $metadata;
            }
            array_push($returnArray[$idSection]['groups'], $group);
            $dbObj->next();
        }
        return $returnArray;
    }
    
    public function getMetadataByMetagroup(int $idGroup, bool $withValuesCount = false): array
    {
        $query = sprintf('SELECT Metadata.name, Metadata.type, RelMetadataGroupMetadata.required, 
            RelMetadataGroupMetadata.idRelMetadataGroupMetadata, RelMetadataGroupMetadata.readonly, RelMetadataGroupMetadata.enabled
            FROM RelMetadataGroupMetadata
            JOIN Metadata ON RelMetadataGroupMetadata.idMetadata = Metadata.idMetadata
            WHERE idMetadataGroup = %s', $idGroup);
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->query($query);
        $returnArray = [];
        while (! $dbObj->EOF) {
            $id = $dbObj->getValue('idRelMetadataGroupMetadata');
            $returnArray[$id] = [
                'name' => $dbObj->getValue('name'),
                'id' => $id,
                'type' => $dbObj->getValue('type'),
                'required' => (bool) $dbObj->getValue('required'),
                'readonly' => (bool) $dbObj->getValue('readonly'),
                'enabled' => (bool) $dbObj->getValue('enabled')
            ];
            if ($withValuesCount) {
                $returnArray[$id]['values'] = Metadata::countMetadataValues($id, $idGroup);
            }
            $dbObj->next();
        }
        return $returnArray;
    }

    public function deleteMetadataValuesByNodeIdAndGroupId(int $idNode, int $idGroup)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf('DELETE mv FROM MetadataValue mv JOIN RelMetadataGroupMetadata ON 
            mv.idRelMetadataGroupMetadata = RelMetadataGroupMetadata.idRelMetadataGroupMetadata 
            WHERE idNode = %s AND idMetadataGroup = %s AND readonly IS FALSE', $idNode, $idGroup);
        $dbObj->execute($query);
        $valid = $dbObj->EOF;
        return $valid;
    }

    public function addMetadataValuesByNodeId(array $metadataArray, int $idNode)
    {
        $fail = false;
        $dbObj = new \Ximdex\Runtime\Db();
        foreach ($metadataArray as $key => $value) {
            if (! empty($value)) {
                
                // If the value is only for read continue to the next one without saving
                try {
                    if (self::getReadOnlyValueByRel($key)) {
                        continue;
                    }
                } catch (\Exception $e) {
                    $this->messages->add($e->getMessage(), MSG_TYPE_ERROR);
                    $fail = true;
                    break;
                }
                $query = sprintf('INSERT INTO MetadataValue (idNode, idRelMetadataGroupMetadata, value) VALUES (%s, %s, \'%s\')'
                    , $idNode, $key, $value);
                if ($dbObj->execute($query) === false) {
                    $fail = true;
                    break;
                }
            }
        }
        if (! $fail) {
            $this->messages->add(_('The metadata has been successfully added'), MSG_TYPE_NOTICE);
            return true;
        }
        $this->messages->add(_('The operation has failed'), MSG_TYPE_ERROR);
        return false;
    }

    public static function relMetadataAndGroup(int $idMetadata, int $idGroup, bool $required = false, bool $readonly = false
        , $enabled = true) : int
    {
        $metadata = new Metadata($idMetadata);
        if (! $metadata->get('idMetadata')) {
                throw new \Exception(_('Metadata selected does not exists'));
        }
        if (! $metadata->get('defaultValue') and $required and $readonly) {
            throw new \Exception(_('A metadata without default value is not valid for required and read only properties'));
        }
        $query = "SELECT * FROM RelMetadataGroupMetadata WHERE idMetadataGroup = {$idGroup} AND idMetadata = {$idMetadata}";
        $dbObj = new \Ximdex\Runtime\Db();
        if ($dbObj->query($query) === false) {
            throw new \Exception(_("Error making query for the relation of metadata {$idMetadata} and group {$idGroup}"));
        }
        if ($dbObj->numRows) {
            throw new \Exception(_('The relation of metadata and group is already defined'));
        }
        $query = 'INSERT INTO RelMetadataGroupMetadata (idMetadataGroup, idMetadata, required, readonly, enabled)' 
            . ' VALUES (' . $idGroup . ', ' . $idMetadata . ', ' . (int) $required . ', ' . (int) $readonly . ', ' . (int) $enabled . ')';
        if ($dbObj->execute($query) === false) {
            throw new \Exception(_('Could not create the relation between metadata and group'));
        }
        return (int) $dbObj->newID;
    }
    
    public static function updateRelMetadataAndGroup(int $idRel, bool $required = false, bool $readonly = false, $enabled = true) : int
    {
        $sql = "SELECT idMetadata AS id FROM RelMetadataGroupMetadata WHERE idRelMetadataGroupMetadata = {$idRel}";
        $dbObj = new \Ximdex\Runtime\Db();
        if ($dbObj->query($sql) === false) {
            throw new \Exception(_('Could not retrieve the relation between metadata and group'));
        }
        if (! $dbObj->getValue('id')) {
            return 0;
        }
        $metadata = new Metadata((int) $dbObj->getValue('id'));
        if (! $metadata->get('idMetadata')) {
            throw new \Exception(_('Metadata selected does not exists'));
        }
        if (! $metadata->get('defaultValue') and $required and $readonly) {
            throw new \Exception(_('A metadata without default value is not valid for required and read only properties'));
        }
        $query = 'UPDATE RelMetadataGroupMetadata'
            . ' SET required = ' . (int) $required . ', readonly = ' . (int) $readonly . ', enabled = ' . (int) $enabled
            . " WHERE idRelMetadataGroupMetadata = {$idRel}";
        if ($dbObj->execute($query) === false) {
            throw new \Exception(_('Could not update the relation between metadata and group'));
        }
        return (int) $dbObj->numRows;
    }
    
    public static function deleteRelMetadataAndGroup(int $idRel) : int
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $query = "DELETE FROM RelMetadataGroupMetadata WHERE idRelMetadataGroupMetadata = {$idRel}";
        if ($dbObj->execute($query) === false) {
            throw new \Exception(_('Could not delete the relation between metadata and group'));
        }
        return (int) $dbObj->numRows;
    }
    
    /**
     * Get metadata from node and group. Caution: If group is null, metadata name can be override
     *  
     * @param int $idNode
     * @param int $idGroup
     * @return array
     */
    public static function getByNodeAndGroup(int $idNode, int $idGroup = null)
    {
        $metadata = [];
        $query = sprintf(
            'SELECT Metadata.name as name, MetadataValue.value as value, Metadata.defaultValue
                as defaultValue, Metadata.type as type  FROM RelMetadataGroupMetadata JOIN  Metadata ON 
                RelMetadataGroupMetadata.idMetadata = Metadata.idMetadata  LEFT JOIN MetadataValue ON 
                MetadataValue.idRelMetadataGroupMetadata = RelMetadataGroupMetadata.idRelMetadataGroupMetadata 
                WHERE (RelMetadataGroupMetadata.required = TRUE OR MetadataValue.value <> \'\') and idNode = %s',
            $idNode
        );
        if (! is_null($idGroup)) {
            $query .= sprintf(' and MetadataGroup.idMetadataGroup = %s ', $idGroup);
        }
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->query($query);
        while (! $dbObj->EOF) {
            $val = $dbObj->getValue('value');
            $val = ! empty($val) ? $val : $dbObj->getValue('defaultValue');
            $metadata[$dbObj->getValue('name')] = static::getMetadataValue($idNode, $val, $dbObj->getValue('type'));
            $dbObj->Next();
        }
        return $metadata;
    }
    
    public static function countMetadataValues(int $metadataId, int $groupId = null) : int
    {
        $query = "SELECT COUNT(mv.idRelMetadataGroupMetadata) AS total FROM MetadataValue mv 
            JOIN RelMetadataGroupMetadata mg ON mg.idRelMetadataGroupMetadata = mv.idRelMetadataGroupMetadata 
            AND mg.idMetadata = {$metadataId}";
        if ($groupId) {
            $query .= " AND mg.idMetadataGroup = {$groupId}";
        }
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->query($query);
        if ($dbObj->getValue('total')) {
            return (int) $dbObj->getValue('total');
        }
        return 0;
    }
    
    public static function clearSchemeNodeTypes() : int
    {
        $query = 'DELETE FROM RelMetadataSchemeNodeType';
        $dbObj = new \Ximdex\Runtime\Db();
        if ($dbObj->execute($query) === false) {
            throw new \Exception(_('Could not remove the relations between metadata schemes and node types'));
        }
        return (int) $dbObj->newID;
    }
    
    public static function relSchemeAndNodeType(int $schemeId, int $nodeTypeId) : int
    {
        $query = "SELECT * FROM RelMetadataSchemeNodeType WHERE idMetadataScheme = {$schemeId} AND idNodeType = {$nodeTypeId}";
        $dbObj = new \Ximdex\Runtime\Db();
        if ($dbObj->query($query) === false) {
            throw new \Exception(_("Error making query for the relation of metadata scheme: {$schemeId} and node type: {$nodeTypeId}"));
        }
        if ($dbObj->numRows) {
            return 0;
        }
        $query = 'INSERT INTO RelMetadataSchemeNodeType (idMetadataScheme, idNodeType) VALUES (' . $schemeId . ', ' . $nodeTypeId . ')';
        if ($dbObj->execute($query) === false) {
            throw new \Exception(_("Could not create the relation between metadata scheme: {$schemeId} and node type: {$nodeTypeId}"));
        }
        return (int) $dbObj->newID;
    }
    
    private function getMetadataByMetagroupAndNodeId(int $idGroup, int $nodeId): array
    {
        $query = sprintf('SELECT Metadata.name, Metadata.type, RelMetadataGroupMetadata.required,
            RelMetadataGroupMetadata.idRelMetadataGroupMetadata,
            (
            	CASE
                	WHEN MetadataValue.value IS NULL THEN Metadata.defaultValue
                    ELSE MetadataValue.value
                END
            ) AS value
            , RelMetadataGroupMetadata.readonly
            FROM RelMetadataGroupMetadata
            JOIN Metadata ON RelMetadataGroupMetadata.idMetadata = Metadata.idMetadata AND RelMetadataGroupMetadata.enabled IS TRUE
            LEFT JOIN MetadataValue ON MetadataValue.idRelMetadataGroupMetadata = RelMetadataGroupMetadata.idRelMetadataGroupMetadata
            AND MetadataValue.idNode = %s
            WHERE idMetadataGroup = %s', $nodeId, $idGroup);
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->query($query);
        $returnArray = array();
        while (! $dbObj->EOF) {
            $value = static::getMetadataValue($nodeId, $dbObj->getValue('value'), $dbObj->getValue('type'));
            $returnArray[] = [
                'name' => $dbObj->getValue('name'),
                'id' => (int) $dbObj->getValue('idRelMetadataGroupMetadata'),
                'value' => $value,
                'type' => $dbObj->getValue('type'),
                'required' => (bool) $dbObj->getValue('required'),
                'readonly' => (bool) $dbObj->getValue('readonly')
            ];
            $dbObj->next();
        }
        return $returnArray;
    }

    private static function getMetadataValue(int $idNode, string $val = null, string $type = null)
    {
        $result = $val;
        if (strcmp('date', $type) == 0) {
            $result = DateTime::createFromFormat('Y-m-d', $val);
            if ($result) {
                $result = $result->format('Y-m-d');
            } else {
                $result = $val;
            }
        } elseif (in_array($type, static::META_FILES)) {
            $result = is_null($val) || filter_var($val, FILTER_VALIDATE_URL) ? $val : "@@@RMximdex.pathto({$val})@@@";
        }
        $result = static::prepareMacro($idNode, $val);
        return $result;
    }

    private static function prepareMacro(int $idNode, string $value = null)
    {
        $macro = preg_replace('/(@@@)/', '', $value);
        $params = null;
        $result = $value;
        if (strpos($macro, 'MDximdex') === 0) {
            $macro = str_replace('MDximdex.', '', $macro);
            preg_match('/[[:word:]]+\((.*)\)/m', $macro, $params);
            $macro = 'macro' . ucfirst(str_replace('(' . ($params[1] ?? '') . ')', '', $macro));
            $params = static::prepareMacroParams($idNode, $params[1] ?? null);
            if (method_exists(__CLASS__, $macro)) {
                $result = static::$macro($params);
            }
        }
        return $result;
    }

    private static function prepareMacroParams(int $idNode, string $params = null) : ?array
    {
        $result = null;
        if ($params) {
            $result = explode(',', $params);
            foreach ($result as &$param) {
                $param = trim($param);
                if (strtolower($param) === 'this') {
                    $param = $idNode;
                }
            }
        }
        return $result;
    }

    private static function macroCurdate()
    {
        return date('Y-m-d');
    }

    /**
     * Get the current user by token
     *
     * @return string|null
     */
    private static function macroUser() : ?string
    {   
        $name = null;
        $token = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
        if (is_null($token)) {
            $token = isset($_GET['token']) ? $_GET['token'] : null;
        }
        if (! is_null($token)) {
            $user = User::getByToken($token);
            $name = $user->get('Name');
        } else {
            $id = (int) Session::get('userID');
            if (! $id) {
                $id = User::XIMDEX_ID;
            }
            $user = new User($id);
            if ($user->getRealName()) {
                $name = $user->getRealName();
            }
        }
        return is_string($name) ? $name : null;
    }

    private static function macroCurname(array $params) : string
    {
        [$nodeId] = $params;
        $node = new Node($nodeId);
        $nodeName = $node->getNodeName();
        if (App::getValue('PublishPathFormat') == App::PREFIX) {
            $parts = explode('-', $nodeName);
            if (count($parts) > 1) {
                unset($parts[count($parts) - 1]);
                $nodeName = implode('-', $parts);
            }
        }
        return $nodeName;
    }
    
    private static function getReadOnlyValueByRel(int $idRel) : bool
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $query = "SELECT readonly FROM RelMetadataGroupMetadata WHERE idRelMetadataGroupMetadata = $idRel";
        $res = $dbObj->query($query);
        if ($res === false or $dbObj->EOF) {
            throw new \Exception(_("Cannot retrieve readonly data for relation: $idRel"));
        }
        return (bool) $dbObj->GetValue('readonly');
    }
}
