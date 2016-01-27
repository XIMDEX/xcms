<?php
/**
 * Created by PhpStorm.
 * User: drzippie
 * Date: 27/1/16
 * Time: 15:39
 */

namespace Ximdex\Models;


use Ximdex\Data\GenericData;

/**
 * Class ConfigOrm
 * @package Ximdex\Models
 */
class ConfigOrm extends GenericData
{
    var $_idField = 'IdConfig';
    var $_table = 'Config';
    var $_metaData = array(
        'IdConfig' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'ConfigKey' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'ConfigValue' => array('type' => "blob", 'not_null' => 'false')
    );
    var $_uniqueConstraints = array(
        'IdConfig' => array('IdConfig', 'ConfigKey'), 'ConfigKey' => array('ConfigKey')
    );
    var $_indexes = array('IdConfig');
    var $IdConfig;
    var $ConfigKey = 0;
    var $ConfigValue;
}