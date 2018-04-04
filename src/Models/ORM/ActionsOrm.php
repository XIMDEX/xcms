<?php

namespace Ximdex\Models\ORM;

use Ximdex\Data\GenericData;

class ActionsOrm extends GenericData
{
    var $_idField = 'IdAction';
    var $_table = 'Actions';
    var $_metaData = array(
        'IdAction' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'IdNodeType' => array('type' => "int(12)", 'not_null' => 'true'),
        'Name' => array('type' => "varchar(100)", 'not_null' => 'true'),
        'Command' => array('type' => "varchar(100)", 'not_null' => 'true'),
        'Icon' => array('type' => "varchar(100)", 'not_null' => 'true'),
        'Description' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'Sort' => array('type' => "int(12)", 'not_null' => 'false'),
        'Module' => array('type' => "varchar(250)", 'not_null' => 'false'),
        'Multiple' => array('type' => "tinyint(1)", 'not_null' => 'true'),
        'Params' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'IsBulk' => array('type' => "tinyint(1)", 'not_null' => 'true')
    );
    var $_uniqueConstraints = array(
        'IdAction' => array('IdAction')
    );
    var $_indexes = array('IdAction');
    var $IdAction;
    var $IdNodeType = 0;
    var $Name;
    var $Command;
    var $Icon;
    var $Description;
    var $Sort;
    var $Module;
    var $Multiple = 0;
    var $Params;
    var $IsBulk = 0;
}