<?php

if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../../../');
}

require_once(XIMDEX_ROOT_PATH . '/script/diffChecker/lmd.class.php');

$lmd = new lmd();

/* r7626: Deleting ximTAX */
$lmd->query("DELETE FROM Actions WHERE IdAction=6912 AND Command like 'deletedicfolder'");
$lmd->query("DELETE FROM Actions WHERE IdAction=6911 AND Command like 'createdic'");
$lmd->query("DELETE FROM Actions WHERE IdAction=6910 AND Command like 'createdicfolder'");
$lmd->query("DELETE FROM Actions WHERE IdAction=6909 AND Command like 'deassigndictionary'");
$lmd->query("DELETE FROM Actions WHERE IdAction=6908 AND Command like 'assigndictionary'");
$lmd->query("DELETE FROM Actions WHERE IdAction=6907 AND Command like 'deletedic'");
$lmd->query("DELETE FROM Actions WHERE IdAction=6906 AND Command like 'deletedicvalue'");
$lmd->query("DELETE FROM Actions WHERE IdAction=6905 AND Command like 'modifynodeprop'");
$lmd->query("DELETE FROM Actions WHERE IdAction=6904 AND Command like 'createdicvalue'");
$lmd->query("DELETE FROM Actions WHERE IdAction=6903 AND Command like 'createdic'");
$lmd->query("DELETE FROM Actions WHERE IdAction=6902 AND Command like 'deleteprop'");
$lmd->query("DELETE FROM Actions WHERE IdAction=6901 AND Command like 'modifyprop'");
$lmd->query("DELETE FROM Actions WHERE IdAction=6900 AND Command like 'createprop'");
$lmd->query("DELETE FROM Actions WHERE IdAction=6913 AND Command like 'createpropset'");
$lmd->query("DELETE FROM Actions WHERE IdAction=6914 AND Command like 'deletepropset'");
$lmd->query("DELETE FROM Actions WHERE IdAction=6915 AND Command like 'updatepropset'");
$lmd->query("DELETE FROM Actions WHERE IdAction=6916 AND Command like 'modifynodeprop'");
$lmd->query("DELETE FROM NodeTypes WHERE IdNodeType=5062 AND Name like 'XimTaxContainer'");

/* r7627: Deleting ximTAX2 module */
$lmd->query("DELETE FROM Nodes WHERE IdNode=5062 AND IdNodeType=5007");

/* r7631: Deleting ximTAX nodes */
$lmd->query("DELETE FROM Nodes WHERE IdNode=6910 AND IdParent=5063 AND IdNodeType=5008");
$lmd->query("DELETE FROM Nodes WHERE IdNode=6909 AND IdParent=5059 AND IdNodeType=5008");
$lmd->query("DELETE FROM Nodes WHERE IdNode=6908 AND IdParent=5059 AND IdNodeType=5008");
$lmd->query("DELETE FROM Nodes WHERE IdNode=6190 AND IdParent=5041 AND IdNodeType=5008");
$lmd->query("DELETE FROM Nodes WHERE IdNode=6191 AND IdParent=5039 AND IdNodeType=5008");
$lmd->query("DELETE FROM Nodes WHERE IdNode=6907 AND IdParent=5064 AND IdNodeType=5008");
$lmd->query("DELETE FROM Nodes WHERE IdNode=6904 AND IdParent=5065 AND IdNodeType=5008");
$lmd->query("DELETE FROM Nodes WHERE IdNode=6903 AND IdParent=5063 AND IdNodeType=5008");
$lmd->query("DELETE FROM Nodes WHERE IdNode=6902 AND IdParent=5059 AND IdNodeType=5008");
$lmd->query("DELETE FROM Nodes WHERE IdNode=6901 AND IdParent=5059 AND IdNodeType=5008");
$lmd->query("DELETE FROM Nodes WHERE IdNode=6900 AND IdParent=5068 AND IdNodeType=5008");
$lmd->query("DELETE FROM Nodes WHERE IdNode=6905 AND IdParent=5015 AND IdNodeType=5008");
$lmd->query("DELETE FROM Nodes WHERE IdNode=6911 AND IdParent=5066 AND IdNodeType=5008");
$lmd->query("DELETE FROM Nodes WHERE IdNode=6912 AND IdParent=5066 AND IdNodeType=5008");
$lmd->query("DELETE FROM Nodes WHERE IdNode=6913 AND IdParent=5068 AND IdNodeType=5008");
$lmd->query("DELETE FROM Nodes WHERE IdNode=6914 AND IdParent=5068 AND IdNodeType=5008");
$lmd->query("DELETE FROM Nodes WHERE IdNode=6915 AND IdParent=5068 AND IdNodeType=5008");

/* Disabling the publish for PTD  */
$lmd->query("Update NodeTypes set  IsPublicable = 0  where  IdNodeType = '5077'");

/* r7641: Deleting tuples from ximTAX */
$lmd->query("DELETE FROM nodes WHERE IdNode=6906 AND IdParent=5064 AND IdNodeType=5008");

/* r7676: Updating name and descrption of the showassocnodes action */
$lmd->query("UPDATE Actions SET Name='Gestionar asociaciones' AND Description='Gestiona asociaciones de nodos con el ximlet' where IdAction=7236");

/* r7709: Disabling the copy action for pvds and pts containers (ximpvd and ximptd) */
$lmd->query("UPDATE Actions SET Sort='-30' WHERE IdAction=7211"); //ximptd
$lmd->query("UPDATE Actions SET Sort='-30' WHERE IdAction=7226"); //ximpvd

/* r7747: Changing add link folder node action name */
$lmd->query("UPDATE Actions SET Name='Añadir nueva categoría de enlaces' WHERE IdAction=6069");
$lmd->query("UPDATE Actions SET Name='Añadir nueva categoría de enlaces' WHERE IdAction=6070");

/* r7753: Updating Ximdex version */
$lmd->query("UPDATE Config SET ConfigValue='Ximdex 3.1' WHERE ConfigKey like 'VersionName'");

/* r7800 and r7801: Defining default roles and states relations */
$lmd->query("INSERT INTO `RelRolesStates` VALUES (3, 202, 7)"); //Editor for publishing
$lmd->query("INSERT INTO `RelRolesStates` VALUES (4, 203, 7)"); //Publicado for publishing
$lmd->query("INSERT INTO `RelRolesStates` VALUES (5, 203, 8)"); //Publicador for editing
$lmd->query("INSERT INTO `RelRolesStates` VALUES (6, 204, 7)"); //Experto for publishing
$lmd->query("INSERT INTO `RelRolesStates` VALUES (7, 204, 8)"); //Experto for editing
$lmd->query("INSERT INTO `RelRolesActions` (IdRol, IdAction, IdState, IdContext, IdPipeline) VALUES (203, 6098, 7, 1, 3)"); //Allowing Publicador to go forward on workflow to publish
$lmd->query("UPDATE State SET NextState=8 where IdState=7"); //It was defined as 401, next state could not been reached
$lmd->query("UPDATE State SET NextState=7 where IdState=8"); //It was defined as 402, next state could not been reached



?>
