<?php

namespace Ximdex\Models;

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


    function getMetadataGroupByNodeType(int $idNodeType): array
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query(sprintf("SELECT MetadataGroup.idMetadataGroup, name FROM RelMetadataGroupNodeType 
            JOIN MetadataGroup ON
            RelMetadataGroupNodeType.idMetadataGroup = MetadataGroup.idMetadataGroup 
            WHERE idNodeType = %s", $idNodeType));
        $returnArray = array();

        while (!$dbObj->EOF) {
            $returnArray[] = [
                'id' => $dbObj->GetValue('idMetadataGroup'),
                'name' => $dbObj->GetValue('name')
            ];

            $dbObj->Next();
        }
        unset($dbObj);

        return $returnArray;
    }


    function getMetadataByMetagroup(int $idGroup): array
    {


        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query(sprintf("SELECT Metadata.name, 
                        RelMetadataGroupMetadata.idRelMetadataGroupMetadata, value
                        FROM RelMetadataGroupMetadata JOIN 
                        Metadata ON RelMetadataGroupMetadata.idMetadata =
                        Metadata.idMetadata 
                        LEFT JOIN MetadataValue ON MetadataValue.idRelMetadataGroupMetadata = RelMetadataGroupMetadata                            .idRelMetadataGroupMetadata
                        WHERE idMetadataGroup = %s ", $idGroup));
        $returnArray = array();

        $returnArray = array();

        while (!$dbObj->EOF) {
            $returnArray[] = [
                'name' => $dbObj->GetValue('name'),
                'id' => $dbObj->GetValue('idRelMetadataGroupMetadata'),
                'value' => $dbObj->GetValue('value'),
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
        $valid  = $dbObj->EOF;

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


}