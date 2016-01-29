/* XBUK nodetypes */
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (9001,'XBUKProject','xbukproject','nodetype','XBUK project',1,0,1,0,0,1,0,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (9002,'XBUKSession','xbuksession','nodetype','XBUK session',1,0,1,0,0,1,0,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (9003,'XBUKScreen','xbukscreen','nodetype','XBUK screen',1,0,1,0,0,1,0,0,0,0,0,NULL);

/* XBUK Actions */
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9004,5012,'Add new XBUK project','addfoldernode','create_proyect.png','Create a new node with XBUK project type',11,NULL,0,'nodetypeid=9001');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9005,9001,'Add new XBUK session','addfoldernode','create_proyect.png','Create a new node with XBUK session type',11,NULL,0,'nodetypeid=9002');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9006,9002,'Add new XBUK screen','addfoldernode','create_proyect.png','Create a new node with XBUK screen type',11,NULL,0,'nodetypeid=9003');

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9007,9001,'Delete XBUK project','deletenode','delete_proyect.png','Delete a XBUK project',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9008,9002,'Delete XBUK session','deletenode','delete_proyect.png','Delete a XBUK session',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9009,9003,'Delete XBUK screen','deletenode','delete_proyect.png','Delete a XBUK screen',75,NULL,1,'');

/* XBUK NodeAllowedContents */
INSERT INTO `NodeAllowedContents` VALUES (150,5012,9001,0);
INSERT INTO `NodeAllowedContents` VALUES (151,9001,9002,0);
INSERT INTO `NodeAllowedContents` VALUES (152,9002,9003,0);

/* XBUK RelRolesActions */

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9004,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,9004,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9005,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,9005,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9006,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,9006,0,1,3);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9007,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,9007,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9008,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,9008,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9009,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,9009,0,1,3);
