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
    const META_FILES = [
        'image',
        'file',
        'link'
    ];

    public $_idField = 'IdMetadata';
    public $_table = 'Metadata';
    public $_metaData = array(
        'idMetadata' => array('type' => 'int(12)', 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'name' => array('type' => 'varchar(255)', 'not_null' => 'true'),
        'defaultValue' => array('type' => 'text', 'not_null' => 'false'),
        'type' => array('type' => 'varchar(255)', 'not_null' => 'true'),
        'readonly' => array('type' => 'int(1)', 'not_null' => 'true')
    );
    public $_uniqueConstraints = array(
        'name' => array('name'),
    );
    public $_indexes = array('idMetadata');
    public $idMetadata;
    public $name;
    public $defaultValue;
    public $type = 'text';
    public $readonly;

    public function getMetadataSchemeAndGroupByNodeType(int $idNodeType, int $nodeId = null): array
    {
        if (! $idNodeType) {
            throw new \Exception('No node type ID given to obtain metadata schemes');
        }
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf('SELECT MetadataGroup.idMetadataGroup, MetadataGroup.name as groupName,
              MetadataScheme.name as sectionName, MetadataScheme.idMetadataScheme FROM RelMetadataSchemeNodeType
              JOIN MetadataScheme ON MetadataScheme.idMetadataScheme = RelMetadataSchemeNodeType.idMetadataScheme
              JOIN MetadataGroup ON MetadataScheme.idMetadataScheme = MetadataGroup.idMetadataScheme
              WHERE idNodeType = %s', $idNodeType);
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
            $query .= ' AND MetadataScheme.idMetadataScheme IN (' . implode(', ', $inheritedGroups) . ')';
        }
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

    public function getMetadataByMetagroupAndNodeId(int $idGroup, int $nodeId): array
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
            FROM RelMetadataGroupMetadata JOIN
            Metadata ON RelMetadataGroupMetadata.idMetadata =
            Metadata.idMetadata
            LEFT JOIN MetadataValue ON MetadataValue.idRelMetadataGroupMetadata = RelMetadataGroupMetadata.idRelMetadataGroupMetadata
            and MetadataValue.idNode = %s
            WHERE idMetadataGroup = %s', $nodeId, $idGroup);
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->query($query);
        $returnArray = array();
        while (! $dbObj->EOF) {
            $value = static::getMetadataValue($nodeId, $dbObj->getValue('value'), $dbObj->getValue('type'));
            $returnArray[] = [
                'name' => $dbObj->getValue('name'),
                'id' => $dbObj->getValue('idRelMetadataGroupMetadata'),
                'value' => $value,
                'type' => $dbObj->getValue('type'),
                'required' => $dbObj->getValue('required'),
                'readonly' => $dbObj->getValue('readonly')
            ];
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
        $dbObj = new \Ximdex\Runtime\Db();
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
        $dbObj->query($query);
        while (! $dbObj->EOF) {
            $val = $dbObj->getValue('value');
            $val = ! empty($val) ? $val : $dbObj->getValue('defaultValue');
            $metadata[$dbObj->getValue('name')] = static::getMetadataValue($idNode, $val, $dbObj->getValue('type'));
            $dbObj->Next();
        }
        return $metadata;
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
            if ($id) {
                $user = new User($id);
                if ($user->getRealName()) {
                    $name = $user->getRealName();
                }
            }
        }
        return is_string($name) ? $name : null;
    }

    private static function macroCurname(array $params) : string
    {
        [$nodeId] = $params;
        $node = new Node($nodeId);
        $nodeName = $node->get('Name');
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
            throw new \Exception(_("Cannot retrieve readonly data for relation") . ": $idRel");
        }
        return (bool) $dbObj->GetValue('readonly');
    }
}
