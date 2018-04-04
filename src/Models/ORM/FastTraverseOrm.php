<?php

namespace Ximdex\Models\ORM;

use Ximdex\Data\GenericData;

class FastTraverseOrm extends GenericData
{
    var $_idField = array('IdNode', 'idChild');
    var $_table = 'FastTraverse';
    var $_metaData = array(
        'IdNode' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'false', 'primary_key' => true),
        'IdChild' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'false', 'primary_key' => true),
        'Depth' => array('type' => "int(12)", 'not_null' => 'true')
    );
    var $_uniqueConstraints = array();
    var $_indexes = array('idNode', 'idChild');
    var $IdNode;
    var $IdChild;
    var $Depth = 0;
}