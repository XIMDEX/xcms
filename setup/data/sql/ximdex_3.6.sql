
CREATE TABLE `Actions` (
  `IdAction` int(12) unsigned NOT NULL auto_increment,
  `IdNodeType` int(12) unsigned NOT NULL default '0',
  `Name` varchar(100) NOT NULL default '',
  `Command` varchar(100) NOT NULL default '',
  `Icon` varchar(100) NOT NULL default '',
  `Description` varchar(255) default NULL,
  `Sort` int(12) default NULL,
  `Module` varchar(250) default NULL,
  `Multiple` tinyint(1) unsigned NOT NULL default '0',
  `Params` varchar(255) default NULL,
  `IsBulk` tinyint(1) unsigned NOT NULL default 0,
  PRIMARY KEY  (`IdAction`),
  UNIQUE KEY `IdAction` (`IdAction`),
  KEY `IdAction_2` (`IdAction`,`IdNodeType`)
) ENGINE=MyISAM COMMENT='Commands which can be executed in a node';



INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6043,5018,'Add XML documents','fileupload_common_multiple','add_xml.png','Create new XML documents in several languages',9,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6044,5018,'Add new document','createxmlcontainer','add_xml.png','Create a new document structured in several languages',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6071,5050,'Add new external link','createlink','add_external_link.png','Create a new external link',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6072,5048,'Add new external link','createlink','add_external_link.png','Create a new external link',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6138,5054,'Add new ximlet','createxmlcontainer','add_xml.png','Create a new document structured in several languages',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6140,5055,'Add new ximlet','createxmlcontainer','add_xml.png','Create a new document structured in several languages',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6151,5056,'Add new language','addlangxmlcontainer','add_language_xml.png','Add a new language to ximlet',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6047,5030,'Add new language','createlanguage','add_language.png','Add a new language',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6048,5029,'Add new channel','createchannel','add_channel.png','Add a new channel to Ximdex',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6011,5012,'Add new XCMS project','addfoldernode','create_proyect.png','Create a new node with project type',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6012,5013,'Add new server','addfoldernode','create_server.png','Create a new server',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6013,5014,'Add new section','addsectionnode','add_section.png','Create a new section',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6014,5015,'Add new section','addsectionnode','add_section.png','Create a new section',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6015,5016,'Add new image folder','addfoldernode','add_folder_images.png','Create a new image folder',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6016,5017,'Add new image folder','addfoldernode','add_folder_images.png','Create a new image folder',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6018,5020,'Add ximclude folder','addfoldernode','add_import.png','Create a new import folder',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6019,5021,'Add ximclude folder','addfoldernode','add_import.png','Create a nex import folder',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6020,5022,'Add common folder','addfoldernode','add_folder_common.png','Create a new common folder',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6021,5023,'Add common folder','addfoldernode','add_folder_common.png','Create a new common folder',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6022,5024,'Add CSS folder','addfoldernode','add_folder_css.png','Create a new CSS folder',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6023,5025,'Add CSS folder','addfoldernode','add_folder_css.png','Create a new CSS folder',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6069,5050,'Add new link subfolder','addfoldernode','add_links_category.png','Create a new link subfolder',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6070,5048,'Add new link subfolder','addfoldernode','add_links_category.png','Create a new link subfolder',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6137,5054,'Add new ximlet folder','addfoldernode','add_folder_ximlet.png','Create a new ximlet folder',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6139,5055,'Add new ximlet folder','addfoldernode','add_folder_ximlet.png','Create a new ximlet folder',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6001,5003,'Add new user','createuser','add_user.png','Create a new Ximdex user',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6006,5004,'Add new group','creategroup','add_group.png','Create a new group',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6009,5006,'Add node type','createnodetype','create_type_node.png','Add a node type',-10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6010,5005,'Add new role','createrole','create_rol.png','Create a new role',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6045,5031,'Add new language','addlangxmlcontainer','add_language_xml.png','Add a document with a different language',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6046,5079,'Manage workflow','modifystates','manage_states.png','Add a new status to the workflow',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`)  VALUES (6206,5083,'Add new metadata file', 'createxmlcontainer', 'add_xml.png', 'Create a new metadata document', 10, NULL, 0, '', 0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6343,5016,'Add images','fileupload_common_multiple','upload_image.png','Add an image set to the server',10,NULL,0,'type=image');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6344,5017,'Add images','fileupload_common_multiple','upload_image.png','Add an image set to the server',10,NULL,0,'type=image');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6345,5026,'Add templates','fileupload_common_multiple','add_template_ptd.png','Add a set of templates to the server',9,NULL,0,'type=ptd');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6346,5022,'Add files','fileupload_common_multiple','add_file_common.png','Add a set of files to the server',10,NULL,0,'type=common');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6347,5023,'Add files','fileupload_common_multiple','add_file_common.png','Add a set of files to the server',10,NULL,0,'type=common');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6348,5024,'Add style sheets','fileupload_common_multiple','add_file_css.png','Add a set of style sheets to the server',10,NULL,0,'type=css');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6349,5025,'Add style sheets','fileupload_common_multiple','add_file_css.png','Add a set of style sheets to the server',10,NULL,0,'type=css');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6236,5020,'Add files','fileupload_common_multiple','add_nodes_ht.png','Add multiple files',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7301,5035,'Add workflow', 'addworkflow', 'xix.png', 'Add a new workflow', -10, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`)  VALUES (8115,5053,'Add schemes', 'fileupload_common_multiple', 'add_template_pvd.png', 'Add a set of schemes to the server', 9, NULL, 0, 'type=pvd', 0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`)  VALUES (8122,5022,'Add empty file','newemptynode','add_file_common.png','Create a new empty file',9,NULL,0,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`)  VALUES (8123,5023,'Add empty file','newemptynode','add_file_common.png','Create a new empty file',9,NULL,0,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`)  VALUES (8124,5024,'Add empty stylesheet','newemptynode','add_file_common.png','Create a new empty file',9,NULL,0,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`)  VALUES (8125,5025,'Add empty stylesheet','newemptynode','add_file_common.png','Create a new empty file',9,NULL,0,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`)  VALUES (8126,5026,'Add empty XSL template','newemptynode','add_file_common.png','Create a new empty file',9,NULL,0,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`)  VALUES (8127,5053,'Add an empty RNG schema','newemptynode','add_file_common.png','Create a new empty file',9,NULL,0,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`)  VALUES (8128,5020,'Add empty file','newemptynode','add_file_common.png','Create a new empty file',9,NULL,0,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`)  VALUES (8129,5021,'Add empty file','newemptynode','add_file_common.png','Create a new empty file',9,NULL,0,'',0);


-- EDIT
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)       VALUES (6136,5032,'Edit in text mode','edittext','edit_file_xml_txt.png','Edit content of structured document in plain text',21,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)       VALUES (6065,5032,'Edit XML document','xmleditor2','edit_file_xml.png','Edit content of XML document',20,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)       VALUES (6094,5039,'Edit file','edittext','edit_file_txt.png','Edit content of text document',20,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)       VALUES (6105,5028,'Edit','edittext','edit_file_css.png','Edit content of text document',20,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)       VALUES (6154,5057,'Edit ximlet','xmleditor2','edit_file_xml.png','Modify content of a ximlet',20,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)       VALUES (6157,5057,'Edit in text mode','edittext','edit_file_xml_txt.png','Edit ximlet with text mode',21,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`)   VALUES (6209,5085,'Edit metadata in text mode', 'edittext', 'edit_file_xml_txt.png', 'Edit metadata content in text plain mode', 21, NULL, 0, '', 0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`)   VALUES (6210,5085,'Edit metadata in XML', 'xmleditor2', 'edit_file_xml.png', 'Edit metadata content in Xedit', 20, NULL, 0, '', 0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)       VALUES (6227,5076,'Edit in text mode','edittext','edit_html_txt_file.png','Edit content of text document',21,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)       VALUES (6229,5076,'Edit HTML','htmleditor','edit_html_file.png','Edita el documento html',21,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)       VALUES (7304,5321,'Edit RNG', 'edittext', 'edit_template_view.png', 'Edit RNG schema', 20, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)       VALUES (7306,5077,'Edit XSL template', 'edittext', 'edit_template_ptd.png', 'Edit a XSL template', 20, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)       VALUES (7319,5078,'Edit RNG schema', 'edittext', 'edit_template_view.png', 'Edit a RNG schema', 20, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`)             VALUES (8132,5031,'Edit metadata', 'managemetadata', 'xix.png', 'Edit the metadata info', 20, NULL, 0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`)             VALUES (8133,5040,'Edit metadata', 'managemetadata', 'xix.png', 'Edit the metadata info', 20, NULL, 0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`)             VALUES (8134,5039,'Edit metadata', 'managemetadata', 'xix.png', 'Edit the metadata info', 20, NULL, 0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`)             VALUES (8135,5041,'Edit metadata', 'managemetadata', 'xix.png', 'Edit the metadata info', 20, NULL, 0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)       VALUES (6073,5049,'Modify external link','modifylink','modify_link.png','Modify properties of external link',20,NULL,0,'');


-- COPY
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6600,5013,'Copy','copy','Copy_proyecto.png','Copy a complete project',30,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6601,5050,'Copy','copyNode','copiar_documento.png','Copy a link',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6602,5048,'Copy','copyNode','copiar_seccion.png','Copy a link folder',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6604,5023,'Copy','copyNode','copiar_carpeta_common.png','Copy a common subfolder',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6606,5025,'Copy','copyNode','copiar_seccion.png','Copy a CSS subfolder',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6608,5017,'Copy','copyNode','copiar_carpeta_images.png','Copy a image subfolder',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6610,5021,'Copy','copyNode','copiar_carpeta_ximclude.png','Copy a ximclude subfolder ',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6612,5031,'Copy','copyNode','copiar_carpeta_ximdoc.png','Copy a XML document',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6614,5055,'Copy','copyNode','copiar_carpeta_ximlet.png','Copy a ximlet subfolder',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6615,5056,'Copy','copyNode','copiar_carpeta_ximlet.png','Copy a ximlet document',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6616,5049,'Copy','copyNode','copiar_documento.png','Copy a link',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6617,5057,'Copy','copyNode','copiar_documento.png','Copy a ximlet',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6619,5014,'Copy','copyNode','copiar_servidor.png','Copy a complete server',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6620,5015,'Copy','copyNode','copiar_seccion.png','Copy a complete section',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6621,5053,'Copy','copyNode','copiar_seccion.png','Copy a complete schema',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6622,5026,'Copy','copyNode','copiar_carpeta_ximptd.png','Copy a complete template',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6629,5028,'Copy','copyNode','copiar_documento.png','Copy a style sheet',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6626,5039,'Copy','copyNode','copiar_documento.png','Copy a text file',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6627,5040,'Copy','copyNode','copiar_documento.png','Copy a image file',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6628,5041,'Copy','copyNode','copiar_documento.png','Copy a binary file',-30,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7200,5040,'Copy', 'copy', 'copiar_documento.png', 'Copy a image to another destination', 30, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7201,5017,'Copy', 'copy', 'copiar_carpeta_images.png', 'Copy a image subfolder to another destination', 31, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7203,5014,'Copy', 'copy', 'copiar_servidor.png', 'Copy a server to another destination', 30, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7206,5021,'Copy', 'copy', 'copiar_carpeta_ximclude.png', 'Copy a ximclude subfolder to another destination', 31, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7208,5023,'Copy', 'copy', 'copiar_carpeta_common.png', 'Copia a common subfolder to another destination', 31, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7210,5025,'Copy', 'copy', 'copiar_seccion.png', 'Copy a CSS subfolder to another destination', 31, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7211,5026,'Copy', 'copy', 'copiar_carpeta_ximptd.png', 'Copy a templates folder to another destination', 31, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7212,5028,'Copy', 'copy', 'copiar_documento.png', 'Copy a CSS document to another destination', 30, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7213,5031,'Copy', 'copy', 'copiar_carpeta_ximdoc.png', 'Copy a document to another destination', 30, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7215,5039, 'Copy', 'copy', 'copiar_documento.png', 'Copy a text document to another destination', 30, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7216,5041, 'Copy', 'copy', 'copiar_documento.png', 'Copy a binary document to another destination', 30, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7217,5048, 'Copy', 'copy', 'copiar_carpeta_ximlink.png', 'Copy a link subfolder to another destination', 31, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7218,5049, 'Copy', 'copy', 'copiar_documento.png', 'Copy a link to another destination', 30, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7219,5050, 'Copy', 'copy', 'copiar_carpeta_ximlink.png', 'Copy a links folder to another destination', 31, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7221,5055, 'Copy', 'copy', 'copiar_carpeta_ximlet.png', 'Copy a ximlet subfolder to another destination',31, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7222,5056, 'Copy', 'copy', 'copiar_carpeta_ximdoc.png', 'Copy a ximlet document to another destination', 30, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7223,5057, 'Copy', 'copy', 'copiar_carpeta_ximlet.png', 'Copy a ximlet document to another destination', 30, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7224,5076, 'Copy', 'copy', 'copiar_documento.png', 'Copy a HTML document to another destination', 30, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7225,5015, 'Copy', 'copy', 'copiar_seccion.png', 'Copy a section to another destination', 30, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7226,5053, 'Copy', 'copy', 'copiar_carpeta_ximdoc.png', 'Copy a schemes folder to another destination', 31, NULL,0,'');

-- INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6603,5022,'Copy','copyNode','copiar_seccion.png','Copy a complete common folder',-93,'ximIO',0,'');
-- INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES('7220', '5054', 'Copy', 'copy', 'copiar_carpeta_ximlet.png', 'Copy a ximlet folder to another destination', '30', NULL,0,'');
-- INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7209, 5024, 'Copy', 'copy', 'copiar_seccion.png', 'Copy a CSS folder to another destination', '30', NULL,0,'');
-- INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7207, 5022, 'Copy', 'copy', 'copiar_carpeta_common.png', 'Copy a common folder to another destination', '30', NULL,0,'');
-- INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7204, 5018, 'Copy', 'copy', 'copiar_carpeta_ximdoc.png', 'Copy a documents folder to another destination', '30', NULL,0,'');
-- INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7205, 5020, 'Copy', 'copy', 'copiar_carpeta_ximclude.png', 'Copy a ximclude folder to another destination', '30', NULL,0,'');
-- INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7202, 5016, 'Copy', 'copy', 'copiar_carpeta_images.png', 'Copy a image folder to another destination', '30', NULL,0,'');
-- INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6613,5054,'Copy','copyNode','copiar_carpeta_ximlet.png','Copy a complete ximlet folder',-93,'ximIO',0,'');
-- INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6611,5018,'Copy','copyNode','copiar_carpeta_ximdoc.png','Copy a complete documents folder',-93,'ximIO',0,'');
-- INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6609,5020,'Copy','copyNode','copiar_carpeta_ximclude.png','Copy a complete ximclude folder',-93,'ximIO',0,'');
-- INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6607,5016,'Copy','copyNode','copiar_carpeta_images.png','Copy a complete image folder',-93,'ximIO',0,'');
-- INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6605,5024,'Copy','copyNode','copiar_seccion.png','Copy a complete CSS folder',-93,'ximIO',0,'');

-- MOVE
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (6170,5014,'Move node','movenode','move_node.png','Move a node',40,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (6171,5015,'Move node','movenode','move_node.png','Move a node',40,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (6172,5017,'Move node','movenode','move_node.png','Move a node',40,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (6173,5021,'Move node','movenode','move_node.png','Move a node',40,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (6174,5023,'Move node','movenode','move_node.png','Move a node',40,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (6175,5028,'Move node','movenode','move_node.png','Move a node',40,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (6176,5031,'Move node','movenode','move_node.png','Move a node',40,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (6177,5039,'Move node','movenode','move_node.png','Move a node',40,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (6178,5040,'Move node','movenode','move_node.png','Move a node',40,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (6179,5041,'Move node','movenode','move_node.png','Move a node',40,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (6181,5048,'Move node','movenode','move_node.png','Move a node',40,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (6182,5049,'Move node','movenode','move_node.png','Move a node',40,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (6183,5056,'Move node','movenode','move_node.png','Move a node',40,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (6233,5076,'Move file','movenode','move_node.png','Move a node',40,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (8120,5077,'Move node','movenode','move_node.png','Move a node',40,NULL,1,'');

-- Modify
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6002,5009,'Modify user data','modifyuser','modify_user.png','Change user data',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6005,5010,'Modify role','modifyrole','modify_rol.png','Manage role attributions',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6008,5007,'Modify node type','modifynodetype','modify_nodetype.png','Modify a node type',-60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6081,5010,'Modify associated workflow status','modifystatesrole','modify_state_workflow-rol.png','Modify associated status with this role',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6082,5036,'Modify associated roles','modifyrolesstate','manage_states-rol.png','Modify associated roles with this status',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6095,5033,'Channel properties','modifychannel','properties_channel.png','Modify channel properties',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6096,5011,'Group properties','modifygroup','properties_group.png','Modify group properties',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6097,5034,'Modify language','modifylanguage','modify_language.png','Modify data of a language',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6101,5011,'Change users','modifygroupusers','modify_users.png','Modify list of users that integrate this group',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6125,5014,'Manage servers','modifyserver','modify_sinc_data.png','Modify data connection with the production environment',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7307,5013,'Modify properties','manageproperties','xix.png','Modify properties of a project', 60, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7308,5014,'Modify properties','manageproperties','xix.png','Modify properties of a server', 60, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7309,5015,'Modify properties','manageproperties','xix.png','Modify properties of a section', 60, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7310,5018,'Modify properties','manageproperties','xix.png','Modify properties of a document folder', 61, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7311,5031,'Modify properties','manageproperties','xix.png','Modify properties', 60, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7312,5032,'Modify properties','manageproperties','xix.png','Modify properties of a document', 60, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7317,5082,'Modify properties','manageproperties','xix.png','Modify global properties',60, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7321,5078,'Modify properties','renamenode', 'modiy_templateview', 'Modify properties of a RNG schema', 60, NULL , '0', NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6004,5009,'Manage groups','modifyusergroups','manage_user_groups.png','Enroll, disenroll, and change user role in groups where he/she belongs to',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6007,5015,'Configure section','managefolders','change_name_section.png','Configure the folders of a section',61,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6025,5015,'Change section name','renamenode','change_name_section.png','Change a section name',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6026,5013,'Change project name','renamenode','change_name_proyect.png','Change a project name',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6027,5014,'Change server name','renamenode','change_name_server.png','Change a server name',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6028,5017,'Change name','renamenode','change_name_folder_images.png','Change name of selected folder',61,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6030,5021,'Change name','renamenode','change_name_folder_import.png','Change name of selected folder',61,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6031,5023,'Change name','renamenode','change_name_folder_common.png','Change name of selected folder',61,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6032,5025,'Change name','renamenode','change_name_folder_css.png','Change folder name',61,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6123,5014,'Associated groups','modifygroupsnode','groups_server.png','Manage associations of groups with this node',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6064,5031,'Change name of XML document','renamenode','change_name_xml.png','Change the document name and all its language versions',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6074,5015,'Associated groups','modifygroupsnode','groups_section.png','Manage associations of groups with this node',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6103,5028,'Change name','renamenode','change_name_file_css.png','Change file name on import folder',61,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6083,5041,'Change name','renamenode','change_name_file_txt_bin.png','Change name of selected file',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6085,5040,'Change image name','renamenode','change_name_image.png','Change file name',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6087,5039,'Change name','renamenode','change_name_file_txt_bin.png','Change file name on import folder',61,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6122,5013,'Associated groups','modifygroupsnode','groups_project.png','Manage associations of groups with this node',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6118,5048,'Change name','renamenode','modify_link_folder.png','Change name of selected folder',61,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6142,5055,'Change name','renamenode','change_name_folder_ximlet.png','Change folder name',61,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6153,5056,'Change ximlet name','renamenode','change_name_xml.png','Change ximlet name',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`)   VALUES (6208, 5084, 'Change name', 'renamenode', 'change_name_xml.png', 'Change the metadatas name and all its language versions', 60, NULL, 0, '', 0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6230,5076,'Change name','renamenode','change_name_html_file.png','Change name of selected file',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7236, 5057, 'Manage associations', 'showassocnodes', 'xix.png', 'Manage node associations with ximlet', -60, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`,`Description`,`Sort`,`Module`,`Multiple`,`Params`)           VALUES (7229, 5048, 'Check links', 'linkreport', 'xix.png', 'Check broken links', 60, NULL,0,NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`,`Description`,`Sort`,`Module`,`Multiple`,`Params`)           VALUES (7230, 5049, 'Check links', 'linkreport', 'xix.png', 'Check broken links',-60, NULL,0,NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`,`Description`,`Sort`,`Module`,`Multiple`,`Params`)           VALUES (7231, 5050, 'Check links', 'linkreport', 'xix.png', 'Check broken links', 60, NULL,0,NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)          VALUES (8117,5077,'Change name','renamenode','modify_template_ptd.png','Change template name',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`)               VALUES (8136, 5082, 'Set allowed extensions', 'setextensions', 'modify_users.png', 'Set allowed extensions', 60, NULL, 0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`)               VALUES (8137, 5081, 'Manage module', 'moduleslist', 'xix.png', 'Manage module', 60, NULL, 0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6184,5015,'Associate a ximlet with a section','addximlet','asociate_ximlet_folder.png','Associate a ximlet with a section',62,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6202,5014,'Associate ximlet with server','addximlet','asociate_ximlet_server.png','Associate a ximlet with a server',62,NULL,1,'');

-- WORKFLOW
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6098,5032,'Move to next state','workflow_forward_advance','change_next_state.png','Move to the next state',73,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6099,5032,'Move to previous state','workflow_backward','change_last_state.png','Move to the previous state',73,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6126,5039,'Move to next state','workflow_forward_advance','change_next_state.png','Move a text document to the next state',73,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6127,5039,'Move to previous state','workflow_backward','change_last_state.png','Move a text document to the previous state',73,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6128,5041,'Move to next state','workflow_forward_advance','change_next_state.png','Move a text document to the next state',73,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6129,5041,'Move to previous state','workflow_backward','change_last_state.png','Move a text document to the previous state',73,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6130,5040,'Move to next state','workflow_forward_advance','change_next_state.png','Move a text document to the next state',73,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6131,5040,'Move to previous state','workflow_backward','change_last_state.png','Move a text document to the previous state',73,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6132,5028,'Move to next state','workflow_forward_advance','change_next_state.png','Move a text document to the next state',73,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6133,5028,'Move to previous state','workflow_backward','change_last_state.png','Move a text document to the previous state',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6100,5032,'Advance publisher','workflow_forward_advance','change_next_state.png','Advance publish',-70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6204,5015,'Publish section','publicatesection','publicate_section.png','Publish a section massively',70,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7016, 5057, 'Publish ximlet', 'publicateximlet', 'xix.png', 'Publish documents associated with a ximlet', 70, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8101, 5016, 'Publish section', 'publicatesection', 'publicate_section.png', 'Publish a section massively', 70, NULL, 1, '');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8102, 5017, 'Publish section', 'publicatesection', 'publicate_section.png', 'Publish a section massively', 70, NULL, 1, '');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8103, 5024, 'Publish section', 'publicatesection', 'publicate_section.png', 'Publish a section massively', 70, NULL, 1, '');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8104, 5025, 'Publish section', 'publicatesection', 'publicate_section.png', 'Publish a section massively', 70, NULL, 1, '');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8105, 5022, 'Publish section', 'publicatesection', 'publicate_section.png', 'Publish a section massively', 70, NULL, 1, '');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8106, 5023, 'Publish section', 'publicatesection', 'publicate_section.png', 'Publish a section massively', 70, NULL, 1, '');

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (9500,5032,'Expire document','expiredoc','expire_section.png','Expire a document',74,NULL,0,'');

-- VERSION MANAGER
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6144,5040,'Version manager','manageversions','manage_versions.png','Manage version repository',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6145,5041,'Version manager','manageversions','manage_versions.png','Manage repository of versions',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6146,5028,'Version manager','manageversions','manage_versions.png','Manage repository of versions',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6148,5039,'Version manager','manageversions','manage_versions.png','Manage repository of versions',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6150,5057,'Version manager','manageversions','manage_versions.png','Manage repository of versions',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6143,5032,'Version manager','manageversions','manage_versions.png','Manage repository of versions',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6231,5076,'Version manager','manageversions','manage_html_versions.png','Manage repository of versions',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)          VALUES (8119,5077,'Version manager','manageversions','manage_versions.png','Manage repository of versions',60,NULL,0,'');

-- ENLACE SIMBÓLICO
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6135,5032,'Symbolic link','xmlsetlink','file_xml_symbolic.png','Modify document which borrows the content',74,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6156,5057,'Symbolic link','xmlsetlink','file_xml_symbolic.png','Modify the ximlet which borrows the content',74,NULL,0,'');

-- DELETE
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6003,5009,'Remove user','deleteuser','delete_user.png','Remove an user from system',75,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6034,5013,'Delete project','deletenode','delete_proyect.png','Delete a project',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6035,5014,'Delete server','deletenode','delete_server.png','Delete a server',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6036,5015,'Delete section','deletenode','delete_section.png','Delete a section',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6037,5017,'Delete folder','deletenode','delete_folder_images.png','Delete selected folder',76,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6039,5021,'Delete folder','deletenode','delete_folder_import.png','Delete selected folder',76,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6040,5023,'Delete folder','deletenode','delete_folder_common.png','Delete selected folder',76,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6041,5025,'Delete folder','deletenode','delete_folder_css.png','Delete selected folder',76,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6108,5011,'Delete group','deletenode','delete_group.png','Delete select group',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6063,5031,'Delete document','deletenode','delete_xml.png','Delete XML document in all its languages',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6067,5008,'Delete action','deletenode','delete.png','Delete the action',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6084,5041,'Delete file','deletenode','delete_file_txt_bin.png','Delete selected file',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6086,5040,'Delete image','deletenode','delete_image.png','Delete an image',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6088,5039,'Delete','deletenode','delete_file_txt_bin.png','Delete file of import folder',76,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6107,5033,'Delete channel','deletenode','delete_channel.png','Delete a channel if it has not associated documents',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6102,5034,'Delete','deletenode','delete_language.png','Delete a language from the system',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6106,5028,'Delete','deletenode','delete_file_css.png','Delete file of import folder',76,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6109,5010,'Delete role','deletenode','delete_role.png','Delete a selected role if it is not in use',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6121,5036,'Delete','deletenode','delete_state.png','Delete a state if it is not an initial or final one and it is not in use',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6119,5048,'Delete folder','deletenode','delete_link_folder.png','Delete selected folder',76,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6120,5049,'Delete link','deletenode','delete_link.png','Delete selected link',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6134,5032,'Delete document','deletenode','delete_file_xml.png','Delete selected XML document',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6141,5055,'Delete folder','deletenode','delete_folder_ximlet.png','Delete selected folder',76,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6152,5056,'Delete ximlet','deletenode','delete_xml.png','Delete a ximlet permanently from system',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6155,5057,'Delete document','deletenode','delete_file_xml.png','Delete a ximlet and all its dependencies',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`)  VALUES (6207,5084,'Delete metadata document', 'deletenode', 'delete_xml.png', 'Delete metadata document in all its languages',75, NULL, 1, '', 0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6232,5076,'Delete file','deletenode','delete_html_file.png','Delete HTML file',75,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (7305,5077,'Delete XSL template', 'deletenode', 'delete_template_ptd.png', 'Delete a XSL template',75, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)      VALUES (7318,5078,'Delete RNG schema', 'deletenode', 'delete_template_view.png', 'Delete a RNG schema',75, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (8121,5026,'Delete templates','deletetemplates','delete_template_ptd.png','Delete selected templates',75,NULL,0,'');

-- OTHERS
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6049,5041,'Download file','filedownload','download_file_txt_bin.png','Download selected file',80,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6050,5040,'Download image','filedownload','download_image.png','Download an image to a local hard disk',80,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6051,5039,'Download file','filedownload','download_file_txt_bin.png','Download a file to a local hard disk',80,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6052,5040,'Image preview','filepreview','view_image.png','Preview an image',80,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6104,5028,'Download file','filedownload','download_file_css.png','Download a file to a local hard disk',80,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6228,5076,'Download file','filedownload','download_html_file.png','Download selected file',80,NULL,0,'');

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6205,5015,'Expire section','expiresection','expire_section.png','Expire a section',80,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7239, '0', 'Action report', 'actionsstats', 'xix.png', 'Show action report', 80, NULL, 0, NULL);

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8107,5016,'Download all images','filedownload_multiple','download_image.png','Download all images',80,NULL,0,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8108,5017,'Download all images','filedownload_multiple','download_image.png','Download all images',80,NULL,0,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8109,5024,'Download all style sheets','filedownload_multiple','download_file_css.png','Download all style sheets',80,NULL,0,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8110,5025,'Download all style sheets','filedownload_multiple','download_file_css.png','Download all style sheets',80,NULL,0,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8111,5026,'Download all templates','filedownload_multiple','download_template_ptd.png','Download all templates',80,NULL,0,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8112,5053,'Download all templates','filedownload_multiple','download_template_view.png','Download all templates',80,NULL,0,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8113,5022,'Download all files','filedownload_multiple','download_html_file.png','Download all files',80,NULL,0,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8114,5023,'Download all files','filedownload_multiple','download_html_file.png','Download all files',80,NULL,0,'',1);

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8118,5077,'Download template','filedownload','download_template_ptd.png','Download a template',80,NULL,0,'');

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`,`IsBulk`)   VALUES (6147, 5016, 'Image viewer', 'filepreview', 'view_image.png', 'Preview the images', 80, NULL, 0, 'method=showAll', 0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`,`IsBulk`)   VALUES (6149, 5017, 'Image viewer', 'filepreview', 'view_image.png', 'Preview the images', 80, NULL, 0, 'method=showAll', 0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)        VALUES (6385,5032,'Preview', 'preview', 'xix.png', 'Preview of the document', 80, NULL, 0,'');

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`)   VALUES (8116, 5014, 'Status Report', 'checkstatus', 'manage_versions.png', 'Status Report to inform the user', 95,NULL,0,NULL,0);

-- export && import
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6500,5012,'Export','serializeNodeXML','xix.png','Export all projects',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6501,5013,'Export','serializeNodeXML','xix.png','Export a complete project',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6502,5050,'Export','serializeNodeXML','xix.png','Export a link',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6503,5048,'Export','serializeNodeXML','xix.png','Export a link folder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6504,5022,'Export','serializeNodeXML','xix.png','Export a complete common folder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6505,5023,'Export','serializeNodeXML','xix.png','Export a common subfolder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6506,5024,'Export','serializeNodeXML','xix.png','Export a complete CSS folder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6507,5025,'Export','serializeNodeXML','xix.png','Export a CSS subfolder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6508,5016,'Export','serializeNodeXML','xix.png','Export a complete image folder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6509,5017,'Export','serializeNodeXML','xix.png','Export a image subfolder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6510,5020,'Export','serializeNodeXML','xix.png','Export a complete ximclude folder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6511,5021,'Export','serializeNodeXML','xix.png','Export a ximclude subfolder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6512,5018,'Export','serializeNodeXML','xix.png','Export a complete documents folder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6513,5031,'Export','serializeNodeXML','xix.png','Export a XML document',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6514,5054,'Export','serializeNodeXML','xix.png','Export a complete ximlet folder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6515,5055,'Export','serializeNodeXML','xix.png','Export a ximlet subfolder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6516,5056,'Export','serializeNodeXML','xix.png','Export a ximlet document',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6517,5014,'Export','serializeNodeXML','xix.png','Export a complete server',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6518,5015,'Export','serializeNodeXML','xix.png','Export a complete section',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6519,5053,'Export','serializeNodeXML','xix.png','Export a complete schema',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6520,5026,'Export','serializeNodeXML','xix.png','Export a complete template',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6550,5012,'Import','deserializeNodeXML','xix.png','Import all projects',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6551,5013,'Import','deserializeNodeXML','xix.png','Import a complete project',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6552,5050,'Import','deserializeNodeXML','xix.png','Import a link',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6553,5048,'Import','deserializeNodeXML','xix.png','Import a link folder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6554,5022,'Import','deserializeNodeXML','xix.png','Import a complete common folder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6555,5023,'Import','deserializeNodeXML','xix.png','Import a common subfolder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6556,5024,'Import','deserializeNodeXML','xix.png','Import a complete CSS folder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6557,5025,'Import','deserializeNodeXML','xix.png','Import a CSS subfolder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6558,5016,'Import','deserializeNodeXML','xix.png','Import a complete image folder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6559,5017,'Import','deserializeNodeXML','xix.png','Import a image subfolder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6560,5020,'Import','deserializeNodeXML','xix.png','Import a complete ximclude folder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6561,5021,'Import','deserializeNodeXML','xix.png','Import a ximclude subfolder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6562,5018,'Import','deserializeNodeXML','xix.png','Import a complete documents folder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6563,5031,'Import','deserializeNodeXML','xix.png','Import a XML document',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6564,5054,'Import','deserializeNodeXML','xix.png','Import a complete ximlet folder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6565,5055,'Import','deserializeNodeXML','xix.png','Import a ximlet subfolder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6566,5056,'Import','deserializeNodeXML','xix.png','Import a ximlet document',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6567,5014,'Import','deserializeNodeXML','xix.png','Import a complete server',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6568,5015,'Import','deserializeNodeXML','xix.png','Import a complete section',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6569,5053,'Import','deserializeNodeXML','xix.png','Import a complete schema',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6570,5026,'Import','deserializeNodeXML','xix.png','Import a complete template',-92,'ximIO',0,'');

/*Info node for every nodetype*/
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9600, 5001, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9601, 5002, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9602, 5003, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9603, 5004, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9604, 5005, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9605, 5006, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9606, 5007, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9607, 5008, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9608, 5009, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9609, 5010, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9610, 5011, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9611, 5012, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9612, 5013, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9613, 5014, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9614, 5015, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9615, 5016, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9616, 5017, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9617, 5018, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9618, 5020, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9619, 5021, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9620, 5022, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9621, 5023, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9622, 5024, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9623, 5025, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9624, 5026, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9625, 5028, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9626, 5029, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9627, 5030, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9628, 5031, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9629, 5032, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9630, 5033, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9631, 5034, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9632, 5035, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9633, 5036, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9634, 5037, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9635, 5038, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9636, 5039, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9637, 5040, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9638, 5041, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9639, 5043, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9640, 5044, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9641, 5048, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9642, 5049, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9643, 5050, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9644, 5053, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9645, 5054, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9646, 5055, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9647, 5056, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9648, 5057, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9649, 5058, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9650, 5059, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9651, 5060, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9652, 5061, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9653, 5063, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9654, 5066, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9655, 5068, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9656, 5076, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9657, 5077, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9658, 5078, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9659, 5079, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9660, 5080, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9661, 5081, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9662, 5082, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9663, 5083, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9664, 5084, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9665, 5085, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9666, 5300, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9667, 5301, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9668, 5302, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9669, 5303, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9670, 5304, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9671, 5305, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9672, 5306, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9673, 5307, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9674, 5308, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9675, 5309, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9676, 5310, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9677, 5311, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9678, 5312, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9679, 5313, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`,`Module`, `Multiple`,`Params`,`IsBulk`)
 VALUES(9680, 5320, 'Info node', 'infonode','infonode.png','Info node',70,NULL,0,NULL,0);
UNLOCK TABLES;


/*!40000 ALTER TABLE `Actions` ENABLE KEYS */;
-- No actions for nodes
DROP TABLE IF EXISTS `NoActionsInNode`;
CREATE TABLE `NoActionsInNode` (
  `IdNode` INT NOT NULL ,
  `IdAction` INT NOT NULL COMMENT 'Actions not allowed for a Node',
  PRIMARY KEY ( `IdNode` , `IdAction` )
) ENGINE = MyISAM COMMENT = 'List of Actions not allowed in a Node';

-- Delete group "General"
INSERT INTO `NoActionsInNode` ( `IdNode` , `IdAction` ) VALUES ( '101', '6108' );
-- Modify Group "General"
INSERT INTO `NoActionsInNode` (`IdNode` , `IdAction` ) VALUES ( '101', '6096');
-- Delete user "Ximdex"
INSERT INTO `NoActionsInNode` (`IdNode` ,`IdAction`)VALUES ('301', '6003');
-- Delete rol "Administrator"
INSERT INTO `NoActionsInNode` (`IdNode` ,`IdAction`)VALUES ('201', '6109');

--
-- Table structure for table `
--

DROP TABLE IF EXISTS `Channels`;
CREATE TABLE `Channels` (
  `IdChannel` int(12) unsigned NOT NULL,
  `Name` varchar(255) NOT NULL default '0',
  `Description` varchar(255) default '0',
  `DefaultExtension` varchar(255) default NULL,
  `Format` varchar(255) default NULL,
  `Filter` varchar(255) default NULL,
  `RenderMode` varchar(255) default NULL,
  `OutputType` varchar(100) default NULL,
  `Default_Channel` boolean NOT NULL default FALSE,
  PRIMARY KEY  (`IdChannel`)
) ENGINE=MyISAM COMMENT='Available channels used to transform content';

--
-- Dumping data for table `Channels`
--


/*!40000 ALTER TABLE `Channels` DISABLE KEYS */;
LOCK TABLES `Channels` WRITE;
INSERT INTO `Channels` (`IdChannel`, `Name`, `Description`, `DefaultExtension`, `Format`, `Filter`, `RenderMode`,`OutputType`, `Default_channel`) VALUES(10001, 'html', 'Html channel', 'html', NULL, NULL, 'ximdex','web', 1);
UNLOCK TABLES;
/*!40000 ALTER TABLE `Channels` ENABLE KEYS */;

--
-- Table structure for table `Config`
--


DROP TABLE IF EXISTS `Config`;
CREATE TABLE `Config` (
  `IdConfig` int(12) unsigned NOT NULL auto_increment,
  `ConfigKey` varchar(255) NOT NULL default '0',
  `ConfigValue` blob,
  PRIMARY KEY  (`IdConfig`),
  UNIQUE KEY `IdConfig` (`IdConfig`,`ConfigKey`),
  UNIQUE KEY `ConfigKey` (`ConfigKey`),
  KEY `IdConfig_2` (`IdConfig`)
) ENGINE=MyISAM COMMENT='Table with configuration parameters of Ximdex CMS';

--
-- Dumping data for table `Config`
--

/*!40000 ALTER TABLE `Config` DISABLE KEYS */;
LOCK TABLES `Config` WRITE;
INSERT INTO `Config` VALUES (1,'AppRoot','');
INSERT INTO `Config` VALUES (2,'NodeRoot','/data/nodes');
INSERT INTO `Config` VALUES (3,'TempRoot','/data/tmp');
INSERT INTO `Config` VALUES (4,'SyncRoot','/data/sync');
INSERT INTO `Config` VALUES (5,'FileRoot','/data/files');
INSERT INTO `Config` VALUES (6,'UrlRoot','');
INSERT INTO `Config` VALUES (7,'GeneralGroup','101');
INSERT INTO `Config` VALUES (8,'ProjectsNode','10000');
INSERT INTO `Config` VALUES (9,'GeneratorCommand','/modules/dexT/dexTdin_xmd25.pl');
INSERT INTO `Config` VALUES (10,'EncodingTag','<?xml version=\"1.0\" encoding=\"UTF-8\"?>');
INSERT INTO `Config` VALUES (11,'DoctypeTag','<!DOCTYPE docxap [\n<!ENTITY Ntilde \"_MAPGENcode_Ntilde_\">\n<!ENTITY ntilde \"_MAPGENcode_ntilde_\">\n<!ENTITY aacute \"_MAPGENcode_aacute_\">\n<!ENTITY eacute \"_MAPGENcode_eacute_\">\n<!ENTITY iacute \"_MAPGENcode_iacute_\">\n<!ENTITY oacute \"_MAPGENcode_oacute_\">\n<!ENTITY uacute \"_MAPGENcode_uacute_\">\n<!ENTITY Aacute \"_MAPGENcode_Aacute_\">\n<!ENTITY Eacute \"_MAPGENcode_Eacute_\">\n<!ENTITY Iacute \"_MAPGENcode_Iacute_\">\n <!ENTITY Oacute \"_MAPGENcode_Oacute_\">\n<!ENTITY Uacute \"_MAPGENcode_Uacute_\">\n<!ENTITY agrave \"_MAPGENcode_agrave_\">\n <!ENTITY egrave \"_MAPGENcode_egrave_\">\n<!ENTITY igrave \"_MAPGENcode_igrave_\">\n <!ENTITY ograve \"_MAPGENcode_ograve_\">\n<!ENTITY ugrave \"_MAPGENcode_ugrave_\">\n<!ENTITY Agrave \"_MAPGENcode_Agrave_\">\n<!ENTITY Egrave \"_MAPGENcode_Egrave_\">\n<!ENTITY Igrave \"_MAPGENcode_Igrave_\">\n<!ENTITY Ograve \"_MAPGENcode_Ograve_\">\n<!ENTITY Ugrave \"_MAPGENcode_Ugrave_\">\n<!ENTITY auml   \"_MAPGENcode_auml_\">\n<!ENTITY euml   \"_MAPGENcode_euml_\">\n<!ENTITY iuml   \"_MAPGENcode_iuml_\">\n<!ENTITY ouml   \"_MAPGENcode_ouml_\">\n<!ENTITY uuml   \"_MAPGENcode_uuml_\">\n<!ENTITY Auml   \"_MAPGENcode_Auml_\">\n<!ENTITY Euml   \"_MAPGENcode_Euml_\">\n<!ENTITY Iuml   \"_MAPGENcode_Iuml_\">\n<!ENTITY Ouml   \"_MAPGENcode_Ouml_\">\n<!ENTITY Uuml   \"_MAPGENcode_Uuml_\">\n<!ENTITY Ccedil \"_MAPGENcode_Ccedil_\">\n<!ENTITY ccedil \"_MAPGENcode_ccedil_\">\n<!ENTITY ordf   \"_MAPGENcode_ordf_\">\n<!ENTITY ordm   \"_MAPGENcode_ordm_\">\n<!ENTITY iquest \"_MAPGENcode_iquest_\">\n<!ENTITY iexcl  \"_MAPGENcode_iexcl_\">\n<!ENTITY nbsp   \"_MAPGENcode_nbsp_\">\n<!ENTITY middot \"_MAPGENcode_middot_\">\n<!ENTITY acute  \"_MAPGENcode_acute_\">\n<!ENTITY copy  \"_MAPGENcode_copy_\">\n]>\n');
INSERT INTO `Config` VALUES (12,'DefaultLanguage','es');
INSERT INTO `Config` VALUES (13,'BlockExpireTime','120');
INSERT INTO `Config` VALUES (14,'MaximunGapSizeTolerance','180');
INSERT INTO `Config` VALUES (15,'SynchronizerCommand','/modules/synchronizer/ximCRON.pl');
INSERT INTO `Config` VALUES (16,'SchemasDirName','schemes');
INSERT INTO `Config` VALUES (17,'TemplatesDirName','templates');
INSERT INTO `Config` VALUES (18,'PurgeSubversionsOnNewVersion','1');
INSERT INTO `Config` VALUES (19,'MaxSubVersionsAllowed','4');
INSERT INTO `Config` VALUES (20,'PreviewInServer','0');
INSERT INTO `Config` VALUES (21,'PurgeVersionsOnNewVersion','0');
INSERT INTO `Config` VALUES (22,'MaxVersionsAllowed','3');
INSERT INTO `Config` VALUES (23,'ximid','-');
INSERT INTO `Config` VALUES (24,'VersionName','Ximdex 3.6');
INSERT INTO `Config` VALUES (25,'UTFLevel','0');
INSERT INTO `Config` VALUES (26,'EmptyHrefCode','/404.html');
INSERT INTO `Config` VALUES (27, 'defaultRNG', NULL);
INSERT INTO `Config` VALUES (28, 'defaultChannel', NULL);
INSERT INTO `Config` VALUES (29, 'dexCache', NULL);
INSERT INTO `Config` VALUES (30, 'PublishOnDisabledServers', NULL);
INSERT INTO `Config` VALUES (31, 'defaultWebdavPVD', NULL);
INSERT INTO `Config` VALUES (32, 'locale', 'en_US');
INSERT INTO `Config` VALUES (33, 'displayEncoding', 'UTF-8');
INSERT INTO `Config` VALUES (34, 'dbEncoding', 'ISO-8859-1');
INSERT INTO `Config` VALUES (35, 'dataEncoding', 'UTF-8');
INSERT INTO `Config` VALUES (36, 'workingEncoding', 'UTF-8');
INSERT INTO `Config` VALUES (37, 'ActionsStats', 0);
INSERT INTO `Config` VALUES (38, 'IdDefaultWorkflow', 403);
INSERT INTO `Config` VALUES (39, 'DefaultInitialStatus', 'Edici�n');
INSERT INTO `Config` VALUES (40, 'DefaultFinalStatus', 'Publicaci�n');
INSERT INTO `Config` VALUES (41, 'PullMode', 0);
INSERT INTO `Config` VALUES (42, 'EnricherKey', '');
INSERT INTO `Config` VALUES (43, 'AddVersionUsesPool', '0');
INSERT INTO `Config` VALUES (44, 'StructuralDeps', 'css,asset,script');
INSERT INTO `Config` VALUES (45, 'xplorer', '1');
INSERT INTO `Config` VALUES (46, 'SyncStats', '0');
INSERT INTO `Config` VALUES (47, 'XslIncludesOnServer', '0');
INSERT INTO `Config` VALUES (48, 'TokenTTL','30');
INSERT INTO `Config` VALUES (49, 'ApiIV','');
INSERT INTO `Config` VALUES (50, 'ApiKey','');
INSERT INTO `Config` VALUES (51, 'DevEnv','1');
-- PublishPathFormat: Accepts default, prefix and sufix values
INSERT INTO `Config` VALUES (52, 'PublishPathFormat','prefix');
INSERT INTO `Config` VALUES (53, 'ChunksFolder','uploaded_files');
INSERT INTO `Config` VALUES (54, 'UploadsFolder','uploaded_files');
INSERT INTO `Config` VALUES (55, 'MaxItemsPerGroup',50);
INSERT INTO `Config` VALUES (NULL, 'DisableCache',0);

UNLOCK TABLES;
/*!40000 ALTER TABLE `Config` ENABLE KEYS */;

--
-- Table structure for table `Dependencies`
--

DROP TABLE IF EXISTS `Dependencies`;
CREATE TABLE `Dependencies` (
  `IdDep` int(12) unsigned NOT NULL auto_increment,
  `IdNodeMaster` int(12) unsigned NOT NULL default '0',
  `IdNodeDependent` int(12) unsigned NOT NULL default '0',
  `DepType` int(6) NOT NULL default '0',
  `version` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`IdDep`),
  KEY `IdNodeMaster` (`IdNodeMaster`),
  KEY `IdNodeDependent` (`IdNodeDependent`),
  KEY `DepType` (`DepType`)
) ENGINE=MyISAM COMMENT='All the dependencies by type on Ximdex CMS';

--
-- Dumping data for table `Dependencies`
--
/*!40000 ALTER TABLE `Dependencies` DISABLE KEYS */;
LOCK TABLES `Dependencies` WRITE;
UNLOCK TABLES;


--
-- Table structure for table `DependenceTypes`
--

DROP TABLE IF EXISTS `DependenceTypes`;
CREATE TABLE `DependenceTypes` (
  `IdDepType` int(6) unsigned NOT NULL auto_increment,
  `Type` varchar(31)  NOT NULL default '0',
  PRIMARY KEY (`IdDepType`)
) ENGINE=MyISAM;


--
-- Dumping data for table `DependenceTypes`
--
LOCK TABLES `DependenceTypes` WRITE;

INSERT INTO `DependenceTypes` VALUES (NULL, 'asset');
INSERT INTO `DependenceTypes` VALUES (NULL, 'channel');
INSERT INTO `DependenceTypes` VALUES (NULL, 'language');
INSERT INTO `DependenceTypes` VALUES (NULL, 'schema');
INSERT INTO `DependenceTypes` VALUES (NULL, 'symlink');
INSERT INTO `DependenceTypes` VALUES (NULL, 'template');
INSERT INTO `DependenceTypes` VALUES (NULL, 'ximlet');
INSERT INTO `DependenceTypes` VALUES (NULL, 'ximlink');

UNLOCK TABLES;
--
-- Table structure for table `FastTraverse`
--

DROP TABLE IF EXISTS `FastTraverse`;
CREATE TABLE `FastTraverse` (
  `IdNode` int(12) unsigned NOT NULL default '0',
  `IdChild` int(12) unsigned NOT NULL default '0',
  `Depth` int(12) unsigned default '0',
  PRIMARY KEY  (`IdNode`,`IdChild`),
  UNIQUE KEY `IdNode` (`IdNode`,`IdChild`),
  KEY `IdN` (`IdNode`),
  KEY `IdC` (`IdChild`)
) ENGINE=MyISAM COMMENT='Fast scan of node hierarchies';

--
-- Dumping data for table `FastTraverse`
--

INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 2, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 2, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (3, 3, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 3, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 3, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (4, 4, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 4, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 4, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5, 5, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (7, 7, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 7, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 7, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (8, 8, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 8, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 8, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (9, 9, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 9, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 9, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (10, 10, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 10, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 10, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (11, 11, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 11, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 11, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (12, 12, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 12, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 12, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (301, 301, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (3, 301, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 301, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 301, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (101, 101, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (4, 101, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 101, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 101, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (201, 201, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5, 201, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 201, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 201, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (202, 202, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5, 202, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 202, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 202, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (203, 203, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5, 203, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 203, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 203, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (204, 204, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5, 204, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 204, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 204, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5001, 5001, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5001, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5001, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5001, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5002, 5002, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5002, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5002, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5002, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5003, 5003, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5003, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5003, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5003, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5004, 5004, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5004, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5004, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5004, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5005, 5005, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5005, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5005, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5005, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5006, 5006, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5006, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5006, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5006, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5007, 5007, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5007, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5007, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5007, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5008, 5008, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5008, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5008, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5008, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5009, 5009, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5009, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5009, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5009, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5010, 5010, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5010, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5010, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5010, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5011, 5011, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5011, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5011, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5011, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5012, 5012, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5012, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5012, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5012, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5013, 5013, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5013, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5013, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5013, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5014, 5014, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5014, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5014, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5014, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5015, 5015, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5015, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5015, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5015, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5016, 5016, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5016, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5016, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5016, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5017, 5017, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5017, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5017, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5017, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5018, 5018, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5018, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5018, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5018, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5020, 5020, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5020, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5020, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5020, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5021, 5021, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5021, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5021, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5021, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5022, 5022, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5022, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5022, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5022, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5023, 5023, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5023, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5023, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5023, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5024, 5024, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5024, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5024, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5024, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5025, 5025, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5025, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5025, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5025, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5026, 5026, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5026, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5026, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5026, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5028, 5028, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5028, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5028, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5028, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5029, 5029, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5029, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5029, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5029, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5030, 5030, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5030, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5030, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5030, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5031, 5031, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5031, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5031, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5031, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5032, 5032, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5032, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5032, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5032, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5033, 5033, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5033, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5033, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5033, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5034, 5034, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5034, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5034, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5034, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5035, 5035, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5035, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5035, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5035, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5036, 5036, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5036, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5036, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5036, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5037, 5037, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5037, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5037, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5037, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5038, 5038, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5038, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5038, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5038, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5039, 5039, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5039, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5039, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5039, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5040, 5040, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5040, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5040, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5040, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5041, 5041, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5041, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5041, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5041, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5042, 5042, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5042, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5042, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5042, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5043, 5043, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5043, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5043, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5043, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5047, 5047, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5047, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5047, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5047, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5048, 5048, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5048, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5048, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5048, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5049, 5049, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5049, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5049, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5049, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5050, 5050, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5050, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5050, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5050, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5053, 5053, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5053, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5053, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5053, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5054, 5054, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5054, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5054, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5054, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5055, 5055, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5055, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5055, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5055, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5056, 5056, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5056, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5056, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5056, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5057, 5057, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5057, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5057, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5057, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5058, 5058, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5058, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5058, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5058, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5059, 5059, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5059, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5059, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5059, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5060, 5060, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5060, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5060, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5060, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5063, 5063, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5063, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5063, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5063, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5064, 5064, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5064, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5064, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5064, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5065, 5065, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5065, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5065, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5065, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5066, 5066, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5066, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5066, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5066, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5068, 5068, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5068, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5068, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5068, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5076, 5076, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5076, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5076, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5076, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5300, 5300, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5300, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5300, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5300, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5301, 5301, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5301, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5301, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5301, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5302, 5302, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5302, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5302, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5302, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5303, 5303, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5303, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5303, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5303, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5304, 5304, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5304, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5304, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5304, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5305, 5305, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5305, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5305, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5305, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5306, 5306, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5306, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5306, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5306, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5307, 5307, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5307, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5307, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5307, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5308, 5308, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 5308, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 5308, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 5308, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (10002, 10002, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (7, 10002, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 10002, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 10002, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (10003, 10003, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (7, 10003, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 10003, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 10003, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (403, 403, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (8, 403, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 403, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 403, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (10001, 10001, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (9, 10001, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 10001, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 10001, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1001, 1001, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (10, 1001, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 1001, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 1001, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1002, 1002, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (10, 1002, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 1002, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 1002, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1003, 1003, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (10, 1003, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 1003, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 1003, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (60, 60, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (11, 60, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 60, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 60, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (14, 14, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (12, 14, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 14, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 14, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6001, 6001, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5003, 6001, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6001, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6001, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6001, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6006, 6006, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5004, 6006, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6006, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6006, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6006, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6010, 6010, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5005, 6010, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6010, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6010, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6010, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6009, 6009, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5006, 6009, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6009, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6009, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6009, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6008, 6008, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5007, 6008, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6008, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6008, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6008, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6067, 6067, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5008, 6067, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6067, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6067, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6067, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6002, 6002, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5009, 6002, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6002, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6002, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6002, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6003, 6003, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5009, 6003, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6003, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6003, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6003, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6004, 6004, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5009, 6004, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6004, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6004, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6004, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6005, 6005, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5010, 6005, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6005, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6005, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6005, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6081, 6081, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5010, 6081, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6081, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6081, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6081, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6109, 6109, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5010, 6109, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6109, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6109, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6109, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6096, 6096, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5011, 6096, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6096, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6096, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6096, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6101, 6101, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5011, 6101, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6101, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6101, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6101, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6108, 6108, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5011, 6108, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6108, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6108, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6108, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6011, 6011, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5012, 6011, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6011, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6011, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6011, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6500, 6500, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5012, 6500, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6500, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6500, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6500, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6550, 6550, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5012, 6550, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6550, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6550, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6550, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6012, 6012, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5013, 6012, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6012, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6012, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6012, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6026, 6026, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5013, 6026, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6026, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6026, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6026, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6034, 6034, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5013, 6034, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6034, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6034, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6034, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6122, 6122, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5013, 6122, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6122, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6122, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6122, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6501, 6501, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5013, 6501, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6501, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6501, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6501, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6551, 6551, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5013, 6551, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6551, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6551, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6551, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6600, 6600, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5013, 6600, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6600, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6600, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6600, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (7233, 7233, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5013, 7233, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 7233, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 7233, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 7233, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (7235, 7235, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5013, 7235, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 7235, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 7235, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 7235, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6013, 6013, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5014, 6013, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6013, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6013, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6013, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6027, 6027, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5014, 6027, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6027, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6027, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6027, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6035, 6035, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5014, 6035, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6035, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6035, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6035, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6123, 6123, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5014, 6123, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6123, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6123, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6123, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6125, 6125, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5014, 6125, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6125, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6125, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6125, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6170, 6170, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5014, 6170, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6170, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6170, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6170, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6202, 6202, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5014, 6202, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6202, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6202, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6202, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6517, 6517, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5014, 6517, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6517, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6517, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6517, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6567, 6567, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5014, 6567, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6567, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6567, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6567, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6619, 6619, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5014, 6619, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6619, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6619, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6619, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6007, 6007, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5015, 6007, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6007, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6007, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6007, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6014, 6014, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5015, 6014, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6014, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6014, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6014, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6025, 6025, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5015, 6025, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6025, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6025, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6025, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6036, 6036, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5015, 6036, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6036, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6036, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6036, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6074, 6074, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5015, 6074, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6074, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6074, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6074, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6171, 6171, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5015, 6171, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6171, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6171, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6171, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6184, 6184, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5015, 6184, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6184, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6184, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6184, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6204, 6204, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5015, 6204, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6204, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6204, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6204, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6205, 6205, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5015, 6205, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6205, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6205, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6205, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6518, 6518, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5015, 6518, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6518, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6518, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6518, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6568, 6568, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5015, 6568, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6568, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6568, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6568, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6620, 6620, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5015, 6620, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6620, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6620, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6620, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6015, 6015, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5016, 6015, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6015, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6015, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6015, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6343, 6343, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5016, 6343, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6343, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6343, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6343, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6508, 6508, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5016, 6508, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6508, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6508, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6508, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6558, 6558, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5016, 6558, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6558, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6558, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6558, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6607, 6607, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5016, 6607, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6607, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6607, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6607, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6016, 6016, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5017, 6016, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6016, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6016, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6016, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6028, 6028, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5017, 6028, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6028, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6028, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6028, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6037, 6037, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5017, 6037, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6037, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6037, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6037, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6172, 6172, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5017, 6172, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6172, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6172, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6172, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6344, 6344, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5017, 6344, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6344, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6344, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6344, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6509, 6509, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5017, 6509, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6509, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6509, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6509, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6559, 6559, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5017, 6559, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6559, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6559, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6559, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6608, 6608, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5017, 6608, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6608, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6608, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6608, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6043, 6043, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5018, 6043, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6043, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6043, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6043, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6044, 6044, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5018, 6044, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6044, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6044, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6044, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6124, 6124, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5018, 6124, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6124, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6124, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6124, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6512, 6512, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5018, 6512, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6512, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6512, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6512, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6562, 6562, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5018, 6562, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6562, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6562, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6562, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6611, 6611, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5018, 6611, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6611, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6611, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6611, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6018, 6018, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5020, 6018, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6018, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6018, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6018, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6236, 6236, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5020, 6236, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6236, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6236, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6236, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6510, 6510, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5020, 6510, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6510, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6510, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6510, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6560, 6560, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5020, 6560, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6560, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6560, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6560, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6609, 6609, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5020, 6609, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6609, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6609, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6609, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6019, 6019, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5021, 6019, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6019, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6019, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6019, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6030, 6030, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5021, 6030, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6030, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6030, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6030, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6039, 6039, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5021, 6039, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6039, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6039, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6039, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6173, 6173, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5021, 6173, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6173, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6173, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6173, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6511, 6511, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5021, 6511, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6511, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6511, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6511, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6561, 6561, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5021, 6561, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6561, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6561, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6561, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6610, 6610, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5021, 6610, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6610, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6610, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6610, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6020, 6020, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5022, 6020, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6020, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6020, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6020, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6346, 6346, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5022, 6346, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6346, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6346, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6346, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6504, 6504, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5022, 6504, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6504, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6504, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6504, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6554, 6554, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5022, 6554, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6554, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6554, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6554, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6603, 6603, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5022, 6603, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6603, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6603, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6603, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6021, 6021, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5023, 6021, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6021, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6021, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6021, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6031, 6031, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5023, 6031, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6031, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6031, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6031, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6040, 6040, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5023, 6040, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6040, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6040, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6040, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6174, 6174, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5023, 6174, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6174, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6174, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6174, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6347, 6347, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5023, 6347, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6347, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6347, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6347, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6505, 6505, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5023, 6505, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6505, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6505, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6505, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6555, 6555, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5023, 6555, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6555, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6555, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6555, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6604, 6604, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5023, 6604, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6604, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6604, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6604, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6022, 6022, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5024, 6022, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6022, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6022, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6022, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6348, 6348, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5024, 6348, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6348, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6348, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6348, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6506, 6506, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5024, 6506, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6506, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6506, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6506, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6556, 6556, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5024, 6556, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6556, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6556, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6556, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6605, 6605, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5024, 6605, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6605, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6605, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6605, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6023, 6023, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5025, 6023, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6023, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6023, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6023, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6032, 6032, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5025, 6032, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6032, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6032, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6032, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6041, 6041, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5025, 6041, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6041, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6041, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6041, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6349, 6349, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5025, 6349, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6349, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6349, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6349, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6507, 6507, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5025, 6507, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6507, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6507, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6507, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6557, 6557, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5025, 6557, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6557, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6557, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6557, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6606, 6606, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5025, 6606, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6606, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6606, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6606, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6345, 6345, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5026, 6345, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6345, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6345, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6345, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6520, 6520, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5026, 6520, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6520, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6520, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6520, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6570, 6570, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5026, 6570, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6570, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6570, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6570, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6622, 6622, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5026, 6622, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6622, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6622, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6622, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (8121, 8121, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5026, 8121, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 8121, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 8121, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 8121, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6103, 6103, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5028, 6103, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6103, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6103, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6103, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6104, 6104, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5028, 6104, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6104, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6104, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6104, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6105, 6105, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5028, 6105, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6105, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6105, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6105, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6106, 6106, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5028, 6106, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6106, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6106, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6106, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6132, 6132, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5028, 6132, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6132, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6132, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6132, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6133, 6133, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5028, 6133, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6133, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6133, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6133, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6146, 6146, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5028, 6146, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6146, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6146, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6146, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6175, 6175, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5028, 6175, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6175, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6175, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6175, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6629, 6629, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5028, 6629, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6629, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6629, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6629, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6048, 6048, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5029, 6048, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6048, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6048, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6048, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6047, 6047, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5030, 6047, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6047, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6047, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6047, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6045, 6045, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5031, 6045, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6045, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6045, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6045, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6063, 6063, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5031, 6063, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6063, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6063, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6063, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6064, 6064, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5031, 6064, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6064, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6064, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6064, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6176, 6176, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5031, 6176, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6176, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6176, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6176, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6513, 6513, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5031, 6513, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6513, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6513, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6513, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6563, 6563, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5031, 6563, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6563, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6563, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6563, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6612, 6612, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5031, 6612, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6612, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6612, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6612, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6065, 6065, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5032, 6065, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6065, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6065, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6065, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6098, 6098, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5032, 6098, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6098, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6098, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6098, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6099, 6099, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5032, 6099, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6099, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6099, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6099, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6100, 6100, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5032, 6100, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6100, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6100, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6100, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6117, 6117, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5032, 6117, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6117, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6117, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6117, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6134, 6134, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5032, 6134, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6134, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6134, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6134, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6135, 6135, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5032, 6135, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6135, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6135, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6135, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6136, 6136, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5032, 6136, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6136, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6136, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6136, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6143, 6143, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5032, 6143, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6143, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6143, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6143, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6385, 6385, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5032, 6385, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6385, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6385, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6385, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6095, 6095, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5033, 6095, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6095, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6095, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6095, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6107, 6107, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5033, 6107, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6107, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6107, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6107, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (7234, 7234, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5033, 7234, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 7234, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 7234, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 7234, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6097, 6097, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5034, 6097, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6097, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6097, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6097, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6102, 6102, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5034, 6102, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6102, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6102, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6102, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (7232, 7232, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5034, 7232, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 7232, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 7232, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 7232, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6046, 6046, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5035, 6046, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6046, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6046, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6046, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6082, 6082, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5036, 6082, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6082, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6082, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6082, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6121, 6121, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5036, 6121, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6121, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6121, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6121, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6342, 6342, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5037, 6342, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6342, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6342, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6342, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6051, 6051, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5039, 6051, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6051, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6051, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6051, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6087, 6087, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5039, 6087, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6087, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6087, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6087, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6088, 6088, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5039, 6088, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6088, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6088, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6088, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6094, 6094, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5039, 6094, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6094, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6094, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6094, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6126, 6126, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5039, 6126, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6126, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6126, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6126, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6127, 6127, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5039, 6127, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6127, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6127, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6127, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6148, 6148, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5039, 6148, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6148, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6148, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6148, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6177, 6177, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5039, 6177, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6177, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6177, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6177, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6626, 6626, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5039, 6626, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6626, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6626, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6626, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6050, 6050, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5040, 6050, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6050, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6050, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6050, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6052, 6052, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5040, 6052, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6052, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6052, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6052, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6085, 6085, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5040, 6085, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6085, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6085, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6085, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6086, 6086, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5040, 6086, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6086, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6086, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6086, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6130, 6130, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5040, 6130, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6130, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6130, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6130, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6131, 6131, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5040, 6131, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6131, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6131, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6131, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6144, 6144, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5040, 6144, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6144, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6144, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6144, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6168, 6168, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5040, 6168, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6168, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6168, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6168, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6178, 6178, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5040, 6178, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6178, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6178, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6178, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6627, 6627, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5040, 6627, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6627, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6627, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6627, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6049, 6049, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5041, 6049, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6049, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6049, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6049, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6083, 6083, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5041, 6083, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6083, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6083, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6083, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6084, 6084, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5041, 6084, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6084, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6084, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6084, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6128, 6128, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5041, 6128, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6128, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6128, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6128, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6129, 6129, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5041, 6129, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6129, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6129, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6129, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6145, 6145, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5041, 6145, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6145, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6145, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6145, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6179, 6179, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5041, 6179, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6179, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6179, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6179, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6628, 6628, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5041, 6628, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6628, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6628, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6628, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6070, 6070, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5048, 6070, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6070, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6070, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6070, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6072, 6072, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5048, 6072, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6072, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6072, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6072, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6118, 6118, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5048, 6118, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6118, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6118, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6118, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6119, 6119, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5048, 6119, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6119, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6119, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6119, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6181, 6181, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5048, 6181, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6181, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6181, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6181, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6503, 6503, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5048, 6503, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6503, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6503, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6503, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6553, 6553, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5048, 6553, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6553, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6553, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6553, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6602, 6602, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5048, 6602, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6602, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6602, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6602, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (7229, 7229, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5048, 7229, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 7229, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 7229, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 7229, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6073, 6073, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5049, 6073, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6073, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6073, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6073, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6120, 6120, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5049, 6120, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6120, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6120, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6120, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6182, 6182, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5049, 6182, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6182, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6182, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6182, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6616, 6616, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5049, 6616, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6616, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6616, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6616, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6069, 6069, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5050, 6069, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6069, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6069, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6069, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6071, 6071, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5050, 6071, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6071, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6071, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6071, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6502, 6502, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5050, 6502, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6502, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6502, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6502, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6552, 6552, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5050, 6552, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6552, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6552, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6552, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6601, 6601, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5050, 6601, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6601, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6601, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6601, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (7231, 7231, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5050, 7231, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 7231, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 7231, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 7231, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6519, 6519, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5053, 6519, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6519, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6519, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6519, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6569, 6569, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5053, 6569, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6569, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6569, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6569, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6621, 6621, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5053, 6621, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6621, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6621, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6621, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6137, 6137, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5054, 6137, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6137, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6137, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6137, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6138, 6138, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5054, 6138, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6138, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6138, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6138, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6514, 6514, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5054, 6514, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6514, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6514, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6514, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6564, 6564, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5054, 6564, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6564, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6564, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6564, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6613, 6613, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5054, 6613, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6613, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6613, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6613, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6139, 6139, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5055, 6139, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6139, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6139, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6139, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6140, 6140, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5055, 6140, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6140, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6140, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6140, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6141, 6141, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5055, 6141, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6141, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6141, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6141, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6142, 6142, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5055, 6142, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6142, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6142, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6142, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6515, 6515, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5055, 6515, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6515, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6515, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6515, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6565, 6565, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5055, 6565, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6565, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6565, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6565, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6614, 6614, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5055, 6614, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6614, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6614, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6614, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6151, 6151, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5056, 6151, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6151, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6151, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6151, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6152, 6152, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5056, 6152, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6152, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6152, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6152, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6153, 6153, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5056, 6153, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6153, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6153, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6153, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6183, 6183, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5056, 6183, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6183, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6183, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6183, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6516, 6516, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5056, 6516, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6516, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6516, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6516, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6566, 6566, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5056, 6566, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6566, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6566, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6566, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6615, 6615, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5056, 6615, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6615, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6615, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6615, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6150, 6150, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5057, 6150, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6150, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6150, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6150, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6154, 6154, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5057, 6154, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6154, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6154, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6154, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6155, 6155, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5057, 6155, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6155, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6155, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6155, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6156, 6156, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5057, 6156, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6156, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6156, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6156, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6157, 6157, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5057, 6157, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6157, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6157, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6157, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6617, 6617, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5057, 6617, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6617, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6617, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6617, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (7236, 7236, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5057, 7236, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 7236, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 7236, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 7236, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6708, 6708, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5063, 6708, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6708, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6708, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6708, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6227, 6227, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5076, 6227, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6227, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6227, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6227, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6228, 6228, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5076, 6228, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6228, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6228, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6228, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6229, 6229, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5076, 6229, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6229, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6229, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6229, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6230, 6230, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5076, 6230, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6230, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6230, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6230, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6231, 6231, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5076, 6231, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6231, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6231, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6231, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6232, 6232, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5076, 6232, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6232, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6232, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6232, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6233, 6233, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5076, 6233, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6233, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6233, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6233, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6234, 6234, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5076, 6234, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6234, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6234, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6234, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6700, 6700, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5300, 6700, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6700, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6700, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6700, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6701, 6701, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5300, 6701, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6701, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6701, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6701, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (7017, 7017, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5300, 7017, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 7017, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 7017, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 7017, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6304, 6304, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5301, 6304, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6304, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6304, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6304, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6702, 6702, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5301, 6702, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6702, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6702, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6702, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6703, 6703, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5302, 6703, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6703, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6703, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6703, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6704, 6704, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5302, 6704, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6704, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6704, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6704, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6706, 6706, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5303, 6706, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6706, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6706, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6706, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6300, 6300, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5304, 6300, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6300, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6300, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6300, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6710, 6710, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5304, 6710, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6710, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6710, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6710, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6709, 6709, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5305, 6709, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6709, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6709, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6709, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6715, 6715, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5305, 6715, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6715, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6715, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6715, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6717, 6717, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5305, 6717, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6717, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6717, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6717, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (7018, 7018, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5305, 7018, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 7018, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 7018, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 7018, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (7019, 7019, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5305, 7019, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 7019, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 7019, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 7019, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6713, 6713, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5306, 6713, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6713, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6713, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6713, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6714, 6714, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5306, 6714, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6714, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6714, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6714, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6720, 6720, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5306, 6720, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6720, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6720, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6720, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6723, 6723, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5306, 6723, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6723, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6723, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6723, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6314, 6314, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5307, 6314, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6314, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6314, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6314, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6317, 6317, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5307, 6317, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6317, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6317, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6317, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6318, 6318, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5307, 6318, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6318, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6318, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6318, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6319, 6319, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5307, 6319, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6319, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6319, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6319, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6320, 6320, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5307, 6320, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6320, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6320, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6320, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6321, 6321, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5307, 6321, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6321, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6321, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6321, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6322, 6322, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5307, 6322, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6322, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6322, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6322, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6716, 6716, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5307, 6716, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6716, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6716, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6716, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6728, 6728, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5307, 6728, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6728, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6728, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6728, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6729, 6729, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5307, 6729, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6729, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6729, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6729, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6730, 6730, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5307, 6730, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6730, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6730, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6730, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6731, 6731, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5307, 6731, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6731, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6731, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6731, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6323, 6323, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5308, 6323, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6323, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6323, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6323, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6324, 6324, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5308, 6324, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6324, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6324, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6324, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6325, 6325, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5308, 6325, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6325, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6325, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6325, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6326, 6326, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5308, 6326, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6326, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6326, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6326, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6327, 6327, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5308, 6327, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6327, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6327, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6327, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6328, 6328, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5308, 6328, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6328, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6328, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6328, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6329, 6329, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5308, 6329, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6329, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6329, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6329, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6330, 6330, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5308, 6330, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6330, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6330, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6330, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6331, 6331, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5308, 6331, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6331, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6331, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6331, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6740, 6740, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (5308, 6740, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (6, 6740, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 6740, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 6740, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (50, 50, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (60, 50, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (11, 50, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 50, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 50, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (51, 51, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (60, 51, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (11, 51, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 51, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 51, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (52, 52, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (60, 52, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (11, 52, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 52, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 52, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (53, 53, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (60, 53, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (11, 53, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 53, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 53, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (54, 54, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (60, 54, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (11, 54, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 54, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 54, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (55, 55, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (60, 55, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (11, 55, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 55, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 55, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (56, 56, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (60, 56, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (11, 56, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 56, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 56, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (57, 57, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (60, 57, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (11, 57, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 57, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 57, 4);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (58, 58, 0);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (60, 58, 1);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (11, 58, 2);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (2, 58, 3);
INSERT INTO FastTraverse (IdNode, IdChild, Depth) VALUES (1, 58, 4);


/*!40000 ALTER TABLE `FastTraverse` DISABLE KEYS */;
LOCK TABLES `FastTraverse` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `FastTraverse` ENABLE KEYS */;

--
-- Table structure for table `Groups`
--

DROP TABLE IF EXISTS `Groups`;
CREATE TABLE `Groups` (
  `IdGroup` int(12) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`IdGroup`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM COMMENT='Groups defined on the system';

--
-- Dumping data for table `Groups`
--


/*!40000 ALTER TABLE `Groups` DISABLE KEYS */;
LOCK TABLES `Groups` WRITE;
INSERT INTO `Groups` VALUES (101,'General');
UNLOCK TABLES;
/*!40000 ALTER TABLE `Groups` ENABLE KEYS */;

--
-- Table structure for table `IsoCodes`
--

DROP TABLE IF EXISTS `IsoCodes`;
CREATE TABLE `IsoCodes` (
  `IdIsoCode` int(12) unsigned NOT NULL auto_increment,
  `Iso2` char(2) default NULL,
  `Iso3` char(3) default NULL,
  `Name` varchar(255) default NULL,
  PRIMARY KEY  (`IdIsoCode`),
  UNIQUE KEY `name` (`Name`),
  UNIQUE KEY `iso2` (`Iso2`),
  UNIQUE KEY `iso3` (`Iso3`)
) ENGINE=MyISAM COMMENT='ISO codes supported for languages';

--
-- Dumping data for table `IsoCodes`
--



/*!40000 ALTER TABLE `IsoCodes` DISABLE KEYS */;
LOCK TABLES `IsoCodes` WRITE;
INSERT INTO `IsoCodes` VALUES (1,'fr','fra','French');
INSERT INTO `IsoCodes` VALUES (2,'it','ita','Italian');
INSERT INTO `IsoCodes` VALUES (3,'es','esp','Spanish');
INSERT INTO `IsoCodes` VALUES (4,'de','ger','German');
INSERT INTO `IsoCodes` VALUES (5,'en','eng','English');
INSERT INTO `IsoCodes` VALUES (6,'pt','pot','Portuguese');
INSERT INTO `IsoCodes` VALUES (7,'gl','glg','Galician');
INSERT INTO `IsoCodes` VALUES (8,'ca','cat','Catalan');
INSERT INTO `IsoCodes` VALUES (9,'eu','eus','Euskera');
INSERT INTO `IsoCodes` VALUES (10,'va','val','Valencian');
UNLOCK TABLES;
/*!40000 ALTER TABLE `IsoCodes` ENABLE KEYS */;





DROP TABLE IF EXISTS  `Locales`;
CREATE TABLE `Locales` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Code` varchar(5) NOT NULL COMMENT 'Locale in ISO 639 ',
  `Name` varchar(20) NOT NULL COMMENT 'Lang name',
  `Enabled` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Enabled(1)|Not Enabled(0)',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  COMMENT='Ximdex CMS default languages';

LOCK TABLES `Locales` WRITE;
INSERT INTO `Locales` (`ID`, `Code`, `Name`, `Enabled`) VALUES(1, 'es_ES', 'Spanish', 1);
INSERT INTO `Locales` (`ID`, `Code`, `Name`, `Enabled`) VALUES(2, 'en_US', 'English', 1);
INSERT INTO `Locales` (`ID`, `Code`, `Name`, `Enabled`) VALUES(3, 'de_DE', 'German', 0);
INSERT INTO `Locales` (`ID`, `Code`, `Name`, `Enabled`) VALUES(4, 'pt_BR', 'Portuguese', 0);
UNLOCK TABLES;

--
-- Table structure for table `Languages`
--

DROP TABLE IF EXISTS `Languages`;
CREATE TABLE `Languages` (
  `IdLanguage` int(12) unsigned NOT NULL,
  `Name` varchar(255) NOT NULL default '',
  `IsoName` varchar(255) default NULL,
  `Enabled` tinyint(1) unsigned NULL default '1',
  PRIMARY KEY  (`IdLanguage`),
  UNIQUE KEY `Name` (`Name`),
  UNIQUE KEY `IdLanguage` (`IdLanguage`),
  KEY `IdLanguage_2` (`IdLanguage`)
) ENGINE=MyISAM COMMENT='Ximdex CMS defined languages';

--
-- Dumping data for table `Languages`
--


/*!40000 ALTER TABLE `Languages` DISABLE KEYS */;
LOCK TABLES `Languages` WRITE;
INSERT INTO `Languages` (`IdLanguage`, `Name`, `IsoName`, `Enabled`) VALUES(10002, 'Spanish', 'es', 1);
INSERT INTO `Languages` (`IdLanguage`, `Name`, `IsoName`, `Enabled`) VALUES(10003, 'English', 'en', 1);
UNLOCK TABLES;
/*!40000 ALTER TABLE `Languages` ENABLE KEYS */;

--
-- Table structure for table `Links`
--

DROP TABLE IF EXISTS `Links`;
CREATE TABLE `Links` (
  `IdLink` int(12) unsigned NOT NULL auto_increment,
  `Url` blob NOT NULL,
  `Error` int(12) unsigned default NULL,
  `ErrorString` varchar(255) default NULL,
  `CheckTime` int(12) unsigned default NULL,
  PRIMARY KEY  (`IdLink`),
  UNIQUE KEY `IdLink` (`IdLink`),
  KEY `IdLink_2` (`IdLink`)
) ENGINE=MyISAM COMMENT='Table of link manager of Ximdex';

--
-- Dumping data for table `Links`
--

--
-- Table structure for table `RelLinkDescriptions`
--

DROP TABLE IF EXISTS `RelLinkDescriptions`;
CREATE TABLE `RelLinkDescriptions` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdLink` int(12) unsigned NOT NULL,
  `Description` varchar(255),
  PRIMARY KEY  (`IdRel`),
  UNIQUE KEY `Description` (`IdLink`, `Description`)
) ENGINE=MyISAM COMMENT='Table of descriptions of Ximdex links';

--
-- Dumping data for table `RelLinkDescriptions`
--


/*!40000 ALTER TABLE `RelLinkDescriptions` DISABLE KEYS */;
LOCK TABLES `RelLinkDescriptions` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `RelLinkDescriptions` ENABLE KEYS */;

--
-- Table structure for table `Messages`
--

DROP TABLE IF EXISTS `Messages`;
CREATE TABLE `Messages` (
  `IdMessage` int(12) unsigned NOT NULL auto_increment,
  `IdFrom` int(12) unsigned NOT NULL default '0',
  `IdOwner` int(12) unsigned NOT NULL default '0',
  `ToString` varchar(255) default NULL,
  `Folder` int(12) unsigned NOT NULL default '1',
  `Subject` varchar(255) default NULL,
  `Content` blob,
  `IsRead` int(1) unsigned NOT NULL default '0',
  `FechaHora` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`IdMessage`)
) ENGINE=MyISAM COMMENT='Messages sent by Ximdex CMS. Deprecated?';

--
-- Table structure for table `NodeAllowedContents`
--

DROP TABLE IF EXISTS `Namespaces`;
CREATE TABLE `Namespaces` (
  `idNamespace` int(12) unsigned NOT NULL auto_increment,
  `service` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `nemo` varchar(255) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `recursive` int(8) NOT NULL default '0',
  `category` varchar(255) NOT NULL,
  `isSemantic` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`idNamespace`)
) ENGINE=MyISAM COMMENT='Namespaces for semantic tagging.';

INSERT INTO Namespaces VALUES (1,'Ximdex','Custom','custom','http://<ximdex_local_url>/',0,'generic',0);


DROP TABLE IF EXISTS `NodeAllowedContents`;
CREATE TABLE `NodeAllowedContents` (
  `IdNodeAllowedContent` int(12) unsigned NOT NULL auto_increment,
  `IdNodeType` int(12) unsigned NOT NULL default '0',
  `NodeType` int(12) unsigned NOT NULL default '0',
  `Amount` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdNodeAllowedContent`),
  UNIQUE KEY `UniqeAmmount` (`IdNodeType`,`NodeType`)
) ENGINE=MyISAM COMMENT='Allowed node types into each type of node';

--
-- Dumping data for table `NodeAllowedContents`
--


/*!40000 ALTER TABLE `NodeAllowedContents` DISABLE KEYS */;
LOCK TABLES `NodeAllowedContents` WRITE;
INSERT INTO `NodeAllowedContents` VALUES (1,5001,5002,1);
INSERT INTO `NodeAllowedContents` VALUES (2,5001,5012,1);
INSERT INTO `NodeAllowedContents` VALUES (3,5002,5003,1);
INSERT INTO `NodeAllowedContents` VALUES (4,5002,5004,1);
INSERT INTO `NodeAllowedContents` VALUES (5,5002,5005,1);
INSERT INTO `NodeAllowedContents` VALUES (6,5002,5006,1);
INSERT INTO `NodeAllowedContents` VALUES (7,5002,5029,1);
INSERT INTO `NodeAllowedContents` VALUES (8,5002,5030,1);
INSERT INTO `NodeAllowedContents` VALUES (9,5002,5035,1);
INSERT INTO `NodeAllowedContents` VALUES (10,5002,5037,1);
INSERT INTO `NodeAllowedContents` VALUES (11,5029,5033,0);
INSERT INTO `NodeAllowedContents` VALUES (12,5004,5011,0);
INSERT INTO `NodeAllowedContents` VALUES (13,5030,5034,0);
INSERT INTO `NodeAllowedContents` VALUES (14,5037,5038,0);
INSERT INTO `NodeAllowedContents` VALUES (15,5005,5010,0);
INSERT INTO `NodeAllowedContents` VALUES (16,5006,5007,0);
INSERT INTO `NodeAllowedContents` VALUES (17,5007,5008,0);
INSERT INTO `NodeAllowedContents` VALUES (18,5003,5009,0);
INSERT INTO `NodeAllowedContents` VALUES (19,5035,5036,0);
INSERT INTO `NodeAllowedContents` VALUES (20,5012,5013,0);
INSERT INTO `NodeAllowedContents` VALUES (21,5013,5014,0);
INSERT INTO `NodeAllowedContents` VALUES (22,5013,5026,1);
INSERT INTO `NodeAllowedContents` VALUES (23,5013,5047,1);
INSERT INTO `NodeAllowedContents` VALUES (24,5013,5050,1);
INSERT INTO `NodeAllowedContents` VALUES (25,5014,5014,0);
INSERT INTO `NodeAllowedContents` VALUES (26,5014,5024,1);
INSERT INTO `NodeAllowedContents` VALUES (27,5014,5016,1);
INSERT INTO `NodeAllowedContents` VALUES (28,5014,5018,1);
INSERT INTO `NodeAllowedContents` VALUES (29,5014,5026,1);
INSERT INTO `NodeAllowedContents` VALUES (30,5014,5022,1);
INSERT INTO `NodeAllowedContents` VALUES (31,5014,5020,1);
INSERT INTO `NodeAllowedContents` VALUES (32,5015,5016,1);
INSERT INTO `NodeAllowedContents` VALUES (33,5015,5018,1);
INSERT INTO `NodeAllowedContents` VALUES (34,5015,5026,1);
INSERT INTO `NodeAllowedContents` VALUES (35,5015,5022,1);
INSERT INTO `NodeAllowedContents` VALUES (36,5015,5020,1);
INSERT INTO `NodeAllowedContents` VALUES (37,5015,5015,0);
INSERT INTO `NodeAllowedContents` VALUES (38,5016,5017,0);
INSERT INTO `NodeAllowedContents` VALUES (39,5016,5040,0);
INSERT INTO `NodeAllowedContents` VALUES (40,5017,5040,0);
INSERT INTO `NodeAllowedContents` VALUES (41,5017,5017,0);
INSERT INTO `NodeAllowedContents` VALUES (42,5017,5039,0);
INSERT INTO `NodeAllowedContents` VALUES (43,5018,5031,0);
INSERT INTO `NodeAllowedContents` VALUES (44,5021,5076,0);
INSERT INTO `NodeAllowedContents` VALUES (45,5055,5056,0);
INSERT INTO `NodeAllowedContents` VALUES (46,5020,5021,0);
-- INSERT INTO `NodeAllowedContents` VALUES (47,5020,5039,0);
INSERT INTO `NodeAllowedContents` VALUES (48,5021,5039,0);
INSERT INTO `NodeAllowedContents` VALUES (49,5021,5021,0);
INSERT INTO `NodeAllowedContents` VALUES (50,5022,5023,0);
INSERT INTO `NodeAllowedContents` VALUES (51,5022,5039,0);
INSERT INTO `NodeAllowedContents` VALUES (52,5022,5040,0);
INSERT INTO `NodeAllowedContents` VALUES (53,5022,5041,0);
INSERT INTO `NodeAllowedContents` VALUES (54,5023,5023,0);
INSERT INTO `NodeAllowedContents` VALUES (55,5023,5039,0);
INSERT INTO `NodeAllowedContents` VALUES (56,5023,5040,0);
INSERT INTO `NodeAllowedContents` VALUES (57,5023,5041,0);
INSERT INTO `NodeAllowedContents` VALUES (58,5026,5077,0);
INSERT INTO `NodeAllowedContents` VALUES (59,5053,5078,0);
INSERT INTO `NodeAllowedContents` VALUES (60,5035,5079,0);
INSERT INTO `NodeAllowedContents` VALUES (61,5014,5083,1);
INSERT INTO `NodeAllowedContents` VALUES (62,5083,5084,0);
INSERT INTO `NodeAllowedContents` VALUES (63,5084,5085,0);
INSERT INTO `NodeAllowedContents` VALUES (64,5016,5039,0);
INSERT INTO `NodeAllowedContents` VALUES (66,5024,5025,0);
INSERT INTO `NodeAllowedContents` VALUES (67,5024,5028,0);
INSERT INTO `NodeAllowedContents` VALUES (68,5025,5025,0);
INSERT INTO `NodeAllowedContents` VALUES (69,5025,5028,0);
INSERT INTO `NodeAllowedContents` VALUES (70,5050,5048,0);
INSERT INTO `NodeAllowedContents` VALUES (71,5050,5049,0);
INSERT INTO `NodeAllowedContents` VALUES (72,5048,5048,0);
INSERT INTO `NodeAllowedContents` VALUES (73,5048,5049,0);
INSERT INTO `NodeAllowedContents` VALUES (74,5013,5062,2);
INSERT INTO `NodeAllowedContents` VALUES (75,5014,5015,0);
INSERT INTO `NodeAllowedContents` VALUES (76,5013,5053,1);
INSERT INTO `NodeAllowedContents` VALUES (77,5031,5032,0);
INSERT INTO `NodeAllowedContents` VALUES (78,5056,5057,0);
INSERT INTO `NodeAllowedContents` VALUES (85,5020,5076,0);
INSERT INTO `NodeAllowedContents` VALUES (86,5014,5054,1);
INSERT INTO `NodeAllowedContents` VALUES (87,5015,5054,1);
INSERT INTO `NodeAllowedContents` VALUES (88,5054,5056,0);
INSERT INTO `NodeAllowedContents` VALUES (90,5018,5032,0);
INSERT INTO `NodeAllowedContents` VALUES (91,5054,5055,0);
INSERT INTO `NodeAllowedContents` VALUES (92,5055,5055,0);

UNLOCK TABLES;
/*!40000 ALTER TABLE `NodeAllowedContents` ENABLE KEYS */;

--
-- Table structure for table `NodeDefaultContents`
--

DROP TABLE IF EXISTS `NodeDefaultContents`;
CREATE TABLE `NodeDefaultContents` (
  `IdNodeDefaultContent` int(12) unsigned NOT NULL auto_increment,
  `IdNodeType` int(12) unsigned NOT NULL default '0',
  `NodeType` int(12) unsigned NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '0',
  `State` int(12) unsigned default NULL,
  `Params` varchar(255) default NULL,
  PRIMARY KEY  (`IdNodeDefaultContent`),
  UNIQUE KEY `UniqueName` (`Name`,`IdNodeType`)
) ENGINE=MyISAM COMMENT='Default content of each node';

--
-- Dumping data for table `NodeDefaultContents`
--


/*!40000 ALTER TABLE `NodeDefaultContents` DISABLE KEYS */;
LOCK TABLES `NodeDefaultContents` WRITE;
INSERT INTO `NodeDefaultContents` VALUES (1,5015,5016,'images',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (3,5015,5018,'documents',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (4,5015,5026,'templates',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (5,5015,5022,'common',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (6,5015,5020,'ximclude',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (7,5014,5024,'css',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (8,5014,5016,'images',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (10,5014,5018,'documents',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (11,5014,5026,'templates',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (12,5014,5022,'common',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (13,5014,5020,'ximclude',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (14,5013,5026,'templates',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (16,5013,5050,'links',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (17,5013,5053,'schemes',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (18,5014,5054,'ximlet',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (19,5015,5054,'ximlet',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (20,5014,5083,'metadata',NULL,NULL);

UNLOCK TABLES;
/*!40000 ALTER TABLE `NodeDefaultContents` ENABLE KEYS */;

--
-- Table structure for table `NodeDependencies`
--

DROP TABLE IF EXISTS `NodeDependencies`;
CREATE TABLE `NodeDependencies` (
  `IdNode` int(12) unsigned NOT NULL default '0',
  `IdResource` int(12) unsigned NOT NULL default '0',
  `IdChannel` int(12) unsigned default NULL,
  PRIMARY KEY  (`IdNode`,`IdResource`)
) ENGINE=MyISAM COMMENT='Dependencies between nodes in Ximdex CMS';

--
-- Dumping data for table `NodeDependencies`
--


/*!40000 ALTER TABLE `NodeDependencies` DISABLE KEYS */;
LOCK TABLES `NodeDependencies` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `NodeDependencies` ENABLE KEYS */;

--
-- Table structure for table `NodeEdition`
--

DROP TABLE IF EXISTS `NodeEdition`;
CREATE TABLE `NodeEdition` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `IdNode` int(11) unsigned NOT NULL,
  `IdUser` int(11) unsigned NOT NULL,
  `StartTime` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='XML edition information. For concurrency issues';

--
-- Table structure for table `NodeNameTranslations`
--

DROP TABLE IF EXISTS `NodeNameTranslations`;
CREATE TABLE `NodeNameTranslations` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdNode` int(12) unsigned NOT NULL default '0',
  `IdLanguage` int(12) unsigned NOT NULL default '0',
  `Name` varchar(255) default '0',
  PRIMARY KEY  (`IdRel`),
  UNIQUE KEY `NodeLanguage` (`IdNode`,`IdLanguage`)
) ENGINE=MyISAM COMMENT='Alias for nodes in other languages';

--
-- Dumping data for table `NodeNameTranslations`
--


/*!40000 ALTER TABLE `NodeNameTranslations` DISABLE KEYS */;
LOCK TABLES `NodeNameTranslations` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `NodeNameTranslations` ENABLE KEYS */;

--
-- Table structure for table `NodeTypes`
--

DROP TABLE IF EXISTS `NodeTypes`;
CREATE TABLE `NodeTypes` (
  `IdNodeType` int(12) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '0',
  `Class` varchar(255) NOT NULL default '',
  `Icon` varchar(255) NOT NULL default '',
  `Description` varchar(255) default NULL,
  `IsRenderizable` int(1) unsigned default NULL,
  `HasFSEntity` int(1) unsigned default NULL,
  `CanAttachGroups` int(1) unsigned default NULL,
  `IsSection` int(1) unsigned default NULL,
  `IsFolder` int(1) unsigned default NULL,
  `IsVirtualFolder` int(1) unsigned default NULL,
  `IsPlainFile` int(1) unsigned default NULL,
  `IsStructuredDocument` int(1) unsigned default NULL,
  `IsPublishable` int(1) unsigned default NULL,
  `IsHidden` int(1) unsigned default 0,
  `CanDenyDeletion` int(1) unsigned default NULL,
  `isGenerator` TINYINT(1) default 0,
  `IsEnriching` TINYINT(1) default 0,
  `System` int(1) unsigned default NULL,
  `Module` varchar(255) default NULL,
  PRIMARY KEY  (`IdNodeType`),
  UNIQUE KEY `IdType` (`Name`),
  KEY `IdType_2` (`IdNodeType`)
) ENGINE=MyISAM COMMENT='Nodetypes used on Ximdex CMS';
 
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5001,'Root','root','root','Root node of Ximdex',1,0,0,0,0,1,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5002,'ControlCenter','root','controlcenter','Ximdex control center',0,0,1,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5003,'UserManager','root','user','User manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5004,'GroupManager','root','group','Group manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5005,'RoleManager','root','rol','Roles manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5006,'NodeTypeManager','root','nodetype','Type of node manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5007,'NodeType','nodetypenode','nodetype','Definition of node type of Ximdex',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5008,'Action','actionnode','action','Action run on node type of Ximdex',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5009,'User','usernode','user','Ximdex user',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5010,'Role','rolenode','rol','Role on user group of Ximdex',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5011,'Group','groupnode','group','User group of Ximdex',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5013,'Project','foldernode','nodetype','Ximdex project',1,1,1,0,0,1,0,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5012,'Projects','projects','projects','Root of Ximdex projects',1,0,1,0,1,1,0,0,0,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5032,'XmlDocument','xmldocumentnode','doc','XML document',1,1,0,0,0,0,0,1,1,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5014,'Server','servernode','server','Content server of Ximdex',1,1,1,1,1,1,0,0,1,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5015,'Section','sectionnode','folder','Ximdex section',1,1,1,1,1,0,0,0,1,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5016,'ImagesRootFolder','foldernode','folder_images','Root of image folder',1,1,0,0,1,0,0,0,1,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5017,'ImagesFolder','foldernode','folder_images','Image folder',1,1,0,0,1,0,0,0,1,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5018,'XmlRootFolder','foldernode','folder_xml','Root of XML folder',1,1,0,0,1,1,0,0,0,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5020,'ImportRootFolder','foldernode','folder_import','Root of import folder',1,1,0,0,1,0,0,0,0,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5021,'ImportFolder','foldernode','folder_import','Import folder',1,1,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5022,'CommonRootFolder','foldernode','folder_common','Root of common folder',1,1,0,0,1,0,0,0,1,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5023,'CommonFolder','foldernode','folder_common','Common folder',1,1,0,0,1,0,0,0,1,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5024,'CssRootFolder','foldernode','folder_css','Root of CSS folder',1,1,0,0,1,0,0,0,1,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5025,'CssFolder','foldernode','folder_css','CSS folder',1,1,0,0,1,0,0,0,1,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5026,'TemplatesRootFolder','foldernode','folder_templates','Root of template folder',1,1,0,0,1,0,0,0,0,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5031,'XmlContainer','Xmlcontainernode','contenedordoc','Container of XML docs',1,0,0,0,1,1,0,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5029,'ChannelManager','root','channel','Channel manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5030,'LanguageManager','root','language','Language manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5033,'Channel','channelnode','channel','Channel',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5034,'Language','languagenode','language','Language',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5035,'WorkflowManager','root','workflow','Workflow manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5036,'WorkflowState','statenode','workflow','Workflow status',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5037,'PermissionManager','root','permissions','Permits manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5038,'Permission','root','permission','Permit',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5039,'TextFile','commonnode','text_file','Text file',1,1,0,0,0,0,1,0,1,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5040,'ImageFile','imagenode','image','Image file',1,1,0,0,0,0,1,0,1,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5041,'BinaryFile','commonnode','binary_file','Binary file',1,1,0,0,0,0,1,0,1,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5043,'ErrorFolder','foldernode','foldergray','Output error folder',1,1,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5044,'ConfigFolder','foldernode','modulesconfig','Container of settings',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5050,'LinkManager','foldernode','folder_links','Root of link manager',0,0,0,0,1,0,0,0,0,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5048,'LinkFolder','foldernode','folder_links','Category of link manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5028,'CssFile','filenode','css_document','Style sheet',1,1,0,0,0,0,1,0,1,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5049,'Link','linknode','link','Ximdex link',0,0,0,0,0,0,0,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5053,'TemplateViewFolder','foldernode','folder_template_view','Folder of view template',1,1,0,0,1,0,0,0,0,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5054,'XimletRootFolder','foldernode','folder_ximlet','Root folder of ximlets in sections',0,0,0,0,1,1,0,0,0,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5055,'XimletFolder','foldernode','folder_ximlet','Ximlets folder',0,0,0,0,1,1,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5056,'XimletContainer','Xmlcontainernode','contenedordoc','Ximlet container',0,0,0,0,1,1,0,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5057,'Ximlet','XimletNode','doc','ximlet',0,0,0,0,0,0,0,1,1,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5058,'PropertiesManager','root','folder_system_properties','Property manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5059,'Property','propertynode','property','Property',0,0,0,0,0,0,0,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5060,'ProjectPropFolder','foldernode','foldergray','Folder of project properties',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5061,'SystemProperty','propertynode','property','System properties',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5063,'ProjectDicFolder','foldernode','folder','Folder of project dictionary',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5066,'DicFolder','foldernode','foldergray','Folder of dictionary values',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5068,'PropSet','foldernode','foldergray','Folder of property set',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5076,'NodeHt','filenode','nodeht_file','Html node',1,1,0,0,0,0,1,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5077,'XslTemplate','xsltnode','xml_document','Template of XSLT transformation',1,1,0,0,0,0,1,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5078,'RngVisualTemplate','rngvisualtemplatenode','xml_document','RNG schema for XML documents',1,1,0,0,0,0,1,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5079,'Workflow','workflow_process','workflow','Workflow for documents',0,0,0,0,0,0,0,0,0,0,1,'');
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`, `IsHidden`) VALUES (5080,'ModulesFolder','foldernode','modulesconfig','Container of module settings',0,0,0,0,1,0,0,0,0,0,1,NULL,1);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5081,'ModuleInfoContainer','foldernode','modulesconfig','Container of a module settings',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5082, 'InheritableProperties', 'foldernode', 'modulesconfig', 'Heritable properties', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`, `IsHidden`) VALUES (5083,'MetaDataSection','foldernode','folder_xml_meta','Metadata Section',1,1,0,0,1,0,0,0,1,NULL,1,NULL,0);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5084,'MetaDataContainer','Xmlcontainernode','metacontainer','Metadata Document',1,0,0,0,1,1,0,0,0,NULL,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5085,'MetaDataDoc','xmldocumentnode','doc','Metadata Language Document',1,0,0,0,0,0,0,1,1,NULL,0,NULL);
 
CREATE TABLE `Nodes` (
  `IdNode` int(12) unsigned NOT NULL auto_increment,
  `IdParent` int(12) unsigned default '0',
  `IdNodeType` int(12) unsigned NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '0',
  `IdState` int(12) unsigned default '0',
  `BlockTime` int(12) default '0',
  `BlockUser` int(12) unsigned default NULL,
  `CreationDate` int(12) unsigned default '0',
  `ModificationDate` int(12) unsigned default '0',
  `Description` varchar(255) default NULL,
  `SharedWorkflow` int(12) unsigned default NULL,
  `Path` text   ,
  PRIMARY KEY  (`IdNode`),
  UNIQUE KEY `UniqueName` (`Name`,`IdParent`),
  KEY `IdNode_2` (`IdNode`,`IdParent`)
) ENGINE=MyISAM COMMENT='Table of system nodes';

--
-- Dumping data for table `Nodes`
--


/*!40000 ALTER TABLE `Nodes` DISABLE KEYS */;
LOCK TABLES `Nodes` WRITE;
INSERT INTO `Nodes` VALUES (1,NULL,5001,'Ximdex',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (2,1,5002,'Control center',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (3,2,5003,'User manager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (4,2,5004,'Group manager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5,2,5005,'Role manager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6,2,5006,'Type of node manager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7,2,5030,'Language manager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8,2,5035,'Workflow manager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (9,2,5029,'Channel manager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (10,2,5037,'Permit manager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (11,2,5058,'Property manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (12,2,5044,'Configuration manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
-- INSERT INTO `Nodes` VALUES (13, 12, 5082, 'Heritable properties', 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT)
INSERT INTO `Nodes` VALUES (14,12,5082,'Allowed extensions',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (101,4,5011,'General',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (201,5,5010,'Administrator',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (202,5,5010,'Editor',NULL,0,NULL,1306935613,1306935613,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (203,5,5010,'Publisher',NULL,0,NULL,1306936491,1306936491,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (204,5,5010,'Expert',NULL,0,NULL,1306937208,1306937208,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (301,3,5009,'ximdex',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (403,8,5079,'Workflow master',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (1001,10,5038,'View all nodes',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (1002,10,5038,'Delete on cascade',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (1003,10,5038,'Receive integrity checks',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5001,6,5007,'Root',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5002,6,5007,'ControlCenter',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5003,6,5007,'UserManager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5004,6,5007,'GroupManager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5005,6,5007,'RoleManager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5006,6,5007,'NodeTypeManager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5007,6,5007,'NodeType',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5008,6,5007,'Action',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5009,6,5007,'User',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5010,6,5007,'Role',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5011,6,5007,'Group',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5012,6,5007,'Projects',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5013,6,5007,'Project',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5014,6,5007,'Server',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5015,6,5007,'Section',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5016,6,5007,'ImagesRootFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5017,6,5007,'ImagesFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5018,6,5007,'XmlRootFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5020,6,5007,'ImportRootFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5021,6,5007,'ImportFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5022,6,5007,'CommonRootFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5023,6,5007,'CommonFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5024,6,5007,'CssRootFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5025,6,5007,'CssFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5026,6,5007,'TemplatesRootFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5028,6,5007,'CssFile',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5029,6,5007,'ChannelManager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5030,6,5007,'LanguageManager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5031,6,5007,'XmlContainer',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5032,6,5007,'XmlDocument',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5033,6,5007,'Channel',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5034,6,5007,'Language',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5035,6,5007,'WorkflowManager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5036,6,5007,'WorkflowState',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5037,6,5007,'PermissionManager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5038,6,5007,'Permission',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5039,6,5007,'TextFile',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5040,6,5007,'ImageFile',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5041,6,5007,'BinaryFile',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5042,6,5007,'HtmlFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5043,6,5007,'ErrorFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5047,6,5007,'TemplateImages',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5048,6,5007,'LinkFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5049,6,5007,'Link',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5050,6,5007,'LinkManager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5053,6,5007,'TemplateViewFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5054,6,5007,'XimletRootFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5055,6,5007,'XimletFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5056,6,5007,'XimletContainer',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5057,6,5007,'Ximlet',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5058,6,5007,'PropertiesManager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5059,6,5007,'Property',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5060,6,5007,'SystemContainer',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5063,6,5007,'ProjectDicFolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5064,6,5007,'Dictionary',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5065,6,5007,'DicValue',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5066,6,5007,'DicFolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5068,6,5007,'PropSet',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5300,6,5007,'XimNewsDateSection',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5301,6,5007,'XimNewsSection',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5302,6,5007,'XimNewsBulletins',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5303,6,5007,'XimNewsNews',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5304,6,5007,'XimNewsImages',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5305,6,5007,'XimNewsBulletin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5306,6,5007,'XimNewsNew',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5307,6,5007,'XimNewsBulletinLanguage',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5308,6,5007,'XimNewsImageFile',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6001,5003,5008,'Add user',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6002,5009,5008,'Modify user data',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6003,5009,5008,'Delete user',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6004,5009,5008,'Manage groups',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6005,5010,5008,'Modify role',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6006,5004,5008,'Add group',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6007,5015,5008,'Configure section',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6008,5007,5008,'Modify node type',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6009,5006,5008,'Add node type',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6010,5005,5008,'Add role',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6011,5012,5008,'Add project',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6012,5013,5008,'Add server',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6013,5014,5008,'Add section',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6014,5015,5008,'Add Section',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6015,5016,5008,'Add image folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6016,5017,5008,'Add image folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6018,5020,5008,'Add import folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6019,5021,5008,'Add import folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6020,5022,5008,'Add common folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6021,5023,5008,'Add common folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6022,5024,5008,'Add CSS folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6023,5025,5008,'Add CSS folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6025,5015,5008,'Change section name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6026,5013,5008,'Change project name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6027,5014,5008,'Change server name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6028,5017,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6030,5021,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6031,5023,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6032,5025,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6034,5013,5008,'Delete proyect',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6035,5014,5008,'Delete server',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6036,5015,5008,'Delete section',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6037,5017,5008,'Delete folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6039,5021,5008,'Delete folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6040,5023,5008,'Delete folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6041,5025,5008,'Delete folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6043,5018,5008,'Add XML Documents',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6044,5018,5008,'Add new XML',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6045,5031,5008,'Add language',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6046,5035,5008,'Manage status',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6047,5030,5008,'Add language',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6048,5029,5008,'Add Channel',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6049,5041,5008,'Download file',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6050,5040,5008,'Download image',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6051,5039,5008,'Download file',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6052,5040,5008,'Image preview',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6063,5031,5008,'Delete document',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6064,5031,5008,'Change XML',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6065,5032,5008,'Edit XML',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6067,5008,5008,'Delete action',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6069,5050,5008,'Add category',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6070,5048,5008,'Add category',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6071,5050,5008,'Add external link',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6072,5048,5008,'Add external link',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6073,5049,5008,'Edit properties',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6074,5015,5008,'Asocciated groups',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6081,5010,5008,'Modify associated status of workflow',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6082,5036,5008,'Modify associated roles',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6083,5041,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6084,5041,5008,'Delete file',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6085,5040,5008,'Change image name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6086,5040,5008,'Delete Image',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6087,5039,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6088,5039,5008,'Delete',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6094,5039,5008,'Edit file',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6095,5033,5008,'Channel properties',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6096,5011,5008,'Group propertis',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6097,5034,5008,'Modify language',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6098,5032,5008,'Move to next state',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6099,5032,5008,'Move to previous state',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6100,5032,5008,'Publish advance',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6101,5011,5008,'Change users',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6102,5034,5008,'Delete',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6103,5028,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6104,5028,5008,'Download file',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6105,5028,5008,'Edit',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6106,5028,5008,'Delete',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6107,5033,5008,'Delete channel',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6108,5011,5008,'Delete group',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6109,5010,5008,'Delete role',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6117,5032,5008,'Generate',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6118,5048,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6119,5048,5008,'Delete folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6120,5049,5008,'Delete link',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6121,5036,5008,'Delete',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6122,5013,5008,'Associated groups',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6123,5014,5008,'Associated groups',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6124,5018,5008,'Modify view template',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6125,5014,5008,'Manage servers',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6126,5039,5008,'Move to next state',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6127,5039,5008,'Move to previous state',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6128,5041,5008,'Move to next state',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6129,5041,5008,'Move to previous state',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6130,5040,5008,'Move to next state',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6131,5040,5008,'Move to previous state',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6132,5028,5008,'Move to next state',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6133,5028,5008,'Move to previous state',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6134,5032,5008,'Delete document',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6135,5032,5008,'Symbolic link',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6136,5032,5008,'Edit in text mode',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6137,5054,5008,'Add new ximlet folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6138,5054,5008,'Add new ximlet',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6139,5055,5008,'Add new ximlet folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6140,5055,5008,'Add new ximlet',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6141,5055,5008,'Delete folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6142,5055,5008,'Change name',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6143,5032,5008,'Version manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6144,5040,5008,'Version manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6145,5041,5008,'Version manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6146,5028,5008,'Version manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6148,5039,5008,'Version manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6150,5057,5008,'Version manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6151,5056,5008,'Add Language',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6152,5056,5008,'Delete ximlet',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6153,5056,5008,'Change ximlet name',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6154,5057,5008,'Edit ximlet',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6155,5057,5008,'Delete document',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6156,5057,5008,'Symbolic link',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6157,5057,5008,'Edit in text mode',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6168,5040,5008,'Replace image',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6170,5014,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6171,5015,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6172,5017,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6173,5021,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6174,5023,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6175,5028,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6176,5031,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6177,5039,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6178,5040,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6179,5041,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6181,5048,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6182,5049,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6183,5056,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6184,5015,5008,'Associate section with ximlet',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6202,5014,5008,'Associate ximlet with server',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6204,5015,5008,'Publish section',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6205,5015,5008,'Expire section',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6300,5304,5008,'Add images',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6304,5301,5008,'Change name',NULL,NULL,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6314,5307,5008,'Edit XML',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6317,5307,5008,'Version repository',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6318,5307,5008,'Generate',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6319,5307,5008,'Delete document',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6320,5307,5008,'Symbolic link',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6321,5307,5008,'Edit in text mode',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6322,5307,5008,'Version manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6323,5308,5008,'Download image',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6324,5308,5008,'Image preview',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6325,5308,5008,'Change image name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6326,5308,5008,'Delete image',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6327,5308,5008,'Move to next state',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6328,5308,5008,'Move to previous state',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6329,5308,5008,'Version manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6330,5308,5008,'Replace image',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6331,5308,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6342,5037,5008,'Move to previous state',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6343,5016,5008,'Add images',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6344,5017,5008,'Add images',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6345,5026,5008,'Add template',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6346,5022,5008,'Add files',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6347,5023,5008,'Add files',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6348,5024,5008,'Add style sheets',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6349,5025,5008,'Add style Sheets',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
-- INSERT INTO `Nodes` VALUES (6350,5020,5008,'Add text files',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
-- INSERT INTO `Nodes` VALUES (6351,5021,5008,'Add text files',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6385,5032,5008,'Preview',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (10000,1,5012,'Projects',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (10001,9,5033, 'Html.html', NULL, 0, NULL, NULL, NULL, 'Html', NULL, '/Ximdex/Centro de Control/Gestor de Canales');
INSERT INTO `Nodes` VALUES (10002, 7,5034, 'Spanish', NULL, 0, NULL, NULL,NULL, 'Spanish language', NULL, '/Ximdex/Centro de Control/Gestor de Idiomas');
INSERT INTO `Nodes` VALUES (10003, 7,5034, 'English', NULL, 0, NULL, NULL,NULL, 'English language', NULL, '/Ximdex/Centro de Control/Gestor de Idiomas');
INSERT INTO `Nodes` VALUES (5076,6,5007,'NodeHt',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6227,5076,5008,'Edit file',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6228,5076,5008,'Download file',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6229,5076,5008,'HTML editor',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6230,5076,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6231,5076,5008,'Version manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6232,5076,5008,'Delete',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6233,5076,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6234,5076,5008,'Replace file',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6236,5020,5008,'Add multiple HTML files',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (50,60,5061,'Language',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (51,60,5061,'Document_type',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (52,60,5061,'Channel',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (53,60,5061,'Channels',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (54,60,5061,'Nodeid',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (55,60,5061,'Project',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (56,60,5061,'Server',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (57,60,5061,'Document',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (58,60,5061,'nombre_documento',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (60,11,5060,'System properties',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6500,5012,5008,'Export all projects',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6501,5013,5008,'Export a complete project',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6502,5050,5008,'Export a link',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6503,5048,5008,'Export a link folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6504,5022,5008,'Export a complete common folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6505,5023,5008,'Export a common subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6506,5024,5008,'Export a complete CSS folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6507,5025,5008,'Export a CSS subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6508,5016,5008,'Export a complete image folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6509,5017,5008,'Export a image subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6510,5020,5008,'Export a complete ximclude folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6511,5021,5008,'Export a ximclude subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6512,5018,5008,'Export a complete documents folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6513,5031,5008,'Export a XML document',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6514,5054,5008,'Export a complete ximlet folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6515,5055,5008,'Export a ximlet subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6516,5056,5008,'Export a ximlet document',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6517,5014,5008,'Export a complete server',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6518,5015,5008,'Export a complete section',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6519,5053,5008,'Export a complete schema',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6520,5026,5008,'Export a complete template',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6550,5012,5008,'Import all projects',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6551,5013,5008,'Import a complete project',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6552,5050,5008,'Import a link',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6553,5048,5008,'Import a complete link folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6554,5022,5008,'Import a complete common folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6555,5023,5008,'Import a common subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6556,5024,5008,'Import a complete CSS folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6557,5025,5008,'Import a CSS subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6558,5016,5008,'Import a complete image folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6559,5017,5008,'Import a image subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6560,5020,5008,'Import a complete ximclude folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6561,5021,5008,'Import a ximclude subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6562,5018,5008,'Import a complete documents folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6563,5031,5008,'Import a XML document',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6564,5054,5008,'Import a complete ximlet folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6565,5055,5008,'Import a ximlet subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6566,5056,5008,'Import a ximlet document',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6567,5014,5008,'Import a complete server',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6568,5015,5008,'Import a complete section',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6569,5053,5008,'Import a complete schema',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6570,5026,5008,'Import a complete template',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6600,5013,5008,'Copy a complete project',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6601,5050,5008,'Copy a link',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6602,5048,5008,'Copy a link folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6603,5022,5008,'Copy a complete common folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6604,5023,5008,'Copy a common subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6605,5024,5008,'Copy a complete CSS folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6606,5025,5008,'Copy a CSS subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6607,5016,5008,'Copy a complete image folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6608,5017,5008,'Copy a image subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6609,5020,5008,'Copy a complete ximclude folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6610,5021,5008,'Copy a ximclude subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6611,5018,5008,'Copy a complete documents folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6612,5031,5008,'Copy a XML document',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6613,5054,5008,'Copy a complete ximlet folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6614,5055,5008,'Copy a ximlet subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6615,5056,5008,'Copy a ximlet document',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6616,5049,5008,'Copy a link',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6617,5057,5008,'Copy a ximlet',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6619,5014,5008,'Copy a complete server',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6620,5015,5008,'Copy a complete section',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6621,5053,5008,'Copy a complete schema',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6622,5026,5008,'Copy a complete template',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6626,5039,5008,'Copy a text file',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6627,5040,5008,'Copy a binary file',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6628,5041,5008,'Copy a binary file',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6629,5028,5008,'Copy a style sheet',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7229,5048,5008,'Check links', 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7231,5050,5008,'Check links', 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7232,5034,5008,'Associate projects with language',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7233,5013,5008,'Associate languages with projects',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7234,5033,5008,'Associate projects with channel',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7235,5013,5008,'Associate channels with project',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7236,5057,5008,'Manage associations',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8117,5077,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8119,5077,5008,'Version manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8120,5077,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8121,5026,5008,'Delete templates',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8122,10000,5080,'Modules',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8123,8122,5081,'ximIO',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8124,8122,5081,'ximLOADER',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8125,8122,5081,'ximNEWS',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8126,8122,5081,'ximSYNC',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8127,8122,5081,'ximTAGS',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8128,8122,5081,'ximTOUR',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8129,8122,5081,'Xowl',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8130,8122,5081,'XSparrow',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);

UNLOCK TABLES;
/*!40000 ALTER TABLE `Nodes` ENABLE KEYS */;

--
-- Table structure for table `Permissions`
--

DROP TABLE IF EXISTS `Permissions`;
CREATE TABLE `Permissions` (
  `IdPermission` int(12) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '0',
  `Description` varchar(255) default NULL,
  PRIMARY KEY  (`IdPermission`),
  UNIQUE KEY `IdName` (`Name`)
) ENGINE=MyISAM COMMENT='Table of system permits';

--
-- Dumping data for table `Permissions`
--


/*!40000 ALTER TABLE `Permissions` DISABLE KEYS */;
LOCK TABLES `Permissions` WRITE;
INSERT INTO `Permissions` VALUES (1001,'View all nodes','View all the nodes');
INSERT INTO `Permissions` VALUES (1002,'Delete on cascade','Multi-section deletion');
INSERT INTO `Permissions` VALUES (1003,'Receive integrity checks','Receive the integrity checks');
INSERT INTO `Permissions` VALUES (1004,'Delete_frames','Delete publication windows');
INSERT INTO `Permissions` VALUES (1006,'Expert_mode_allowed', 'Expert mode edition');
INSERT INTO `Permissions` VALUES (1007,'Ximdex_close', 'Ximdex controlled closing');
INSERT INTO `Permissions` VALUES (1008,'Structural_publication', 'Publication without structure');
INSERT INTO `Permissions` VALUES (1009,'Advanced_publication', 'Advanced publication');
INSERT INTO `Permissions` VALUES (1010,'Ximedit_publication_allowed', 'Publication from editor');

UNLOCK TABLES;
/*!40000 ALTER TABLE `Permissions` ENABLE KEYS */;

--
-- Table structure for table `Protocols`
--

DROP TABLE IF EXISTS `Protocols`;
CREATE TABLE `Protocols` (
  `IdProtocol` varchar(255) NOT NULL default '',
  `DefaultPort` int(12) unsigned default '0',
  `Description` varchar(255) default '0',
  `UsePassword` int(1) unsigned default '0',
  PRIMARY KEY  (`IdProtocol`),
  UNIQUE KEY `IdProtocol` (`IdProtocol`),
  KEY `IdProtocol_2` (`IdProtocol`)
) ENGINE=MyISAM COMMENT='Protocols to synchronize supported by Ximdex CMS';

--
-- Dumping data for table `Protocols`
--


/*!40000 ALTER TABLE `Protocols` DISABLE KEYS */;
LOCK TABLES `Protocols` WRITE;
INSERT INTO `Protocols` VALUES ('SSH',22,'Secure transfer protocol (ssh)',1);
INSERT INTO `Protocols` VALUES ('LOCAL',NULL,'Local synchronization',0);
INSERT INTO `Protocols` VALUES ('FTP',21,'FTP synchronization',1);
INSERT INTO `Protocols` VALUES ('SOLR',8983,'SOLR synchronization',0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `Protocols` ENABLE KEYS */;

--
-- Table structure for table `RelGroupsNodes`
--

DROP TABLE IF EXISTS `RelGroupsNodes`;
CREATE TABLE `RelGroupsNodes` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdGroup` int(12) unsigned NOT NULL default '0',
  `IdNode` int(12) unsigned NOT NULL default '0',
  `IdRole` int(12) unsigned default NULL,
  PRIMARY KEY  (`IdRel`),
  UNIQUE KEY `uniq` (`IdNode`,`IdGroup`),
  KEY `IdGroup` (`IdGroup`),
  KEY `IdNode` (`IdNode`)
) ENGINE=MyISAM COMMENT='Association of user groups with nodes';

--
-- Dumping data for table `RelGroupsNodes`
--


/*!40000 ALTER TABLE `RelGroupsNodes` DISABLE KEYS */;
LOCK TABLES `RelGroupsNodes` WRITE;
INSERT INTO `RelGroupsNodes` VALUES (1,101,2,NULL);
INSERT INTO `RelGroupsNodes` VALUES (2,101,10000,NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `RelGroupsNodes` ENABLE KEYS */;

--
-- Table structure for table `RelRolesActions`
--

DROP TABLE IF EXISTS `RelRolesActions`;
CREATE TABLE `RelRolesActions` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdRol` int(12) unsigned NOT NULL default '0',
  `IdAction` int(12) unsigned NOT NULL default '0',
  `IdState` int(12) unsigned default NULL,
  `IdContext` int(12) NOT NULL default '1',
  `IdPipeline` int(12) NULL,
  PRIMARY KEY  (`IdRel`),
  KEY `IdRol` (`IdRol`),
  KEY `IdAction` (`IdAction`),
  KEY (`IdContext`)
) ENGINE=MyISAM COMMENT='Assignment of default command of each role';

--
-- Dumping data for table `RelRolesActions`
--


/*!40000 ALTER TABLE `RelRolesActions` DISABLE KEYS */;
LOCK TABLES `RelRolesActions` WRITE;
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6231,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6230,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6229,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6228,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6227,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7236,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7236,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7223,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7223,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7016,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7016,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6157,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6157,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6156,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6156,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6155,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6155,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6154,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6154,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6150,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6150,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7222,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6183,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6153,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6152,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6151,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7221,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6376,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6142,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6141,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6140,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6139,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7220,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6375,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6138,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6137,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8115,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8116,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8112,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7226,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6374,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7218,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6182,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6120,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6073,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7212,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7212,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6175,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6175,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6146,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6146,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6133,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6133,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6132,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6132,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6106,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6106,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6105,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6105,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6104,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6104,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6103,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6103,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7229,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7217,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6357,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6181,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6119,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6118,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6072,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6070,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7231,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7219,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6356,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6071,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6069,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7227,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6149,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6149,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6116,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6115,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6114,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6080,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6180,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6113,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6112,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6111,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6110,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8117,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6370,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7216,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7216,7,1,3);
-- INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6190,8,1,3);
-- INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6190,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6179,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6179,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6145,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6145,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6129,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6129,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6128,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6128,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6084,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6084,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6083,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6083,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6049,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6049,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7200,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7200,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6178,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6178,7,1,3);
-- INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6168,8,1,3);
-- INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6168,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6144,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6144,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6131,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6131,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6130,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6130,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6086,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6086,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6085,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6085,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6052,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6052,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6050,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6050,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7215,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7215,7,1,3);
-- INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6191,8,1,3);
-- INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6191,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6177,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6177,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6148,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6148,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6127,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6127,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6126,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6126,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6094,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6094,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6088,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6088,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6087,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6087,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6051,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6051,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6121,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6082,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7301,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6102,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6097,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6107,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6095,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6047,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6048,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7311,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7213,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6176,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6063,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6064,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6045,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8111,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7211,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6367,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6345,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8110,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8110,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8104,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8104,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7210,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7210,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6366,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6366,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6349,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6349,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6041,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6041,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6032,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6032,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6023,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6023,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8109,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8109,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8103,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8103,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7209,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7209,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6365,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6365,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6348,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6348,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6022,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6022,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8114,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8114,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8106,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8106,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7208,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7208,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6355,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6355,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6347,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6347,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6174,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6174,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6040,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6040,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6031,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6031,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6021,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6021,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8113,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8113,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8105,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8105,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7207,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7207,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6354,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6354,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6346,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6346,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6020,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6020,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7206,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6364,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6351,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6173,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6039,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6030,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6019,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7205,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6363,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6236,0,1,3);
-- INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6350,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6018,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7310,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7204,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6352,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6124,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6044,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6043,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8108,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8108,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8102,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8102,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7201,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7201,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6361,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6361,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6344,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6344,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6172,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6172,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6037,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6037,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6028,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6028,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6016,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6016,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8107,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8107,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8101,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8101,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7202,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7202,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6360,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6360,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6343,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6343,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6015,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6015,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7309,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7309,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7225,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7225,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6383,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6383,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6205,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6205,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6204,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6204,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6184,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6184,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6171,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6171,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6074,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6074,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6036,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6036,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6025,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6025,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6014,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6014,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7308,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7308,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7203,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7203,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6384,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6384,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6202,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6202,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6170,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6170,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6125,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6125,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6123,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6123,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6035,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6035,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6027,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6027,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6013,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6013,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7312,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7312,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6385,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6385,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6143,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6143,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6147,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6147,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6136,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6136,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6135,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6135,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6134,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6134,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6100,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6100,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6099,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6099,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6098,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6098,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6065,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6065,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6011,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7307,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6600,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6122,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6034,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6026,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6012,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6101,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6096,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6108,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6109,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6081,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6005,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6004,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6003,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6002,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6067,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6008,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6009,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6010,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6006,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6001,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8122,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8123,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8124,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8125,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8126,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8127,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8128,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8129,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6065,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6098,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6099,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6100,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6135,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6136,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6143,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6385,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6384,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6383,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6360,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,8107,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6361,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,8108,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6352,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6363,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6364,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6354,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,8113,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6355,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,8114,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6365,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,8109,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6366,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,8110,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6367,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,8111,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6051,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6094,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6126,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6127,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6148,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6050,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6052,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6130,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6131,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6144,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6049,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6128,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6129,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6145,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6370,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6112,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6147,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6115,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6149,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6356,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,7231,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6357,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,7229,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6104,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6132,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6133,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6146,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6374,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,8112,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6375,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6150,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6154,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6156,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6157,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,7236,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6227,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6228,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6229,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,6231,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6098,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6098,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6099,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6100,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6135,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6135,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6143,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6385,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6384,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6204,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6205,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6383,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6360,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8101,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8101,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8107,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6361,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8102,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8102,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8108,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6352,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6363,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6364,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6354,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8105,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8105,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8113,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6355,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8106,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8106,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8114,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6365,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8103,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8103,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8109,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6366,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8104,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8104,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8110,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6367,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8111,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6051,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6126,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6127,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6148,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6050,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6052,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6130,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6131,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6144,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6049,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6128,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6129,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6145,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6370,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6112,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6147,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6115,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6149,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6356,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,7231,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6357,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,7229,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6104,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6132,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6133,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6146,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6374,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8112,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6375,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6376,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6150,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6156,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,7016,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,7236,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6228,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,6231,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6012,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6026,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6034,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6122,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6600,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7307,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6011,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6065,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6065,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6098,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6098,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6099,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6099,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6100,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6100,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6134,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6134,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6135,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6135,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6136,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6136,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6143,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6143,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6385,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6385,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7312,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7312,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6013,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6013,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6027,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6027,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6035,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6035,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6123,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6123,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6125,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6125,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6170,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6170,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6202,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6202,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6384,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6384,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7203,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7203,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7308,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7308,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6014,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6014,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6025,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6025,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6036,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6036,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6074,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6074,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6171,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6171,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6184,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6184,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6204,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6204,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6205,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6205,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6383,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6383,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7225,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7225,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7309,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7309,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6015,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6015,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6343,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6343,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6360,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6360,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7202,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7202,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8101,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8101,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8107,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8107,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6016,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6016,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6028,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6028,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6037,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6037,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6172,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6172,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6344,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6344,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6361,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6361,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7201,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7201,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8102,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8102,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8108,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8108,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6044,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6124,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6352,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7204,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7310,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6018,0,1,3);
-- INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6350,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6236,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6363,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7205,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6019,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6030,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6039,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6173,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6351,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6364,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7206,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6020,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6020,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6346,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6346,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6354,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6354,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7207,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7207,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8105,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8105,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8113,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8113,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6021,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6021,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6031,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6031,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6040,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6040,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6174,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6174,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6347,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6347,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6355,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6355,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7208,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7208,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8106,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8106,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8114,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8114,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6022,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6022,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6348,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6348,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6365,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6365,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7209,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7209,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8103,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8103,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8109,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8109,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6023,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6023,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6032,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6032,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6041,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6041,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6349,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6349,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6366,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6366,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7210,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7210,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8104,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8104,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8110,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8110,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6345,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6367,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7211,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8111,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6045,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6064,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6063,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6176,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7213,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7311,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6051,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6051,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6087,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6087,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6088,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6088,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6094,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6094,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6126,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6126,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6127,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6127,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6148,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6148,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6177,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6177,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6191,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6191,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7215,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7215,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6050,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6050,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6052,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6052,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6085,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6085,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6086,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6086,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6130,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6130,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6131,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6131,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6144,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6144,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6168,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6168,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6178,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6178,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7200,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7200,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6049,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6049,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6083,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6083,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6084,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6084,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6128,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6128,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6129,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6129,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6145,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6145,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6179,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6179,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6190,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6190,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7216,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7216,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6370,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6110,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8117,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6111,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6112,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6113,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6147,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6180,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6080,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6114,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6115,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6116,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6149,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7227,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6069,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6071,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6356,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7219,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7231,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6070,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6072,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6118,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6119,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6181,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6357,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7217,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7229,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6103,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6103,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6104,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6104,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6105,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6105,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6106,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6106,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6132,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6132,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6133,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6133,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6146,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6146,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6175,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6175,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7212,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7212,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6073,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6120,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6182,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7218,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6374,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7226,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8112,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8115,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6137,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6138,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6375,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7220,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6139,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6140,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6141,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6142,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6376,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7221,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6151,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6152,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6153,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6183,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7222,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6150,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6150,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6154,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6154,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6155,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6155,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6156,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6156,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6157,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6157,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7016,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7016,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7223,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7223,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7236,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7236,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6227,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6228,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6229,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6230,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6231,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6232,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6233,0,1,3);
-- INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6234,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7224,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7305,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7305,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7306,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7306,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7318,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7319,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7321,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6232,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6233,0,1,3);
-- INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6234,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7224,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7305,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7305,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7306,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7306,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7318,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7319,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7321,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6046,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7317,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7305,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7306,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8118,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8118,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8119,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8119,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8119,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8119,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8120,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8120,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8121,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8121,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6007,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6007,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6206,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6206,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6206,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6207,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6207,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6207,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6208,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6208,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6208,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6209,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6209,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6209,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6210,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6210,8,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6210,7,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8132,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8133,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8134,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8135,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8136,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8137,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,8137,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,8137,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,8137,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7230,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,202,7230,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,203,7230,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7230,0,1,3);

INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9500,0,1,3);
INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,9500,7,1,3);


/* Rel info node actions with every Rol */
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9600, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9600, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9600, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9600, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9600, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9600, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9600, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9600, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9600, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9600, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9600, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9600, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9601, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9601, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9601, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9601, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9601, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9601, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9601, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9601, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9601, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9601, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9601, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9601, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9602, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9602, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9602, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9602, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9602, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9602, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9602, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9602, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9602, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9602, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9602, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9602, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9603, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9603, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9603, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9603, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9603, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9603, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9603, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9603, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9603, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9603, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9603, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9603, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9604, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9604, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9604, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9604, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9604, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9604, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9604, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9604, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9604, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9604, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9604, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9604, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9605, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9605, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9605, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9605, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9605, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9605, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9605, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9605, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9605, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9605, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9605, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9605, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9606, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9606, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9606, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9606, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9606, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9606, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9606, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9606, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9606, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9606, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9606, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9606, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9607, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9607, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9607, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9607, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9607, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9607, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9607, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9607, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9607, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9607, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9607, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9607, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9608, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9608, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9608, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9608, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9608, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9608, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9608, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9608, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9608, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9608, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9608, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9608, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9609, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9609, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9609, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9609, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9609, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9609, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9609, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9609, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9609, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9609, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9609, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9609, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9610, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9610, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9610, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9610, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9610, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9610, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9610, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9610, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9610, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9610, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9610, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9610, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9611, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9611, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9611, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9611, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9611, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9611, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9611, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9611, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9611, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9611, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9611, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9611, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9612, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9612, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9612, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9612, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9612, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9612, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9612, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9612, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9612, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9612, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9612, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9612, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9613, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9613, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9613, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9613, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9613, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9613, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9613, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9613, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9613, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9613, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9613, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9613, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9614, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9614, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9614, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9614, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9614, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9614, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9614, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9614, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9614, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9614, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9614, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9614, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9615, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9615, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9615, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9615, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9615, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9615, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9615, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9615, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9615, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9615, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9615, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9615, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9616, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9616, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9616, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9616, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9616, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9616, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9616, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9616, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9616, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9616, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9616, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9616, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9617, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9617, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9617, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9617, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9617, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9617, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9617, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9617, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9617, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9617, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9617, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9617, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9618, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9618, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9618, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9618, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9618, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9618, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9618, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9618, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9618, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9618, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9618, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9618, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9619, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9619, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9619, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9619, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9619, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9619, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9619, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9619, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9619, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9619, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9619, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9619, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9620, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9620, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9620, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9620, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9620, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9620, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9620, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9620, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9620, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9620, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9620, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9620, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9621, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9621, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9621, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9621, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9621, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9621, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9621, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9621, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9621, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9621, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9621, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9621, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9622, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9622, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9622, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9622, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9622, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9622, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9622, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9622, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9622, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9622, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9622, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9622, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9623, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9623, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9623, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9623, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9623, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9623, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9623, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9623, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9623, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9623, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9623, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9623, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9624, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9624, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9624, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9624, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9624, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9624, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9624, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9624, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9624, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9624, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9624, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9624, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9625, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9625, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9625, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9625, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9625, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9625, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9625, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9625, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9625, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9625, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9625, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9625, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9626, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9626, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9626, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9626, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9626, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9626, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9626, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9626, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9626, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9626, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9626, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9626, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9627, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9627, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9627, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9627, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9627, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9627, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9627, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9627, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9627, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9627, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9627, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9627, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9628, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9628, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9628, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9628, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9628, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9628, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9628, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9628, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9628, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9628, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9628, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9628, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9629, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9629, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9629, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9629, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9629, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9629, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9629, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9629, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9629, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9629, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9629, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9629, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9630, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9630, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9630, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9630, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9630, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9630, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9630, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9630, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9630, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9630, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9630, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9630, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9631, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9631, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9631, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9631, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9631, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9631, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9631, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9631, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9631, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9631, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9631, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9631, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9632, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9632, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9632, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9632, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9632, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9632, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9632, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9632, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9632, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9632, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9632, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9632, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9633, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9633, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9633, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9633, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9633, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9633, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9633, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9633, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9633, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9633, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9633, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9633, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9634, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9634, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9634, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9634, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9634, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9634, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9634, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9634, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9634, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9634, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9634, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9634, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9635, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9635, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9635, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9635, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9635, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9635, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9635, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9635, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9635, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9635, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9635, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9635, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9636, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9636, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9636, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9636, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9636, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9636, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9636, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9636, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9636, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9636, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9636, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9636, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9637, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9637, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9637, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9637, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9637, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9637, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9637, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9637, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9637, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9637, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9637, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9637, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9638, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9638, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9638, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9638, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9638, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9638, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9638, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9638, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9638, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9638, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9638, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9638, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9639, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9639, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9639, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9639, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9639, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9639, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9639, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9639, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9639, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9639, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9639, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9639, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9640, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9640, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9640, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9640, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9640, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9640, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9640, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9640, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9640, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9640, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9640, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9640, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9641, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9641, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9641, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9641, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9641, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9641, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9641, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9641, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9641, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9641, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9641, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9641, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9642, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9642, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9642, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9642, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9642, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9642, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9642, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9642, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9642, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9642, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9642, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9642, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9643, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9643, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9643, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9643, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9643, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9643, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9643, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9643, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9643, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9643, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9643, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9643, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9644, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9644, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9644, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9644, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9644, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9644, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9644, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9644, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9644, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9644, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9644, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9644, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9645, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9645, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9645, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9645, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9645, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9645, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9645, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9645, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9645, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9645, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9645, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9645, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9646, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9646, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9646, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9646, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9646, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9646, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9646, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9646, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9646, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9646, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9646, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9646, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9647, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9647, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9647, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9647, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9647, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9647, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9647, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9647, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9647, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9647, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9647, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9647, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9648, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9648, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9648, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9648, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9648, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9648, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9648, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9648, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9648, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9648, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9648, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9648, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9649, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9649, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9649, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9649, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9649, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9649, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9649, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9649, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9649, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9649, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9649, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9649, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9650, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9650, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9650, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9650, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9650, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9650, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9650, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9650, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9650, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9650, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9650, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9650, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9651, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9651, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9651, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9651, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9651, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9651, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9651, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9651, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9651, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9651, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9651, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9651, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9652, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9652, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9652, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9652, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9652, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9652, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9652, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9652, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9652, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9652, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9652, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9652, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9653, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9653, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9653, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9653, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9653, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9653, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9653, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9653, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9653, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9653, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9653, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9653, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9654, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9654, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9654, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9654, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9654, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9654, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9654, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9654, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9654, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9654, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9654, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9654, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9655, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9655, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9655, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9655, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9655, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9655, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9655, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9655, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9655, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9655, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9655, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9655, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9656, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9656, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9656, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9656, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9656, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9656, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9656, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9656, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9656, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9656, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9656, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9656, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9657, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9657, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9657, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9657, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9657, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9657, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9657, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9657, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9657, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9657, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9657, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9657, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9658, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9658, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9658, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9658, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9658, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9658, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9658, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9658, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9658, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9658, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9658, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9658, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9659, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9659, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9659, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9659, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9659, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9659, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9659, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9659, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9659, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9659, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9659, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9659, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9660, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9660, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9660, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9660, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9660, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9660, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9660, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9660, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9660, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9660, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9660, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9660, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9661, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9661, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9661, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9661, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9661, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9661, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9661, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9661, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9661, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9661, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9661, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9661, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9662, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9662, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9662, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9662, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9662, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9662, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9662, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9662, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9662, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9662, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9662, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9662, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9663, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9663, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9663, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9663, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9663, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9663, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9663, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9663, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9663, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9663, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9663, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9663, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9664, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9664, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9664, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9664, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9664, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9664, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9664, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9664, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9664, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9664, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9664, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9664, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9665, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9665, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9665, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9665, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9665, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9665, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9665, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9665, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9665, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9665, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9665, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9665, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9666, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9666, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9666, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9666, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9666, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9666, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9666, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9666, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9666, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9666, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9666, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9666, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9667, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9667, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9667, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9667, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9667, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9667, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9667, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9667, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9667, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9667, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9667, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9667, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9668, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9668, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9668, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9668, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9668, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9668, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9668, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9668, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9668, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9668, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9668, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9668, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9669, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9669, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9669, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9669, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9669, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9669, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9669, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9669, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9669, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9669, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9669, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9669, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9670, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9670, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9670, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9670, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9670, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9670, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9670, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9670, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9670, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9670, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9670, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9670, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9671, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9671, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9671, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9671, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9671, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9671, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9671, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9671, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9671, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9671, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9671, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9671, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9672, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9672, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9672, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9672, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9672, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9672, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9672, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9672, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9672, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9672, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9672, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9672, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9673, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9673, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9673, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9673, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9673, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9673, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9673, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9673, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9673, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9673, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9673, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9673, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9674, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9674, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9674, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9674, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9674, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9674, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9674, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9674, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9674, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9674, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9674, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9674, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9675, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9675, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9675, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9675, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9675, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9675, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9675, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9675, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9675, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9675, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9675, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9675, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9676, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9676, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9676, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9676, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9676, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9676, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9676, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9676, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9676, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9676, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9676, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9676, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9677, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9677, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9677, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9677, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9677, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9677, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9677, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9677, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9677, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9677, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9677, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9677, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9678, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9678, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9678, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9678, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9678, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9678, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9678, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9678, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9678, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9678, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9678, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9678, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9679, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9679, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9679, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9679, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9679, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9679, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9679, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9679, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9679, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9679, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9679, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9679, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9680, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9680, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 201, 9680, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9680, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9680, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 202, 9680, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9680, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9680, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 203, 9680, 8,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9680, 0,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9680, 7,1,3);
INSERT INTO `RelRolesActions`(`IdRol`, `IdAction`, `idState`, `IdContext`, `IdPipeline`)
      VALUES( 204, 9680, 8,1,3);

UNLOCK TABLES;
/*!40000 ALTER TABLE `RelRolesActions` ENABLE KEYS */;

--
-- Table structure for table `RelRolesPermissions`
--

DROP TABLE IF EXISTS `RelRolesPermissions`;
CREATE TABLE `RelRolesPermissions` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdRole` int(12) unsigned NOT NULL default '0',
  `IdPermission` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdRel`)
) ENGINE=MyISAM COMMENT='Association of roles and permits';

--
-- Dumping data for table `RelRolesPermissions`
--


/*!40000 ALTER TABLE `RelRolesPermissions` DISABLE KEYS */;
LOCK TABLES `RelRolesPermissions` WRITE;
INSERT INTO `RelRolesPermissions` VALUES (320,201,1004);
INSERT INTO `RelRolesPermissions` VALUES (319,201,1003);
INSERT INTO `RelRolesPermissions` VALUES (318,201,1002);
INSERT INTO `RelRolesPermissions` VALUES (317,201,1001);
INSERT INTO `RelRolesPermissions` VALUES (302,202,1001);
INSERT INTO `RelRolesPermissions` VALUES (303,202,1007);
INSERT INTO `RelRolesPermissions` VALUES (304,203,1001);
INSERT INTO `RelRolesPermissions` VALUES (305,203,1004);
INSERT INTO `RelRolesPermissions` VALUES (306,203,1007);
INSERT INTO `RelRolesPermissions` VALUES (307,203,1008);
INSERT INTO `RelRolesPermissions` VALUES (308,203,1009);
INSERT INTO `RelRolesPermissions` VALUES (309,204,1001);
INSERT INTO `RelRolesPermissions` VALUES (310,204,1002);
INSERT INTO `RelRolesPermissions` VALUES (311,204,1004);
INSERT INTO `RelRolesPermissions` VALUES (312,204,1006);
INSERT INTO `RelRolesPermissions` VALUES (313,204,1007);
INSERT INTO `RelRolesPermissions` VALUES (314,204,1008);
INSERT INTO `RelRolesPermissions` VALUES (315,204,1009);
INSERT INTO `RelRolesPermissions` VALUES (316,204,1010);
INSERT INTO `RelRolesPermissions` VALUES (321,201,1006);
INSERT INTO `RelRolesPermissions` VALUES (322,201,1007);
INSERT INTO `RelRolesPermissions` VALUES (323,201,1008);
INSERT INTO `RelRolesPermissions` VALUES (324,201,1009);
INSERT INTO `RelRolesPermissions` VALUES (325,201,1010);
UNLOCK TABLES;
/*!40000 ALTER TABLE `RelRolesPermissions` ENABLE KEYS */;

--
-- Table structure for table `RelRolesStates`
--

DROP TABLE IF EXISTS `RelRolesStates`;
CREATE TABLE `RelRolesStates` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdRole` int(12) unsigned NOT NULL default '0',
  `IdState` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdRel`),
  UNIQUE KEY `items` (`IdRole`,`IdState`)
) ENGINE=MyISAM COMMENT='Association of roles with status transitions';

--
-- Dumping data for table `RelRolesStates`
--


/*!40000 ALTER TABLE `RelRolesStates` DISABLE KEYS */;
LOCK TABLES `RelRolesStates` WRITE;
INSERT INTO `RelRolesStates` VALUES (1,201,7);
INSERT INTO `RelRolesStates` VALUES (2,201,8);
INSERT INTO `RelRolesStates` VALUES (3,202,7);
INSERT INTO `RelRolesStates` VALUES (4,203,7);
INSERT INTO `RelRolesStates` VALUES (5,203,8);
INSERT INTO `RelRolesStates` VALUES (6,204,7);
INSERT INTO `RelRolesStates` VALUES (7,204,8);
UNLOCK TABLES;
/*!40000 ALTER TABLE `RelRolesStates` ENABLE KEYS */;

--
-- Table structure for table `RelServersChannels`
--

DROP TABLE IF EXISTS `RelServersChannels`;
CREATE TABLE `RelServersChannels` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdServer` int(12) unsigned default '0',
  `IdChannel` int(12) unsigned default '0',
  PRIMARY KEY  (`IdRel`),
  UNIQUE KEY `IdRel` (`IdRel`)
) ENGINE=MyISAM COMMENT='Table which associates physical servers with channels .';

--
-- Dumping data for table `RelServersChannels`
--


/*!40000 ALTER TABLE `RelServersChannels` DISABLE KEYS */;
LOCK TABLES `RelServersChannels` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `RelServersChannels` ENABLE KEYS */;

--
-- Table structure for table `RelServersStates`
--

DROP TABLE IF EXISTS `RelServersStates`;
CREATE TABLE `RelServersStates` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdServer` int(12) unsigned default '0',
  `IdState` int(12) unsigned default '0',
  PRIMARY KEY  (`IdRel`),
  UNIQUE KEY `IdRel` (`IdRel`),
  KEY `IdRel_2` (`IdRel`)
) ENGINE=MyISAM COMMENT='Table which associates servers with workflow status';

--
-- Dumping data for table `RelServersStates`
--


/*!40000 ALTER TABLE `RelServersStates` DISABLE KEYS */;
LOCK TABLES `RelServersStates` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `RelServersStates` ENABLE KEYS */;

--
-- Table structure for table `RelStrDocChannels`
--

DROP TABLE IF EXISTS `RelStrDocChannels`;
CREATE TABLE `RelStrDocChannels` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdDoc` int(12) unsigned default '0',
  `IdChannel` int(12) unsigned default '0',
  PRIMARY KEY  (`IdRel`),
  UNIQUE KEY `IdDoc` (`IdDoc`,`IdChannel`)
) ENGINE=MyISAM COMMENT='Association between structured documents and their channels';

--
-- Dumping data for table `RelStrDocChannels`
--


/*!40000 ALTER TABLE `RelStrDocChannels` DISABLE KEYS */;
LOCK TABLES `RelStrDocChannels` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `RelStrDocChannels` ENABLE KEYS */;

--
-- Table structure for table `RelTemplateContainer`
--

DROP TABLE IF EXISTS `RelTemplateContainer`;
CREATE TABLE `RelTemplateContainer` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdTemplate` int(12) unsigned NOT NULL default '0',
  `IdContainer` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdRel`)
) ENGINE=MyISAM COMMENT='Associate template with container';

--
-- Dumping data for table `RelTemplateContainer`
--

/*!40000 ALTER TABLE `RelTemplateContainer` DISABLE KEYS */;
LOCK TABLES `RelTemplateContainer` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `RelTemplateContainer` ENABLE KEYS */;

--
-- Table structure for table `RelUsersGroups`
-- 0, 1, 3

DROP TABLE IF EXISTS `RelUsersGroups`;
CREATE TABLE `RelUsersGroups` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdUser` int(12) unsigned NOT NULL default '0',
  `IdGroup` int(12) unsigned NOT NULL default '0',
  `IdRole` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdRel`),
  KEY `IdUSer` (`IdUser`),
  KEY `IdGroup` (`IdGroup`)
) ENGINE=MyISAM COMMENT='Assing users to a group with a role';

--
-- Dumping data for table `RelUsersGroups`
--

/*!40000 ALTER TABLE `RelUsersGroups` DISABLE KEYS */;
LOCK TABLES `RelUsersGroups` WRITE;
INSERT INTO `RelUsersGroups` VALUES (1,301,101,201);
UNLOCK TABLES;
/*!40000 ALTER TABLE `RelUsersGroups` ENABLE KEYS */;

--
-- Table structure for table `Roles`
--

DROP TABLE IF EXISTS `Roles`;
CREATE TABLE `Roles` (
  `IdRole` int(12) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '0',
  `Icon` varchar(255) default NULL,
  `Description` varchar(255) default NULL,
  PRIMARY KEY  (`IdRole`),
  UNIQUE KEY `IdRole` (`Name`)
) ENGINE=MyISAM COMMENT='Table of roles that an user can play into a group';

--
-- Dumping data for table `Roles`
--


/*!40000 ALTER TABLE `Roles` DISABLE KEYS */;
LOCK TABLES `Roles` WRITE;
INSERT INTO `Roles` VALUES (201,'Administrator','','Main administrator');
INSERT INTO `Roles` VALUES (202,'Editor','','Editor user');
INSERT INTO `Roles` VALUES (203,'Publisher','','Publisher user');
INSERT INTO `Roles` VALUES (204,'Expert',NULL,'Expert user');
UNLOCK TABLES;
/*!40000 ALTER TABLE `Roles` ENABLE KEYS */;

--
-- Table structure for table `Servers`
--

DROP TABLE IF EXISTS `Servers`;
CREATE TABLE `Servers` (
  `IdServer` int(12) unsigned NOT NULL auto_increment,
  `IdNode` int(12) unsigned NOT NULL default '0',
  `IdProtocol` varchar(255) default NULL,
  `Login` varchar(255) default NULL,
  `Password` varchar(255) default NULL,
  `Host` varchar(255) default NULL,
  `Port` int(12) unsigned default NULL,
  `Url` blob,
  `InitialDirectory` blob,
  `OverrideLocalPaths` int(1) unsigned default '0',
  `Enabled` int(1) unsigned default '1',
  `Previsual` int(1) default '0',
  `Description` varchar(255) default NULL,
  `otf` int(1) unsigned default '0',
  `idEncode` varchar(255) default NULL,
  PRIMARY KEY  (`IdServer`)
) ENGINE=MyISAM COMMENT='Table with info about Ximdex servers';

--
-- Dumping data for table `Servers`
--


/*!40000 ALTER TABLE `Servers` DISABLE KEYS */;
LOCK TABLES `Servers` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `Servers` ENABLE KEYS */;

--
-- Table structure for table `States`
--

DROP TABLE IF EXISTS `States`;
CREATE TABLE `States` (
  `IdState` int(12) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '0',
  `Description` varchar(255) default '0',
  `IsRoot` int(1) unsigned default '0',
  `IsEnd` int(1) unsigned default '0',
  `NextState` int(12) unsigned default NULL,
  PRIMARY KEY  (`IdState`)
) ENGINE=MyISAM COMMENT='Table of Workflow status';

--
-- Dumping data for table `States`
--


/*!40000 ALTER TABLE `States` DISABLE KEYS */;
LOCK TABLES `States` WRITE;
INSERT INTO `States` VALUES (7,'Edition','Document is under development',1,0,8);
INSERT INTO `States` VALUES (8,'Publication','Document is waiting for being published',0,1,7);
UNLOCK TABLES;
/*!40000 ALTER TABLE `States` ENABLE KEYS */;

--
-- Table structure for table `StructuredDocuments`
--

DROP TABLE IF EXISTS `StructuredDocuments`;
CREATE TABLE `StructuredDocuments` (
  `IdDoc` int(12) unsigned NOT NULL default '0',
  `Name` varchar(255) default NULL,
  `IdCreator` int(12) unsigned default '0',
  `CreationDate` timestamp NOT NULL default CURRENT_TIMESTAMP ,
  `UpdateDate` timestamp NULL ,
  `IdLanguage` int(12) default '0',
  `IdTemplate` int(12) unsigned NOT NULL default '0',
  `TargetLink` int(12) unsigned default NULL,
  PRIMARY KEY  (`IdDoc`)
) ENGINE=MyISAM COMMENT='Table of strutured documents of Ximdex';

--
-- Dumping data for table `StructuredDocuments`
--


/*!40000 ALTER TABLE `StructuredDocuments` DISABLE KEYS */;
LOCK TABLES `StructuredDocuments` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `StructuredDocuments` ENABLE KEYS */;

--
-- Table structure for table `Synchronizer`
--

DROP TABLE IF EXISTS `Synchronizer`;
CREATE TABLE `Synchronizer` (
  `IdSync` int(12) unsigned NOT NULL auto_increment,
  `IdServer` int(12) unsigned NOT NULL default '0',
  `IdNode` int(12) unsigned NOT NULL default '0',
  `IdChannel` int(12) unsigned default NULL,
  `DateUp` int(14) unsigned NOT NULL default '0',
  `DateDown` int(14) unsigned default '0',
  `State` varchar(255) default 'DUE',
  `Error` varchar(255) default NULL,
  `ErrorLevel` varchar(255) default NULL,
  `RemotePath` blob NOT NULL,
  `FileName` varchar(255) NOT NULL default '',
  `Retry` int(12) unsigned default '0',
  `Linked` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdSync`),
  UNIQUE KEY `IdSync` (`IdSync`),
  KEY `IdSync_2` (`IdSync`,`IdServer`,`IdNode`,`IdChannel`,`DateUp`,`DateDown`,`State`)
) ENGINE=MyISAM DELAY_KEY_WRITE=1 COMMENT='Table of sync of Ximdex';

--
-- Dumping data for table `Synchronizer`
--


/*!40000 ALTER TABLE `Synchronizer` DISABLE KEYS */;
LOCK TABLES `Synchronizer` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `Synchronizer` ENABLE KEYS */;

--
-- Table structure for table `SynchronizerDependencies`
--

DROP TABLE IF EXISTS `SynchronizerDependencies`;
CREATE TABLE `SynchronizerDependencies` (
  `IdSync` int(12) unsigned NOT NULL default '0',
  `IdResource` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdSync`,`IdResource`),
  KEY `IdSync` (`IdSync`,`IdResource`)
) ENGINE=MyISAM DELAY_KEY_WRITE=1 COMMENT='Table of dependencies of publication windows of Ximdex';

--
-- Dumping data for table `SynchronizerDependencies`
--


/*!40000 ALTER TABLE `SynchronizerDependencies` DISABLE KEYS */;
LOCK TABLES `SynchronizerDependencies` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `SynchronizerDependencies` ENABLE KEYS */;

--
-- Table structure for table `SynchronizerHistory`
--

DROP TABLE IF EXISTS `SynchronizerHistory`;
CREATE TABLE `SynchronizerHistory` (
  `IdSync` int(12) unsigned NOT NULL auto_increment,
  `IdServer` int(12) unsigned NOT NULL default '0',
  `IdNode` int(12) unsigned NOT NULL default '0',
  `IdChannel` int(12) unsigned default NULL,
  `DateUp` int(14) unsigned NOT NULL default '0',
  `DateDown` int(14) unsigned default '0',
  `State` varchar(255) default 'DUE',
  `Error` varchar(255) default NULL,
  `ErrorLevel` varchar(255) default NULL,
  `RemotePath` blob NOT NULL,
  `FileName` varchar(255) NOT NULL default '',
  `Retry` int(12) unsigned default '0',
  `Linked` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdSync`)
) ENGINE=MyISAM DELAY_KEY_WRITE=1 COMMENT='Table of sync history of Ximdex';

--
-- Table structure for table `SynchronizerDependenciesHistory`
--

DROP TABLE IF EXISTS `SynchronizerDependenciesHistory`;
CREATE TABLE `SynchronizerDependenciesHistory` (
  `IdSync` int(12) unsigned NOT NULL default '0',
  `IdResource` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdSync`,`IdResource`)
) ENGINE=MyISAM DELAY_KEY_WRITE=1 COMMENT='Historical information of publications. Deprecated?';


--
-- Table structure for table `SynchronizerGroups`
--

DROP TABLE IF EXISTS `SynchronizerGroups`;
CREATE TABLE `SynchronizerGroups` (
  `IdMaster` int(12) unsigned NOT NULL default '0',
  `IdSlave` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdMaster`,`IdSlave`),
  UNIQUE KEY `IdMaster` (`IdMaster`,`IdSlave`),
  KEY `IdMaster_2` (`IdMaster`,`IdSlave`)
) ENGINE=MyISAM COMMENT='Table of sharing workflow between nodes';

--
-- Dumping data for table `SynchronizerGroups`
--


/*!40000 ALTER TABLE `SynchronizerGroups` DISABLE KEYS */;
LOCK TABLES `SynchronizerGroups` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `SynchronizerGroups` ENABLE KEYS */;

--
-- Table structure for table `SystemProperties`
--

DROP TABLE IF EXISTS `SystemProperties`;
CREATE TABLE `SystemProperties` (
  `IdSysProp` int(12) unsigned NOT NULL auto_increment,
  `Name` varchar(250) NOT NULL default '0',
  PRIMARY KEY  (`IdSysProp`)
) ENGINE=MyISAM;

--
-- Dumping data for table `SystemProperties`
--


/*!40000 ALTER TABLE `SystemProperties` DISABLE KEYS */;
LOCK TABLES `SystemProperties` WRITE;
INSERT INTO `SystemProperties` VALUES (1,'Language');
INSERT INTO `SystemProperties` VALUES (2,'Document_type');
INSERT INTO `SystemProperties` VALUES (3,'Channel');
INSERT INTO `SystemProperties` VALUES (4,'Channels');
INSERT INTO `SystemProperties` VALUES (5,'Nodeid');
INSERT INTO `SystemProperties` VALUES (6,'Project');
INSERT INTO `SystemProperties` VALUES (7,'Server');
INSERT INTO `SystemProperties` VALUES (8,'Document');
INSERT INTO `SystemProperties` VALUES (9,'Document_name');
UNLOCK TABLES;
/*!40000 ALTER TABLE `SystemProperties` ENABLE KEYS */;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
CREATE TABLE `Users` (
  `IdUser` int(12) unsigned NOT NULL,
  `Login` varchar(255) NOT NULL default '0',
  `Pass` varchar(255) NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '0',
  `Email` varchar(255) NOT NULL default '',
  `Locale` varchar(5) DEFAULT NULL COMMENT 'User Locale',
  `LastLogin` int(14) unsigned default '0',
  `NumAccess` int(12) unsigned default '0',
  PRIMARY KEY  (`IdUser`),
  UNIQUE KEY `login` (`Login`)
) ENGINE=MyISAM COMMENT='Users registered on Ximdex CMS';


--
-- Dumping data for table `Users`
--


/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
LOCK TABLES `Users` WRITE;
INSERT INTO `Users` VALUES (301,'ximdex','$1$qSGCbgO1$yqxywUuVs1w5pd7capSQV.','Administrador de ximdex','notify@ximdex.org', NULL,0,0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `Users` ENABLE KEYS */;

--
-- Table structure for table `Versions`
--

DROP TABLE IF EXISTS `Versions`;
CREATE TABLE `Versions` (
  `IdVersion` int(12) unsigned NOT NULL auto_increment,
  `IdNode` int(12) unsigned NOT NULL default '0',
  `Version` int(12) unsigned NOT NULL default '0',
  `SubVersion` int(12) unsigned NOT NULL default '0',
  `File` varchar(255) NOT NULL default '',
  `IdUser` int(12) unsigned default '0',
  `Date` int(14) unsigned default '0',
  `Comment` blob,
  `IdSync` int(12) unsigned default NULL,
  PRIMARY KEY  (`IdVersion`),
  KEY `Version` (`SubVersion`,`IdNode`,`Version`),
  KEY `IdNode` (`IdNode`)
) ENGINE=MyISAM COMMENT='Table of contents and version management';
 
CREATE TABLE `NodeConstructors` (
  `IdNodeConstructor` int(11) NOT NULL auto_increment,
  `IdNodeType` int(11) NOT NULL,
  `IdAction` int(11) NOT NULL,
  PRIMARY KEY  (`IdNodeConstructor`)
);

 
INSERT INTO `NodeConstructors` (`IdNodeConstructor`, `IdNodeType`, `IdAction`) VALUES
  (1, 5026, 6012),
  (2, 5050, 6011),
  (3, 5053, 6011),
  (4, 5014, 6012),
  (5, 5048, 6070),
  (6, 5049, 6072),
  (8, 5024, 6012),
  (9, 5016, 6012),
  (10, 5018, 6012),
  (11, 5022, 6012),
  (12, 5020, 6012),
  (13, 5054, 6012),
  (14, 5015, 6013),
  (15, 5031, 6044),
  (16, 5056, 6138),
  (17, 5032, 6045),
  (18, 5057, 6151),
  (19, 5013, 6011),
  (20, 5017, 6015),
  (21, 5300, 6013),
  (22, 5306, 6013),
  (23, 5301, 6013),
  (24, 5304, 6013),
  (25, 5305, 6710),
  (26, 5309, 6045),
  (27, 5302, 6702),
  (28, 5307, 6726),
  (29, 5310, 6720),
  (30, 5303, 6769),
  (31, 5308, 6702),
  (32, 5311, 6769);

DROP TABLE IF EXISTS `RelNodeTypeMimeType`;
CREATE TABLE `RelNodeTypeMimeType` (
  `idRelNodeTypeMimeType` int(12) unsigned NOT NULL auto_increment,
  `idNodeType` int(12) unsigned NOT NULL default '0',
  `extension` varchar(255) NULL,
  `filter` char(50) NULL,
  PRIMARY KEY  (`idRelNodeTypeMimeType`)
) ENGINE=MyISAM COMMENT='Relation between nodetypes and mime-types' AUTO_INCREMENT= 141 ;

--
-- Dumping data for table `RelNodeTypeMimeType`
--

INSERT INTO `RelNodeTypeMimeType` VALUES (1, 5001, '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (2, 5002, '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (3, 5003,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (4, 5004,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (5, 5005,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (6, 5006,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (7, 5007,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (8, 5008,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (9, 5009,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (10, 5010,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (11, 5011,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (12, 5012,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (13, 5013,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (14, 5014,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (15, 5015,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (16, 5016,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (17, 5017,  '', '');
-- INSERT INTO `RelNodeTypeMimeType` VALUES (18, 5018,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (19, 5018,  ';xml;', 'xml');
INSERT INTO `RelNodeTypeMimeType` VALUES (20, 5020,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (21, 5021,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (22, 5022,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (23, 5023,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (24, 5024,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (25, 5025,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (26, 5026,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (27, 5027,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (28, 5028,  ';css;', 'css');
INSERT INTO `RelNodeTypeMimeType` VALUES (29, 5029,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (30, 5030,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (31, 5031,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (32, 5032,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (33, 5033,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (34, 5034,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (35, 5035,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (36, 5036,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (37, 5037,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (38, 5038,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (39, 5039,  ';txt;js;json;coffee;css;scss;svg;php;asp;aspx;', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (40, 5040,  ';jpeg;jpg;gif;png;ico;bmp;', 'image');
INSERT INTO `RelNodeTypeMimeType` VALUES (41, 5041,  '*', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (42, 5042,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (43, 5043,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (47, 5047,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (48, 5048,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (49, 5049,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (50, 5050,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (53, 5053,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (54, 5054,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (55, 5055,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (56, 5056,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (57, 5057,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (58, 5058,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (59, 5059,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (60, 5060,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (61, 5061,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (62, 5062,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (63, 5063,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (64, 5064,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (65, 5065,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (66, 5066,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (68, 5068,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (69, 5069,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (70, 5070,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (71, 5071,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (72, 5072,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (73, 5073,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (74, 5074,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (75, 5075,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (76, 5076,  ';ht;html;htm;xhtml;', 'html');
INSERT INTO `RelNodeTypeMimeType` VALUES (77, 5300,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (78, 5301,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (79, 5302,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (80, 5303,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (81, 5304,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (82, 5305,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (83, 5306,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (84, 5307,  ';jpeg;jpg;gif;png;', 'ximnewsimage');
INSERT INTO `RelNodeTypeMimeType` VALUES (85, 5308,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (86, 5309,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (87, 5310,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (88, 5320,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (89, 5083, '','');
INSERT INTO `RelNodeTypeMimeType` VALUES (90, 5084, '','');
INSERT INTO `RelNodeTypeMimeType` VALUES (91, 5085, '','');
-- INSERT INTO `RelNodeTypeMimeType` VALUES (139, 5032,  '', '');
-- INSERT INTO `RelNodeTypeMimeType` VALUES (140, 5055,  '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (147, 5077,  ';xsl;', 'ptd');
INSERT INTO `RelNodeTypeMimeType` VALUES (151, 5078,  ';xml;', 'pvd');
-- INSERT INTO `RelNodeTypeMimeType` VALUES (152, 5041,  '*', '');


DROP TABLE IF EXISTS `RelNodeTypeMetadata`;
CREATE TABLE `RelNodeTypeMetadata` (
  `idRel` int(11) NOT NULL auto_increment,
  `idNodeType` varchar(255) NOT NULL,
  `force` tinyint(1) unsigned NOT NULL default 0,
  PRIMARY KEY  (`idRel`),
  UNIQUE KEY `idNodeType` (`idNodeType`)
);

INSERT INTO `RelNodeTypeMetadata` VALUES (NULL,5032,0);
INSERT INTO `RelNodeTypeMetadata` VALUES (NULL,5039,0);
INSERT INTO `RelNodeTypeMetadata` VALUES (NULL,5040,0);
INSERT INTO `RelNodeTypeMetadata` VALUES (NULL,5041,0);


DROP TABLE IF EXISTS `SectionTypes`;
CREATE TABLE `SectionTypes` (
  `idSectionType` int(11) NOT NULL auto_increment,
  `sectionType` varchar(255) NOT NULL,
  `idNodeType` int(11) NOT NULL,
  `module` varchar(255) default NULL,
  PRIMARY KEY  (`idSectionType`),
  UNIQUE KEY `sectionType` (`sectionType`),
  KEY `idSectionType` (`idSectionType`)
);

INSERT INTO `SectionTypes` ( `idSectionType` , `sectionType` , `idNodeType`  , `module` ) VALUES ('1', 'Normal', 5015, NULL);
INSERT INTO `SectionTypes` ( `idSectionType` , `sectionType` , `idNodeType`  , `module` ) VALUES ('2', 'ximNEWS', 5300, 'ximNEWS');

-- PIPELINE SYSTEM BEGIN
DROP TABLE IF EXISTS `PipeCaches`;
CREATE TABLE `PipeCaches` (
  `id` int(11) NOT NULL auto_increment,
  `IdVersion` int(11) NOT NULL,
  `IdPipeTransition` int(11) NOT NULL,
  `File` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
);


-- --------------------------------------------------------

--
-- Table structure for table `PipeNodeTypes`
--
DROP TABLE IF EXISTS `PipeNodeTypes`;
CREATE TABLE `PipeNodeTypes` (
  `id` int(11) NOT NULL auto_increment,
  `IdPipeline` int(11) NOT NULL,
  `IdNodeType` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

-- --------------------------------------------------------

--
--  Table structure for table `PipeProcess`
--
DROP TABLE IF EXISTS `PipeProcess`;
CREATE TABLE IF NOT EXISTS `PipeProcess` (
  `id` int(11) NOT NULL auto_increment,
  `IdTransitionFrom` int(11) default NULL,
  `IdTransitionTo` int(11) NOT NULL,
  `IdPipeline` int(11) default NULL,
  `Name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `PipeProcess`
--

INSERT INTO `PipeProcess` (`id`, `IdTransitionFrom`, `IdTransitionTo`, `IdPipeline`, `Name`) VALUES
  (1, 1, 3, 1, 'StrDocToDexT'),
  (2, 3, 4, 1, 'StrDocFromDexTToFinal'),
  (3, NULL, 5, 2, 'NotStrDocToFinal'),
  (4, NULL, 6, 3, 'workflow'),
  (5, 7, 9, 4, 'StrDocToXedit');

-- --------------------------------------------------------

--
-- Table structure for table `PipeProperties`
--
DROP TABLE IF EXISTS `PipeProperties`;
CREATE TABLE `PipeProperties` (
  `id` int(11) NOT NULL auto_increment,
  `IdPipeTransition` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
);

INSERT INTO `PipeProperties` (`id`, `IdPipeTransition`, `Name`) VALUES
  (1, 1, 'CHANNEL'),
  (2, 4, 'CHANNEL'),
  (3, 4, 'SERVER'),
  (4, 2, 'CHANNEL'),
  (5, 3, 'CHANNEL'),
  (6, 3, 'TRANSFORMER');
-- --------------------------------------------------------

--
-- Table structure for table `PipePropertyValues`
--
DROP TABLE IF EXISTS `PipePropertyValues`;
CREATE TABLE `PipePropertyValues` (
  `id` int(11) NOT NULL auto_increment,
  `IdPipeProperty` int(11) NOT NULL,
  `IdPipeCache` int(11) NOT NULL,
  `Value` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `PipeStatus`
--
DROP TABLE IF EXISTS `PipeStatus`;
CREATE TABLE IF NOT EXISTS `PipeStatus` (
  `id` int(11) NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL,
  `Description` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `PipeStatus`
--

INSERT INTO `PipeStatus` (`id`, `Name`, `Description`) VALUES
  (1, 'ChannelFilter', ''),
  (2, 'Renderized', ''),
  (3, 'PreFilter', ''),
  (4, 'Dependencies', ''),
  (5, 'DexT', ''),
  (6, 'FilterMacros', ''),
  (7, 'Edición', 'El documento está en fase de desarrollo'),
  (8, 'Publicación', 'El documento está a la espera de ser publicado.'),
  (9, 'Xedit', '');
-- --------------------------------------------------------

--
-- Table structure for table `PipeTransitions`
--
DROP TABLE IF EXISTS `PipeTransitions`;
CREATE TABLE IF NOT EXISTS `PipeTransitions` (
  `id` int(11) NOT NULL auto_increment,
  `IdStatusFrom` int(11) default NULL,
  `IdStatusTo` int(11) NOT NULL,
  `IdPipeProcess` int(11) default NULL,
  `Cacheable` tinyint(1) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Callback` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PipeTransitions`
--

INSERT INTO `PipeTransitions` (`id`, `IdStatusFrom`, `IdStatusTo`, `IdPipeProcess`, `Cacheable`, `Name`, `Callback`) VALUES
  (1, NULL, 2, 1, 1, 'ToRenderize', 'NodeToRenderizedContent'),
  (2, 2, 3, 1, 1, 'FromRenderToPreFilter', 'PrefilterMacros'),
  (3, 3, 5, 1, 1, 'FromPreFilterToDexT', 'Transformer'),
  (4, 5, 6, 2, 0, 'FromToDexTToFinal', 'FilterMacros'),
  (5, NULL, 6, 3, 0, 'ToFinal', 'Common'),
  (6, 7, 8, 4, 0, 'Edici�n_to_Publicaci�n', '-'),
  (7, NULL, 2, 5, 0, 'ToRenderize', 'NodeToRenderizedContent'),
  (8, 2, 9, 5, 0, 'FromRenderToXedit', 'Xedit'),
  (9, 9, 3, 5, 0, 'FromXeditToPreFilter', 'PrefilterMacros');

-- --------------------------------------------------------

--
-- Table structure for table `Pipelines`
--
DROP TABLE IF EXISTS `Pipelines`;
CREATE TABLE IF NOT EXISTS `Pipelines` (
  `id` int(11) NOT NULL auto_increment,
  `Pipeline` varchar(255) NOT NULL,
  `IdNode` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `IdNode` (`IdNode`)
);

INSERT INTO `Pipelines` (`id`, `Pipeline`, `IdNode`) VALUES
  (1, 'PublishStrDoc', NULL),
  (2, 'PublishNotStrDoc', NULL),
  (3, 'Workflow master', 403),
  (4, 'XeditStrDoc', NULL);

--
-- Table structure for table `PipeCacheTemplates`
--
DROP TABLE IF EXISTS `PipeCacheTemplates`;
CREATE TABLE `PipeCacheTemplates` (
  `id` int(11) NOT NULL auto_increment,
  `NodeId` int(11) NOT NULL,
  `DocIdVersion` int(11) NOT NULL,
  `TemplateIdVersion` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);


-- PIPELINE SYSTEM END

DROP TABLE IF EXISTS `NodeProperties`;
CREATE TABLE `NodeProperties` (
  `IdNodeProperty` INT NOT NULL AUTO_INCREMENT,
  `IdNode` INT NOT NULL ,
  `Property` VARCHAR( 255 ) NOT NULL ,
  `Value` blob NOT NULL ,
  PRIMARY KEY ( `IdNodeProperty` ) ,
  INDEX ( `IdNode` )
);


INSERT INTO `NodeProperties` (`IdNode`, `Property`, `Value`) VALUES
  (10000, 'Transformer', 'xslt'),
  (10000, 'pipeline', '3');

-- --------------------------------------------------------

--
-- Table structure for table `RelPropertyValues`
--

DROP TABLE IF EXISTS `Contexts`;
CREATE TABLE `Contexts` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `Context` VARCHAR( 255 ) NOT NULL ,
  INDEX ( `Context` )
);

INSERT INTO `Contexts` ( `id` , `Context` )
VALUES (
  1 , 'ximdex'
), (
  2 , 'webdav'
);

DROP TABLE IF EXISTS `NodetypeModes`;
CREATE TABLE `NodetypeModes` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `IdNodeType` INT NOT NULL ,
  `Mode` ENUM( 'C', 'R', 'U', 'D' ) NOT NULL ,
  `IdAction` INT NULL ,
  INDEX ( `IdNodeType` , `IdAction` )
);

INSERT INTO `NodetypeModes` (`id`, `IdNodeType`, `Mode`, `IdAction`) VALUES
  (1, 5001, 'C', NULL),
  (2, 5001, 'R', NULL),
  (3, 5001, 'U', NULL),
  (4, 5001, 'D', NULL),
  (5, 5002, 'C', NULL),
  (6, 5002, 'R', NULL),
  (7, 5002, 'U', NULL),
  (8, 5002, 'D', NULL),
  (9, 5003, 'C', NULL),
  (10, 5003, 'R', NULL),
  (11, 5003, 'U', NULL),
  (12, 5003, 'D', NULL),
  (13, 5004, 'C', NULL),
  (14, 5004, 'R', NULL),
  (15, 5004, 'U', NULL),
  (16, 5004, 'D', NULL),
  (17, 5005, 'C', NULL),
  (18, 5005, 'R', NULL),
  (19, 5005, 'U', NULL),
  (20, 5005, 'D', NULL),
  (21, 5006, 'C', NULL),
  (22, 5006, 'R', NULL),
  (23, 5006, 'U', NULL),
  (24, 5006, 'D', NULL),
  (25, 5007, 'C', NULL),
  (26, 5007, 'R', NULL),
  (27, 5007, 'U', NULL),
  (28, 5007, 'D', NULL),
  (29, 5008, 'C', NULL),
  (30, 5008, 'R', NULL),
  (31, 5008, 'U', NULL),
  (32, 5008, 'D', NULL),
  (33, 5009, 'C', NULL),
  (34, 5009, 'R', NULL),
  (35, 5009, 'U', NULL),
  (36, 5009, 'D', NULL),
  (37, 5010, 'C', NULL),
  (38, 5010, 'R', NULL),
  (39, 5010, 'U', NULL),
  (40, 5010, 'D', NULL),
  (41, 5011, 'C', NULL),
  (42, 5011, 'R', NULL),
  (43, 5011, 'U', NULL),
  (44, 5011, 'D', NULL),
  (45, 5012, 'C', NULL),
  (46, 5012, 'R', NULL),
  (47, 5012, 'U', NULL),
  (48, 5012, 'D', NULL),
  (49, 5013, 'C', 6011),
  (50, 5013, 'R', NULL),
  (51, 5013, 'U', 6026),
  (52, 5013, 'D', 6034),
  (53, 5014, 'C', 6012),
  (54, 5014, 'R', NULL),
  (55, 5014, 'U', 6027),
  (56, 5014, 'D', 6035),
  (57, 5015, 'C', 6013),
  (58, 5015, 'R', NULL),
  (59, 5015, 'U', 6025),
  (60, 5015, 'D', 6036),
  (61, 5016, 'C', NULL),
  (62, 5016, 'R', NULL),
  (63, 5016, 'U', NULL),
  (64, 5016, 'D', NULL),
  (65, 5017, 'C', 6015),
  (66, 5017, 'R', NULL),
  (67, 5017, 'U', 6028),
  (68, 5017, 'D', 6037),
  (69, 5018, 'C', NULL),
  (70, 5018, 'R', NULL),
  (71, 5018, 'U', NULL),
  (72, 5018, 'D', NULL),
  (73, 5020, 'C', NULL),
  (74, 5020, 'R', NULL),
  (75, 5020, 'U', NULL),
  (76, 5020, 'D', NULL),
  (77, 5021, 'C', 5018),
  (78, 5021, 'R', NULL),
  (79, 5021, 'U', 6030),
  (80, 5021, 'D', 6039),
  (81, 5022, 'C', NULL),
  (82, 5022, 'R', NULL),
  (83, 5022, 'U', NULL),
  (84, 5022, 'D', NULL),
  (85, 5023, 'C', 6020),
  (86, 5023, 'R', NULL),
  (87, 5023, 'U', 6031),
  (88, 5023, 'D', 6040),
  (89, 5024, 'C', NULL),
  (90, 5024, 'R', NULL),
  (91, 5024, 'U', NULL),
  (92, 5024, 'D', NULL),
  (93, 5025, 'C', 6022),
  (94, 5025, 'R', NULL),
  (95, 5025, 'U', 6032),
  (96, 5025, 'D', 6041),
  (97, 5026, 'C', NULL),
  (98, 5026, 'R', NULL),
  (99, 5026, 'U', NULL),
  (100, 5026, 'D', NULL),
  (102, 5028, 'R', NULL),
  (103, 5028, 'U', 6105),
  (104, 5028, 'D', 6106),
  (105, 5029, 'C', NULL),
  (106, 5029, 'R', NULL),
  (107, 5029, 'U', NULL),
  (108, 5029, 'D', NULL),
  (109, 5030, 'C', NULL),
  (110, 5030, 'R', NULL),
  (111, 5030, 'U', NULL),
  (112, 5030, 'D', NULL),
  (113, 5031, 'C', 6044),
  (114, 5031, 'R', NULL),
  (115, 5031, 'U', 6045),
  (116, 5031, 'D', 6063),
  (117, 5032, 'C', 6045),
  (118, 5032, 'R', NULL),
  (119, 5032, 'U', 6136),
  (120, 5032, 'D', 6134),
  (121, 5033, 'C', 6048),
  (122, 5033, 'R', NULL),
  (123, 5033, 'U', 6095),
  (124, 5033, 'D', 6107),
  (125, 5034, 'C', 6047),
  (126, 5034, 'R', NULL),
  (127, 5034, 'U', 6097),
  (128, 5034, 'D', 6102),
  (129, 5035, 'C', NULL),
  (130, 5035, 'R', NULL),
  (131, 5035, 'U', NULL),
  (132, 5035, 'D', NULL),
  (133, 5036, 'C', NULL),
  (134, 5036, 'R', NULL),
  (135, 5036, 'U', NULL),
  (136, 5036, 'D', NULL),
  (137, 5037, 'C', NULL),
  (138, 5037, 'R', NULL),
  (139, 5037, 'U', NULL),
  (140, 5037, 'D', NULL),
  (141, 5038, 'C', NULL),
  (142, 5038, 'R', NULL),
  (143, 5038, 'U', NULL),
  (144, 5038, 'D', NULL),
  (146, 5039, 'R', NULL),
  (147, 5039, 'U', 6094),
  (148, 5039, 'D', 6088),
  (150, 5040, 'R', NULL),
  (151, 5040, 'U', 6085),
  (152, 5040, 'D', 6086),
  (154, 5041, 'R', NULL),
  (155, 5041, 'U', 6083),
  (156, 5041, 'D', 6084),
  (157, 5043, 'C', NULL),
  (158, 5043, 'R', NULL),
  (159, 5043, 'U', NULL),
  (160, 5043, 'D', NULL),
  (169, 5048, 'C', 6069),
  (170, 5048, 'R', NULL),
  (171, 5048, 'U', 6118),
  (172, 5048, 'D', 6119),
  (173, 5049, 'C', 6071),
  (174, 5049, 'R', NULL),
  (175, 5049, 'U', 6073),
  (176, 5049, 'D', 6120),
  (177, 5050, 'C', NULL),
  (178, 5050, 'R', NULL),
  (179, 5050, 'U', NULL),
  (180, 5050, 'D', NULL),
  (181, 5053, 'C', NULL),
  (182, 5053, 'R', NULL),
  (183, 5053, 'U', NULL),
  (184, 5053, 'D', NULL),
  (185, 5054, 'C', NULL),
  (186, 5054, 'R', NULL),
  (187, 5054, 'U', NULL),
  (188, 5054, 'D', NULL),
  (189, 5055, 'C', 6137),
  (190, 5055, 'R', NULL),
  (191, 5055, 'U', 6142),
  (192, 5055, 'D', 6141),
  (193, 5056, 'C', 6138),
  (194, 5056, 'R', NULL),
  (195, 5056, 'U', 6153),
  (196, 5056, 'D', 6152),
  (197, 5057, 'C', 6151),
  (198, 5057, 'R', NULL),
  (199, 5057, 'U', 6157),
  (200, 5057, 'D', 6155),
  (201, 5058, 'C', NULL),
  (202, 5058, 'R', NULL),
  (203, 5058, 'U', NULL),
  (204, 5058, 'D', NULL),
  (205, 5059, 'C', NULL),
  (206, 5059, 'R', NULL),
  (207, 5059, 'U', NULL),
  (208, 5059, 'D', NULL),
  (209, 5060, 'C', NULL),
  (210, 5060, 'R', NULL),
  (211, 5060, 'U', NULL),
  (212, 5060, 'D', NULL),
  (213, 5061, 'C', NULL),
  (214, 5061, 'R', NULL),
  (215, 5061, 'U', NULL),
  (216, 5061, 'D', NULL),
  (217, 5062, 'C', NULL),
  (218, 5062, 'R', NULL),
  (219, 5062, 'U', NULL),
  (220, 5062, 'D', NULL),
  (221, 5063, 'C', NULL),
  (222, 5063, 'R', NULL),
  (223, 5063, 'U', NULL),
  (224, 5063, 'D', NULL),
  (225, 5064, 'C', NULL),
  (226, 5064, 'R', NULL),
  (227, 5064, 'U', NULL),
  (228, 5064, 'D', NULL),
  (229, 5065, 'C', NULL),
  (230, 5065, 'R', NULL),
  (231, 5065, 'U', NULL),
  (232, 5065, 'D', NULL),
  (233, 5066, 'C', NULL),
  (234, 5066, 'R', NULL),
  (235, 5066, 'U', NULL),
  (236, 5066, 'D', NULL),
  (241, 5068, 'C', NULL),
  (242, 5068, 'R', NULL),
  (243, 5068, 'U', NULL),
  (244, 5068, 'D', NULL),
  (274, 5076, 'R', NULL),
  (275, 5076, 'U', 6229),
  (276, 5076, 'D', 6232);

DROP TABLE IF EXISTS `UpdateDb_historic`;
CREATE TABLE IF NOT EXISTS `UpdateDb_historic` (
  `IdLog` int(11) NOT NULL auto_increment,
  `Priority` int(11) NOT NULL,
  `LogText` varchar(255) NOT NULL,
  PRIMARY KEY  (`IdLog`)
);

DROP TABLE IF EXISTS `Updater_DiffsApplied`;
CREATE TABLE `Updater_DiffsApplied` (
  `id` int(12) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `execs` int(2) NOT NULL,
  `module` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17;

--
-- `ActionsStats` table structure
--

DROP TABLE IF EXISTS `ActionsStats`;
CREATE TABLE `ActionsStats` (
  `IdStat` int(11) unsigned NOT NULL auto_increment,
  `IdAction` int(11) unsigned default NULL,
  `IdNode` int(11) unsigned default NULL,
  `IdUser` int(11) unsigned default NULL,
  `Method` varchar(255) default NULL,
  `TimeStamp` int(11) unsigned NOT NULL,
  `Duration` float(11,6) unsigned NOT NULL,
  PRIMARY KEY  (`IdStat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Usage stats for actions';

--
-- Table structure for table `Encodes`
--

DROP TABLE IF EXISTS `Encodes`;
CREATE TABLE `Encodes` (
  `IdEncode` varchar(255) NOT NULL default '',
  `Description` varchar(255) default '0',
  PRIMARY KEY  (`IdEncode`),
  UNIQUE KEY `IdEncode` (`IdEncode`),
  KEY `IdEncode_2` (`IdEncode`)
) ENGINE=MyISAM COMMENT='Available encodings on Ximdex CMS';

--
-- Dumping data for table `Encodes`
--

LOCK TABLES `Encodes` WRITE;
INSERT INTO `Encodes` VALUES ('UTF-8','UTF-8 Encoding');
INSERT INTO `Encodes` VALUES ('ISO-8859-1','ISO-8859-1 Encoding');
UNLOCK TABLES;



-- Table structure for table RelStrdocTemplate

DROP TABLE IF EXISTS `RelStrdocTemplate`;
CREATE TABLE RelStrdocTemplate (
  id int(12) unsigned NOT NULL auto_increment,
  source int(12) unsigned NOT NULL default '0',
  target int(12) unsigned NOT NULL default '0',
  PRIMARY KEY (id),
  UNIQUE KEY `rel` (`source`,`target`),
  INDEX `RelStrdocTemplate_source` (`source`),
  INDEX `RelStrdocTemplate_target` (`target`)
) ENGINE=MyISAM;

-- Table structure for table RelSectionXimlet

DROP TABLE IF EXISTS `RelSectionXimlet`;
CREATE TABLE RelSectionXimlet (
  id int(12) unsigned NOT NULL auto_increment,
  source int(12) unsigned NOT NULL default '0',
  target int(12) unsigned NOT NULL default '0',
  PRIMARY KEY (id),
  UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MyISAM;

-- Table structure for table RelBulletinXimlet

DROP TABLE IF EXISTS `RelBulletinXimlet`;
CREATE TABLE RelBulletinXimlet (
  id int(12) unsigned NOT NULL auto_increment,
  source int(12) unsigned NOT NULL default '0',
  target int(12) unsigned NOT NULL default '0',
  PRIMARY KEY (id),
  UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MyISAM;

-- Table structure for table RelNode2Asset

DROP TABLE IF EXISTS `RelNode2Asset`;
CREATE TABLE RelNode2Asset (
  id int(12) unsigned NOT NULL auto_increment,
  source int(12) unsigned NOT NULL default '0',
  target int(12) unsigned NOT NULL default '0',
  PRIMARY KEY (id),
  UNIQUE KEY `rel` (`source`,`target`),
  INDEX `RelXml2Xml_source` (`source`),
  INDEX `RelXml2Xml_target` (`target`)
) ENGINE=MyISAM;

-- Table structure for table RelStrdocAsset

DROP TABLE IF EXISTS `RelXml2Xml`;
CREATE TABLE RelXml2Xml (
  id int(12) unsigned NOT NULL auto_increment,
  source int(12) unsigned NOT NULL default '0',
  target int(12) unsigned NOT NULL default '0',
  PRIMARY KEY (id),
  UNIQUE KEY `rel` (`source`,`target`),
  INDEX `RelXml2Xml_source` (`source`),
  INDEX `RelXml2Xml_target` (`target`)
) ENGINE=MyISAM;



-- --------------------------------------------------------

--
-- Table structure for table `PortalVersions`
--

DROP TABLE IF EXISTS `PortalVersions`;
CREATE TABLE `PortalVersions` (
  `id` int(12) unsigned NOT NULL auto_increment,
  `IdPortal` int(12) unsigned default '0',
  `Version` int(12) unsigned default '0',
  `TimeStamp` int(12) unsigned default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `RelFramesPortal`
--

DROP TABLE IF EXISTS `RelFramesPortal`;
CREATE TABLE `RelFramesPortal` (
  `id` int(12) unsigned NOT NULL auto_increment,
  `IdPortalVersion` int(12) unsigned default '0',
  `IdFrame` int(12) unsigned default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `PortalFrame` (`IdPortalVersion`,`IdFrame`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `List`;
CREATE TABLE `List` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `IdList` INT NOT NULL ,
  `Name` VARCHAR( 250 ) NOT NULL ,
  `Description` VARCHAR( 250 ) NULL ,
  PRIMARY KEY ( `id` )
) ENGINE = MyISAM;


DROP TABLE IF EXISTS `List_Label`;
CREATE TABLE `List_Label` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `Name` VARCHAR( 250 ) NOT NULL ,
  `Description` VARCHAR( 250 ) NULL ,
  PRIMARY KEY ( `id` )
) ENGINE = MyISAM;


DROP TABLE IF EXISTS `RelVersionsLabel`;
CREATE TABLE `RelVersionsLabel` (
  `id` int(12) unsigned NOT NULL auto_increment,
  `idVersion` int(12) unsigned default '0',
  `idLabel` int(12) unsigned default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `VersionsLabelRest` (`idVersion`,`idLabel`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `NodeSets`;
CREATE TABLE `NodeSets` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(100) default NULL,
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `U_SET` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `RelNodeSetsNode`;
CREATE TABLE `RelNodeSetsNode` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `IdSet` int(10) unsigned NOT NULL,
  `IdNode` int(12) unsigned NOT NULL,
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `U_SETNODES` (`IdSet`,`IdNode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `RelNodeSetsUsers`;
CREATE TABLE `RelNodeSetsUsers` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `IdSet` int(10) unsigned NOT NULL,
  `IdUser` int(12) unsigned NOT NULL,
  `Owner` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `U_SETUSERS` (`IdSet`,`IdUser`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `SearchFilters`;
CREATE TABLE `SearchFilters` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(100) default NULL,
  `Handler` varchar(5) NOT NULL,
  `Filter` text NOT NULL,
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `U_FILTER` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- For Tags and Namespaces
--
DROP TABLE IF EXISTS `Namespaces`;
CREATE TABLE `Namespaces` (
  `idNamespace` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `service` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `nemo` varchar(255) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `recursive` int(8) NOT NULL DEFAULT 0,
  `category` varchar(255) NOT NULL,
  `isSemantic` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`idNamespace`),
  UNIQUE KEY `Nemo` (`nemo`),
  UNIQUE KEY `Type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO Namespaces (service, type, nemo, uri, recursive, category,isSemantic)
VALUES("Ximdex", "Custom", "custom", "http://<ximdex_local_url>/", 0, "generic",0);

--
-- Table structure for table RelNodeVersionMetadataVersion
--
DROP TABLE IF EXISTS `RelNodeVersionMetadataVersion`;
CREATE TABLE RelNodeVersionMetadataVersion (
  id int(12) unsigned NOT NULL auto_increment,
  idrnm int(12) unsigned NOT NULL,
  idNodeVersion int(12) unsigned NOT NULL default 0,
  idMetadataVersion int(12) unsigned NOT NULL default 0,
  PRIMARY KEY (id),
  UNIQUE KEY `rel` (`idNodeVersion`,`idMetadataVersion`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `RelNodeMetadata`;
CREATE TABLE `RelNodeMetadata` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdNode` int(12) unsigned NOT NULL default '0',
  `IdMetadata` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdRel`)
) ENGINE=MyISAM COMMENT='Tabla de relación entre metadatas y nodos de Ximdex';
