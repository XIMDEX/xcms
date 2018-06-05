<?php

namespace Ximdex\Models\ORM;

use Ximdex\Data\GenericData;

/**
 * Class RolesOrmextends
 * 
 * @package Ximdex\Models
 */
class RolesOrm extends GenericData
{
    var $_idField = 'IdRole';
    var $_table = 'Roles';
    var $_metaData = array(
        'IdRole' => array('type' => "int(12)", 'not_null' => 'true', 'primary_key' => true),
        'Name' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'Icon' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'Description' => array('type' => "varchar(255)", 'not_null' => 'false')
    );
    var $_uniqueConstraints = array(
        'IdRole' => array('Name')
    );
    var $_indexes = array('IdRole');
    var $IdRole;
    var $Name = 0;
    var $Icon;
    var $Description;
}