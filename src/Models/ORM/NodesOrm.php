<?php

namespace Ximdex\Models\ORM;

use Ximdex\Data\GenericData;

/**
 * Class NodesOrm
 * 
 * @package Ximdex\Models
 */
class NodesOrm extends GenericData
{
    var $_idField = 'IdNode';
    var $_table = 'Nodes';
    var $_metaData = array(
        'IdNode' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'IdParent' => array('type' => "int(12)", 'not_null' => 'false'),
        'IdNodeType' => array('type' => "int(12)", 'not_null' => 'true'),
        'Name' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'IdState' => array('type' => "int(12)", 'not_null' => 'false'),
        'BlockTime' => array('type' => "int(12)", 'not_null' => 'false'),
        'BlockUser' => array('type' => "int(12)", 'not_null' => 'false'),
        'CreationDate' => array('type' => "int(12)", 'not_null' => 'false'),
        'ModificationDate' => array('type' => "int(12)", 'not_null' => 'false'),
        'Description' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'SharedWorkflow' => array('type' => "int(12)", 'not_null' => 'false')
    );
    var $_uniqueConstraints = array(
        'IdNode' => array('IdNode', 'IdParent')
    );
    var $_indexes = array('IdNode');
    var $IdNode;
    var $IdParent = null;
    var $IdNodeType;
    var $Name;
    var $IdState = null;
    var $BlockTime = null;
    var $BlockUser = null;
    var $CreationDate = null;
    var $ModificationDate = null;
    var $Description = null;
    var $SharedWorkflow = null;
}
