<?php

namespace Ximdex\Models\ORM;


use Ximdex\Data\GenericData;

/**
 * Class ActionStatsOrm
 * @package Ximdex\Models
 */
class ActionsStatsOrm extends GenericData
{
    var $_idField = 'IdStat';
    var $_table = 'ActionsStats';
    var $_metaData = array(
        'IdStat' => array('type' => "int(11)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'IdAction' => array('type' => "int(11)", 'not_null' => 'false'),
        'IdNode' => array('type' => "int(11)", 'not_null' => 'false'),
        'IdUser' => array('type' => "int(11)", 'not_null' => 'false'),
        'Method' => array('type' => "varchar(255)", 'not_null' => 'false'),
        'TimeStamp' => array('type' => "int(11)", 'not_null' => 'true'),
        'Duration' => array('type' => "float(11, 6)", 'not_null' => 'true')
    );
    var $_uniqueConstraints = array();
    var $_indexes = array('IdStat');
    var $IdStat;
    var $IdAction;
    var $IdNode;
    var $IdUser;
    var $Method;
    var $TimeStamp;
    var $Duration;
}