/* XBUK nodetypes */
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (9001,'XBUKProject','xbukproject','nodetype','XBUK project',0,0,0,0,0,1,0,0,0,0,0,'XBUK');
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (9002,'XBUKSession','xbuksession','nodetype','XBUK session',0,0,0,0,0,1,0,0,0,0,0,'XBUK');
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (9003,'XBUKScreen','xbukscreen','nodetype','XBUK screen',0,0,0,0,0,1,0,0,0,0,0,'XBUK');

/* Repository Nodetypes */
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (9010,'Repository','repository','nodetype','Repository',0,0,0,0,0,1,0,0,0,0,0,'XBUK');
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (9013,'ImageFolder','VirtualImageFolder','nodetype','Image folder',0,0,0,0,0,1,0,0,0,0,0,'XBUK');
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (9014,'VideoFolder','VirtualVideoFolder','nodetype','Video folder',0,0,0,0,0,1,0,0,0,0,0,'XBUK');
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (9015,'WidgetFolder','VirtualWidgetFolder','nodetype','Widget folder',0,0,0,0,0,1,0,0,0,0,0,'XBUK');
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (9016,'OtherFolder','VirtualOtherFolder','nodetype','Other folder',0,0,0,0,0,1,0,0,0,0,0,'XBUK');

/* XBUK Actions */
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9004,5012,'Add new XBUK project','addfoldernode','create_proyect.png','Create a new node with XBUK project type',11,NULL,0,'nodetypeid=9001');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9005,9001,'Add new XBUK session','addfoldernode','create_proyect.png','Create a new node with XBUK session type',11,NULL,0,'nodetypeid=9002');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9006,9002,'Add new XBUK screen','addfoldernode','create_proyect.png','Create a new node with XBUK screen type',11,NULL,0,'nodetypeid=9003');

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9007,9001,'Delete XBUK project','deletenode','delete_proyect.png','Delete a XBUK project',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9008,9002,'Delete XBUK session','deletenode','delete_proyect.png','Delete a XBUK session',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9009,9003,'Delete XBUK screen','deletenode','delete_proyect.png','Delete a XBUK screen',75,NULL,1,'');

/* Repository Actions */
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9011,5012,'Add new repository','addfoldernode','create_proyect.png','Create a new repository',11,NULL,0,'nodetypeid=9010');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9012,9010,'Delete repository','deletenode','delete_proyect.png','Delete a repository',75,NULL,1,'');

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9017,9010,'Add new image folder','addfoldernode','create_proyect.png','Create a new image folder',11,NULL,0,'nodetypeid=9013');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9018,9013,'Delete repository','deletenode','delete_proyect.png','Delete a repository',75,NULL,1,'');

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9019,9010,'Add new video folder','addfoldernode','create_proyect.png','Create a new video folder',11,NULL,0,'nodetypeid=9014');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9020,9014,'Delete video folder','deletenode','delete_proyect.png','Delete a video folder',75,NULL,1,'');

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9021,9010,'Add new widget folder','addfoldernode','create_proyect.png','Create a new widget folder',11,NULL,0,'nodetypeid=9015');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9022,9015,'Delete widget folder','deletenode','delete_proyect.png','Delete a widget folder',75,NULL,1,'');

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9023,9010,'Add new other folder','addfoldernode','create_proyect.png','Create a new other folder',11,NULL,0,'nodetypeid=9016');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (9024,9016,'Delete other folder','deletenode','delete_proyect.png','Delete a other folder',75,NULL,1,'');

/* XBUK NodeAllowedContents */
INSERT INTO `NodeAllowedContents` VALUES (150,5012,9001,0);
INSERT INTO `NodeAllowedContents` VALUES (151,9001,9002,0);
INSERT INTO `NodeAllowedContents` VALUES (152,9002,9003,0);

/* Repository NodeAllowedContents */
INSERT INTO `NodeAllowedContents` VALUES (153,5012,9010,0);
INSERT INTO `NodeAllowedContents` VALUES (154,9010,9013,0);
INSERT INTO `NodeAllowedContents` VALUES (155,9010,9014,0);
INSERT INTO `NodeAllowedContents` VALUES (156,9010,9015,0);
INSERT INTO `NodeAllowedContents` VALUES (157,9010,9016,0);


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

/* Repository RelRolesActions */
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9011,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,9011,0,1,3);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9012,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,9012,0,1,3);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9017,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,9017,0,1,3);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9018,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,9018,0,1,3);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9019,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,9019,0,1,3);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9020,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,9020,0,1,3);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9021,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,9021,0,1,3);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9022,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,9022,0,1,3);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9023,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,9023,0,1,3);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9024,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,9024,0,1,3);