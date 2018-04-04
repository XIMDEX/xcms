<?php

namespace Ximdex\Models\ORM;

use Ximdex\Data\GenericData;

/**
 * Class RelRolesPermissionsOrm
 * 
 * @package Ximdex\Models
 */
 class RelRolesPermissionsOrm extends GenericData
{
    var $_idField = 'IdRel';
    var $_table = 'RelRolesPermissions';
    var $_metaData = array(
        'IdRel' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'IdRole' => array('type' => "int(12)", 'not_null' => 'true'),
        'IdPermission' => array('type' => "int(12)", 'not_null' => 'true')
    );
    var $_uniqueConstraints = array();
    var $_indexes = array('IdRel');
    var $IdRel;
    var $IdRole = 0;
    var $IdPermission = 0;
}