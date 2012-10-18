<?php

if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../../../');
}

require_once(XIMDEX_ROOT_PATH . '/script/diffChecker/lmd.class.php');

$lmd = new lmd();


#add table locale
$lmd->query("CREATE TABLE `Locales` ( `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,`Code` varchar(5) NOT NULL COMMENT 'Locale in ISO 639 ',`Name` varchar(20) NOT NULL COMMENT 'Lang name',`Enabled` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Enabled(1)|Not Enabled(0)',PRIMARY KEY (`ID`)) TYPE=MyISAM  COMMENT='Ximdex Locales'");

#add locale list
$lmd->query("INSERT INTO `Locales` (`ID`, `Code`, `Name`, `Enabled`) VALUES(1, 'es_ES', 'Spanish', 1)");
$lmd->query("INSERT INTO `Locales` (`ID`, `Code`, `Name`, `Enabled`) VALUES(2, 'en_US', 'English', 1)");

#add user locale column in users table
$lmd->query("ALTER TABLE `Users` ADD `Locale` VARCHAR( 5 ) NULL DEFAULT NULL COMMENT 'User Locale'");

?>