<?php
namespace Ximdex\Models;


use Ximdex\Data\GenericData;

/**
 * Class NodesOrm
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
    var $IdParent = 0;
    var $IdNodeType = 0;
    var $Name = 0;
    var $IdState = 0;
    var $BlockTime = 0;
    var $BlockUser;
    var $CreationDate = 0;
    var $ModificationDate = 0;
    var $Description;
    var $SharedWorkflow;

}