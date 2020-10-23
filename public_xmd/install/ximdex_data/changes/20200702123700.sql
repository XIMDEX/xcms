SET FOREIGN_KEY_CHECKS=0;

INSERT INTO NodeTypes (IdNodeType , Name,Class,Icon,Description,IsRenderizable,HasFSEntity,CanAttachGroups,IsSection,IsFolder,IsVirtualFolder,IsPlainFile,IsStructuredDocument,IsPublishable,IsHidden,CanDenyDeletion,isGenerator,IsEnriching,`System`,Module,workflowId,HasMetadata) VALUES
(5120,'JSONSchemaFile','JSONSchemaFile','doc','Json Schema File',0,1,0,0,0,0,1,0,0,0,0,0,0,0,NULL,NULL,0)
,(5119,'JSONSchemaFolder','JSONSchemaFolder','doc','Json Schema Folder',0,1,0,0,1,1,0,0,0,0,0,0,0,0,NULL,NULL,0)
,(5118,'JSONContainer','XmlContainerNode','contenedordoc','Json Container',1,0,1,0,1,1,0,0,0,0,0,0,0,0,NULL,NULL,0)
,(5117,'JSONDocument','JSONDocument','doc','Json Document',1,1,0,0,0,0,0,1,1,0,0,0,0,0,NULL,NULL,0)
,(5116, 'XLMSRootFolderTest','XLMSRootFolderTest','folder_xml','XLMS Folder Test',1,1,0,0,1,1,0,0,0,0,1,0,0,1,NULL,NULL,0)
,(5115, 'XLMSRootFolderMultimedia','XLMSRootFolderMultimedia','folder_xml','XLMS Folder Multimedia',0,1,0,0,1,1,0,0,0,0,0,0,0,0,NULL,NULL,0)
,(5114, 'XLMSRootFolderCourse','XLMSRootFolderCourse','folder_xml','XLMS Folder Course',1,1,0,0,1,1,0,0,0,0,1,0,0,1,NULL,NULL,0)
,(5113, 'XLMSRootFolderUnit','XLMSRootFolderUnit','folder_xml','XLMS Root Folder Unit',1,1,0,0,1,1,0,0,0,0,1,0,0,1,NULL,NULL,0)
,(5112, 'XLMSProject','XLMSProject','project','XLMS Project',1,1,0,1,1,1,0,0,1,0,0,0,0,0,NULL,NULL,0)
;

INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(117, 5012, 5112, 0);

INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(118, 5112, 5113, 0);

INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(119, 5112, 5114, 0);

INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(120, 5112, 5115, 0);

INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(121, 5112, 5116, 0);

INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(122, 5112, 5119, 0);


/* Root Folder Unit */
INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(123, 5113, 5022, 0);
INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(124, 5113, 5118, 0);



/* Root Folder Course */
INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(125, 5114, 5022, 0);
INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(126, 5114, 5118, 0);


/* Root Folder Multimedia */
INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(127, 5115, 5022, 0);

INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(128, 5115, 5118, 0);

INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(129, 5115, 5016, 0);

/* Root Folder Test */
INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(130, 5116, 5022, 0);
INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(131, 5116, 5118, 0);

/* JSon Container */
INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(132, 5118, 5117, 0);

/* Schema Folder */
INSERT INTO NodeAllowedContents
(IdNodeAllowedContent, IdNodeType, NodeType, Amount)
VALUES(133, 5119, 5120, 0);


/* XLMSProject */
INSERT
	INTO
	Actions (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, IsBulk )
VALUES (8138, 5012, 'Add new XLMS project', 'addfoldernode', 'create_proyect.png', 'Create a new node with XLMS project type', 11, NULL, 0, 'nodetypeid=5112', 0);

INSERT
	INTO
	Actions (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, IsBulk)
VALUES (8139, 5112, 'Delete XLMS project', 'deletenode', 'delete_proyect.png', 'Delete a XLMS project', 75, NULL, 1, '', 0);

INSERT
	INTO
	Actions (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, IsBulk)
VALUES (8140, 5112, 'Associated groups', 'modifygroupsnode', 'groups_server.png', 'Manage associations of groups with this node', 60, NULL, 0, '', 0);

/* XLMS Root Folder Unit */
INSERT
	INTO
	Actions (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, IsBulk)
VALUES (8141, 5113, 'Add new Unit', 'createxmlcontainer', 'create_proyect.png', 'Create a new Unit', 11, NULL, 0, 'type=JSON', 0);

INSERT
	INTO
	Actions (IdAction, IdNodeType, Name, Command, Icon, Description, Sort, Module, Multiple, Params, IsBulk)
VALUES (8142, 5113, 'Add common folder', 'addfoldernode', 'add_folder_common.png', 'Create a new common folder', 11, NULL, 0, 'nodetypeid=5022', 0);

INSERT
	INTO
	Actions (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, IsBulk)
VALUES (8143, 5113, 'Associated groups', 'modifygroupsnode', 'groups_server.png', 'Manage associations of groups with this node', 60, NULL, 0, '', 0);

/* XLMS Root Folder Course */
INSERT
	INTO
	Actions (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, IsBulk)
VALUES (8144, 5114, 'Add new Course', 'createxmlcontainer', 'create_proyect.png', 'Create a new Course', 11, NULL, 0, 'type=JSON', 0);

INSERT
	INTO
	Actions (IdAction, IdNodeType, Name, Command, Icon, Description, Sort, Module, Multiple, Params, IsBulk)
VALUES (8145, 5114, 'Add common folder', 'addfoldernode', 'add_folder_common.png', 'Create a new common folder', 11, NULL, 0, 'nodetypeid=5022', 0);

INSERT
	INTO
	Actions (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, IsBulk)
VALUES (8146, 5114, 'Associated groups', 'modifygroupsnode', 'groups_server.png', 'Manage associations of groups with this node', 60, NULL, 0, '', 0);

/* XLMS Root Folder Multimedia */
INSERT
	INTO
	Actions (IdAction, IdNodeType, Name, Command, Icon, Description, Sort, Module, Multiple, Params, IsBulk)
VALUES (8147, 5115, 'Add common folder', 'addfoldernode', 'add_folder_common.png', 'Create a new common folder', 11, NULL, 0, 'nodetypeid=5022', 0);

INSERT
	INTO
	Actions (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, IsBulk)
VALUES (8148, 5115, 'Associated groups', 'modifygroupsnode', 'groups_server.png', 'Manage associations of groups with this node', 60, NULL, 0, '', 0);

/* XLMS Root Folder Test */
INSERT
	INTO
	Actions (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, IsBulk)
VALUES (8149, 5116, 'Add new Test', 'createxmlcontainer', 'create_proyect.png', 'Create a new Test', 11, NULL, 0, 'type=JSON', 0);

INSERT
	INTO
	Actions (IdAction, IdNodeType, Name, Command, Icon, Description, Sort, Module, Multiple, Params, IsBulk)
VALUES (8150, 5116, 'Add common folder', 'addfoldernode', 'add_folder_common.png', 'Create a new common folder', 11, NULL, 0, 'nodetypeid=5022', 0);

INSERT
	INTO
	Actions (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, IsBulk)
VALUES (8151, 5116, 'Associated groups', 'modifygroupsnode', 'groups_server.png', 'Manage associations of groups with this node', 60, NULL, 0, '', 0);


/* Json container */

INSERT INTO Actions (IdAction, IdNodeType,Name,Command,Icon,Description,Sort,Module,Multiple,Params,IsBulk) VALUES
(8152,5118,'Publish document','publicatesection','publicate_section.png','Publish the XML or HTML documents container',70,NULL,0,NULL,0)
,(8153,5118,'Expire document','expiresection','expire_section.png','Expire the HTML documents container',71,NULL,0,NULL,0)
,(8154,5118,'Add new language','addlangxmlcontainer','add_language_xml.png','Add a document with a different language',10,NULL,0,'',0)
,(8155,5118,'Copy','copy','copiar_carpeta_ximdoc.png','Copy a document to another destination',30,NULL,0,'',0)
,(8156,5118,'Move node','movenode','move_node.png','Move a node',35,NULL,0,'',0)
,(8157,5118,'Change name of JSON document','renamenode','change_name_xml.png','Change the document name and all its language versions',25,NULL,0,'',0)
,(8158,5118,'Delete document','deletenode','delete_xml.png','Delete HTML document in all its languages',90,NULL,1,'',0)
,(8159,5118,'Modify properties','manageproperties','xix.png','Modify properties',27,NULL,0,NULL,0)
,(8160,5118,'Semantic Tags','setmetadata','change_next_state.png','Managing semantic tags related to the current node.',81,NULL,0,NULL,0)
,(8161,5118,'Associated groups','modifygroupsnode','groups_server.png','Manage associations of groups with this node',60,NULL,0,'',0)
;

/* JSON Document */
INSERT INTO Actions (IdAction, IdNodeType,Name,Command,Icon,Description,Sort,Module,Multiple,Params,IsBulk) VALUES
(8162,5117,'Edit metadata','metadata','add_xml.png','Manage metadata for HTML document',82,NULL,0,NULL,0)
,(8163,5117,'Edit JSON File','edittext','edit_file_xml_txt.png','Edit content of HTML in plain text',21,NULL,0,'type=json',0)
,(8164,5117,'Move to next state','workflow_forward','change_next_state.png','Move to the next state',72,NULL,0,NULL,0)
,(8165,5117,'Move to previous state','workflow_backward','change_last_state.png','Move to the previous state',73,NULL,0,NULL,0)
,(8166,5117,'Expire','expiredoc','expire_section.png','Expire a document',76,NULL,0,'',0)
,(8167,5117,'Version manager','manageversions','manage_versions.png','Manage repository of versions',80,NULL,0,'',0)
,(8168,5117,'Delete document','deletenode','delete_file_xml.png','Delete selected HTML document',90,NULL,1,'',0)
,(8169,5117,'Semantic Tags','setmetadata','change_next_state.png','Managing semantic tags related to the current node.',81,NULL,0,NULL,0)
;

/* Folder Schemas */
INSERT INTO Actions (IdAction, IdNodeType,Name,Command,Icon,Description,Sort,Module,Multiple,Params,IsBulk) VALUES
(8170, 5119,'Semantic Tags','setmetadata','change_next_state.png','Managing semantic tags related to the current node.',999,NULL,0,NULL,0)
,(8171, 5119,'Copy','copy','copiar_carpeta_ximdoc.png','Copy a Schemas folder to another destination',31,NULL,0,'',0)
,(8172, 5119,'Download all Schemas','filedownload_multiple','download_template_view.png','Download all Schema files',80,NULL,0,'',1)
,(8173, 5119,'Upload schema files','fileupload_common_multiple','add_template_pvd.png','Add a set of Schema files to the server',9,NULL,0,'type=json',0)
,(8174, 5119,'Add a new empty schema file','newemptynode','add_file_common.png','Create a new empty Schema file',9,NULL,0,'nodetypeid=5120',0)
;

/* File Schema */

INSERT INTO Actions (IdAction, IdNodeType,Name,Command,Icon,Description,Sort,Module,Multiple,Params,IsBulk) VALUES
(8175, 5120,'Semantic Tags','setmetadata','change_next_state.png','Managing semantic tags related to the current node.',999,NULL,0,NULL,0)
,(8176, 5120,'Delete Schema File','deletenode','delete_template_view.png','Delete a Schema File',75,NULL,0,NULL,0)
,(8177, 5120,'Edit Schema File','edittext','edit_template_view.png','Edit a Schema File',20,NULL,0,NULL,0)
,(8178, 5120,'Modify properties','renamenode','modiy_templateview','Modify properties of a Schema File',60,NULL,0,NULL,0)
;


INSERT  INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8138,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8138,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8138,NULL);


INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8139,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8139,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8139,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8140,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8140,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8140,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8141,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8141,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8141,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8142,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8142,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8142,NULL);


INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8143,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8143,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8143,NULL);


INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8144,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8144,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8144,NULL);


INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8145,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8145,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8145,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8146,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8146,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8146,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8147,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8147,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8147,NULL);


INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8148,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8148,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8148,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8149,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8149,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8149,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8150,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8150,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8150,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8151,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8151,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8151,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8152,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8152,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8152,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8153,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8153,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8153,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8154,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8154,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8154,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8155,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8155,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8155,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8156,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8156,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8156,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8157,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8157,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8157,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8158,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8158,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8158,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8159,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8159,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8159,NULL);


INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8160,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8160,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8160,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8161,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8161,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8161,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8162,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8162,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8162,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8163, NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8163,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8163,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8164,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8164,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8164,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8165,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8165,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8165,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8166,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8166,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8166,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8167,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8167,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8167,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8168,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8168,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8168,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8169,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8169,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8169,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8170,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8170,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8170,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8171,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8171,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8171,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8172,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8172,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8172,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8173,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8173,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8173,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8174,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8174,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8174,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8175,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8175,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8175,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8176,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8176,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8176,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8177,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8177,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8177,NULL);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,201,8178,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,202,8178,NULL);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL,203,8178,NULL);


INSERT INTO RelNodeTypeMimeType (idNodeType,extension,`filter`) VALUES
(5117,';json;','json')
,(5120,';json;','json')
;

INSERT INTO NodeDefaultContents (IdNodeType,NodeType,Name,State,Params) VALUES
(5112,5113,'Units',NULL,NULL)
,(5112,5114,'Courses',NULL,NULL)
,(5112,5115,'Multimedia',NULL,NULL)
,(5112,5116,'Test',NULL,NULL)
,(5112,5119,'Schemas',NULL,NULL)
,(5114,5022,'Common',NULL,NULL)
,(5115,5016,'Images',NULL,NULL)
,(5115,5022,'Video',NULL,NULL)
,(5115,5022,'Others',NULL,NULL)
,(5119,5120,'validate.json',NULL,NULL)
;


SET FOREIGN_KEY_CHECKS=1;




