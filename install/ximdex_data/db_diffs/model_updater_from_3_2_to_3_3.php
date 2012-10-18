<?php

if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../../../');
}

require_once(XIMDEX_ROOT_PATH . '/script/diffChecker/lmd.class.php');

$lmd = new lmd();

//No actions for nodes
$lmd->query("CREATE TABLE `NoActionsInNode` ( `IdNode` INT NOT NULL , `IdAction` INT NOT NULL COMMENT 'Action dont allowed for IdNode', PRIMARY KEY ( `IdNode` , `IdAction` ) ) ENGINE = MYISAM COMMENT = 'List Actions dont allowed in IdNode';");
//Delete group "General"
$lmd->query("INSERT INTO `NoActionsInNode` ( `IdNode` , `IdAction` ) VALUES ( '101', '6108' );");
// Modify Group "General"
$lmd->query("INSERT INTO `NoActionsInNode` (`IdNode` , `IdAction` ) VALUES ( '101', '6096');");
//Delete user "Ximdex"
$lmd->query("INSERT INTO `NoActionsInNode` (`IdNode` ,`IdAction`)VALUES ('301', '6003');");
// Deleete rol "Administrator"
$lmd->query("INSERT INTO `NoActionsInNode` (`IdNode` ,`IdAction`)VALUES ('201', '6109');");
// Modify Role "Administrator"
$lmd->query("INSERT INTO `NoActionsInNode` (`IdNode` ,`IdAction`) VALUES ( '201', '6005');");

//add "Change name" action para xsl template
$lmd->query("INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8117,5077,'Change name','renamenode','modify_template_ptd.png','Change template name',70,NULL,0,'');");
$lmd->query("INSERT INTO `Nodes` VALUES (8117,5077,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);");
$lmd->query("INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8117,0,1,3);");
$lmd->query("INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8117,0,1,3);");

//add "Download template" para xsl template
$lmd->query("INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8118,5077,'Download template','filedownload','download_template_ptd.png','Download a template',20,NULL,0,'');");
$lmd->query("INSERT INTO `Nodes` VALUES (8118,5044,5008,'Download template',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);");
$lmd->query("INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8118,0,1,3);");
$lmd->query("INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8118,0,1,3);");

//add "Version manager" para xsl template
$lmd->query("INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8119,5077,'Version manager','manageversions','manage_versions.png','Manage repository of versions',77,NULL,0,'');");
$lmd->query("INSERT INTO `Nodes` VALUES (8119,5077,5008,'Version manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);");
$lmd->query(" INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8119,0,1,3);");
$lmd->query(" INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8119,0,1,3);");

//add "move node" para xsl template
$lmd->query("INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8120,5077,'Move node','movenode','move_node.png','Move a node',90,NULL,1,'');");
$lmd->query("INSERT INTO `Nodes` VALUES (8120,5077,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
");
$lmd->query("INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8120,0,1,3);");
$lmd->query("INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8120,0,1,3);");

//add "remove templates" para  TemplatesRootFolder (5026)
$lmd->query("INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8121,5026,'Delete templates','deletetemplates','delete_template_ptd.png','Delete selected templates',90,NULL,0,'');");
$lmd->query("INSERT INTO `Nodes` VALUES (8121,5026,5008,'Delete templates',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);");
$lmd->query("INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8121,0,1,3);");
$lmd->query("INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8121,0,1,3);");
