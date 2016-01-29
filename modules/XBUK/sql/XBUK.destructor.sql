/* XBUK nodetypes */
DELETE FROM `NodeTypes` WHERE `IdNodeType` = 9001;
DELETE FROM `NodeTypes` WHERE `IdNodeType` = 9002;
DELETE FROM `NodeTypes` WHERE `IdNodeType` = 9003;

/* XBUK Actions */
DELETE FROM `Actions` WHERE `IdAction` = 9004;
DELETE FROM `Actions` WHERE `IdAction` = 9005;
DELETE FROM `Actions` WHERE `IdAction` = 9006;

DELETE FROM `Actions` WHERE `IdAction` = 9007;
DELETE FROM `Actions` WHERE `IdAction` = 9008;
DELETE FROM `Actions` WHERE `IdAction` = 9009;

/* XBUK NodeAllowedContents */
DELETE FROM `NodeAllowedContents` WHERE `IdNodeAllowedContent` = 150;
DELETE FROM `NodeAllowedContents` WHERE `IdNodeAllowedContent` = 151;
DELETE FROM `NodeAllowedContents` WHERE `IdNodeAllowedContent` = 152;

/* XBUK RelRolesActions */

DELETE FROM `RelRolesActions` WHERE `IdRol` = 201 AND `IdAction` =  9004 AND `IdState` = 0 AND `IdContext` = 1 AND `IdPipeline` = 3;
DELETE FROM `RelRolesActions` WHERE `IdRol` = 204 AND `IdAction` =  9004 AND `IdState` = 0 AND `IdContext` = 1 AND `IdPipeline` = 3;
DELETE FROM `RelRolesActions` WHERE `IdRol` = 201 AND `IdAction` =  9005 AND `IdState` = 0 AND `IdContext` = 1 AND `IdPipeline` = 3;
DELETE FROM `RelRolesActions` WHERE `IdRol` = 204 AND `IdAction` =  9005 AND `IdState` = 0 AND `IdContext` = 1 AND `IdPipeline` = 3;
DELETE FROM `RelRolesActions` WHERE `IdRol` = 201 AND `IdAction` =  9006 AND `IdState` = 0 AND `IdContext` = 1 AND `IdPipeline` = 3;
DELETE FROM `RelRolesActions` WHERE `IdRol` = 204 AND `IdAction` =  9006 AND `IdState` = 0 AND `IdContext` = 1 AND `IdPipeline` = 3;

DELETE FROM `RelRolesActions` WHERE `IdRol` = 201 AND `IdAction` =  9007 AND `IdState` = 0 AND `IdContext` = 1 AND `IdPipeline` = 3;
DELETE FROM `RelRolesActions` WHERE `IdRol` = 204 AND `IdAction` =  9007 AND `IdState` = 0 AND `IdContext` = 1 AND `IdPipeline` = 3;
DELETE FROM `RelRolesActions` WHERE `IdRol` = 201 AND `IdAction` =  9008 AND `IdState` = 0 AND `IdContext` = 1 AND `IdPipeline` = 3;
DELETE FROM `RelRolesActions` WHERE `IdRol` = 204 AND `IdAction` =  9008 AND `IdState` = 0 AND `IdContext` = 1 AND `IdPipeline` = 3;
DELETE FROM `RelRolesActions` WHERE `IdRol` = 201 AND `IdAction` =  9009 AND `IdState` = 0 AND `IdContext` = 1 AND `IdPipeline` = 3;
DELETE FROM `RelRolesActions` WHERE `IdRol` = 204 AND `IdAction` =  9009 AND `IdState` = 0 AND `IdContext` = 1 AND `IdPipeline` = 3;
