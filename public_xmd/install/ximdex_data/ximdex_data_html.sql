-- SQL DATA FOR HTML DOCUMENTS

-- Pipeline (5)
INSERT INTO `Pipelines` (`id`, `Pipeline`) VALUES (5, 'PublishHTMLDoc');

INSERT INTO `PipeTransitions` (`id`, `IdStatusFrom`, `IdStatusTo`, `IdPipeProcess`, `Cacheable`, `Name`, `Callback`) VALUES 
    ('10', NULL, '3', '6', '1', 'PrepareHTML', 'PrepareHTML'), 
    ('11', '3', '6', '7', '0', 'PublishHTML', 'FilterMacros');

INSERT INTO `PipeProcess` (`id`, `IdTransitionFrom`, `IdTransitionTo`, `IdPipeline`, `Name`) VALUES 
    ('6', NULL, '10', '5', 'HTMLToPrepared'), 
    ('7', '10', '11', '5', 'HTMLToPublished');

-- HTMLlayoutsFolder (5105)

INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`
	, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `IsHidden`, `CanDenyDeletion`, `isGenerator`
	, `IsEnriching`, `System`, `Module`) VALUES 
	(5105, 'HTMLlayoutsFolder', 'FolderNode', 'folder_template_view', 'Folder of HTML layouts', 1, 1, 0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, NULL);
	
INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES
	(7404, 5105, 'Add layouts', 'fileupload_common_multiple', 'add_template_pvd.png', 'Add a set of layouts to the server', 9, NULL, 0
		, 'type=layout', 0),
	(7405, 5105, 'Add a new empty layout', 'newemptynode', 'add_file_common.png', 'Create a new empty HMTL layout file', 9, NULL, 0, '', 0),
	(7406, 5105, 'Copy', 'copy', 'copiar_carpeta_ximdoc.png', 'Copy a layouts folder to another destination', 31, NULL, 0, '', 0),
	(7407, 5105, 'Download all layout files', 'filedownload_multiple', 'download_template_view.png', 'Download all layouts', 80, NULL, 0, '', 1),
	(7408, 5105, 'Semantic Tags', 'setmetadata', 'change_next_state.png', 'Managing semantic tags related to the current node', 999
		, 'ximTAGS', 0, NULL, 0);

INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7404, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7405, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7406, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7407, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7408, 0, 1, 3);

INSERT INTO `NodeConstructors` (`IdNodeType`, `IdAction`) VALUES (5105, 6011);
INSERT INTO `NodeConstructors` (`IdNodeType`, `IdAction`) VALUES (5105, 6012);
INSERT INTO `NodeConstructors` (`IdNodeType`, `IdAction`) VALUES (5105, 6013);
INSERT INTO `NodeConstructors` (`IdNodeType`, `IdAction`) VALUES (5105, 6014);

INSERT INTO `NodetypeModes` (`IdNodeType`, `Mode`) VALUES
	(5105, 'C'),
	(5105, 'R'),
	(5105, 'U'),
	(5105, 'D');

INSERT INTO `RelNodeTypeMimeType` (`idNodeType`, `extension`, `filter`) VALUES (5105, '', '');

INSERT INTO `NodeAllowedContents` (`IdNodeType`, `NodeType`) VALUES (5013, 5105);
INSERT INTO `NodeAllowedContents` (`IdNodeType`, `NodeType`) VALUES (5014, 5105);
INSERT INTO `NodeAllowedContents` (`IdNodeType`, `NodeType`) VALUES (5015, 5105);


INSERT INTO `NodeDefaultContents` (`IdNodeType`, `NodeType`, `Name`) VALUES (5013, 5105, 'layouts');
INSERT INTO `NodeDefaultContents` (`IdNodeType`, `NodeType`, `Name`) VALUES (5014, 5105, 'layouts');
INSERT INTO `NodeDefaultContents` (`IdNodeType`, `NodeType`, `Name`) VALUES (5015, 5105, 'layouts');

INSERT INTO `Config` (`ConfigKey`, `ConfigValue`) VALUES ('HTMLLayoutsDirName', 'layouts');

-- HTMLComponentsFolder (5101)

INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`
	, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `IsHidden`, `CanDenyDeletion`, `isGenerator`
	, `IsEnriching`, `System`, `Module`) VALUES
	(5101, 'HTMLComponentsFolder', 'FolderNode', 'folder_template_view', 'Folder of HTML components', 1, 1, 0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, NULL);

INSERT INTO `NodetypeModes` (`IdNodeType`, `Mode`) VALUES
	(5101, 'C'),
	(5101, 'R'),
	(5101, 'U'),
	(5101, 'D');

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES
	(7413, 5101, 'Semantic Tags', 'setmetadata', 'change_next_state.png', 'Managing semantic tags related to the current node.', 999
		, 'ximTAGS', 0, NULL, 0),
	(7414, 5101, 'Copy', 'copy', 'copiar_carpeta_ximdoc.png', 'Copy a HTML components folder to another destination', 31, NULL, 0, '', 0),
	(7415, 5101, 'Download all components', 'filedownload_multiple', 'download_template_view.png', 'Download all HTML components files', 80
			, NULL, 0, '', 1),
	(7416, 5101, 'Upload components files', 'fileupload_common_multiple', 'add_template_pvd.png', 'Add a set of HTML components to the server'
		, 9, NULL, 0, 'type=json', 0),
	(7417, 5101, 'Add a new empty component', 'newemptynode', 'add_file_common.png', 'Create a new empty HTML component file', 9, NULL, 0, '', 0);

INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7413, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7414, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7415, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7416, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7417, 0, 1, 3);

INSERT INTO `Config` (`ConfigKey`, `ConfigValue`) VALUES ('HTMLComponentsDirName', 'components');

INSERT INTO `NodeAllowedContents` (`IdNodeType`, `NodeType`) VALUES (5105, 5101);

INSERT INTO `NodeDefaultContents` (`IdNodeType`, `NodeType`, `Name`) VALUES (5105, 5101, 'components');

INSERT INTO `RelNodeTypeMimeType` (`idNodeType`, `extension`, `filter`) VALUES (5101, ';json;', 'json');

-- HTMLViewsFolder (5106)

INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`
	, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `IsHidden`, `CanDenyDeletion`, `isGenerator`
	, `IsEnriching`, `System`, `Module`) VALUES
	(5106, 'HTMLViewsFolder', 'FolderNode', 'folder_template_view', 'Folder of HTML views', 1, 1, 0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, NULL);

INSERT INTO `NodetypeModes` (`IdNodeType`, `Mode`) VALUES
	(5106, 'C'),
	(5106, 'R'),
	(5106, 'U'),
	(5106, 'D');

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES
	(7418, 5106, 'Semantic Tags', 'setmetadata', 'change_next_state.png', 'Managing semantic tags related to the current node.', 999
		, 'ximTAGS', 0, NULL, 0),
	(7419, 5106, 'Copy', 'copy', 'copiar_carpeta_ximdoc.png', 'Copy a HTML sections folder to another destination', 31, NULL, 0, '', 0),
	(7420, 5106, 'Download all sections', 'filedownload_multiple', 'download_template_view.png', 'Download all HTML views files', 80, NULL, 0, '', 1),
	(7421, 5106, 'Upload views files', 'fileupload_common_multiple', 'add_template_pvd.png', 'Add a set of HTML views to the server', 9, NULL
		, 0, 'type=html', 0),
	(7422, 5106, 'Add a new empty view', 'newemptynode', 'add_file_common.png', 'Create a new empty HTML view file', 9, NULL, 0, '', 0);

INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7418, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7419, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7420, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7421, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7422, 0, 1, 3);

INSERT INTO `Config` (`ConfigKey`, `ConfigValue`) VALUES ('HTMLViewsDirName', 'views');

INSERT INTO `NodeDefaultContents` (`IdNodeType`, `NodeType`, `Name`) VALUES (5105, 5106, 'views');

INSERT INTO `RelNodeTypeMimeType` (`idNodeType`, `extension`, `filter`) VALUES (5106, ';html;', 'html');

INSERT INTO `NodeAllowedContents` (`IdNodeType`, `NodeType`) VALUES (5105, 5106);

-- HTMLLayout (5100)

INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`
	, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `IsHidden`, `CanDenyDeletion`, `isGenerator`
	, `IsEnriching`, `System`, `Module`) VALUES (5100, 'HTMLLayout', 'HTMLLayoutNode', 'xml_document', 'JSON layout schema for HTML documents', 1
	, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, NULL);

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES
	(7400, 5100, 'Semantic Tags', 'setmetadata', 'change_next_state.png', 'Managing semantic tags related to the current node.', 999, 'ximTAGS'
		, 0, NULL, 0),
	(7401, 5100, 'Delete layout', 'deletenode', 'delete_template_view.png', 'Delete layout schema', 75, NULL, 0, NULL, 0),
	(7402, 5100, 'Edit layout', 'edittext', 'edit_template_view.png', 'Edit layout schema', 20, NULL, 0, NULL, 0),
	(7403, 5100, 'Modify properties', 'renamenode', 'modiy_templateview', 'Modify properties of a JSON schema', 60, NULL, 0, NULL, 0);

INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7400, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7401, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7402, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7403, 0, 1, 3);

INSERT INTO `RelNodeTypeMimeType` (`idNodeType`, `extension`, `filter`) VALUES (5100, ';json;', 'json');

INSERT INTO `NodeAllowedContents` (`IdNodeType`, `NodeType`) VALUES (5105, 5100);

-- HTMLComponent (5107)

INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`
	, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `IsHidden`, `CanDenyDeletion`, `isGenerator`
	, `IsEnriching`, `System`, `Module`) VALUES (5107, 'HTMLComponent', 'HTMLComponentNode', 'xml_document', 'JSON component for HTML documents', 1
	, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, NULL);

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES
	(7409, 5107, 'Semantic Tags', 'setmetadata', 'change_next_state.png', 'Managing semantic tags related to the current node.', 999, 'ximTAGS'
		, 0, NULL, 0),
	(7410, 5107, 'Delete component', 'deletenode', 'delete_template_view.png', 'Delete a component schema', 75, NULL, 0, NULL, 0),
	(7411, 5107, 'Edit component', 'edittext', 'edit_template_view.png', 'Edit a component schema', 20, NULL, 0, NULL, 0),
	(7412, 5107, 'Modify properties', 'renamenode', 'modiy_templateview', 'Modify properties of a component schema', 60, NULL, 0, NULL, 0);

INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7409, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7410, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7411, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7412, 0, 1, 3);

INSERT INTO `RelNodeTypeMimeType` (`idNodeType`, `extension`, `filter`) VALUES (5107, ';json;', 'json');

INSERT INTO `NodeAllowedContents` (`IdNodeType`, `NodeType`) VALUES (5101, 5107);

-- HTMLView (5108)

INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`
	, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `IsHidden`, `CanDenyDeletion`, `isGenerator`
	, `IsEnriching`, `System`, `Module`) VALUES (5108, 'HTMLView', 'HTMLViewNode', 'xml_document', 'HTML view for HTML documents', 1
	, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, NULL);

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES
	(7470, 5108, 'Semantic Tags', 'setmetadata', 'change_next_state.png', 'Managing semantic tags related to the current node.', 999, 'ximTAGS'
		, 0, NULL, 0),
	(7471, 5108, 'Delete view', 'deletenode', 'delete_template_view.png', 'Delete a HTML view', 75, NULL, 0, NULL, 0),
	(7472, 5108, 'Edit view', 'edittext', 'edit_template_view.png', 'Edit a HTML view', 20, NULL, 0, NULL, 0),
	(7473, 5108, 'Modify properties', 'renamenode', 'modiy_templateview', 'Modify properties of a HTML view', 60, NULL, 0, NULL, 0);

INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7470, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7471, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7472, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7473, 0, 1, 3);

INSERT INTO `RelNodeTypeMimeType` (`idNodeType`, `extension`, `filter`) VALUES (5108, ';html;', 'html');

INSERT INTO `NodeAllowedContents` (`IdNodeType`, `NodeType`) VALUES (5106, 5108);

-- JsRootFolder (5090)
	
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`
	, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `IsHidden`, `CanDenyDeletion`
	, `isGenerator`, `IsEnriching`, `System`, `Module`) VALUES
	(5090, 'JsRootFolder', 'FolderNode', 'folder_import', 'Root of Javascript folder', 1, 1, 0, 0, 1, 0, 0, 0, 1, 0, 1, 0, 0, 1, NULL);
	
INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES
	(7425, 5090, 'Add new JS folder', 'addfoldernode', 'add_folder_css.png', 'Create a new javascript folder', 11, NULL, 0, '', 0),
	(7426, 5090, 'Upload JS files', 'fileupload_common_multiple', 'add_file_css.png', 'Add a set of javascript files to the server', 10
		, NULL, 0, 'type=js', 0),
	(7427, 5090, 'Semantic Tags', 'setmetadata', 'change_next_state.png', 'Managing semantic tags related to the current node.', 999
		, 'ximTAGS', 0, NULL, 0),
	(7428, 5090, 'Publish section', 'publicatesection', 'publicate_section.png', 'Publish a section massively', 70, NULL, 1, '', 0),
	(7429, 5090, 'Download all JS files', 'filedownload_multiple', 'download_file_css.png', 'Download all javascript files', 80, NULL, 0, '', 1),
	(7430, 5090, 'Add an empty JS document', 'newemptynode', 'add_file_common.png', 'Create a new javascript empty file', 9, NULL, 0, '', 0);

INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7425, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7426, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7427, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7428, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7429, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7430, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7425, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7426, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7427, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7428, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7429, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7430, 8, 1, 3);

INSERT INTO `NodeConstructors` (`IdNodeType`, `IdAction`) VALUES (5090, 6012);

INSERT INTO `NodeDefaultContents` (`IdNodeType`, `NodeType`, `Name`) VALUES (5014, 5090, 'js');

INSERT INTO `NodetypeModes` (`IdNodeType`, `Mode`) VALUES
	(5090, 'C'),
	(5090, 'R'),
	(5090, 'U'),
	(5090, 'D');

INSERT INTO `RelNodeTypeMimeType` (`idNodeType`, `extension`, `filter`) VALUES (5090, '', '');

INSERT INTO `NodeAllowedContents` (`IdNodeType`, `NodeType`, `Amount`) VALUES (5014, 5090, 1);

-- JsFolder (5091)

INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`
	, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `IsHidden`, `CanDenyDeletion`, `isGenerator`
	, `IsEnriching`, `System`, `Module`) VALUES
	(5091, 'JsFolder', 'FolderNode', 'folder_import', 'Javascript folder', 1, 1, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 1, NULL);

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES
	(7431, 5091, 'Add JS files', 'fileupload_common_multiple', 'add_file_css.png', 'Add a set of javascript files to the server', 10
		, NULL, 0, 'type=js', 0),
	(7432, 5091, 'Add JS folder', 'addfoldernode', 'add_folder_css.png', 'Create a new javascript folder', 11, NULL, 0, '', 0),
	(7433, 5091, 'Add empty JS document', 'newemptynode', 'add_file_common.png', 'Create a new javascript empty file', 9, NULL, 0, '', 0),
	(7434, 5091, 'Copy', 'copy', 'copiar_seccion.png', 'Copy a javascript subfolder to another destination', 31, NULL, 0, '', 0),
	(7435, 5091, 'Change name', 'renamenode', 'change_name_folder_css.png', 'Change folder name', 61, NULL, 0, '', 0),
	(7436, 5091, 'Publish section', 'publicatesection', 'publicate_section.png', 'Publish a section massively', 70, NULL, 1, '', 0),
	(7437, 5091, 'Delete folder', 'deletenode', 'delete_folder_css.png', 'Delete selected folder', 76, NULL, 1, '', 0),
	(7438, 5091, 'Download all JS files', 'filedownload_multiple', 'download_file_css.png', 'Download all javascript files', 80, NULL, 0, '', 1),
	(7439, 5091, 'Semantic Tags', 'setmetadata', 'change_next_state.png', 'Managing semantic tags related to the current node.', 999
		, 'ximTAGS', 0, NULL, 0);

INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7431, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7432, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7433, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7434, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7435, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7436, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7431, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7432, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7433, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7434, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7435, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7436, 8, 1, 3);

INSERT INTO `NodeAllowedContents` (`IdNodeType`, `NodeType`, `Amount`) VALUES (5090, 5091, 0), (5091, 5091, 0);

INSERT INTO `NodetypeModes` (`IdNodeType`, `Mode`, `IdAction`) VALUES
	(5091, 'C', 7432),
	(5091, 'R', NULL),
	(5091, 'U', 7435),
	(5091, 'D', 7437);

INSERT INTO `RelNodeTypeMimeType` (`idNodeType`, `extension`, `filter`) VALUES (5091, '', '');

-- JsFile (5092)

INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`
	, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `IsHidden`, `CanDenyDeletion`, `isGenerator`
	, `IsEnriching`, `System`, `Module`) VALUES 
	(5092, 'JsFile', 'FileNode', 'css_document', 'Java Script document', 1, 1, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, NULL);

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES
	(7440, 5092, 'Edit', 'edittext', 'edit_file_css.png', 'Edit content of javascript document', 20, NULL, 0, '', 0),
	(7441, 5092, 'Copy', 'copy', 'copiar_documento.png', 'Copy a javascript document to another destination', 30, NULL, 0, '', 0),
	(7442, 5092, 'Move node', 'movenode', 'move_node.png', 'Move a node', 40, NULL, 1, '', 0),
	(7443, 5092, 'Change name', 'renamenode', 'change_name_file_css.png', 'Change file name on import folder', 61, NULL, 0, '', 0),
	(7444, 5092, 'Publish', 'workflow_forward', 'change_next_state.png', 'Move a javascript document to the next state', 70, NULL, 0, '', 0),
	(7445, 5092, 'Move to previous state', 'workflow_backward', 'change_last_state.png', 'Move a text document to the previous state', -70
		, NULL, 0, '', 0),
	(7446, 5092, 'Version manager', 'manageversions', 'manage_versions.png', 'Manage repository of versions', 73, NULL, 0, '', 0),
	(7447, 5092, 'Delete', 'deletenode', 'delete_file_css.png', 'Delete file of import folder', 76, NULL, 1, '', 0),
	(7448, 5092, 'Download file', 'filedownload', 'download_file_css.png', 'Download a file to a local hard disk', 80, NULL, 0, '', 0),
	(7449, 5092, 'Semantic Tags', 'setmetadata', 'change_next_state.png', 'Managing semantic tags related to the current node.', 999
		, 'ximTAGS', 0, NULL, 0);

INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7440, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7440, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7441, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7441, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7442, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7442, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7443, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7443, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7444, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7444, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7445, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7445, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7446, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7446, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7447, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7447, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7448, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7448, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7449, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7449, 8, 1, 3);

INSERT INTO `NodeAllowedContents` (`IdNodeType`, `NodeType`, `Amount`) VALUES (5090, 5092, 0), (5091, 5092, 0);

INSERT INTO `NodetypeModes` (`IdNodeType`, `Mode`, `IdAction`) VALUES
	(5092, 'R', NULL),
	(5092, 'U', 7440),
	(5092, 'D', 7447);

INSERT INTO `RelNodeTypeMimeType` (`idNodeType`, `extension`, `filter`) VALUES (5092, ';js;', 'js');

-- HTMLContainer (5103)

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES 
	(7424, 5018, 'Add new HTML document', 'createxmlcontainer', 'add_xml.png', 'Create a new HTML document in several languages', 11
	, NULL, 0, 'type=HTML', 0);

INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7424, 0, 1, 3);

INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`
	, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `IsHidden`, `CanDenyDeletion`, `isGenerator`
	, `IsEnriching`, `System`, `Module`) VALUES
	(5103, 'HTMLContainer', 'XmlContainerNode', 'contenedordoc', 'Container of HTML documents', 1, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, NULL);

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES
	(7450, 5103, 'Add new language', 'addlangxmlcontainer', 'add_language_xml.png', 'Add a document with a different language', 10, NULL, 0, '', 0),
	(7451, 5103, 'Copy', 'copy', 'copiar_carpeta_ximdoc.png', 'Copy a document to another destination', 30, NULL, 0, '', 0),
	(7452, 5103, 'Move node', 'movenode', 'move_node.png', 'Move a node', 40, NULL, 0, '', 0),
	(7453, 5103, 'Change name of HTML document', 'renamenode', 'change_name_xml.png', 'Change the document name and all its language versions'
		, 60, NULL, 0, '', 0),
	(7454, 5103, 'Delete document', 'deletenode', 'delete_xml.png', 'Delete HTML document in all its languages', 75, NULL, 1, '', 0),
	(7455, 5103, 'Edit metadata', 'managemetadata', 'xix.png', 'Edit the metadata info', 20, NULL, 0, NULL, 0),
	(7456, 5103, 'Modify properties', 'manageproperties', 'xix.png', 'Modify properties', 60, NULL, 0, NULL, 0),
	(7457, 5103, 'Semantic Tags', 'setmetadata', 'change_next_state.png', 'Managing semantic tags related to the current node.', 999
		, 'ximTAGS', 0, NULL, 0);

INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7450, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7451, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7452, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7453, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7454, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7455, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7456, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7457, 0, 1, 3);
	
INSERT INTO `NodeConstructors` (`IdNodeType`, `IdAction`) VALUES (5103, 7424);

INSERT INTO `NodetypeModes` (`IdNodeType`, `Mode`, `IdAction`) VALUES
	(5103, 'C', 7424),
	(5103, 'R', NULL),
	(5103, 'U', 7450),
	(5103, 'D', 7454);

-- HTMLDocument (5104)

INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`
	, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `IsHidden`, `CanDenyDeletion`, `isGenerator`
	, `IsEnriching`, `System`, `Module`) VALUES 
	(5104, 'HtmlDocument', 'XmlDocumentNode', 'doc', 'HTML document', 1, 1, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, NULL);

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES
	(7458, 5104, 'Edit in text mode', 'edittext', 'edit_file_xml_txt.png', 'Edit content of HTML in plain text', 21, NULL, 0, 'type=text', 0),
	(7459, 5104, 'Edit HTML document', 'xedit', 'edit_file_xml.png', 'Edit content of HTML document with the wysiwyg editor', 20, NULL, 0
	       , 'type=html', 0),
	(7460, 5104, 'Publish', 'workflow_forward', 'change_next_state.png', 'Move to the next state', 70, NULL, 0, '', 0),
	(7461, 5104, 'Move to previous state', 'workflow_backward', 'change_last_state.png', 'Move to the previous state', -70, NULL, 0, '', 0),
	(7462, 5104, 'Expire document', 'expiredoc', 'expire_section.png', 'Expire a document', 71, NULL, 0, '', 0),
	(7463, 5104, 'Version manager', 'manageversions', 'manage_versions.png', 'Manage repository of versions', 73, NULL, 0, '', 0),
	(7464, 5104, 'Symbolic link', 'xmlsetlink', 'file_xml_symbolic.png', 'Modify document which borrows the content', 74, NULL, 0, '', 0),
	(7465, 5104, 'Delete document', 'deletenode', 'delete_file_xml.png', 'Delete selected HTML document', 75, NULL, 1, '', 0),
	(7466, 5104, 'Preview', 'preview', 'xix.png', 'Preview of the document', 80, NULL, 0, '', 0),
	(7467, 5104, 'Semantic Tags', 'setmetadata', 'change_next_state.png', 'Managing semantic tags related to the current node.', 999
	       , 'ximTAGS', 0, NULL, 0);

INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7458, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7458, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7459, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7459, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7460, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7460, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7461, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7461, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7462, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7462, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7463, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7463, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7464, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7464, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7465, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7465, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7466, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7466, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7467, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (201, 7467, 8, 1, 3);

INSERT INTO `NodeAllowedContents` (`IdNodeType`, `NodeType`, `Amount`) VALUES
	(5018, 5103, 0),
	(5103, 5104, 0);

INSERT INTO `NodeConstructors` (`IdNodeType`, `IdAction`) VALUES (5104, 7450);

INSERT INTO `NodetypeModes` (`IdNodeType`, `Mode`, `IdAction`) VALUES
	(5104, 'C', 7450),
	(5104, 'R', NULL),
	(5104, 'U', 7458),
	(5104, 'D', 7465);

-- INSERT INTO `RelNodeTypeMetadata` (`idNodeType`, `force`) VALUES (5104, 0);

INSERT INTO `RelNodeTypeMimeType` (`idNodeType`, `extension`, `filter`) VALUES (5104, '', '');

-- HTML EDITOR CONFIGURATION

INSERT INTO `Config` (`ConfigKey`, `ConfigValue`) VALUES ('HTMLEditorURL', null), ('HTMLEditorEnabled', '0');