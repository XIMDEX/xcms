CREATE TABLE `Actions` (
  `IdAction` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdNodeType` int(12) unsigned NOT NULL DEFAULT '0',
  `Name` varchar(100) NOT NULL DEFAULT '',
  `Command` varchar(100) NOT NULL DEFAULT '',
  `Icon` varchar(100) NOT NULL DEFAULT '',
  `Description` varchar(255) DEFAULT NULL,
  `Sort` int(12) DEFAULT NULL,
  `Module` varchar(250) DEFAULT NULL,
  `Multiple` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Params` varchar(255) DEFAULT NULL,
  `IsBulk` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdAction`),
  UNIQUE KEY `IdAction` (`IdAction`),
  KEY `IdAction_2` (`IdAction`,`IdNodeType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Commands which can be executed in a node';

CREATE TABLE `ActionsStats` (
  `IdStat` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `IdAction` int(11) unsigned DEFAULT NULL,
  `IdNode` int(11) unsigned DEFAULT NULL,
  `IdUser` int(11) unsigned DEFAULT NULL,
  `Method` varchar(255) DEFAULT NULL,
  `TimeStamp` int(11) unsigned NOT NULL,
  `Duration` float(11,6) unsigned NOT NULL,
  PRIMARY KEY (`IdStat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Usage stats for actions';

CREATE TABLE `Channels` (
  `IdChannel` int(12) unsigned NOT NULL,
  `Name` varchar(255) NOT NULL DEFAULT '0',
  `Description` varchar(255) DEFAULT '0',
  `DefaultExtension` varchar(255) DEFAULT NULL,
  `Format` varchar(255) DEFAULT NULL,
  `Filter` varchar(255) DEFAULT NULL,
  `RenderMode` varchar(255) DEFAULT NULL,
  `OutputType` varchar(100) DEFAULT NULL,
  `Default_Channel` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdChannel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Available channels used to transform content';

CREATE TABLE `Config` (
  `IdConfig` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `ConfigKey` varchar(255) NOT NULL DEFAULT '0',
  `ConfigValue` TEXT,
  PRIMARY KEY (`IdConfig`),
  UNIQUE KEY `ConfigKey` (`ConfigKey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table with configuration parameters of Ximdex CMS';

CREATE TABLE `Contexts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Context` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Context` (`Context`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `DependenceTypes` (
  `IdDepType` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `Type` varchar(31) NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdDepType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Dependencies` (
  `IdDep` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdNodeMaster` int(12) unsigned NOT NULL DEFAULT '0',
  `IdNodeDependent` int(12) unsigned NOT NULL DEFAULT '0',
  `DepType` int(6) NOT NULL DEFAULT '0',
  `version` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdDep`),
  KEY `IdNodeMaster` (`IdNodeMaster`),
  KEY `IdNodeDependent` (`IdNodeDependent`),
  KEY `DepType` (`DepType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='All the dependencies by type on Ximdex CMS';

CREATE TABLE `Encodes` (
  `IdEncode` varchar(255) NOT NULL DEFAULT '',
  `Description` varchar(255) DEFAULT '0',
  PRIMARY KEY (`IdEncode`),
  UNIQUE KEY `IdEncode` (`IdEncode`),
  KEY `IdEncode_2` (`IdEncode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Available encodings on Ximdex CMS';

CREATE TABLE `FastTraverse` (
  `IdNode` int(12) unsigned NOT NULL DEFAULT '0',
  `IdChild` int(12) unsigned NOT NULL DEFAULT '0',
  `Depth` int(12) unsigned DEFAULT '0',
  PRIMARY KEY (`IdNode`,`IdChild`),
  KEY `IdN` (`IdNode`),
  KEY `IdC` (`IdChild`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Fast scan of node hierarchies';

CREATE TABLE `Groups` (
  `IdGroup` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdGroup`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Groups defined on the system';

CREATE TABLE `IsoCodes` (
  `IdIsoCode` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `Iso2` char(2) DEFAULT NULL,
  `Iso3` char(3) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`IdIsoCode`),
  UNIQUE KEY `name` (`Name`),
  UNIQUE KEY `iso2` (`Iso2`),
  UNIQUE KEY `iso3` (`Iso3`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='ISO codes supported for languages';

CREATE TABLE `Languages` (
  `IdLanguage` int(12) unsigned NOT NULL,
  `Name` varchar(255) NOT NULL DEFAULT '',
  `IsoName` varchar(255) DEFAULT NULL,
  `Enabled` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`IdLanguage`),
  UNIQUE KEY `Name` (`Name`),
  UNIQUE KEY `IdLanguage` (`IdLanguage`),
  KEY `IdLanguage_2` (`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Ximdex CMS defined languages';

CREATE TABLE `Links` (
  `IdLink` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `Url` blob NOT NULL,
  `Error` int(12) unsigned DEFAULT NULL,
  `ErrorString` varchar(255) DEFAULT NULL,
  `CheckTime` int(12) unsigned DEFAULT NULL,
  PRIMARY KEY (`IdLink`),
  UNIQUE KEY `IdLink` (`IdLink`),
  KEY `IdLink_2` (`IdLink`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table of link manager of Ximdex';

CREATE TABLE `List` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `IdList` int(11) NOT NULL,
  `Name` varchar(250) NOT NULL,
  `Description` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `List_Label` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(250) NOT NULL,
  `Description` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Locales` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Code` varchar(5) NOT NULL COMMENT 'Locale in ISO 639 ',
  `Name` varchar(20) NOT NULL COMMENT 'Lang name',
  `Enabled` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Enabled(1)|Not Enabled(0)',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Ximdex CMS default languages';

CREATE TABLE `Messages` (
  `IdMessage` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdFrom` int(12) unsigned NOT NULL DEFAULT '0',
  `IdOwner` int(12) unsigned NOT NULL DEFAULT '0',
  `ToString` varchar(255) DEFAULT NULL,
  `Folder` int(12) unsigned NOT NULL DEFAULT '1',
  `Subject` varchar(255) DEFAULT NULL,
  `Content` blob,
  `IsRead` int(1) unsigned NOT NULL DEFAULT '0',
  `FechaHora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`IdMessage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Messages sent by Ximdex CMS. Deprecated?';

CREATE TABLE `Namespaces` (
  `idNamespace` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `service` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `nemo` varchar(255) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `recursive` int(8) NOT NULL DEFAULT '0',
  `category` varchar(255) NOT NULL,
  `isSemantic` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idNamespace`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Namespaces for semantic tagging.';

CREATE TABLE `NoActionsInNode` (
  `IdNode` int(11) NOT NULL,
  `IdAction` int(11) NOT NULL COMMENT 'Actions not allowed for a Node',
  PRIMARY KEY (`IdNode`,`IdAction`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='List of Actions not allowed in a Node';

CREATE TABLE `NodeAllowedContents` (
  `IdNodeAllowedContent` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdNodeType` int(12) unsigned NOT NULL DEFAULT '0',
  `NodeType` int(12) unsigned NOT NULL DEFAULT '0',
  `Amount` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdNodeAllowedContent`),
  UNIQUE KEY `UniqeAmmount` (`IdNodeType`,`NodeType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Allowed node types into each type of node';

CREATE TABLE `NodeConstructors` (
  `IdNodeConstructor` int(11) NOT NULL AUTO_INCREMENT,
  `IdNodeType` int(11) NOT NULL,
  `IdAction` int(11) NOT NULL,
  PRIMARY KEY (`IdNodeConstructor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `NodeDefaultContents` (
  `IdNodeDefaultContent` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdNodeType` int(12) unsigned NOT NULL DEFAULT '0',
  `NodeType` int(12) unsigned NOT NULL DEFAULT '0',
  `Name` varchar(255) NOT NULL DEFAULT '0',
  `State` int(12) unsigned DEFAULT NULL,
  `Params` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`IdNodeDefaultContent`),
  UNIQUE KEY `UniqueName` (`Name`,`IdNodeType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Default content of each node';

CREATE TABLE `NodeDependencies` (
  `IdNode` int(12) unsigned NOT NULL DEFAULT '0',
  `IdResource` int(12) unsigned NOT NULL DEFAULT '0',
  `IdChannel` int(12) unsigned DEFAULT NULL,
  PRIMARY KEY (`IdNode`,`IdResource`),
  KEY `IdResource` (`IdResource`),
  KEY `IdNode` (`IdNode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Dependencies between nodes in Ximdex CMS';

CREATE TABLE `NodeEdition` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `IdNode` int(11) unsigned NOT NULL,
  `IdUser` int(11) unsigned NOT NULL,
  `StartTime` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='XML edition information. For concurrency issues';

CREATE TABLE `NodeNameTranslations` (
  `IdRel` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdNode` int(12) unsigned NOT NULL DEFAULT '0',
  `IdLanguage` int(12) unsigned NOT NULL DEFAULT '0',
  `Name` varchar(255) DEFAULT '0',
  PRIMARY KEY (`IdRel`),
  UNIQUE KEY `NodeLanguage` (`IdNode`,`IdLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Alias for nodes in other languages';

CREATE TABLE `NodeProperties` (
  `IdNodeProperty` int(11) NOT NULL AUTO_INCREMENT,
  `IdNode` int(11) NOT NULL,
  `Property` varchar(255) NOT NULL,
  `Value` text NOT NULL,
  PRIMARY KEY (`IdNodeProperty`),
  KEY `IdNode` (`IdNode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Nodes` (
  `IdNode` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdParent` int(12) unsigned DEFAULT '0',
  `IdNodeType` int(12) unsigned NOT NULL DEFAULT '0',
  `Name` varchar(255) NOT NULL DEFAULT '0',
  `IdState` int(12) unsigned DEFAULT '0',
  `BlockTime` int(12) DEFAULT '0',
  `BlockUser` int(12) unsigned DEFAULT NULL,
  `CreationDate` int(12) unsigned DEFAULT '0',
  `ModificationDate` int(12) unsigned DEFAULT '0',
  `Description` varchar(255) DEFAULT NULL,
  `SharedWorkflow` int(12) unsigned DEFAULT NULL,
  `Path` text,
  `sortorder` int(11) DEFAULT '0',
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`IdNode`),
  UNIQUE KEY `UniqueName` (`Name`,`IdParent`),
  KEY `IdNode_2` (`IdNode`,`IdParent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table of system nodes';

CREATE TABLE `NodeSets` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `U_SET` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `NodetypeModes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `IdNodeType` int(11) NOT NULL,
  `Mode` enum('C','R','U','D') NOT NULL,
  `IdAction` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IdNodeType` (`IdNodeType`,`IdAction`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `NodeTypes` (
  `IdNodeType` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL DEFAULT '0',
  `Class` varchar(255) NOT NULL DEFAULT '',
  `Icon` varchar(255) NOT NULL DEFAULT '',
  `Description` varchar(255) DEFAULT NULL,
  `IsRenderizable` int(1) unsigned DEFAULT NULL,
  `HasFSEntity` int(1) unsigned DEFAULT NULL,
  `CanAttachGroups` int(1) unsigned DEFAULT NULL,
  `IsSection` int(1) unsigned DEFAULT NULL,
  `IsFolder` int(1) unsigned DEFAULT NULL,
  `IsVirtualFolder` int(1) unsigned DEFAULT NULL,
  `IsPlainFile` int(1) unsigned DEFAULT NULL,
  `IsStructuredDocument` int(1) unsigned DEFAULT NULL,
  `IsPublishable` int(1) unsigned DEFAULT NULL,
  `IsHidden` int(1) unsigned DEFAULT '0',
  `CanDenyDeletion` int(1) unsigned DEFAULT NULL,
  `isGenerator` tinyint(1) DEFAULT '0',
  `IsEnriching` tinyint(1) DEFAULT '0',
  `System` int(1) unsigned DEFAULT NULL,
  `Module` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`IdNodeType`),
  UNIQUE KEY `IdType` (`Name`),
  KEY `IdType_2` (`IdNodeType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Nodetypes used on Ximdex CMS';

CREATE TABLE `Permissions` (
  `IdPermission` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL DEFAULT '0',
  `Description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`IdPermission`),
  UNIQUE KEY `IdName` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table of system permits';

CREATE TABLE `PipeCaches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `IdVersion` int(11) NOT NULL,
  `IdPipeTransition` int(11) NOT NULL,
  `File` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `PipeCacheTemplates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `NodeId` int(11) NOT NULL,
  `DocIdVersion` int(11) NOT NULL,
  `TemplateIdVersion` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Pipelines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Pipeline` varchar(255) NOT NULL,
  `IdNode` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IdNode` (`IdNode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `PipeNodeTypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `IdPipeline` int(11) NOT NULL,
  `IdNodeType` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `PipeProcess` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `IdTransitionFrom` int(11) DEFAULT NULL,
  `IdTransitionTo` int(11) NOT NULL,
  `IdPipeline` int(11) DEFAULT NULL,
  `Name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `PipeProperties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `IdPipeTransition` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `PipePropertyValues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `IdPipeProperty` int(11) NOT NULL,
  `IdPipeCache` int(11) NOT NULL,
  `Value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `PipeStatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Description` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `PipeTransitions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `IdStatusFrom` int(11) DEFAULT NULL,
  `IdStatusTo` int(11) NOT NULL,
  `IdPipeProcess` int(11) DEFAULT NULL,
  `Cacheable` tinyint(1) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Callback` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `PortalVersions` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdPortal` int(12) unsigned DEFAULT '0',
  `Version` int(12) unsigned DEFAULT '0',
  `TimeStamp` int(12) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Protocols` (
  `IdProtocol` varchar(255) NOT NULL DEFAULT '',
  `DefaultPort` int(12) unsigned DEFAULT '0',
  `Description` varchar(255) DEFAULT '0',
  `UsePassword` int(1) unsigned DEFAULT '0',
  PRIMARY KEY (`IdProtocol`),
  UNIQUE KEY `IdProtocol` (`IdProtocol`),
  KEY `IdProtocol_2` (`IdProtocol`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Protocols to synchronize supported by Ximdex CMS';

CREATE TABLE `RelBulletinXimlet` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `source` int(12) unsigned NOT NULL DEFAULT '0',
  `target` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RelFramesPortal` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdPortalVersion` int(12) unsigned DEFAULT '0',
  `IdFrame` int(12) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `PortalFrame` (`IdPortalVersion`,`IdFrame`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RelGroupsNodes` (
  `IdRel` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdGroup` int(12) unsigned NOT NULL DEFAULT '0',
  `IdNode` int(12) unsigned NOT NULL DEFAULT '0',
  `IdRole` int(12) unsigned DEFAULT NULL,
  PRIMARY KEY (`IdRel`),
  UNIQUE KEY `uniq` (`IdNode`,`IdGroup`),
  KEY `IdGroup` (`IdGroup`),
  KEY `IdNode` (`IdNode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Association of user groups with nodes';

CREATE TABLE `RelLinkDescriptions` (
  `IdRel` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdLink` int(12) unsigned NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`IdRel`),
  UNIQUE KEY `Description` (`IdLink`,`Description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table of descriptions of Ximdex links';

CREATE TABLE `RelNode2Asset` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `source` int(12) unsigned NOT NULL DEFAULT '0',
  `target` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rel` (`source`,`target`),
  KEY `RelXml2Xml_source` (`source`),
  KEY `RelXml2Xml_target` (`target`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RelNodeMetadata` (
  `IdRel` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdNode` int(12) unsigned NOT NULL DEFAULT '0',
  `IdMetadata` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdRel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabla de relación entre metadatas y nodos de Ximdex';

CREATE TABLE `RelNodeSetsNode` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IdSet` int(10) unsigned NOT NULL,
  `IdNode` int(12) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `U_SETNODES` (`IdSet`,`IdNode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RelNodeSetsUsers` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IdSet` int(10) unsigned NOT NULL,
  `IdUser` int(12) unsigned NOT NULL,
  `Owner` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `U_SETUSERS` (`IdSet`,`IdUser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RelNodeTypeMetadata` (
  `idRel` int(11) NOT NULL AUTO_INCREMENT,
  `idNodeType` varchar(255) NOT NULL,
  `force` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idRel`),
  UNIQUE KEY `idNodeType` (`idNodeType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RelNodeTypeMimeType` (
  `idRelNodeTypeMimeType` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `idNodeType` int(12) unsigned NOT NULL DEFAULT '0',
  `extension` varchar(255) DEFAULT NULL,
  `filter` char(50) DEFAULT NULL,
  PRIMARY KEY (`idRelNodeTypeMimeType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Relation between nodetypes and mime-types';

CREATE TABLE `RelNodeVersionMetadataVersion` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `idrnm` int(12) unsigned NOT NULL,
  `idNodeVersion` int(12) unsigned NOT NULL DEFAULT '0',
  `idMetadataVersion` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rel` (`idNodeVersion`,`idMetadataVersion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RelRolesActions` (
  `IdRel` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdRol` int(12) unsigned NOT NULL DEFAULT '0',
  `IdAction` int(12) unsigned NOT NULL DEFAULT '0',
  `IdState` int(12) unsigned DEFAULT NULL,
  `IdContext` int(12) NOT NULL DEFAULT '1',
  `IdPipeline` int(12) DEFAULT NULL,
  PRIMARY KEY (`IdRel`),
  KEY `IdRol` (`IdRol`),
  KEY `IdAction` (`IdAction`),
  KEY `IdContext` (`IdContext`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Assignment of default command of each role';

CREATE TABLE `RelRolesPermissions` (
  `IdRel` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdRole` int(12) unsigned NOT NULL DEFAULT '0',
  `IdPermission` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdRel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Association of roles and permits';

CREATE TABLE `RelRolesStates` (
  `IdRel` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdRole` int(12) unsigned NOT NULL DEFAULT '0',
  `IdState` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdRel`),
  UNIQUE KEY `items` (`IdRole`,`IdState`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Association of roles with status transitions';

CREATE TABLE `RelSectionXimlet` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `source` int(12) unsigned NOT NULL DEFAULT '0',
  `target` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RelServersChannels` (
  `IdRel` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdServer` int(12) unsigned DEFAULT '0',
  `IdChannel` int(12) unsigned DEFAULT '0',
  PRIMARY KEY (`IdRel`),
  UNIQUE KEY `IdRel` (`IdRel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table which associates physical servers with channels .';

CREATE TABLE `RelServersStates` (
  `IdRel` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdServer` int(12) unsigned DEFAULT '0',
  `IdState` int(12) unsigned DEFAULT '0',
  PRIMARY KEY (`IdRel`),
  UNIQUE KEY `IdRel` (`IdRel`),
  KEY `IdRel_2` (`IdRel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table which associates servers with workflow status';

CREATE TABLE `RelStrdocTemplate` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `source` int(12) unsigned NOT NULL DEFAULT '0',
  `target` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rel` (`source`,`target`),
  KEY `RelStrdocTemplate_source` (`source`),
  KEY `RelStrdocTemplate_target` (`target`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RelTemplateContainer` (
  `IdRel` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdTemplate` int(12) unsigned NOT NULL DEFAULT '0',
  `IdContainer` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdRel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Associate template with container';

CREATE TABLE `RelUsersGroups` (
  `IdRel` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdUser` int(12) unsigned NOT NULL DEFAULT '0',
  `IdGroup` int(12) unsigned NOT NULL DEFAULT '0',
  `IdRole` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdRel`),
  KEY `IdUSer` (`IdUser`),
  KEY `IdGroup` (`IdGroup`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Assing users to a group with a role';

CREATE TABLE `RelVersionsLabel` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `idVersion` int(12) unsigned DEFAULT '0',
  `idLabel` int(12) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `VersionsLabelRest` (`idVersion`,`idLabel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RelXml2Xml` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `source` int(12) unsigned NOT NULL DEFAULT '0',
  `target` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rel` (`source`,`target`),
  KEY `RelXml2Xml_source` (`source`),
  KEY `RelXml2Xml_target` (`target`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Roles` (
  `IdRole` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL DEFAULT '0',
  `Icon` varchar(255) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`IdRole`),
  UNIQUE KEY `IdRole` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table of roles that an user can play into a group';

CREATE TABLE `SearchFilters` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) DEFAULT NULL,
  `Handler` varchar(5) NOT NULL,
  `Filter` text NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `U_FILTER` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `SectionTypes` (
  `idSectionType` int(11) NOT NULL AUTO_INCREMENT,
  `sectionType` varchar(255) NOT NULL,
  `idNodeType` int(11) NOT NULL,
  `module` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idSectionType`),
  UNIQUE KEY `sectionType` (`sectionType`),
  KEY `idSectionType` (`idSectionType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Servers` (
  `IdServer` int(12) UNSIGNED NOT NULL,
  `IdNode` int(12) UNSIGNED NOT NULL DEFAULT '0',
  `IdProtocol` varchar(255) DEFAULT NULL,
  `Login` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Host` varchar(255) DEFAULT NULL,
  `Port` int(12) UNSIGNED DEFAULT NULL,
  `Url` TEXT,
  `InitialDirectory` TEXT,
  `OverrideLocalPaths` int(1) UNSIGNED DEFAULT '0',
  `Enabled` int(1) UNSIGNED DEFAULT '1',
  `Previsual` int(1) DEFAULT '0',
  `Description` varchar(255) DEFAULT NULL,
  `idEncode` varchar(255) NOT NULL DEFAULT 'UTF-8'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table with info about Ximdex servers';
ALTER TABLE `Servers` ADD PRIMARY KEY (`IdServer`);
ALTER TABLE `Servers` MODIFY `IdServer` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `States` (
  `IdState` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL DEFAULT '0',
  `Description` varchar(255) DEFAULT '0',
  `IsRoot` int(1) unsigned DEFAULT '0',
  `IsEnd` int(1) unsigned DEFAULT '0',
  `NextState` int(12) unsigned DEFAULT NULL,
  PRIMARY KEY (`IdState`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table of Workflow status';

CREATE TABLE `StructuredDocuments` (
  `IdDoc` int(12) unsigned NOT NULL DEFAULT '0',
  `Name` varchar(255) DEFAULT NULL,
  `IdCreator` int(12) unsigned DEFAULT '0',
  `CreationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UpdateDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `IdLanguage` int(12) DEFAULT '0',
  `IdTemplate` int(12) unsigned NOT NULL DEFAULT '0',
  `TargetLink` int(12) unsigned DEFAULT NULL,
  PRIMARY KEY (`IdDoc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table of structured documents of Ximdex';
ALTER TABLE `StructuredDocuments` ADD `XsltErrors` TEXT NULL DEFAULT NULL COMMENT 'XSL transformation process errors' AFTER `TargetLink`;

CREATE TABLE `Synchronizer` (
  `IdSync` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdServer` int(12) unsigned NOT NULL DEFAULT '0',
  `IdNode` int(12) unsigned NOT NULL DEFAULT '0',
  `IdChannel` int(12) unsigned DEFAULT NULL,
  `DateUp` int(14) unsigned NOT NULL DEFAULT '0',
  `DateDown` int(14) unsigned DEFAULT '0',
  `State` varchar(255) DEFAULT 'DUE',
  `Error` varchar(255) DEFAULT NULL,
  `ErrorLevel` varchar(255) DEFAULT NULL,
  `RemotePath` blob NOT NULL,
  `FileName` varchar(255) NOT NULL DEFAULT '',
  `Retry` int(12) unsigned DEFAULT '0',
  `Linked` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdSync`),
  UNIQUE KEY `IdSync` (`IdSync`),
  KEY `IdSync_2` (`IdSync`,`IdServer`,`IdNode`,`IdChannel`,`DateUp`,`DateDown`,`State`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 COMMENT='Table of sync of Ximdex';

CREATE TABLE `SynchronizerDependencies` (
  `IdSync` int(12) unsigned NOT NULL DEFAULT '0',
  `IdResource` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdSync`,`IdResource`),
  KEY `IdSync` (`IdSync`,`IdResource`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 COMMENT='Table of dependencies of publication windows of Ximdex';

CREATE TABLE `SynchronizerDependenciesHistory` (
  `IdSync` int(12) unsigned NOT NULL DEFAULT '0',
  `IdResource` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdSync`,`IdResource`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 COMMENT='Historical information of publications. Deprecated?';

CREATE TABLE `SynchronizerGroups` (
  `IdMaster` int(12) unsigned NOT NULL DEFAULT '0',
  `IdSlave` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdMaster`,`IdSlave`),
  UNIQUE KEY `IdMaster` (`IdMaster`,`IdSlave`),
  KEY `IdMaster_2` (`IdMaster`,`IdSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table of sharing workflow between nodes';

CREATE TABLE `SynchronizerHistory` (
  `IdSync` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdServer` int(12) unsigned NOT NULL DEFAULT '0',
  `IdNode` int(12) unsigned NOT NULL DEFAULT '0',
  `IdChannel` int(12) unsigned DEFAULT NULL,
  `DateUp` int(14) unsigned NOT NULL DEFAULT '0',
  `DateDown` int(14) unsigned DEFAULT '0',
  `State` varchar(255) DEFAULT 'DUE',
  `Error` varchar(255) DEFAULT NULL,
  `ErrorLevel` varchar(255) DEFAULT NULL,
  `RemotePath` blob NOT NULL,
  `FileName` varchar(255) NOT NULL DEFAULT '',
  `Retry` int(12) unsigned DEFAULT '0',
  `Linked` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdSync`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 COMMENT='Table of sync history of Ximdex';


CREATE TABLE `UpdateDb_historic` (
  `IdLog` int(11) NOT NULL AUTO_INCREMENT,
  `Priority` int(11) NOT NULL,
  `LogText` varchar(255) NOT NULL,
  PRIMARY KEY (`IdLog`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Updater_DiffsApplied` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `execs` int(2) NOT NULL,
  `module` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Users` (
  `IdUser` int(12) unsigned NOT NULL,
  `Login` varchar(255) NOT NULL DEFAULT '0',
  `Pass` varchar(255) NOT NULL DEFAULT '0',
  `Name` varchar(255) NOT NULL DEFAULT '0',
  `Email` varchar(255) NOT NULL DEFAULT '',
  `Locale` varchar(5) DEFAULT NULL COMMENT 'User Locale',
  `LastLogin` int(14) unsigned DEFAULT '0',
  `NumAccess` int(12) unsigned DEFAULT '0',
  PRIMARY KEY (`IdUser`),
  UNIQUE KEY `login` (`Login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Users registered on Ximdex CMS';

CREATE TABLE `Versions` (
  `IdVersion` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `IdNode` int(12) unsigned NOT NULL DEFAULT '0',
  `Version` int(12) unsigned NOT NULL DEFAULT '0',
  `SubVersion` int(12) unsigned NOT NULL DEFAULT '0',
  `File` varchar(255) NOT NULL DEFAULT '',
  `IdUser` int(12) unsigned DEFAULT '0',
  `Date` int(14) unsigned DEFAULT '0',
  `Comment` blob,
  `IdSync` int(12) unsigned DEFAULT NULL,
  PRIMARY KEY (`IdVersion`),
  KEY `Version` (`SubVersion`,`IdNode`,`Version`),
  KEY `IdNode` (`IdNode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table of contents and version management';

/*
CREATE TABLE `RelStrdocNode` (
	`id` int(12) unsigned NOT NULL auto_increment,
	`source` int(12) unsigned NOT NULL default '0',
	`target` int(12) unsigned NOT NULL default '0',
	PRIMARY KEY (id),
	UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RelStrdocAsset` (
	`id` int(12) unsigned NOT NULL auto_increment,
	`source` int(12) unsigned NOT NULL default '0',
	`target` int(12) unsigned NOT NULL default '0',
	PRIMARY KEY (id),
	UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/

CREATE TABLE RelStrdocXimlet (
	id int(12) unsigned NOT NULL auto_increment,
	source int(12) unsigned NOT NULL default '0',
	target int(12) unsigned NOT NULL default '0',
	PRIMARY KEY (id),
	UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `RelDocumentFolderToTemplatesIncludeFile` (
  `id` int(12) NOT NULL,
  `source` int(12) NOT NULL COMMENT 'idNode of the XML document folder',
  `target` int(12) NOT NULL COMMENT 'idNode of the templates folder'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Relation between XML document and the templates_include.xsl file associated';
ALTER TABLE `RelDocumentFolderToTemplatesIncludeFile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_source` (`source`) USING BTREE,
  ADD KEY `source` (`source`),
  ADD KEY `target` (`target`);
ALTER TABLE `RelDocumentFolderToTemplatesIncludeFile` MODIFY `id` int(12) NOT NULL AUTO_INCREMENT;
