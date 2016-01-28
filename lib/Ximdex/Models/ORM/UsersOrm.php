<?php

namespace Ximdex\Models\ORM;
use Ximdex\Data\GenericData ;

class UsersORM extends GenericData   {
    var $_idField = 'IdUser';
    var $_table = 'Users';
    var $_metaData = array(
        'IdUser' => array('type' => "int(12)", 'not_null' => 'true', 'primary_key' => true),
        'Login' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'Pass' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'Name' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'Email' => array('type' => "varchar(255)", 'not_null' => 'true'),
        'Locale' => array('type' => "varchar(5)", 'not_null' => 'false'),
        'LastLogin' => array('type' => "int(14)", 'not_null' => 'false'),
        'NumAccess' => array('type' => "int(12)", 'not_null' => 'false')
    );
    var $_uniqueConstraints = array(
        'login' => array('Login')
    );
    var $_indexes = array('IdUser');
    var $IdUser;
    var $Login = 0;
    var $Pass = 0;
    var $Name = 0;
    var $Email;
    var $Locale;
    var $LastLogin;
    var $NumAccess;
}