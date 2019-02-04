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
use Ximdex\Data\GenericData;

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
        'IdMetadata' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'name' => array('type' => "varchar(255)", 'not_null' => 'true')
    );
    public $_uniqueConstraints = array(
        'Name' => array('name'),
    );
    public $_indexes = array('IdMetadata');
    public $IdMetadata;
    public $Name = 0;

    public function getMetadataSectionAndGroupByNodeType(int $idNodeType, int $nodeId = null): array
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query(sprintf("SELECT MetadataGroup.idMetadataGroup, MetadataGroup.name as groupName,
              MetadataSection.name as sectionName, MetadataSection.idMetadataSection FROM RelMetadataSectionNodeType 
              JOIN MetadataSection ON MetadataSection.idMetadataSection = RelMetadataSectionNodeType.idMetadataSection 
              JOIN MetadataGroup ON MetadataSection.idMetadataSection = MetadataGroup.idMetadataSection
              WHERE idNodeType = %s", $idNodeType));
        $returnArray = array();
        while (! $dbObj->EOF) {
            $idSection = $dbObj->GetValue('idMetadataSection');
            $group = [
                'id' => $dbObj->GetValue('idMetadataGroup'),
                'name' => $dbObj->GetValue('groupName')
            ];
            if (! isset($returnArray[$idSection])) {
                $returnArray[$idSection] = [
                    'groups' => [],
                    'name' => $dbObj->GetValue('sectionName')
                ];
            }
            if (! is_null($nodeId)) {
                $metadata = $this->getMetadataByMetagroupAndNodeId($group['id'], $nodeId);
                $group['metadata'] = $metadata;
            }
            array_push($returnArray[$idSection]['groups'], $group);
            $dbObj->Next();
        }
        return $returnArray;
    }


    public function getMetadataByMetagroupAndNodeId(int $idGroup, int $nodeId): array
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query(sprintf("SELECT Metadata.name, Metadata.type, RelMetadataGroupMetadata.required,
            RelMetadataGroupMetadata.idRelMetadataGroupMetadata, 
            (
            	CASE
                	WHEN MetadataValue.value IS NULL THEN Metadata.defaultValue
                    ELSE MetadataValue.value
                END
            ) AS value
            FROM RelMetadataGroupMetadata JOIN 
            Metadata ON RelMetadataGroupMetadata.idMetadata =
            Metadata.idMetadata 
            LEFT JOIN MetadataValue ON MetadataValue.idRelMetadataGroupMetadata = RelMetadataGroupMetadata.idRelMetadataGroupMetadata 
            and MetadataValue.idNode = %s
            WHERE idMetadataGroup = %s", $nodeId, $idGroup));
        $returnArray = array();
        while (! $dbObj->EOF) {
            $value = static::getMetadataValue($nodeId, $dbObj->GetValue('value'), $dbObj->GetValue('type'));
            $returnArray[] = [
                'name' => $dbObj->GetValue('name'),
                'id' => $dbObj->GetValue('idRelMetadataGroupMetadata'),
                'value' => $value,
                'type' => $dbObj->GetValue('type'),
                'required' => $dbObj->GetValue('required'),
            ];
            $dbObj->Next();
        }
        return $returnArray;
    }

    public function insertMetadata(int $idGroup, array $metadata): array
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query(sprintf("SELECT Metadata.idMetadata, Metadata.name
            FROM RelMetadataGroupMetadata JOIN 
            Metadata ON RelMetadataGroupMetadata.idMetadata =
            Metadata.idMetadata
            WHERE idMetadataGroup = %s ", $idGroup));
        $returnArray = array();
        while (!$dbObj->EOF) {
            $returnArray[] = [
                'id' => $dbObj->GetValue('idMetadata'),
                'name' => $dbObj->GetValue('name')
            ];
            $dbObj->Next();
        }
        return $returnArray;
    }

    public function deleteMetadataValuesByNodeIdAndGroupId(int $idNode, int $idGroup)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf("DELETE mv FROM MetadataValue mv JOIN RelMetadataGroupMetadata ON 
            mv.idRelMetadataGroupMetadata = RelMetadataGroupMetadata.idRelMetadataGroupMetadata 
            WHERE idNode=%s and idMetadataGroup=%s;", $idNode, $idGroup);
        $dbObj->Execute($query);
        $valid = $dbObj->EOF;
        return $valid;
    }

    public function addMetadataValuesByNodeId(array $metadataArray, int $idNode)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        foreach ($metadataArray as $key => $value) {
            if (! empty($value)) {
                $query = sprintf("INSERT INTO MetadataValue(idNode,idRelMetadataGroupMetadata, value) 
                    VALUES (%s, %s, \"%s\")", $idNode, $key, $value);
                $dbObj->Execute($query);
            }
        }
        if ($dbObj->EOF) {
            $this->messages->add(_('The metadata has been successfully added'), MSG_TYPE_NOTICE);
            return true;
        } else {
            $this->messages->add(_('The operation has failed'), MSG_TYPE_ERROR);
            return false;
        }
    }

    /**
     * Get metadata from node and group. Caution: If group is null, metadata name can be override.
     *
     * @param $idNode
     * @param $idGroup
     *
     * @return array
     */
    public static function getByNodeAndGroup($idNode, $idGroup = null)
    {
        $metadata = [];
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf(
            "SELECT Metadata.name as name, MetadataValue.value as value, Metadata.defaultValue
                as defaultValue, Metadata.type as type  FROM RelMetadataGroupMetadata JOIN  Metadata ON 
                RelMetadataGroupMetadata.idMetadata = Metadata.idMetadata  LEFT JOIN MetadataValue ON 
                MetadataValue.idRelMetadataGroupMetadata = RelMetadataGroupMetadata.idRelMetadataGroupMetadata 
                WHERE (RelMetadataGroupMetadata.required = TRUE OR MetadataValue.value <> '') and idNode = %s",
            $idNode
        );
        if (! is_null($idGroup)) {
            $query .= sprintf(' and MetadataGroup.idMetadataGroup = %s ', $idGroup);
        }
        $dbObj->Query($query);
        while (! $dbObj->EOF) {
            $val = $dbObj->GetValue('value');
            $val = !empty($val) ? $val : $dbObj->GetValue('defaultValue');
            $metadata[$dbObj->GetValue('name')] = static::getMetadataValue($idNode, $val, $dbObj->GetValue('type'));
            $dbObj->Next();
        }
        return $metadata;
    }

    private static function getMetadataValue($idNode, ?string $val, ?string $type)
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

    private static function prepareMacro($idNode, ?string $value)
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

    private static function prepareMacroParams($idNode, ?string $params) : ?array
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
}
