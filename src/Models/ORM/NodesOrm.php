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
    public $_idField = 'IdNode';
    public $_table = 'Nodes';
    public $_metaData = array(
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
        'SharedWorkflow' => array('type' => "int(12)", 'not_null' => 'false'),
        'ActiveNF' => array('type' => "int(12)", 'not_null' => 'false')
    );
    public $_uniqueConstraints = array(
        'IdNode' => array('IdNode', 'IdParent')
    );
    public $_indexes = array('IdNode');
    public $IdNode;
    public $IdParent = null;
    public $IdNodeType;
    public $Name;
    public $IdState = null;
    public $BlockTime = null;
    public $BlockUser = null;
    public $CreationDate = null;
    public $ModificationDate = null;
    public $Description = null;
    public $SharedWorkflow = null;
    public $ActiveNF = null;
}
