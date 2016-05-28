
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


CREATE TABLE `NoActionsInNode` (
  `IdNode` INT NOT NULL ,
  `IdAction` INT NOT NULL COMMENT 'Actions not allowed for a Node',
  PRIMARY KEY ( `IdNode` , `IdAction` )
) ENGINE = MyISAM COMMENT = 'List of Actions not allowed in a Node';

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

CREATE TABLE `Config` (
  `IdConfig` int(12) unsigned NOT NULL auto_increment,
  `ConfigKey` varchar(255) NOT NULL default '0',
  `ConfigValue` blob,
  PRIMARY KEY  (`IdConfig`),
  UNIQUE KEY `IdConfig` (`IdConfig`,`ConfigKey`),
  UNIQUE KEY `ConfigKey` (`ConfigKey`),
  KEY `IdConfig_2` (`IdConfig`)
) ENGINE=MyISAM COMMENT='Table with configuration parameters of Ximdex CMS';


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

CREATE TABLE `DependenceTypes` (
  `IdDepType` int(6) unsigned NOT NULL auto_increment,
  `Type` varchar(31)  NOT NULL default '0',
  PRIMARY KEY (`IdDepType`)
) ENGINE=MyISAM;

CREATE TABLE `FastTraverse` (
  `IdNode` int(12) unsigned NOT NULL default '0',
  `IdChild` int(12) unsigned NOT NULL default '0',
  `Depth` int(12) unsigned default '0',
  PRIMARY KEY  (`IdNode`,`IdChild`),
  UNIQUE KEY `IdNode` (`IdNode`,`IdChild`),
  KEY `IdN` (`IdNode`),
  KEY `IdC` (`IdChild`)
) ENGINE=MyISAM COMMENT='Fast scan of node hierarchies';

CREATE TABLE `Groups` (
  `IdGroup` int(12) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`IdGroup`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM COMMENT='Groups defined on the system';

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

CREATE TABLE `Locales` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Code` varchar(5) NOT NULL COMMENT 'Locale in ISO 639 ',
  `Name` varchar(20) NOT NULL COMMENT 'Lang name',
  `Enabled` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Enabled(1)|Not Enabled(0)',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  COMMENT='Ximdex CMS default languages';


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


CREATE TABLE `RelLinkDescriptions` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdLink` int(12) unsigned NOT NULL,
  `Description` varchar(255),
  PRIMARY KEY  (`IdRel`),
  UNIQUE KEY `Description` (`IdLink`, `Description`)
) ENGINE=MyISAM COMMENT='Table of descriptions of Ximdex links';


CREATE TABLE `Messages` (
  `IdMessage` int(12) unsigned NOT NULL auto_increment,
  `IdFrom` int(12) unsigned NOT NULL default '0',
  `IdOwner` int(12) unsigned NOT NULL default '0',
  `ToString` varchar(255) default NULL,
  `Folder` int(12) unsigned NOT NULL default '1',
  `Subject` varchar(255) default NULL,
  `Content` blob,
  `IsRead` int(1) unsigned NOT NULL default '0',
  `FechaHora` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`IdMessage`)
) ENGINE=MyISAM COMMENT='Messages sent by Ximdex CMS. Deprecated?';


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

CREATE TABLE `NodeAllowedContents` (
  `IdNodeAllowedContent` int(12) unsigned NOT NULL auto_increment,
  `IdNodeType` int(12) unsigned NOT NULL default '0',
  `NodeType` int(12) unsigned NOT NULL default '0',
  `Amount` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdNodeAllowedContent`),
  UNIQUE KEY `UniqeAmmount` (`IdNodeType`,`NodeType`)
) ENGINE=MyISAM COMMENT='Allowed node types into each type of node';

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

CREATE TABLE `NodeDependencies` (
  `IdNode` int(12) unsigned NOT NULL default '0',
  `IdResource` int(12) unsigned NOT NULL default '0',
  `IdChannel` int(12) unsigned default NULL,
  PRIMARY KEY  (`IdNode`,`IdResource`)
) ENGINE=MyISAM COMMENT='Dependencies between nodes in Ximdex CMS';

CREATE TABLE `NodeEdition` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `IdNode` int(11) unsigned NOT NULL,
  `IdUser` int(11) unsigned NOT NULL,
  `StartTime` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='XML edition information. For concurrency issues';

CREATE TABLE `NodeNameTranslations` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdNode` int(12) unsigned NOT NULL default '0',
  `IdLanguage` int(12) unsigned NOT NULL default '0',
  `Name` varchar(255) default '0',
  PRIMARY KEY  (`IdRel`),
  UNIQUE KEY `NodeLanguage` (`IdNode`,`IdLanguage`)
) ENGINE=MyISAM COMMENT='Alias for nodes in other languages';


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


CREATE TABLE `Permissions` (
  `IdPermission` int(12) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '0',
  `Description` varchar(255) default NULL,
  PRIMARY KEY  (`IdPermission`),
  UNIQUE KEY `IdName` (`Name`)
) ENGINE=MyISAM COMMENT='Table of system permits';

CREATE TABLE `Protocols` (
  `IdProtocol` varchar(255) NOT NULL default '',
  `DefaultPort` int(12) unsigned default '0',
  `Description` varchar(255) default '0',
  `UsePassword` int(1) unsigned default '0',
  PRIMARY KEY  (`IdProtocol`),
  UNIQUE KEY `IdProtocol` (`IdProtocol`),
  KEY `IdProtocol_2` (`IdProtocol`)
) ENGINE=MyISAM COMMENT='Protocols to synchronize supported by Ximdex CMS';

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


CREATE TABLE `RelRolesPermissions` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdRole` int(12) unsigned NOT NULL default '0',
  `IdPermission` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdRel`)
) ENGINE=MyISAM COMMENT='Association of roles and permits';

CREATE TABLE `RelRolesStates` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdRole` int(12) unsigned NOT NULL default '0',
  `IdState` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdRel`),
  UNIQUE KEY `items` (`IdRole`,`IdState`)
) ENGINE=MyISAM COMMENT='Association of roles with status transitions';

CREATE TABLE `RelServersChannels` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdServer` int(12) unsigned default '0',
  `IdChannel` int(12) unsigned default '0',
  PRIMARY KEY  (`IdRel`),
  UNIQUE KEY `IdRel` (`IdRel`)
) ENGINE=MyISAM COMMENT='Table which associates physical servers with channels .';

CREATE TABLE `RelServersStates` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdServer` int(12) unsigned default '0',
  `IdState` int(12) unsigned default '0',
  PRIMARY KEY  (`IdRel`),
  UNIQUE KEY `IdRel` (`IdRel`),
  KEY `IdRel_2` (`IdRel`)
) ENGINE=MyISAM COMMENT='Table which associates servers with workflow status';


CREATE TABLE `RelStrDocChannels` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdDoc` int(12) unsigned default '0',
  `IdChannel` int(12) unsigned default '0',
  PRIMARY KEY  (`IdRel`),
  UNIQUE KEY `IdDoc` (`IdDoc`,`IdChannel`)
) ENGINE=MyISAM COMMENT='Association between structured documents and their channels';

CREATE TABLE `RelTemplateContainer` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdTemplate` int(12) unsigned NOT NULL default '0',
  `IdContainer` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdRel`)
) ENGINE=MyISAM COMMENT='Associate template with container';

CREATE TABLE `RelUsersGroups` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdUser` int(12) unsigned NOT NULL default '0',
  `IdGroup` int(12) unsigned NOT NULL default '0',
  `IdRole` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdRel`),
  KEY `IdUSer` (`IdUser`),
  KEY `IdGroup` (`IdGroup`)
) ENGINE=MyISAM COMMENT='Assing users to a group with a role';

CREATE TABLE `Roles` (
  `IdRole` int(12) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '0',
  `Icon` varchar(255) default NULL,
  `Description` varchar(255) default NULL,
  PRIMARY KEY  (`IdRole`),
  UNIQUE KEY `IdRole` (`Name`)
) ENGINE=MyISAM COMMENT='Table of roles that an user can play into a group';

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

CREATE TABLE `States` (
  `IdState` int(12) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '0',
  `Description` varchar(255) default '0',
  `IsRoot` int(1) unsigned default '0',
  `IsEnd` int(1) unsigned default '0',
  `NextState` int(12) unsigned default NULL,
  PRIMARY KEY  (`IdState`)
) ENGINE=MyISAM COMMENT='Table of Workflow status';

CREATE TABLE `StructuredDocuments` (
  `IdDoc` int(12) unsigned NOT NULL default '0',
  `Name` varchar(255) default NULL,
  `IdCreator` int(12) unsigned default '0',
  `CreationDate` timestamp NOT NULL,
  `UpdateDate` timestamp NOT NULL default '0000-00-00 00:00:00',
  `IdLanguage` int(12) default '0',
  `IdTemplate` int(12) unsigned NOT NULL default '0',
  `TargetLink` int(12) unsigned default NULL,
  PRIMARY KEY  (`IdDoc`)
) ENGINE=MyISAM COMMENT='Table of strutured documents of Ximdex';

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

CREATE TABLE `SynchronizerDependencies` (
  `IdSync` int(12) unsigned NOT NULL default '0',
  `IdResource` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdSync`,`IdResource`),
  KEY `IdSync` (`IdSync`,`IdResource`)
) ENGINE=MyISAM DELAY_KEY_WRITE=1 COMMENT='Table of dependencies of publication windows of Ximdex';

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

CREATE TABLE `SynchronizerDependenciesHistory` (
  `IdSync` int(12) unsigned NOT NULL default '0',
  `IdResource` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdSync`,`IdResource`)
) ENGINE=MyISAM DELAY_KEY_WRITE=1 COMMENT='Historical information of publications. Deprecated?';

CREATE TABLE `SynchronizerGroups` (
  `IdMaster` int(12) unsigned NOT NULL default '0',
  `IdSlave` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdMaster`,`IdSlave`),
  UNIQUE KEY `IdMaster` (`IdMaster`,`IdSlave`),
  KEY `IdMaster_2` (`IdMaster`,`IdSlave`)
) ENGINE=MyISAM COMMENT='Table of sharing workflow between nodes';

CREATE TABLE `SystemProperties` (
  `IdSysProp` int(12) unsigned NOT NULL auto_increment,
  `Name` varchar(250) NOT NULL default '0',
  PRIMARY KEY  (`IdSysProp`)
) ENGINE=MyISAM;

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

CREATE TABLE `RelNodeTypeMimeType` (
  `idRelNodeTypeMimeType` int(12) unsigned NOT NULL auto_increment,
  `idNodeType` int(12) unsigned NOT NULL default '0',
  `extension` varchar(255) NULL,
  `filter` char(50) NULL,
  PRIMARY KEY  (`idRelNodeTypeMimeType`)
) ENGINE=MyISAM COMMENT='Relation between nodetypes and mime-types'   ;


CREATE TABLE `RelNodeTypeMetadata` (
  `idRel` int(11) NOT NULL auto_increment,
  `idNodeType` varchar(255) NOT NULL,
  `force` tinyint(1) unsigned NOT NULL default 0,
  PRIMARY KEY  (`idRel`),
  UNIQUE KEY `idNodeType` (`idNodeType`)
);

CREATE TABLE `SectionTypes` (
  `idSectionType` int(11) NOT NULL auto_increment,
  `sectionType` varchar(255) NOT NULL,
  `idNodeType` int(11) NOT NULL,
  `module` varchar(255) default NULL,
  PRIMARY KEY  (`idSectionType`),
  UNIQUE KEY `sectionType` (`sectionType`),
  KEY `idSectionType` (`idSectionType`)
);

CREATE TABLE `PipeCaches` (
  `id` int(11) NOT NULL auto_increment,
  `IdVersion` int(11) NOT NULL,
  `IdPipeTransition` int(11) NOT NULL,
  `File` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE `PipeNodeTypes` (
  `id` int(11) NOT NULL auto_increment,
  `IdPipeline` int(11) NOT NULL,
  `IdNodeType` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE `PipeProcess` (
  `id` int(11) NOT NULL auto_increment,
  `IdTransitionFrom` int(11) default NULL,
  `IdTransitionTo` int(11) NOT NULL,
  `IdPipeline` int(11) default NULL,
  `Name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1   ;

CREATE TABLE `PipeProperties` (
  `id` int(11) NOT NULL auto_increment,
  `IdPipeTransition` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
);


CREATE TABLE `PipePropertyValues` (
  `id` int(11) NOT NULL auto_increment,
  `IdPipeProperty` int(11) NOT NULL,
  `IdPipeCache` int(11) NOT NULL,
  `Value` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE  `PipeStatus` (
  `id` int(11) NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL,
  `Description` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1   ;


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


 
CREATE TABLE  `Pipelines` (
  `id` int(11) NOT NULL auto_increment,
  `Pipeline` varchar(255) NOT NULL,
  `IdNode` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `IdNode` (`IdNode`)
);


CREATE TABLE `PipeCacheTemplates` (
  `id` int(11) NOT NULL auto_increment,
  `NodeId` int(11) NOT NULL,
  `DocIdVersion` int(11) NOT NULL,
  `TemplateIdVersion` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);


CREATE TABLE `NodeProperties` (
  `IdNodeProperty` INT NULL AUTO_INCREMENT,
  `IdNode` INT NOT NULL ,
  `Property` VARCHAR( 255 ) NOT NULL ,
  `Value` blob NOT NULL ,
  PRIMARY KEY ( `IdNodeProperty` ) ,
  INDEX ( `IdNode` )
);

CREATE TABLE `Contexts` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `Context` VARCHAR( 255 ) NOT NULL ,
  INDEX ( `Context` )
);

CREATE TABLE `NodetypeModes` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `IdNodeType` INT NOT NULL ,
  `Mode` ENUM( 'C', 'R', 'U', 'D' ) NOT NULL ,
  `IdAction` INT NULL ,
  INDEX ( `IdNodeType` , `IdAction` )
);

CREATE TABLE   `UpdateDb_historic` (
  `IdLog` int(11) NOT NULL auto_increment,
  `Priority` int(11) NOT NULL,
  `LogText` varchar(255) NOT NULL,
  PRIMARY KEY  (`IdLog`)
);

CREATE TABLE `Updater_DiffsApplied` (
  `id` int(12) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `execs` int(2) NOT NULL,
  `module` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

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


CREATE TABLE `Encodes` (
  `IdEncode` varchar(255) NOT NULL default '',
  `Description` varchar(255) default '0',
  PRIMARY KEY  (`IdEncode`),
  UNIQUE KEY `IdEncode` (`IdEncode`),
  KEY `IdEncode_2` (`IdEncode`)
) ENGINE=MyISAM COMMENT='Available encodings on Ximdex CMS';


CREATE TABLE RelStrdocTemplate (
  id int(12) unsigned NOT NULL auto_increment,
  source int(12) unsigned NOT NULL default '0',
  target int(12) unsigned NOT NULL default '0',
  PRIMARY KEY (id),
  UNIQUE KEY `rel` (`source`,`target`),
  INDEX `RelStrdocTemplate_source` (`source`),
  INDEX `RelStrdocTemplate_target` (`target`)
) ENGINE=MyISAM;

CREATE TABLE RelSectionXimlet (
  id int(12) unsigned NOT NULL auto_increment,
  source int(12) unsigned NOT NULL default '0',
  target int(12) unsigned NOT NULL default '0',
  PRIMARY KEY (id),
  UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MyISAM;

CREATE TABLE RelBulletinXimlet (
  id int(12) unsigned NOT NULL auto_increment,
  source int(12) unsigned NOT NULL default '0',
  target int(12) unsigned NOT NULL default '0',
  PRIMARY KEY (id),
  UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MyISAM;


CREATE TABLE RelNode2Asset (
  id int(12) unsigned NOT NULL auto_increment,
  source int(12) unsigned NOT NULL default '0',
  target int(12) unsigned NOT NULL default '0',
  PRIMARY KEY (id),
  UNIQUE KEY `rel` (`source`,`target`),
  INDEX `RelXml2Xml_source` (`source`),
  INDEX `RelXml2Xml_target` (`target`)
) ENGINE=MyISAM;


CREATE TABLE RelXml2Xml (
  id int(12) unsigned NOT NULL auto_increment,
  source int(12) unsigned NOT NULL default '0',
  target int(12) unsigned NOT NULL default '0',
  PRIMARY KEY (id),
  UNIQUE KEY `rel` (`source`,`target`),
  INDEX `RelXml2Xml_source` (`source`),
  INDEX `RelXml2Xml_target` (`target`)
) ENGINE=MyISAM;

CREATE TABLE `PortalVersions` (
  `id` int(12) unsigned NOT NULL auto_increment,
  `IdPortal` int(12) unsigned default '0',
  `Version` int(12) unsigned default '0',
  `TimeStamp` int(12) unsigned default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
 
CREATE TABLE `RelFramesPortal` (
  `id` int(12) unsigned NOT NULL auto_increment,
  `IdPortalVersion` int(12) unsigned default '0',
  `IdFrame` int(12) unsigned default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `PortalFrame` (`IdPortalVersion`,`IdFrame`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE `List` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `IdList` INT NOT NULL ,
  `Name` VARCHAR( 250 ) NOT NULL ,
  `Description` VARCHAR( 250 ) NULL ,
  PRIMARY KEY ( `id` )
) ENGINE = MyISAM;

CREATE TABLE `List_Label` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `Name` VARCHAR( 250 ) NOT NULL ,
  `Description` VARCHAR( 250 ) NULL ,
  PRIMARY KEY ( `id` )
) ENGINE = MyISAM;

CREATE TABLE `RelVersionsLabel` (
  `id` int(12) unsigned NOT NULL auto_increment,
  `idVersion` int(12) unsigned default '0',
  `idLabel` int(12) unsigned default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `VersionsLabelRest` (`idVersion`,`idLabel`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `NodeSets` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(100) default NULL,
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `U_SET` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `RelNodeSetsNode` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `IdSet` int(10) unsigned NOT NULL,
  `IdNode` int(12) unsigned NOT NULL,
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `U_SETNODES` (`IdSet`,`IdNode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `RelNodeSetsUsers` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `IdSet` int(10) unsigned NOT NULL,
  `IdUser` int(12) unsigned NOT NULL,
  `Owner` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `U_SETUSERS` (`IdSet`,`IdUser`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `SearchFilters` (
  `Id` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(100) default NULL,
  `Handler` varchar(5) NOT NULL,
  `Filter` text NOT NULL,
  PRIMARY KEY  (`Id`),
  UNIQUE KEY `U_FILTER` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


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

CREATE TABLE RelNodeVersionMetadataVersion (
  id int(12) unsigned NOT NULL auto_increment,
  idrnm int(12) unsigned NOT NULL,
  idNodeVersion int(12) unsigned NOT NULL default 0,
  idMetadataVersion int(12) unsigned NOT NULL default 0,
  PRIMARY KEY (id),
  UNIQUE KEY `rel` (`idNodeVersion`,`idMetadataVersion`)
) ENGINE=MyISAM;

CREATE TABLE `RelNodeMetadata` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdNode` int(12) unsigned NOT NULL default '0',
  `IdMetadata` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdRel`)
) ENGINE=MyISAM COMMENT='Tabla de relación entre metadatas y nodos de Ximdex';

