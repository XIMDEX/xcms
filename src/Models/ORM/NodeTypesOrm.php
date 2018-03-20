<?php

namespace Ximdex\Models\ORM;

use Ximdex\Data\GenericData;

/**
 * Class NodeTypesOrm
 * @package Ximdex\Models
 */
class NodeTypesOrm extends GenericData
{
    var $_idField = 'IdNodeType';
    var $_table = 'NodeTypes';
    var $_metaData = array(
        'IdNodeType' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'Name' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'Class' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'Icon' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'Description' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'IsRenderizable' => array('type' => "int(1)", 'not_null' => 'false'),
        'HasFSEntity' => array('type' => "int(1)", 'not_null' => 'false'),
        'CanAttachGroups' => array('type' => "int(1)", 'not_null' => 'false'),
        'IsSection' => array('type' => "int(1)", 'not_null' => 'false'),
        'IsFolder' => array('type' => "int(1)", 'not_null' => 'false'),
        'IsVirtualFolder' => array('type' => "int(1)", 'not_null' => 'false'),
        'IsPlainFile' => array('type' => "int(1)", 'not_null' => 'false'),
        'IsStructuredDocument' => array('type' => "int(1)", 'not_null' => 'false'),
        'IsPublishable' => array('type' => "int(1)", 'not_null' => 'false'),
        'IsHidden' => array('type' => "int(1)", 'not_null' => 'false'),
        'CanDenyDeletion' => array('type' => "int(1)", 'not_null' => 'false'),
        'System' => array('type' => "int(1)", 'not_null' => 'false'),
        'Module' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'isGenerator' => array('type' => "tinyint(1)", 'not_null' => 'false'),
        'IsEnriching' => array('type' => "tinyint(1)", 'not_null' => 'false')
    );
    var $_uniqueConstraints = array(
        'IdType' => array('Name')
    );
    var $_indexes = array('IdNodeType');
    var $IdNodeType;
    var $Name = 0;
    var $Class;
    var $Icon;
    var $Description;
    var $IsRenderizable;
    var $HasFSEntity;
    var $CanAttachGroups;
    var $IsSection;
    var $IsFolder;
    var $IsVirtualFolder;
    var $IsPlainFile;
    var $IsStructuredDocument;
    var $IsPublishable;
    var $IsHidden;
    var $CanDenyDeletion;
    var $System;
    var $Module;
    var $isGenerator;
    var $IsEnriching;
}