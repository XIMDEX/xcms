INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`)
VALUES (5501, 'AnnotatedNode', 'filenode', 'xml_document.png', 'Archivo xml de Anotación', 1, 1, 0, 0, 0, 0, 1, 0, 1, 1, 0, NULL);

INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`)
VALUES (5502, 'MetaDataSection', 'foldernode', 'folder.png', 'Contenedor de anotaciones xml', 1, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, NULL);

INSERT INTO `NodeAllowedContents` VALUES (NULL,5502, 5501,0);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5022, 5502,0);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5023, 5502,0);

INSERT INTO `RelNodeTypeMimeType` VALUES (Null, 5501, 'text/xml', 'xml', '');

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`)
VALUES (8501, 5501,'Pasar estado siguiente','workflow_forward','change_next_state.png','Pasa al estado siguiente',72,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`)
VALUES (8502, 5501,'Pasar estado anterior','workflow_backward','change_last_state.png','Pasa al estado anterior',70,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`)
VALUES (8503, 5501,'Editar XML','xmleditor','edit_file_xml.png','Edita el contenido del documento XML',1,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`)
VALUES (8504, 5501,'Editar Archivo','edittext','edit_file_txt.png','Edita el contenido del documento de texto',1,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`)
VALUES (8505, 5502,'Publicar Sección MetaData','publicatesection','publicate_section.png','Publica toda la sección de MetaDatas',1,NULL,0);

INSERT INTO `NodeDefaultContents` VALUES (NULL, 5022, 5502,'metadata',NULL,NULL);

--
-- Se asocian las acciones al rol de Administrador
--

INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES ('',201,8501,0);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES ('',201,8502,0);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES ('',201,8503,0);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES ('',201,8504,0);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES ('',201,8505,0);


CREATE TABLE `RelNodeMetaData` (
  `IdNode` int(12) unsigned NOT NULL default '0',
  `IdMetaData` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdNode`)
) ENGINE=MyISAM COMMENT='Tabla de relación entre metadatas y nodos de Ximdex';
