<?php

namespace Ximdex\Models;

use DateTime;
use Ximdex\Data\GenericData;


class Metadata extends GenericData
{

    var $_idField = 'IdMetadata';
    var $_table = 'Metadata';
    var $_metaData = array(
        'IdMetadata' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'name' => array('type' => "varchar(255)", 'not_null' => 'true')
    );
    var $_uniqueConstraints = array(
        'Name' => array('name'),
    );
    var $_indexes = array('IdMetadata');
    var $IdMetadata;
    var $Name = 0;


    function getMetadataSectionAndGroupByNodeType(int $idNodeType, int $nodeId = null): array
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query(sprintf("SELECT MetadataGroup.idMetadataGroup, MetadataGroup.name as groupName,
              MetadataSection.name as sectionName, MetadataSection.idMetadataSection FROM RelMetadataSectionNodeType 
              JOIN MetadataSection ON MetadataSection.idMetadataSection = RelMetadataSectionNodeType.idMetadataSection 
              JOIN MetadataGroup ON MetadataSection.idMetadataSection = MetadataGroup.idMetadataSection
              WHERE idNodeType = %s", $idNodeType));
        $returnArray = array();

        while (!$dbObj->EOF) {
            $idSection = $dbObj->GetValue('idMetadataSection');
            $group = [
                'id' => $dbObj->GetValue('idMetadataGroup'),
                'name' => $dbObj->GetValue('groupName')

            ];
            if (!isset($returnArray[$idSection])) {
                $returnArray[$idSection] = [
                    'groups' => [],
                    'name' => $dbObj->GetValue('sectionName')
                ];
            }
            if (!is_null($nodeId)) {
                $metadata = $this->getMetadataByMetagroupAndNodeId($group['id'], $nodeId);
                $group['metadata'] = $metadata;
            }

            array_push($returnArray[$idSection]['groups'], $group);

            $dbObj->Next();
        }
        unset($dbObj);

        return $returnArray;
    }


    function getMetadataByMetagroupAndNodeId(int $idGroup, int $nodeId): array
    {


        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query(sprintf("SELECT Metadata.name, Metadata.type,
                        RelMetadataGroupMetadata.idRelMetadataGroupMetadata, value
                        FROM RelMetadataGroupMetadata JOIN 
                        Metadata ON RelMetadataGroupMetadata.idMetadata =
                        Metadata.idMetadata 
                        LEFT JOIN MetadataValue ON MetadataValue.idRelMetadataGroupMetadata = RelMetadataGroupMetadata                            .idRelMetadataGroupMetadata and MetadataValue.idNode = %s
                        WHERE idMetadataGroup = %s", $nodeId, $idGroup));

        $returnArray = array();

        while (!$dbObj->EOF) {
            $returnArray[] = [
                'name' => $dbObj->GetValue('name'),
                'id' => $dbObj->GetValue('idRelMetadataGroupMetadata'),
                'value' => $dbObj->GetValue('value'),
                'type' => $dbObj->GetValue('type'),
            ];

            $dbObj->Next();
        }
        unset($dbObj);

        return $returnArray;
    }

    function insertMetadata(int $idGroup, array $metadata): array
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
        unset($dbObj);

        return $returnArray;
    }


    function deleteMetadataValuesByNodeIdAndGroupId(int $idNode, int $idGroup)
    {
        $dbObj = new \Ximdex\Runtime\Db();

        $query = sprintf("DELETE mv FROM MetadataValue mv JOIN RelMetadataGroupMetadata ON 
            mv.idRelMetadataGroupMetadata = RelMetadataGroupMetadata.idRelMetadataGroupMetadata 
            WHERE idNode=%s and idMetadataGroup=%s;", $idNode, $idGroup);

        $dbObj->Execute($query);
        $valid = $dbObj->EOF;

        unset($dbObj);

        return $valid;
    }

    function addMetadataValuesByNodeId(array $metadataArray, int $idNode)
    {
        $dbObj = new \Ximdex\Runtime\Db();


        foreach ($metadataArray as $key => $value) {

            if (!empty($value)) {

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
        unset($dbObj);
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
        $query = sprintf("SELECT Metadata.name as name, MetadataValue.value as value, Metadata.defaultValue
                        as defaultValue, Metadata.type as type  FROM RelMetadataGroupMetadata JOIN  Metadata ON 
                        RelMetadataGroupMetadata.idMetadata = Metadata.idMetadata  LEFT JOIN MetadataValue ON 
                        MetadataValue.idRelMetadataGroupMetadata = RelMetadataGroupMetadata.idRelMetadataGroupMetadata 
                        WHERE (RelMetadataGroupMetadata.required = TRUE OR (MetadataValue.value <> '' and idNode = %s))",
            $idNode);

        if (!is_null($idGroup)) {
            $query .= sprintf(' and MetadataGroup.idMetadataGroup = %s ', $idGroup);
        }

        $dbObj->Query($query);
        while (!$dbObj->EOF) {
            $val = $dbObj->GetValue('value');
            $val = !empty($val) ? $val : $dbObj->GetValue('defaultValue');
            $metadata[$dbObj->GetValue('name')] = static::getMetadataValue($val, $dbObj->GetValue('type'));

            $dbObj->Next();
        }
        unset($dbObj);

        return $metadata;
    }

    private static function getMetadataValue(string $val, string $type)
    {
        $result = '';
        if (strcmp('date', $type) == 0) {
            $result = DateTime::createFromFormat('Y-m-d', $val);
            if ($result == false) {
                $result = date('Y-m-d');
            } else {
                $result = $result->format('Y-m-d');
            }
        } else {
            $result = $val;
        }
        return $result;
    }


}