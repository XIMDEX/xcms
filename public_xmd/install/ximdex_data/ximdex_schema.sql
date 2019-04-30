--
-- Table structure `Actions`
--

CREATE TABLE `Actions` (
  `IdAction` int(12) UNSIGNED NOT NULL,
  `IdNodeType` int(12) UNSIGNED DEFAULT NULL,
  `Name` varchar(100) NOT NULL DEFAULT '',
  `Command` varchar(100) NOT NULL DEFAULT '',
  `Icon` varchar(100) NOT NULL DEFAULT '',
  `Description` varchar(255) DEFAULT NULL,
  `Sort` int(12) DEFAULT NULL,
  `Module` varchar(250) DEFAULT NULL,
  `Multiple` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `Params` varchar(255) DEFAULT NULL,
  `IsBulk` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Commands which can be executed in a node';

-- --------------------------------------------------------

--
-- Table structure `Batchs`
--

CREATE TABLE `Batchs` (
  `IdBatch` int(12) UNSIGNED NOT NULL,
  `TimeOn` int(12) NOT NULL,
  `State` enum('Creating','Waiting','InTime','Closing','Ended','NoFrames','Stopped') NOT NULL DEFAULT 'Creating',
  `ServerFramesTotal` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `ServerFramesPending` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `ServerFramesActive` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `ServerFramesSuccess` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `ServerFramesFatalError` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `ServerFramesTemporalError` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `Type` enum('Up','Down') NOT NULL DEFAULT 'Up',
  `IdBatchDown` int(12) UNSIGNED DEFAULT NULL,
  `IdNodeGenerator` int(12) UNSIGNED DEFAULT NULL,
  `Priority` float(3,2) UNSIGNED NOT NULL DEFAULT 1.00,
  `Cycles` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `IdPortalFrame` int(12) UNSIGNED NOT NULL,
  `UserId` int(12) UNSIGNED DEFAULT NULL,
  `ServerId` int(12) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `ChannelFrames`
--

CREATE TABLE `ChannelFrames` (
  `IdChannelFrame` int(12) UNSIGNED NOT NULL,
  `ChannelId` int(12) UNSIGNED DEFAULT 0,
  `NodeId` int(12) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `Channels`
--

CREATE TABLE `Channels` (
  `IdChannel` int(12) UNSIGNED NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `DefaultExtension` varchar(255) DEFAULT NULL,
  `Format` varchar(255) DEFAULT NULL,
  `Filter` varchar(255) DEFAULT NULL,
  `RenderMode` varchar(255) DEFAULT NULL,
  `OutputType` enum('web','xml','other') NOT NULL,
  `Default_Channel` tinyint(1) NOT NULL DEFAULT 0,
  `RenderType` enum('static','include','dynamic','index') NOT NULL,
  `idLanguage` varchar(50) NOT NULL COMMENT 'Programming language for code macros'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Available channels used to transform content';

-- --------------------------------------------------------

--
-- Table structure `Config`
--

CREATE TABLE `Config` (
  `IdConfig` int(12) UNSIGNED NOT NULL,
  `ConfigKey` varchar(30) NOT NULL,
  `ConfigValue` text DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table with configuration parameters of Ximdex CMS';

-- --------------------------------------------------------

--
-- Table structure `DependenceTypes`
--

CREATE TABLE `DependenceTypes` (
  `IdDepType` int(6) UNSIGNED NOT NULL,
  `Type` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `Dependencies`
--

CREATE TABLE `Dependencies` (
  `IdDep` int(12) UNSIGNED NOT NULL,
  `IdNodeMaster` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `IdNodeDependent` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `DepType` int(6) UNSIGNED NOT NULL,
  `version` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='All the dependencies by type on Ximdex CMS';

-- --------------------------------------------------------

--
-- Table structure `Encodes`
--

CREATE TABLE `Encodes` (
  `IdEncode` varchar(255) NOT NULL DEFAULT '',
  `Description` varchar(255) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Available encodings on Ximdex CMS';

-- --------------------------------------------------------

--
-- Table structure `FastTraverse`
--

CREATE TABLE `FastTraverse` (
  `IdNode` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `IdChild` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `Depth` int(12) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Fast scan of node hierarchies';

-- --------------------------------------------------------

--
-- Table structure `Groups`
--

CREATE TABLE `Groups` (
  `IdGroup` int(12) UNSIGNED NOT NULL,
  `Name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Groups defined on the system';

-- --------------------------------------------------------

--
-- Table structure `IsoCodes`
--

CREATE TABLE `IsoCodes` (
  `IdIsoCode` int(12) UNSIGNED NOT NULL,
  `Iso2` char(2) NOT NULL,
  `Iso3` char(3) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `NativeName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ISO codes supported for languages';

-- --------------------------------------------------------

--
-- Table structure `Languages`
--

CREATE TABLE `Languages` (
  `IdLanguage` int(12) UNSIGNED NOT NULL,
  `Name` varchar(255) NOT NULL DEFAULT '',
  `IsoName` char(2) NOT NULL,
  `Enabled` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ximdex CMS defined languages';

-- --------------------------------------------------------

--
-- Table structure `Links`
--

CREATE TABLE `Links` (
  `IdLink` int(12) UNSIGNED NOT NULL,
  `Url` text NOT NULL,
  `Error` int(12) UNSIGNED DEFAULT NULL,
  `ErrorString` varchar(255) DEFAULT NULL,
  `CheckTime` int(12) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table of link manager of Ximdex';

-- --------------------------------------------------------

--
-- Table structure `List`
--

CREATE TABLE `List` (
  `id` int(11) NOT NULL,
  `IdList` int(11) NOT NULL,
  `Name` varchar(250) NOT NULL,
  `Description` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `List_Label`
--

CREATE TABLE `List_Label` (
  `id` int(11) NOT NULL,
  `Name` varchar(250) NOT NULL,
  `Description` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `Locales`
--

CREATE TABLE `Locales` (
  `ID` smallint(5) UNSIGNED NOT NULL,
  `Code` varchar(5) NOT NULL COMMENT 'Locale in ISO 639 ',
  `Name` varchar(20) NOT NULL COMMENT 'Lang name',
  `Enabled` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Enabled(1)|Not Enabled(0)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ximdex CMS default languages';

-- --------------------------------------------------------

--
-- Table structure `Messages`
--

CREATE TABLE `Messages` (
  `IdMessage` int(12) UNSIGNED NOT NULL,
  `IdFrom` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `IdOwner` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `ToString` varchar(255) DEFAULT NULL,
  `Folder` int(12) UNSIGNED NOT NULL DEFAULT 1,
  `Subject` varchar(255) DEFAULT NULL,
  `Content` blob DEFAULT NULL,
  `IsRead` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `FechaHora` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Messages sent by Ximdex CMS. Deprecated?';

-- --------------------------------------------------------

--
-- Table structure `Metadata`
--

CREATE TABLE `Metadata` (
  `idMetadata` int(12) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `defaultValue` text DEFAULT NULL,
  `type` enum('integer','float','text','boolean','date','array','image','link','file') DEFAULT 'text'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Available metadata for nodes';

-- --------------------------------------------------------

--
-- Table structure `MetadataGroup`
--

CREATE TABLE `MetadataGroup` (
  `idMetadataGroup` int(12) UNSIGNED NOT NULL,
  `idMetadataScheme` int(12) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Available groups for metadata';

-- --------------------------------------------------------

--
-- Table structure `MetadataScheme`
--

CREATE TABLE `MetadataScheme` (
  `idMetadataScheme` int(12) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Available sections for metadata' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure `MetadataValue`
--

CREATE TABLE `MetadataValue` (
  `idNode` int(12) UNSIGNED NOT NULL,
  `idRelMetadataGroupMetadata` int(12) UNSIGNED NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Metadata value for node';

-- --------------------------------------------------------

--
-- Table structure `NoActionsInNode`
--

CREATE TABLE `NoActionsInNode` (
  `IdNode` int(12) UNSIGNED NOT NULL,
  `IdAction` int(12) UNSIGNED NOT NULL COMMENT 'Actions not allowed for a Node'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='List of Actions not allowed in a Node';

-- --------------------------------------------------------

--
-- Table structure `NodeAllowedContents`
--

CREATE TABLE `NodeAllowedContents` (
  `IdNodeAllowedContent` int(12) UNSIGNED NOT NULL,
  `IdNodeType` int(12) UNSIGNED NOT NULL,
  `NodeType` int(12) UNSIGNED NOT NULL,
  `Amount` int(12) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Allowed node types into each type of node';

-- --------------------------------------------------------

--
-- Table structure `NodeConstructors`
--

CREATE TABLE `NodeConstructors` (
  `IdNodeConstructor` int(12) UNSIGNED NOT NULL,
  `IdNodeType` int(12) UNSIGNED NOT NULL,
  `IdAction` int(12) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `NodeDefaultContents`
--

CREATE TABLE `NodeDefaultContents` (
  `IdNodeDefaultContent` int(12) UNSIGNED NOT NULL,
  `IdNodeType` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `NodeType` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `Name` varchar(255) NOT NULL DEFAULT '0',
  `State` int(12) UNSIGNED DEFAULT NULL,
  `Params` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Default content of each node';

-- --------------------------------------------------------

--
-- Table structure `NodeDependencies`
--

CREATE TABLE `NodeDependencies` (
  `id` int(12) UNSIGNED NOT NULL,
  `IdNode` int(12) UNSIGNED NOT NULL,
  `IdResource` int(12) UNSIGNED NOT NULL,
  `IdChannel` int(12) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Dependencies between nodes in Ximdex CMS';

-- --------------------------------------------------------

--
-- Table structure `NodeEdition`
--

CREATE TABLE `NodeEdition` (
  `Id` int(11) UNSIGNED NOT NULL,
  `IdNode` int(11) UNSIGNED NOT NULL,
  `IdUser` int(11) UNSIGNED NOT NULL,
  `StartTime` int(11) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='XML edition information. For concurrency issues';

-- --------------------------------------------------------

--
-- Table structure `NodeFrames`
--

CREATE TABLE `NodeFrames` (
  `IdNodeFrame` int(12) UNSIGNED NOT NULL,
  `NodeId` int(12) UNSIGNED DEFAULT NULL,
  `VersionId` int(12) UNSIGNED DEFAULT NULL,
  `TimeUp` int(12) UNSIGNED DEFAULT NULL,
  `TimeDown` int(12) UNSIGNED DEFAULT NULL,
  `Active` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `GetActivityFrom` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `IsProcessUp` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `IsProcessDown` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `Name` varchar(255) NOT NULL,
  `IdPortalFrame` int(12) UNSIGNED DEFAULT NULL,
  `TimeStampState` int(12) UNSIGNED DEFAULT NULL COMMENT 'Time when first server frame change its status due to transformation process',
  `TimeStampProccesed` int(12) UNSIGNED DEFAULT NULL COMMENT 'Time when last server frame was processed',
  `SF_Total` int(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Number of total server frames related to this node frame',
  `SF_IN` int(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Number of server frames in IN state related to this node frame'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `NodeNameTranslations`
--

CREATE TABLE `NodeNameTranslations` (
  `IdRel` int(12) UNSIGNED NOT NULL,
  `IdNode` int(12) UNSIGNED NOT NULL,
  `IdLanguage` int(12) UNSIGNED NOT NULL,
  `Name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Alias for nodes in other languages';

-- --------------------------------------------------------

--
-- Table structure `NodeProperties`
--

CREATE TABLE `NodeProperties` (
  `IdNodeProperty` int(12) UNSIGNED NOT NULL,
  `IdNode` int(12) UNSIGNED NOT NULL,
  `Property` enum('Transformer','SchemaType','DefaultServerLanguage','channel','language','metadata') NOT NULL,
  `Value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `Nodes`
--

CREATE TABLE `Nodes` (
  `IdNode` int(12) UNSIGNED NOT NULL,
  `IdParent` int(12) UNSIGNED DEFAULT NULL,
  `IdNodeType` int(12) UNSIGNED NOT NULL,
  `Name` varchar(255) NOT NULL,
  `IdState` int(12) UNSIGNED DEFAULT NULL,
  `BlockTime` int(12) DEFAULT NULL,
  `BlockUser` int(12) UNSIGNED DEFAULT NULL,
  `CreationDate` int(12) UNSIGNED DEFAULT NULL,
  `ModificationDate` int(12) UNSIGNED DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `SharedWorkflow` int(12) UNSIGNED DEFAULT NULL,
  `Path` text DEFAULT NULL,
  `ActiveNF` int(12) UNSIGNED DEFAULT NULL COMMENT 'Current active node frame',
  `sortorder` int(11) DEFAULT 0,
  `deleted` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table of system nodes';

-- --------------------------------------------------------

--
-- Table structure `NodeSets`
--

CREATE TABLE `NodeSets` (
  `Id` int(10) UNSIGNED NOT NULL,
  `Name` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `NodesToPublish`
--

CREATE TABLE `NodesToPublish` (
  `Id` int(11) UNSIGNED NOT NULL,
  `IdNode` int(11) UNSIGNED NOT NULL,
  `IdNodeGenerator` int(12) UNSIGNED NOT NULL,
  `Version` int(12) DEFAULT NULL,
  `Subversion` int(12) DEFAULT NULL,
  `DateUp` int(14) UNSIGNED NOT NULL DEFAULT 0,
  `DateDown` int(14) UNSIGNED DEFAULT 0,
  `State` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `UserId` int(12) UNSIGNED DEFAULT NULL,
  `ForcePublication` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `DeepLevel` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `UseCache` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Nodes to push into publishing pool';

-- --------------------------------------------------------

--
-- Table structure `NodetypeModes`
--

CREATE TABLE `NodetypeModes` (
  `id` int(12) UNSIGNED NOT NULL,
  `IdNodeType` int(12) UNSIGNED NOT NULL,
  `Mode` enum('C','R','U','D') NOT NULL,
  `IdAction` int(12) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `NodeTypes`
--

CREATE TABLE `NodeTypes` (
  `IdNodeType` int(12) UNSIGNED NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Class` varchar(255) NOT NULL DEFAULT '',
  `Icon` varchar(255) NOT NULL DEFAULT '',
  `Description` varchar(255) DEFAULT NULL,
  `IsRenderizable` tinyint(1) NOT NULL DEFAULT 0,
  `HasFSEntity` tinyint(1) NOT NULL DEFAULT 0,
  `CanAttachGroups` tinyint(1) NOT NULL DEFAULT 0,
  `IsSection` tinyint(1) NOT NULL DEFAULT 0,
  `IsFolder` tinyint(1) NOT NULL DEFAULT 0,
  `IsVirtualFolder` tinyint(1) NOT NULL DEFAULT 0,
  `IsPlainFile` tinyint(1) NOT NULL DEFAULT 0,
  `IsStructuredDocument` tinyint(1) NOT NULL DEFAULT 0,
  `IsPublishable` tinyint(1) NOT NULL DEFAULT 0,
  `IsHidden` int(1) UNSIGNED DEFAULT 0,
  `CanDenyDeletion` tinyint(1) NOT NULL DEFAULT 0,
  `isGenerator` tinyint(1) DEFAULT 0,
  `IsEnriching` tinyint(1) DEFAULT 0,
  `System` tinyint(1) NOT NULL DEFAULT 0,
  `Module` varchar(255) DEFAULT NULL,
  `workflowId` int(4) UNSIGNED DEFAULT NULL,
  `HasMetadata` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Nodetypes used on Ximdex CMS';

-- --------------------------------------------------------

--
-- Table structure `Permissions`
--

CREATE TABLE `Permissions` (
  `IdPermission` int(12) UNSIGNED NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table of system permits';

-- --------------------------------------------------------

--
-- Table structure `PortalFrames`
--

CREATE TABLE `PortalFrames` (
  `id` int(12) UNSIGNED NOT NULL,
  `IdNodeGenerator` int(12) UNSIGNED DEFAULT NULL,
  `Version` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `CreationTime` int(12) UNSIGNED NOT NULL,
  `PublishingType` enum('Up','Down') NOT NULL,
  `CreatedBy` int(12) UNSIGNED DEFAULT NULL,
  `ScheduledTime` int(12) NOT NULL,
  `StartTime` int(12) DEFAULT NULL,
  `EndTime` int(12) DEFAULT NULL,
  `Status` enum('Created','Active','Ended') DEFAULT NULL,
  `StatusTime` int(12) DEFAULT NULL,
  `SFtotal` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `SFactive` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `SFpending` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `SFsuccess` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `SFfatalError` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `SFsoftError` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `SFdelayed` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `SFstopped` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `Playing` tinyint(1) NOT NULL DEFAULT 0,
  `SuccessRate` float NOT NULL DEFAULT 1,
  `Boost` enum('1','2','4') NOT NULL DEFAULT '1',
  `BoostCycles` float NOT NULL DEFAULT 0,
  `CyclesTotal` int(12) NOT NULL DEFAULT 0,
  `Hidden` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `ProgrammingCode`
--

CREATE TABLE `ProgrammingCode` (
  `id` int(4) UNSIGNED NOT NULL,
  `idLanguage` varchar(50) NOT NULL,
  `idCommand` varchar(50) NOT NULL,
  `code` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `ProgrammingCommand`
--

CREATE TABLE `ProgrammingCommand` (
  `id` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `ProgrammingLanguage`
--

CREATE TABLE `ProgrammingLanguage` (
  `id` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `Protocols`
--

CREATE TABLE `Protocols` (
  `IdProtocol` varchar(255) NOT NULL DEFAULT '',
  `DefaultPort` int(12) UNSIGNED DEFAULT 0,
  `Description` varchar(255) DEFAULT '0',
  `UsePassword` int(1) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Protocols to synchronize supported by Ximdex CMS';

-- --------------------------------------------------------

--
-- Table structure `Pumpers`
--

CREATE TABLE `Pumpers` (
  `PumperId` int(12) UNSIGNED NOT NULL,
  `IdServer` int(12) UNSIGNED NOT NULL,
  `State` enum('New','Starting','Started','Ended') NOT NULL DEFAULT 'New',
  `StartTime` int(12) UNSIGNED DEFAULT 0,
  `CheckTime` int(12) UNSIGNED DEFAULT 0,
  `ProcessId` varchar(255) NOT NULL,
  `VacancyLevel` int(12) DEFAULT 0,
  `Pace` double DEFAULT 0,
  `ProcessedTasks` int(12) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `RelDocumentFolderToTemplatesIncludeFile`
--

CREATE TABLE `RelDocumentFolderToTemplatesIncludeFile` (
  `id` int(12) UNSIGNED NOT NULL,
  `source` int(12) UNSIGNED NOT NULL COMMENT 'idNode of the XML document folder',
  `target` int(12) UNSIGNED NOT NULL COMMENT 'idNode of the templates folder'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Relation between XML document and the templates_include.xsl file associated';

-- --------------------------------------------------------

--
-- Table structure `RelGroupsNodes`
--

CREATE TABLE `RelGroupsNodes` (
  `IdRel` int(12) UNSIGNED NOT NULL,
  `IdGroup` int(12) UNSIGNED NOT NULL,
  `IdNode` int(12) UNSIGNED NOT NULL,
  `IdRole` int(12) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Association of user groups with nodes';

-- --------------------------------------------------------

--
-- Table structure `RelLinkDescriptions`
--

CREATE TABLE `RelLinkDescriptions` (
  `IdRel` int(12) UNSIGNED NOT NULL,
  `IdLink` int(12) UNSIGNED NOT NULL,
  `Description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table of descriptions of Ximdex links';

-- --------------------------------------------------------

--
-- Table structure `RelMetadataGroupMetadata`
--

CREATE TABLE `RelMetadataGroupMetadata` (
  `idRelMetadataGroupMetadata` int(12) UNSIGNED NOT NULL,
  `idMetadataGroup` int(12) UNSIGNED NOT NULL,
  `idMetadata` int(12) UNSIGNED NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `readonly` tinyint(1) NOT NULL DEFAULT 0,
  `enabled` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Relation between metadata and metadata group';

-- --------------------------------------------------------

--
-- Table structure `RelMetadataSchemeNodeType`
--

CREATE TABLE `RelMetadataSchemeNodeType` (
  `idMetadataScheme` int(12) UNSIGNED NOT NULL,
  `idNodeType` int(12) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Relation between section and nodetype' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure `RelNode2Asset`
--

CREATE TABLE `RelNode2Asset` (
  `id` int(12) UNSIGNED NOT NULL,
  `source` int(12) UNSIGNED DEFAULT NULL,
  `target` int(12) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `RelNodeSetsNode`
--

CREATE TABLE `RelNodeSetsNode` (
  `Id` int(10) UNSIGNED NOT NULL,
  `IdSet` int(10) UNSIGNED NOT NULL,
  `IdNode` int(12) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `RelNodeSetsUsers`
--

CREATE TABLE `RelNodeSetsUsers` (
  `Id` int(10) UNSIGNED NOT NULL,
  `IdSet` int(10) UNSIGNED NOT NULL,
  `IdUser` int(12) UNSIGNED NOT NULL,
  `Owner` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `RelNodeTypeMimeType`
--

CREATE TABLE `RelNodeTypeMimeType` (
  `idRelNodeTypeMimeType` int(12) UNSIGNED NOT NULL,
  `idNodeType` int(12) UNSIGNED NOT NULL,
  `extension` varchar(255) DEFAULT NULL,
  `filter` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Relation between nodetypes and mime-types';

-- --------------------------------------------------------

--
-- Table structure `RelRolesActions`
--

CREATE TABLE `RelRolesActions` (
  `IdRel` int(12) UNSIGNED NOT NULL,
  `IdRol` int(12) UNSIGNED NOT NULL,
  `IdAction` int(12) UNSIGNED NOT NULL,
  `IdState` int(12) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Assignment of default command of each role';

-- --------------------------------------------------------

--
-- Table structure `RelRolesPermissions`
--

CREATE TABLE `RelRolesPermissions` (
  `IdRel` int(12) UNSIGNED NOT NULL,
  `IdRole` int(12) UNSIGNED NOT NULL,
  `IdPermission` int(12) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Association of roles and permits';

-- --------------------------------------------------------

--
-- Table structure `RelRolesStates`
--

CREATE TABLE `RelRolesStates` (
  `IdRel` int(12) UNSIGNED NOT NULL,
  `IdRole` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `IdState` int(12) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Association of roles with status transitions';

-- --------------------------------------------------------

--
-- Table structure `RelSectionXimlet`
--

CREATE TABLE `RelSectionXimlet` (
  `id` int(12) UNSIGNED NOT NULL,
  `source` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `target` int(12) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `RelSemanticTagsDescriptions`
--

CREATE TABLE `RelSemanticTagsDescriptions` (
  `IdTagDescription` int(11) UNSIGNED NOT NULL,
  `Tag` int(11) UNSIGNED NOT NULL,
  `Type` enum('GENERICS','ORGANISATIONS','PLACES','PEOPLE') NOT NULL,
  `Link` varchar(250) NOT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Descriptions and info for Tags';

-- --------------------------------------------------------

--
-- Table structure `RelSemanticTagsNodes`
--

CREATE TABLE `RelSemanticTagsNodes` (
  `Node` int(12) UNSIGNED NOT NULL,
  `TagDesc` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'RelTagsDescriptions  id '
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tags for each node';

-- --------------------------------------------------------

--
-- Table structure `RelServersChannels`
--

CREATE TABLE `RelServersChannels` (
  `IdRel` int(12) UNSIGNED NOT NULL,
  `IdServer` int(12) UNSIGNED DEFAULT 0,
  `IdChannel` int(12) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table which associates physical servers with channels .';

-- --------------------------------------------------------

--
-- Table structure `RelServersStates`
--

CREATE TABLE `RelServersStates` (
  `IdRel` int(12) UNSIGNED NOT NULL,
  `IdServer` int(12) UNSIGNED DEFAULT 0,
  `IdState` int(12) UNSIGNED DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table which associates servers with workflow status';

-- --------------------------------------------------------

--
-- Table structure `RelStrdocTemplate`
--

CREATE TABLE `RelStrdocTemplate` (
  `id` int(12) UNSIGNED NOT NULL,
  `source` int(12) UNSIGNED NOT NULL,
  `target` int(12) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `RelStrdocXimlet`
--

CREATE TABLE `RelStrdocXimlet` (
  `id` int(12) UNSIGNED NOT NULL,
  `source` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `target` int(12) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `RelTemplateContainer`
--

CREATE TABLE `RelTemplateContainer` (
  `IdRel` int(12) UNSIGNED NOT NULL,
  `IdTemplate` int(12) UNSIGNED NOT NULL,
  `IdContainer` int(12) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Associate template with container';

-- --------------------------------------------------------

--
-- Table structure `RelUsersGroups`
--

CREATE TABLE `RelUsersGroups` (
  `IdRel` int(12) UNSIGNED NOT NULL,
  `IdUser` int(12) UNSIGNED NOT NULL,
  `IdGroup` int(12) UNSIGNED NOT NULL,
  `IdRole` int(12) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Assing users to a group with a role';

-- --------------------------------------------------------

--
-- Table structure `RelVersionsLabel`
--

CREATE TABLE `RelVersionsLabel` (
  `id` int(12) UNSIGNED NOT NULL,
  `idVersion` int(12) UNSIGNED DEFAULT 0,
  `idLabel` int(12) UNSIGNED DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `RelXml2Xml`
--

CREATE TABLE `RelXml2Xml` (
  `id` int(12) UNSIGNED NOT NULL,
  `source` int(12) UNSIGNED NOT NULL,
  `target` int(12) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `Roles`
--

CREATE TABLE `Roles` (
  `IdRole` int(12) UNSIGNED NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Icon` varchar(255) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table of roles that an user can play into a group';

-- --------------------------------------------------------

--
-- Table structure `SearchFilters`
--

CREATE TABLE `SearchFilters` (
  `Id` int(10) UNSIGNED NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Handler` varchar(5) NOT NULL,
  `Filter` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `Section`
--

CREATE TABLE `Section` (
  `IdNode` int(12) UNSIGNED NOT NULL,
  `idSectionType` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `SectionTypes`
--

CREATE TABLE `SectionTypes` (
  `idSectionType` int(11) NOT NULL,
  `sectionType` enum('Normal','Xnews') NOT NULL,
  `idNodeType` int(12) UNSIGNED NOT NULL,
  `module` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `SemanticNamespaces`
--

CREATE TABLE `SemanticNamespaces` (
  `idNamespace` int(12) UNSIGNED NOT NULL,
  `service` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `nemo` varchar(255) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `recursive` int(8) NOT NULL DEFAULT 0,
  `category` varchar(255) NOT NULL,
  `isSemantic` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Namespaces for semantic tagging.';

-- --------------------------------------------------------

--
-- Table structure `SemanticTags`
--

CREATE TABLE `SemanticTags` (
  `IdTag` int(11) UNSIGNED NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Total` mediumint(6) UNSIGNED NOT NULL DEFAULT 1,
  `IdNamespace` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='List Tags';

-- --------------------------------------------------------

--
-- Table structure `ServerFrames`
--

CREATE TABLE `ServerFrames` (
  `IdSync` int(12) UNSIGNED NOT NULL,
  `IdServer` int(12) UNSIGNED NOT NULL,
  `DateUp` int(14) UNSIGNED NOT NULL,
  `DateDown` int(14) UNSIGNED DEFAULT NULL,
  `State` enum('Pending','Due2In','Due2In_','Due2Out','Due2Out_','Pumped','Out','Closing','In','Replaced','Removed','Canceled','Due2InWithError','Due2OutWithError','Outdated') NOT NULL DEFAULT 'Pending',
  `ErrorLevel` enum('1','2') DEFAULT NULL COMMENT 'Errors: 1 Soft, 2 Hard',
  `RemotePath` text DEFAULT NULL,
  `FileName` varchar(255) NOT NULL DEFAULT '',
  `Retry` int(12) UNSIGNED DEFAULT 0,
  `Linked` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `IdNodeFrame` int(12) UNSIGNED NOT NULL,
  `IdBatchUp` int(12) UNSIGNED DEFAULT NULL,
  `PumperId` int(12) UNSIGNED DEFAULT NULL,
  `IdChannelFrame` int(12) UNSIGNED DEFAULT NULL,
  `FileSize` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `cache` tinyint(1) NOT NULL DEFAULT 1,
  `IdBatchDown` int(12) UNSIGNED DEFAULT NULL,
  `ChannelId` int(12) UNSIGNED DEFAULT NULL,
  `NodeId` int(12) UNSIGNED NOT NULL,
  `IdPortalFrame` int(12) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='XimDEX Table Synchronization';

-- --------------------------------------------------------

--
-- Table structure `Servers`
--

CREATE TABLE `Servers` (
  `IdServer` int(12) UNSIGNED NOT NULL,
  `Description` varchar(255) NOT NULL,
  `IdNode` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `IdProtocol` varchar(255) DEFAULT NULL,
  `Login` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Host` varchar(255) DEFAULT NULL,
  `Port` int(12) UNSIGNED DEFAULT NULL,
  `Url` text DEFAULT NULL,
  `InitialDirectory` text DEFAULT NULL,
  `OverrideLocalPaths` int(1) UNSIGNED DEFAULT 0,
  `Enabled` int(1) UNSIGNED DEFAULT 1,
  `Previsual` int(1) DEFAULT 0,
  `idEncode` varchar(255) NOT NULL DEFAULT 'UTF-8',
  `ActiveForPumping` int(1) UNSIGNED NOT NULL DEFAULT 1,
  `DelayTimeToEnableForPumping` int(12) UNSIGNED DEFAULT NULL,
  `CyclesToRetryPumping` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `Token` varchar(255) DEFAULT NULL COMMENT 'API token',
  `Indexable` tinyint(1) NOT NULL DEFAULT 0,
  `LastSitemapGenerationTime` int(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table with info about Ximdex servers';

-- --------------------------------------------------------

--
-- Table structure `StructuredDocuments`
--

CREATE TABLE `StructuredDocuments` (
  `IdDoc` int(12) UNSIGNED NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `IdCreator` int(12) UNSIGNED DEFAULT NULL,
  `CreationDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `UpdateDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `IdLanguage` int(12) UNSIGNED DEFAULT NULL,
  `IdTemplate` int(12) UNSIGNED DEFAULT NULL,
  `TargetLink` int(12) UNSIGNED DEFAULT NULL,
  `XsltErrors` text DEFAULT NULL COMMENT 'XSL transformation process errors'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table of structured documents of Ximdex';

-- --------------------------------------------------------

--
-- Table structure `Transitions`
--

CREATE TABLE `Transitions` (
  `id` int(12) UNSIGNED NOT NULL,
  `cacheable` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(50) NOT NULL,
  `viewClass` varchar(50) DEFAULT NULL,
  `previousTransitionId` int(12) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `TransitionsCache`
--

CREATE TABLE `TransitionsCache` (
  `id` int(12) UNSIGNED NOT NULL,
  `versionId` int(12) UNSIGNED NOT NULL,
  `transitionId` int(12) UNSIGNED NOT NULL,
  `file` varchar(255) NOT NULL,
  `channelId` int(12) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `Users`
--

CREATE TABLE `Users` (
  `IdUser` int(12) UNSIGNED NOT NULL,
  `Login` varchar(255) NOT NULL,
  `Pass` varchar(255) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL DEFAULT '',
  `Locale` varchar(5) DEFAULT NULL COMMENT 'User Locale',
  `LastLogin` int(14) UNSIGNED DEFAULT NULL,
  `NumAccess` int(12) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Users registered on Ximdex CMS';

-- --------------------------------------------------------

--
-- Table structure `Versions`
--

CREATE TABLE `Versions` (
  `IdVersion` int(12) UNSIGNED NOT NULL,
  `IdNode` int(12) UNSIGNED NOT NULL,
  `Version` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `SubVersion` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `File` varchar(255) DEFAULT NULL,
  `IdUser` int(12) UNSIGNED DEFAULT NULL,
  `Date` int(14) UNSIGNED DEFAULT 0,
  `Comment` text DEFAULT NULL,
  `IdSync` int(12) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table of contents and version management';

-- --------------------------------------------------------

--
-- Table structure `Workflow`
--

CREATE TABLE `Workflow` (
  `id` int(4) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `master` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure `WorkflowStatus`
--

CREATE TABLE `WorkflowStatus` (
  `id` int(12) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL COMMENT 'Class and method names to call separated by @',
  `sort` int(4) UNSIGNED NOT NULL DEFAULT 0,
  `workflowId` int(4) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table indexes `Actions`
--
ALTER TABLE `Actions`
  ADD PRIMARY KEY (`IdAction`),
  ADD KEY `Actions_NodeTypes` (`IdNodeType`);

--
-- Table indexes `Batchs`
--
ALTER TABLE `Batchs`
  ADD PRIMARY KEY (`IdBatch`),
  ADD KEY `IdBatchDown` (`IdBatchDown`),
  ADD KEY `IdNodeGenerator` (`IdNodeGenerator`),
  ADD KEY `IdPortalVersion` (`IdPortalFrame`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `State` (`State`),
  ADD KEY `ServerId` (`ServerId`);

--
-- Table indexes `ChannelFrames`
--
ALTER TABLE `ChannelFrames`
  ADD PRIMARY KEY (`IdChannelFrame`),
  ADD KEY `ChannelId` (`ChannelId`),
  ADD KEY `NodeId` (`NodeId`);

--
-- Table indexes `Channels`
--
ALTER TABLE `Channels`
  ADD PRIMARY KEY (`IdChannel`),
  ADD UNIQUE KEY `Name` (`Name`),
  ADD KEY `idLanguage` (`idLanguage`);

--
-- Table indexes `Config`
--
ALTER TABLE `Config`
  ADD PRIMARY KEY (`IdConfig`),
  ADD UNIQUE KEY `ConfigKey` (`ConfigKey`);

--
-- Table indexes `DependenceTypes`
--
ALTER TABLE `DependenceTypes`
  ADD PRIMARY KEY (`IdDepType`),
  ADD UNIQUE KEY `Type` (`Type`);

--
-- Table indexes `Dependencies`
--
ALTER TABLE `Dependencies`
  ADD PRIMARY KEY (`IdDep`),
  ADD KEY `IdNodeMaster` (`IdNodeMaster`),
  ADD KEY `IdNodeDependent` (`IdNodeDependent`),
  ADD KEY `DepType` (`DepType`);

--
-- Table indexes `Encodes`
--
ALTER TABLE `Encodes`
  ADD PRIMARY KEY (`IdEncode`);

--
-- Table indexes `FastTraverse`
--
ALTER TABLE `FastTraverse`
  ADD PRIMARY KEY (`IdNode`,`IdChild`),
  ADD KEY `IdN` (`IdNode`),
  ADD KEY `IdC` (`IdChild`);

--
-- Table indexes `Groups`
--
ALTER TABLE `Groups`
  ADD PRIMARY KEY (`IdGroup`),
  ADD UNIQUE KEY `Name` (`Name`);

--
-- Table indexes `IsoCodes`
--
ALTER TABLE `IsoCodes`
  ADD PRIMARY KEY (`IdIsoCode`),
  ADD UNIQUE KEY `iso2` (`Iso2`),
  ADD UNIQUE KEY `NativeName` (`NativeName`),
  ADD UNIQUE KEY `name` (`Name`),
  ADD UNIQUE KEY `iso3` (`Iso3`);

--
-- Table indexes `Languages`
--
ALTER TABLE `Languages`
  ADD PRIMARY KEY (`IdLanguage`),
  ADD UNIQUE KEY `Name` (`Name`),
  ADD KEY `Languages_IsoCodes` (`IsoName`);

--
-- Table indexes `Links`
--
ALTER TABLE `Links`
  ADD PRIMARY KEY (`IdLink`);

--
-- Table indexes `List`
--
ALTER TABLE `List`
  ADD PRIMARY KEY (`id`);

--
-- Table indexes `List_Label`
--
ALTER TABLE `List_Label`
  ADD PRIMARY KEY (`id`);

--
-- Table indexes `Locales`
--
ALTER TABLE `Locales`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Code` (`Code`),
  ADD UNIQUE KEY `Name` (`Name`);

--
-- Table indexes `Messages`
--
ALTER TABLE `Messages`
  ADD PRIMARY KEY (`IdMessage`);

--
-- Table indexes `Metadata`
--
ALTER TABLE `Metadata`
  ADD PRIMARY KEY (`idMetadata`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Table indexes `MetadataGroup`
--
ALTER TABLE `MetadataGroup`
  ADD PRIMARY KEY (`idMetadataGroup`),
  ADD UNIQUE KEY `idMetadataScheme` (`idMetadataScheme`,`name`),
  ADD KEY `idMetadataSection` (`idMetadataScheme`);

--
-- Table indexes `MetadataScheme`
--
ALTER TABLE `MetadataScheme`
  ADD PRIMARY KEY (`idMetadataScheme`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Table indexes `MetadataValue`
--
ALTER TABLE `MetadataValue`
  ADD UNIQUE KEY `idNode` (`idNode`,`idRelMetadataGroupMetadata`),
  ADD KEY `idRelMetadataGroupMetadata` (`idRelMetadataGroupMetadata`);

--
-- Table indexes `NoActionsInNode`
--
ALTER TABLE `NoActionsInNode`
  ADD PRIMARY KEY (`IdNode`,`IdAction`),
  ADD KEY `NoActionsInNode_Actions` (`IdAction`);

--
-- Table indexes `NodeAllowedContents`
--
ALTER TABLE `NodeAllowedContents`
  ADD PRIMARY KEY (`IdNodeAllowedContent`),
  ADD UNIQUE KEY `UniqeAmmount` (`IdNodeType`,`NodeType`),
  ADD KEY `NodeAllowedContents_Nodetype_Content` (`NodeType`);

--
-- Table indexes `NodeConstructors`
--
ALTER TABLE `NodeConstructors`
  ADD PRIMARY KEY (`IdNodeConstructor`),
  ADD KEY `NodeConstructors_Actions` (`IdAction`),
  ADD KEY `NodeConstructors_NodeTypes` (`IdNodeType`);

--
-- Table indexes `NodeDefaultContents`
--
ALTER TABLE `NodeDefaultContents`
  ADD PRIMARY KEY (`IdNodeDefaultContent`),
  ADD UNIQUE KEY `UniqueName` (`Name`,`IdNodeType`),
  ADD KEY `NodeDefaultContents_NodeTypes` (`IdNodeType`),
  ADD KEY `NodeDefaultContents_NodeTypes_target` (`NodeType`);

--
-- Table indexes `NodeDependencies`
--
ALTER TABLE `NodeDependencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `IdNode_2` (`IdNode`,`IdResource`,`IdChannel`),
  ADD KEY `IdResource` (`IdResource`),
  ADD KEY `IdNode` (`IdNode`),
  ADD KEY `NodeDependencies_Channels` (`IdChannel`);

--
-- Table indexes `NodeEdition`
--
ALTER TABLE `NodeEdition`
  ADD PRIMARY KEY (`Id`);

--
-- Table indexes `NodeFrames`
--
ALTER TABLE `NodeFrames`
  ADD PRIMARY KEY (`IdNodeFrame`),
  ADD KEY `NodeId` (`NodeId`),
  ADD KEY `VersionId` (`VersionId`),
  ADD KEY `NodeFrames_PortalFrames` (`IdPortalFrame`);

--
-- Table indexes `NodeNameTranslations`
--
ALTER TABLE `NodeNameTranslations`
  ADD PRIMARY KEY (`IdRel`),
  ADD UNIQUE KEY `NodeLanguage` (`IdNode`,`IdLanguage`),
  ADD KEY `NodeNameTranslations_Languages` (`IdLanguage`);

--
-- Table indexes `NodeProperties`
--
ALTER TABLE `NodeProperties`
  ADD PRIMARY KEY (`IdNodeProperty`),
  ADD KEY `IdNode` (`IdNode`);

--
-- Table indexes `Nodes`
--
ALTER TABLE `Nodes`
  ADD PRIMARY KEY (`IdNode`),
  ADD UNIQUE KEY `UniqueName` (`Name`,`IdParent`),
  ADD KEY `IdParent` (`IdParent`),
  ADD KEY `IdState` (`IdState`) USING BTREE,
  ADD KEY `IdNodeType` (`IdNodeType`),
  ADD KEY `Nodes_Users` (`BlockUser`),
  ADD KEY `Nodes_SharedWorkflow` (`SharedWorkflow`),
  ADD KEY `Nodes_NodeFrames` (`ActiveNF`);

--
-- Table indexes `NodeSets`
--
ALTER TABLE `NodeSets`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `U_SET` (`Name`);

--
-- Table indexes `NodesToPublish`
--
ALTER TABLE `NodesToPublish`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `NodesToPublish_Nodes` (`IdNode`),
  ADD KEY `NodesToPublish_Nodes_Generator` (`IdNodeGenerator`),
  ADD KEY `NodesToPublish_Users` (`UserId`);

--
-- Table indexes `NodetypeModes`
--
ALTER TABLE `NodetypeModes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `IdNodeType_2` (`IdNodeType`,`Mode`,`IdAction`),
  ADD KEY `IdNodeType` (`IdNodeType`,`IdAction`),
  ADD KEY `NodetypeModes_Actions` (`IdAction`);

--
-- Table indexes `NodeTypes`
--
ALTER TABLE `NodeTypes`
  ADD PRIMARY KEY (`IdNodeType`),
  ADD UNIQUE KEY `IdType` (`Name`),
  ADD KEY `IdType_2` (`IdNodeType`),
  ADD KEY `NodeTypes_Workflow` (`workflowId`);

--
-- Table indexes `Permissions`
--
ALTER TABLE `Permissions`
  ADD PRIMARY KEY (`IdPermission`),
  ADD UNIQUE KEY `IdName` (`Name`);

--
-- Table indexes `PortalFrames`
--
ALTER TABLE `PortalFrames`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `IdNodeGenerator` (`IdNodeGenerator`,`Version`),
  ADD KEY `IdPortal` (`IdNodeGenerator`),
  ADD KEY `CreatedBy` (`CreatedBy`),
  ADD KEY `Status` (`Status`),
  ADD KEY `PublishingType` (`PublishingType`),
  ADD KEY `ScheduledTime` (`ScheduledTime`),
  ADD KEY `Playing` (`Playing`),
  ADD KEY `Priority` (`SuccessRate`),
  ADD KEY `Cycles` (`BoostCycles`);

--
-- Table indexes `ProgrammingCode`
--
ALTER TABLE `ProgrammingCode`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idProgLanguage` (`idLanguage`,`idCommand`),
  ADD KEY `idCommand` (`idCommand`);

--
-- Table indexes `ProgrammingCommand`
--
ALTER TABLE `ProgrammingCommand`
  ADD PRIMARY KEY (`id`);

--
-- Table indexes `ProgrammingLanguage`
--
ALTER TABLE `ProgrammingLanguage`
  ADD PRIMARY KEY (`id`);

--
-- Table indexes `Protocols`
--
ALTER TABLE `Protocols`
  ADD PRIMARY KEY (`IdProtocol`);

--
-- Table indexes `Pumpers`
--
ALTER TABLE `Pumpers`
  ADD PRIMARY KEY (`PumperId`),
  ADD KEY `IdServer` (`IdServer`),
  ADD KEY `State` (`State`);

--
-- Table indexes `RelDocumentFolderToTemplatesIncludeFile`
--
ALTER TABLE `RelDocumentFolderToTemplatesIncludeFile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_source` (`source`) USING BTREE,
  ADD KEY `source` (`source`),
  ADD KEY `target` (`target`);

--
-- Table indexes `RelGroupsNodes`
--
ALTER TABLE `RelGroupsNodes`
  ADD PRIMARY KEY (`IdRel`),
  ADD UNIQUE KEY `uniq` (`IdNode`,`IdGroup`,`IdRole`) USING BTREE,
  ADD KEY `IdGroup` (`IdGroup`),
  ADD KEY `IdNode` (`IdNode`),
  ADD KEY `RelGroupsNodes_Roles` (`IdRole`);

--
-- Table indexes `RelLinkDescriptions`
--
ALTER TABLE `RelLinkDescriptions`
  ADD PRIMARY KEY (`IdRel`),
  ADD UNIQUE KEY `Description` (`IdLink`,`Description`);

--
-- Table indexes `RelMetadataGroupMetadata`
--
ALTER TABLE `RelMetadataGroupMetadata`
  ADD PRIMARY KEY (`idRelMetadataGroupMetadata`),
  ADD UNIQUE KEY `idMetadataGroup` (`idMetadataGroup`,`idMetadata`),
  ADD KEY `idMetadata` (`idMetadata`);

--
-- Table indexes `RelMetadataSchemeNodeType`
--
ALTER TABLE `RelMetadataSchemeNodeType`
  ADD UNIQUE KEY `idMetadataSection` (`idMetadataScheme`,`idNodeType`),
  ADD KEY `idNodeType` (`idNodeType`);

--
-- Table indexes `RelNode2Asset`
--
ALTER TABLE `RelNode2Asset`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rel` (`source`,`target`),
  ADD KEY `RelXml2Xml_source` (`source`),
  ADD KEY `RelXml2Xml_target` (`target`);

--
-- Table indexes `RelNodeSetsNode`
--
ALTER TABLE `RelNodeSetsNode`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `U_SETNODES` (`IdSet`,`IdNode`);

--
-- Table indexes `RelNodeSetsUsers`
--
ALTER TABLE `RelNodeSetsUsers`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `U_SETUSERS` (`IdSet`,`IdUser`);

--
-- Table indexes `RelNodeTypeMimeType`
--
ALTER TABLE `RelNodeTypeMimeType`
  ADD PRIMARY KEY (`idRelNodeTypeMimeType`),
  ADD UNIQUE KEY `idNodeType` (`idNodeType`);

--
-- Table indexes `RelRolesActions`
--
ALTER TABLE `RelRolesActions`
  ADD PRIMARY KEY (`IdRel`),
  ADD UNIQUE KEY `IdRol_2` (`IdRol`,`IdAction`,`IdState`),
  ADD KEY `IdRol` (`IdRol`),
  ADD KEY `IdAction` (`IdAction`),
  ADD KEY `RelRolesActions_Status` (`IdState`);

--
-- Table indexes `RelRolesPermissions`
--
ALTER TABLE `RelRolesPermissions`
  ADD PRIMARY KEY (`IdRel`),
  ADD UNIQUE KEY `IdRole` (`IdRole`,`IdPermission`),
  ADD KEY `RelRolesPermissions_Permissions` (`IdPermission`);

--
-- Table indexes `RelRolesStates`
--
ALTER TABLE `RelRolesStates`
  ADD PRIMARY KEY (`IdRel`),
  ADD UNIQUE KEY `items` (`IdRole`,`IdState`),
  ADD KEY `RelRolesStates_WorkflowStatus` (`IdState`);

--
-- Table indexes `RelSectionXimlet`
--
ALTER TABLE `RelSectionXimlet`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rel` (`source`,`target`);

--
-- Table indexes `RelSemanticTagsDescriptions`
--
ALTER TABLE `RelSemanticTagsDescriptions`
  ADD PRIMARY KEY (`IdTagDescription`),
  ADD KEY `RelSemanticTagsDescriptions_Tag` (`Tag`);

--
-- Table indexes `RelSemanticTagsNodes`
--
ALTER TABLE `RelSemanticTagsNodes`
  ADD PRIMARY KEY (`Node`,`TagDesc`),
  ADD KEY `TagDesc` (`TagDesc`);

--
-- Table indexes `RelServersChannels`
--
ALTER TABLE `RelServersChannels`
  ADD PRIMARY KEY (`IdRel`),
  ADD KEY `IdChannel` (`IdChannel`),
  ADD KEY `IdServer` (`IdServer`);

--
-- Table indexes `RelServersStates`
--
ALTER TABLE `RelServersStates`
  ADD PRIMARY KEY (`IdRel`),
  ADD UNIQUE KEY `IdRel` (`IdRel`),
  ADD KEY `IdRel_2` (`IdRel`);

--
-- Table indexes `RelStrdocTemplate`
--
ALTER TABLE `RelStrdocTemplate`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rel` (`source`,`target`),
  ADD KEY `RelStrdocTemplate_source` (`source`),
  ADD KEY `RelStrdocTemplate_target` (`target`);

--
-- Table indexes `RelStrdocXimlet`
--
ALTER TABLE `RelStrdocXimlet`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rel` (`source`,`target`);

--
-- Table indexes `RelTemplateContainer`
--
ALTER TABLE `RelTemplateContainer`
  ADD PRIMARY KEY (`IdRel`),
  ADD UNIQUE KEY `IdRel` (`IdRel`,`IdTemplate`),
  ADD KEY `RelTemplateConatiner_Nodes_Container` (`IdContainer`),
  ADD KEY `RelTemplateConatiner_Nodes_Template` (`IdTemplate`);

--
-- Table indexes `RelUsersGroups`
--
ALTER TABLE `RelUsersGroups`
  ADD PRIMARY KEY (`IdRel`),
  ADD UNIQUE KEY `IdUser_2` (`IdUser`,`IdGroup`),
  ADD KEY `IdUSer` (`IdUser`),
  ADD KEY `IdGroup` (`IdGroup`),
  ADD KEY `RelUsersGroups_Roles` (`IdRole`);

--
-- Table indexes `RelVersionsLabel`
--
ALTER TABLE `RelVersionsLabel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `VersionsLabelRest` (`idVersion`,`idLabel`);

--
-- Table indexes `RelXml2Xml`
--
ALTER TABLE `RelXml2Xml`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rel` (`source`,`target`),
  ADD KEY `RelXml2Xml_source` (`source`),
  ADD KEY `RelXml2Xml_target` (`target`);

--
-- Table indexes `Roles`
--
ALTER TABLE `Roles`
  ADD PRIMARY KEY (`IdRole`),
  ADD UNIQUE KEY `IdRole` (`Name`);

--
-- Table indexes `SearchFilters`
--
ALTER TABLE `SearchFilters`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `U_FILTER` (`Name`);

--
-- Table indexes `Section`
--
ALTER TABLE `Section`
  ADD PRIMARY KEY (`IdNode`),
  ADD KEY `idSectionType` (`idSectionType`);

--
-- Table indexes `SectionTypes`
--
ALTER TABLE `SectionTypes`
  ADD PRIMARY KEY (`idSectionType`),
  ADD UNIQUE KEY `sectionType` (`sectionType`),
  ADD KEY `idSectionType` (`idSectionType`),
  ADD KEY `SectionTypes_NodeTypes` (`idNodeType`);

--
-- Table indexes `SemanticNamespaces`
--
ALTER TABLE `SemanticNamespaces`
  ADD PRIMARY KEY (`idNamespace`);

--
-- Table indexes `SemanticTags`
--
ALTER TABLE `SemanticTags`
  ADD PRIMARY KEY (`IdTag`),
  ADD UNIQUE KEY `Name` (`Name`,`IdNamespace`),
  ADD KEY `IdNamespace` (`IdNamespace`);
ALTER TABLE `SemanticTags` ADD FULLTEXT KEY `Name_2` (`Name`);

--
-- Table indexes `ServerFrames`
--
ALTER TABLE `ServerFrames`
  ADD PRIMARY KEY (`IdSync`),
  ADD KEY `IdNodeFrame` (`IdNodeFrame`),
  ADD KEY `IdBatchUp` (`IdBatchUp`),
  ADD KEY `IdServer` (`IdServer`),
  ADD KEY `PumperId` (`PumperId`),
  ADD KEY `IdChannelFrame` (`IdChannelFrame`),
  ADD KEY `IdBatchDown` (`IdBatchDown`),
  ADD KEY `NodeId` (`NodeId`),
  ADD KEY `ChannelId` (`ChannelId`),
  ADD KEY `IdPortalFrame` (`IdPortalFrame`);

--
-- Table indexes `Servers`
--
ALTER TABLE `Servers`
  ADD PRIMARY KEY (`IdServer`),
  ADD KEY `IdNode` (`IdNode`),
  ADD KEY `IdProtocol` (`IdProtocol`),
  ADD KEY `Servers_Encodes` (`idEncode`);

--
-- Table indexes `StructuredDocuments`
--
ALTER TABLE `StructuredDocuments`
  ADD PRIMARY KEY (`IdDoc`),
  ADD KEY `StructuredDocuments_Users` (`IdCreator`),
  ADD KEY `StructuredDocuments_Languages` (`IdLanguage`),
  ADD KEY `StructuredDocuments_Templates` (`IdTemplate`),
  ADD KEY `StructuredDocuments_StructuredDocuments` (`TargetLink`);

--
-- Table indexes `Transitions`
--
ALTER TABLE `Transitions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Name` (`name`),
  ADD KEY `Transitions_Transitions` (`previousTransitionId`);

--
-- Table indexes `TransitionsCache`
--
ALTER TABLE `TransitionsCache`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `file` (`file`),
  ADD UNIQUE KEY `IdVersion` (`versionId`,`transitionId`,`channelId`) USING BTREE,
  ADD KEY `transitionId` (`transitionId`),
  ADD KEY `TransitionsCache_Channels` (`channelId`);

--
-- Table indexes `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`IdUser`),
  ADD UNIQUE KEY `login` (`Login`),
  ADD UNIQUE KEY `Name` (`Name`),
  ADD KEY `Users_Locales` (`Locale`);

--
-- Table indexes `Versions`
--
ALTER TABLE `Versions`
  ADD PRIMARY KEY (`IdVersion`),
  ADD KEY `Version` (`SubVersion`,`IdNode`,`Version`),
  ADD KEY `IdNode` (`IdNode`),
  ADD KEY `Versions_Users` (`IdUser`),
  ADD KEY `Versions_ServerFrames` (`IdSync`);

--
-- Table indexes `Workflow`
--
ALTER TABLE `Workflow`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Table indexes `WorkflowStatus`
--
ALTER TABLE `WorkflowStatus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `WorkflowStatus_Workflow` (`workflowId`);


--
-- AUTO_INCREMENT for table `Actions`
--
ALTER TABLE `Actions`
  MODIFY `IdAction` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Batchs`
--
ALTER TABLE `Batchs`
  MODIFY `IdBatch` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ChannelFrames`
--
ALTER TABLE `ChannelFrames`
  MODIFY `IdChannelFrame` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Config`
--
ALTER TABLE `Config`
  MODIFY `IdConfig` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `DependenceTypes`
--
ALTER TABLE `DependenceTypes`
  MODIFY `IdDepType` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Dependencies`
--
ALTER TABLE `Dependencies`
  MODIFY `IdDep` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `IsoCodes`
--
ALTER TABLE `IsoCodes`
  MODIFY `IdIsoCode` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `List`
--
ALTER TABLE `List`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `List_Label`
--
ALTER TABLE `List_Label`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Locales`
--
ALTER TABLE `Locales`
  MODIFY `ID` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Messages`
--
ALTER TABLE `Messages`
  MODIFY `IdMessage` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Metadata`
--
ALTER TABLE `Metadata`
  MODIFY `idMetadata` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `MetadataGroup`
--
ALTER TABLE `MetadataGroup`
  MODIFY `idMetadataGroup` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `MetadataScheme`
--
ALTER TABLE `MetadataScheme`
  MODIFY `idMetadataScheme` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `NodeAllowedContents`
--
ALTER TABLE `NodeAllowedContents`
  MODIFY `IdNodeAllowedContent` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `NodeConstructors`
--
ALTER TABLE `NodeConstructors`
  MODIFY `IdNodeConstructor` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `NodeDefaultContents`
--
ALTER TABLE `NodeDefaultContents`
  MODIFY `IdNodeDefaultContent` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `NodeDependencies`
--
ALTER TABLE `NodeDependencies`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `NodeEdition`
--
ALTER TABLE `NodeEdition`
  MODIFY `Id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `NodeFrames`
--
ALTER TABLE `NodeFrames`
  MODIFY `IdNodeFrame` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `NodeNameTranslations`
--
ALTER TABLE `NodeNameTranslations`
  MODIFY `IdRel` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `NodeProperties`
--
ALTER TABLE `NodeProperties`
  MODIFY `IdNodeProperty` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Nodes`
--
ALTER TABLE `Nodes`
  MODIFY `IdNode` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `NodeSets`
--
ALTER TABLE `NodeSets`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `NodesToPublish`
--
ALTER TABLE `NodesToPublish`
  MODIFY `Id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `NodetypeModes`
--
ALTER TABLE `NodetypeModes`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `NodeTypes`
--
ALTER TABLE `NodeTypes`
  MODIFY `IdNodeType` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Permissions`
--
ALTER TABLE `Permissions`
  MODIFY `IdPermission` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PortalFrames`
--
ALTER TABLE `PortalFrames`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ProgrammingCode`
--
ALTER TABLE `ProgrammingCode`
  MODIFY `id` int(4) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Pumpers`
--
ALTER TABLE `Pumpers`
  MODIFY `PumperId` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelDocumentFolderToTemplatesIncludeFile`
--
ALTER TABLE `RelDocumentFolderToTemplatesIncludeFile`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelGroupsNodes`
--
ALTER TABLE `RelGroupsNodes`
  MODIFY `IdRel` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelLinkDescriptions`
--
ALTER TABLE `RelLinkDescriptions`
  MODIFY `IdRel` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelMetadataGroupMetadata`
--
ALTER TABLE `RelMetadataGroupMetadata`
  MODIFY `idRelMetadataGroupMetadata` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelNode2Asset`
--
ALTER TABLE `RelNode2Asset`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelNodeSetsNode`
--
ALTER TABLE `RelNodeSetsNode`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelNodeSetsUsers`
--
ALTER TABLE `RelNodeSetsUsers`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelNodeTypeMimeType`
--
ALTER TABLE `RelNodeTypeMimeType`
  MODIFY `idRelNodeTypeMimeType` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelRolesActions`
--
ALTER TABLE `RelRolesActions`
  MODIFY `IdRel` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelRolesPermissions`
--
ALTER TABLE `RelRolesPermissions`
  MODIFY `IdRel` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelRolesStates`
--
ALTER TABLE `RelRolesStates`
  MODIFY `IdRel` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelSectionXimlet`
--
ALTER TABLE `RelSectionXimlet`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelSemanticTagsDescriptions`
--
ALTER TABLE `RelSemanticTagsDescriptions`
  MODIFY `IdTagDescription` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelServersChannels`
--
ALTER TABLE `RelServersChannels`
  MODIFY `IdRel` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelServersStates`
--
ALTER TABLE `RelServersStates`
  MODIFY `IdRel` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelStrdocTemplate`
--
ALTER TABLE `RelStrdocTemplate`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelStrdocXimlet`
--
ALTER TABLE `RelStrdocXimlet`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelTemplateContainer`
--
ALTER TABLE `RelTemplateContainer`
  MODIFY `IdRel` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelUsersGroups`
--
ALTER TABLE `RelUsersGroups`
  MODIFY `IdRel` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelVersionsLabel`
--
ALTER TABLE `RelVersionsLabel`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `RelXml2Xml`
--
ALTER TABLE `RelXml2Xml`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `SearchFilters`
--
ALTER TABLE `SearchFilters`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `SectionTypes`
--
ALTER TABLE `SectionTypes`
  MODIFY `idSectionType` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `SemanticNamespaces`
--
ALTER TABLE `SemanticNamespaces`
  MODIFY `idNamespace` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `SemanticTags`
--
ALTER TABLE `SemanticTags`
  MODIFY `IdTag` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ServerFrames`
--
ALTER TABLE `ServerFrames`
  MODIFY `IdSync` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Servers`
--
ALTER TABLE `Servers`
  MODIFY `IdServer` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Transitions`
--
ALTER TABLE `Transitions`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `TransitionsCache`
--
ALTER TABLE `TransitionsCache`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Versions`
--
ALTER TABLE `Versions`
  MODIFY `IdVersion` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `WorkflowStatus`
--
ALTER TABLE `WorkflowStatus`
  MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;


--
-- Table filters `Actions`
--
ALTER TABLE `Actions`
  ADD CONSTRAINT `Actions_NodeTypes` FOREIGN KEY (`IdNodeType`) REFERENCES `NodeTypes` (`IdNodeType`);

--
-- Table filters `Batchs`
--
ALTER TABLE `Batchs`
  ADD CONSTRAINT `Batchs_Batchs` FOREIGN KEY (`IdBatchDown`) REFERENCES `Batchs` (`IdBatch`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Batchs_Nodes` FOREIGN KEY (`IdNodeGenerator`) REFERENCES `Nodes` (`IdNode`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Batchs_PortalFrames` FOREIGN KEY (`IdPortalFrame`) REFERENCES `PortalFrames` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Batchs_Servers` FOREIGN KEY (`ServerId`) REFERENCES `Servers` (`IdServer`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Batchs_Users` FOREIGN KEY (`UserId`) REFERENCES `Users` (`IdUser`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Table filters `ChannelFrames`
--
ALTER TABLE `ChannelFrames`
  ADD CONSTRAINT `ChannelFrames_Channels` FOREIGN KEY (`ChannelId`) REFERENCES `Channels` (`IdChannel`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ChannelFrames_Nodes` FOREIGN KEY (`NodeId`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `Channels`
--
ALTER TABLE `Channels`
  ADD CONSTRAINT `Channels_Nodes` FOREIGN KEY (`IdChannel`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Channels_ProgrammingLanguage` FOREIGN KEY (`idLanguage`) REFERENCES `ProgrammingLanguage` (`id`);

--
-- Table filters `Dependencies`
--
ALTER TABLE `Dependencies`
  ADD CONSTRAINT `Dependencies_DependenceTypes` FOREIGN KEY (`DepType`) REFERENCES `DependenceTypes` (`IdDepType`),
  ADD CONSTRAINT `Dependencies_Nodes_dep` FOREIGN KEY (`IdNodeDependent`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Dependencies_Nodes_master` FOREIGN KEY (`IdNodeMaster`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `FastTraverse`
--
ALTER TABLE `FastTraverse`
  ADD CONSTRAINT `FastTraverse_Nodes_child` FOREIGN KEY (`IdChild`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FastTraverse_Nodes_parent` FOREIGN KEY (`IdNode`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `Groups`
--
ALTER TABLE `Groups`
  ADD CONSTRAINT `Groups_Nodes` FOREIGN KEY (`IdGroup`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `Languages`
--
ALTER TABLE `Languages`
  ADD CONSTRAINT `Languages_IsoCodes` FOREIGN KEY (`IsoName`) REFERENCES `IsoCodes` (`Iso2`) ON UPDATE CASCADE,
  ADD CONSTRAINT `Languages_Nodes` FOREIGN KEY (`IdLanguage`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `Links`
--
ALTER TABLE `Links`
  ADD CONSTRAINT `Links_Nodes` FOREIGN KEY (`IdLink`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `MetadataGroup`
--
ALTER TABLE `MetadataGroup`
  ADD CONSTRAINT `MetadataGroup_MetadataScheme` FOREIGN KEY (`idMetadataScheme`) REFERENCES `MetadataScheme` (`idMetadataScheme`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `MetadataValue`
--
ALTER TABLE `MetadataValue`
  ADD CONSTRAINT `MetadataValue_Nodes` FOREIGN KEY (`idNode`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `MetadataValue_RelMetadataGroupMetadata` FOREIGN KEY (`idRelMetadataGroupMetadata`) REFERENCES `RelMetadataGroupMetadata` (`idRelMetadataGroupMetadata`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `NoActionsInNode`
--
ALTER TABLE `NoActionsInNode`
  ADD CONSTRAINT `NoActionsInNode_Actions` FOREIGN KEY (`IdAction`) REFERENCES `Actions` (`IdAction`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `NoActionsInNode_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `NodeAllowedContents`
--
ALTER TABLE `NodeAllowedContents`
  ADD CONSTRAINT `NodeAllowedContents_Nodetype_Container` FOREIGN KEY (`IdNodeType`) REFERENCES `NodeTypes` (`IdNodeType`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `NodeAllowedContents_Nodetype_Content` FOREIGN KEY (`NodeType`) REFERENCES `NodeTypes` (`IdNodeType`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `NodeConstructors`
--
ALTER TABLE `NodeConstructors`
  ADD CONSTRAINT `NodeConstructors_Actions` FOREIGN KEY (`IdAction`) REFERENCES `Actions` (`IdAction`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `NodeConstructors_NodeTypes` FOREIGN KEY (`IdNodeType`) REFERENCES `NodeTypes` (`IdNodeType`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `NodeDefaultContents`
--
ALTER TABLE `NodeDefaultContents`
  ADD CONSTRAINT `NodeDefaultContents_NodeTypes` FOREIGN KEY (`IdNodeType`) REFERENCES `NodeTypes` (`IdNodeType`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `NodeDefaultContents_NodeTypes_target` FOREIGN KEY (`NodeType`) REFERENCES `NodeTypes` (`IdNodeType`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `NodeDependencies`
--
ALTER TABLE `NodeDependencies`
  ADD CONSTRAINT `NodeDependencies_Channels` FOREIGN KEY (`IdChannel`) REFERENCES `Channels` (`IdChannel`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `NodeDependencies_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `NodeDependencies_Nodes_Resource` FOREIGN KEY (`IdResource`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `NodeFrames`
--
ALTER TABLE `NodeFrames`
  ADD CONSTRAINT `NodeFrames_PortalFrames` FOREIGN KEY (`IdPortalFrame`) REFERENCES `PortalFrames` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `NodeFrames_Versions` FOREIGN KEY (`VersionId`) REFERENCES `Versions` (`IdVersion`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `NodeFrames_nodes` FOREIGN KEY (`NodeId`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `NodeNameTranslations`
--
ALTER TABLE `NodeNameTranslations`
  ADD CONSTRAINT `NodeNameTranslations_Languages` FOREIGN KEY (`IdLanguage`) REFERENCES `Languages` (`IdLanguage`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `NodeNameTranslations_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `NodeProperties`
--
ALTER TABLE `NodeProperties`
  ADD CONSTRAINT `NodeProperties_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `Nodes`
--
ALTER TABLE `Nodes`
  ADD CONSTRAINT `Nodes_NodeFrames` FOREIGN KEY (`ActiveNF`) REFERENCES `NodeFrames` (`IdNodeFrame`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Nodes_SharedWorkflow` FOREIGN KEY (`SharedWorkflow`) REFERENCES `Nodes` (`IdNode`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Nodes_Users` FOREIGN KEY (`BlockUser`) REFERENCES `Users` (`IdUser`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Nodes_WorkflowStatus` FOREIGN KEY (`IdState`) REFERENCES `WorkflowStatus` (`id`),
  ADD CONSTRAINT `Nodes_Nodes` FOREIGN KEY (`IdParent`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `NodesToPublish`
--
ALTER TABLE `NodesToPublish`
  ADD CONSTRAINT `NodesToPublish_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `NodesToPublish_Nodes_Generator` FOREIGN KEY (`IdNodeGenerator`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `NodesToPublish_Users` FOREIGN KEY (`UserId`) REFERENCES `Users` (`IdUser`) ON DELETE SET NULL;

--
-- Table filters `NodetypeModes`
--
ALTER TABLE `NodetypeModes`
  ADD CONSTRAINT `NodetypeModes_Actions` FOREIGN KEY (`IdAction`) REFERENCES `Actions` (`IdAction`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `NodetypeModes_NodeTypes` FOREIGN KEY (`IdNodeType`) REFERENCES `NodeTypes` (`IdNodeType`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `NodeTypes`
--
ALTER TABLE `NodeTypes`
  ADD CONSTRAINT `NodeTypes_Nodes` FOREIGN KEY (`IdNodeType`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `NodeTypes_Workflow` FOREIGN KEY (`workflowId`) REFERENCES `Workflow` (`id`) ON UPDATE CASCADE;

--
-- Table filters `Permissions`
--
ALTER TABLE `Permissions`
  ADD CONSTRAINT `Permissions_Nodes` FOREIGN KEY (`IdPermission`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `PortalFrames`
--
ALTER TABLE `PortalFrames`
  ADD CONSTRAINT `PortalFrames_Nodes` FOREIGN KEY (`IdNodeGenerator`) REFERENCES `Nodes` (`IdNode`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `PortalFrames_Users` FOREIGN KEY (`CreatedBy`) REFERENCES `Users` (`IdUser`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Table filters `ProgrammingCode`
--
ALTER TABLE `ProgrammingCode`
  ADD CONSTRAINT `ProgrammingCode_ProgrammingLanguage` FOREIGN KEY (`idLanguage`) REFERENCES `ProgrammingLanguage` (`id`),
  ADD CONSTRAINT `ProgrammingCode_ProgrammingCommand` FOREIGN KEY (`idCommand`) REFERENCES `ProgrammingCommand` (`id`);

--
-- Table filters `Pumpers`
--
ALTER TABLE `Pumpers`
  ADD CONSTRAINT `Pumpers_Servers` FOREIGN KEY (`IdServer`) REFERENCES `Servers` (`IdServer`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelDocumentFolderToTemplatesIncludeFile`
--
ALTER TABLE `RelDocumentFolderToTemplatesIncludeFile`
  ADD CONSTRAINT `RelDocumentFolderToTemplatesIncludeFile_Nodes_Source` FOREIGN KEY (`source`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelDocumentFolderToTemplatesIncludeFile_Nodes_Target` FOREIGN KEY (`target`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelGroupsNodes`
--
ALTER TABLE `RelGroupsNodes`
  ADD CONSTRAINT `RelGroupsNodes_Group` FOREIGN KEY (`IdGroup`) REFERENCES `Groups` (`IdGroup`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelGroupsNodes_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelGroupsNodes_Roles` FOREIGN KEY (`IdRole`) REFERENCES `Roles` (`IdRole`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelLinkDescriptions`
--
ALTER TABLE `RelLinkDescriptions`
  ADD CONSTRAINT `RelLinkDescriptions_Links` FOREIGN KEY (`IdLink`) REFERENCES `Links` (`IdLink`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelMetadataGroupMetadata`
--
ALTER TABLE `RelMetadataGroupMetadata`
  ADD CONSTRAINT `RelMetadataGroupMetadata_MetadataGroup` FOREIGN KEY (`idMetadataGroup`) REFERENCES `MetadataGroup` (`idMetadataGroup`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelMetadataGroupMetadata_Metadata` FOREIGN KEY (`idMetadata`) REFERENCES `Metadata` (`idMetadata`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelMetadataSchemeNodeType`
--
ALTER TABLE `RelMetadataSchemeNodeType`
  ADD CONSTRAINT `RelMetadataSchemeNodeType_MetadataScheme` FOREIGN KEY (`idMetadataScheme`) REFERENCES `MetadataScheme` (`idMetadataScheme`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelMetadataSchemeNodeType_NodeTypes` FOREIGN KEY (`idNodeType`) REFERENCES `NodeTypes` (`IdNodeType`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelNode2Asset`
--
ALTER TABLE `RelNode2Asset`
  ADD CONSTRAINT `RelNode2Asset_Nodes_Source` FOREIGN KEY (`source`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelNode2Asset_Nodes_Target` FOREIGN KEY (`target`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelNodeTypeMimeType`
--
ALTER TABLE `RelNodeTypeMimeType`
  ADD CONSTRAINT `RelNodeTypeMimeType_NodeTypes` FOREIGN KEY (`idNodeType`) REFERENCES `NodeTypes` (`IdNodeType`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelRolesActions`
--
ALTER TABLE `RelRolesActions`
  ADD CONSTRAINT `RelRoldesActions_Rol` FOREIGN KEY (`IdRol`) REFERENCES `Roles` (`IdRole`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelRolesActions_Actions` FOREIGN KEY (`IdAction`) REFERENCES `Actions` (`IdAction`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelRolesActions_Status` FOREIGN KEY (`IdState`) REFERENCES `WorkflowStatus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelRolesPermissions`
--
ALTER TABLE `RelRolesPermissions`
  ADD CONSTRAINT `RelRolesPermissions_Permissions` FOREIGN KEY (`IdPermission`) REFERENCES `Permissions` (`IdPermission`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelRolesPermissions_Roles` FOREIGN KEY (`IdRole`) REFERENCES `Roles` (`IdRole`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelRolesStates`
--
ALTER TABLE `RelRolesStates`
  ADD CONSTRAINT `RelRolesStates_Roles` FOREIGN KEY (`IdRole`) REFERENCES `Roles` (`IdRole`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelRolesStates_WorkflowStatus` FOREIGN KEY (`IdState`) REFERENCES `WorkflowStatus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelSemanticTagsDescriptions`
--
ALTER TABLE `RelSemanticTagsDescriptions`
  ADD CONSTRAINT `RelSemanticTagsDescriptions_SemanticTags` FOREIGN KEY (`Tag`) REFERENCES `SemanticTags` (`IdTag`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelSemanticTagsNodes`
--
ALTER TABLE `RelSemanticTagsNodes`
  ADD CONSTRAINT `RelSemanticTagsNodes_Nodes` FOREIGN KEY (`Node`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelSemanticTagsNodes_RelSemanticTags` FOREIGN KEY (`TagDesc`) REFERENCES `RelSemanticTagsDescriptions` (`IdTagDescription`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelServersChannels`
--
ALTER TABLE `RelServersChannels`
  ADD CONSTRAINT `RelServersChannels_Channels` FOREIGN KEY (`IdChannel`) REFERENCES `Channels` (`IdChannel`),
  ADD CONSTRAINT `RelServersChannels_Servers` FOREIGN KEY (`IdServer`) REFERENCES `Servers` (`IdServer`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelStrdocTemplate`
--
ALTER TABLE `RelStrdocTemplate`
  ADD CONSTRAINT `RelStrdocTemplate_Nodes_Source` FOREIGN KEY (`source`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelStrdocTemplate_Nodes_target` FOREIGN KEY (`target`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelTemplateContainer`
--
ALTER TABLE `RelTemplateContainer`
  ADD CONSTRAINT `RelTemplateConatiner_Nodes_Container` FOREIGN KEY (`IdContainer`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelTemplateConatiner_Nodes_Template` FOREIGN KEY (`IdTemplate`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelUsersGroups`
--
ALTER TABLE `RelUsersGroups`
  ADD CONSTRAINT `RelUsersGroups_Groups` FOREIGN KEY (`IdGroup`) REFERENCES `Groups` (`IdGroup`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelUsersGroups_Roles` FOREIGN KEY (`IdRole`) REFERENCES `Roles` (`IdRole`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelUsersGroups_User` FOREIGN KEY (`IdUser`) REFERENCES `Users` (`IdUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `RelXml2Xml`
--
ALTER TABLE `RelXml2Xml`
  ADD CONSTRAINT `RelXml2Xml_Nodes_source` FOREIGN KEY (`source`) REFERENCES `StructuredDocuments` (`IdDoc`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RelXml2Xml_Nodes_target` FOREIGN KEY (`target`) REFERENCES `StructuredDocuments` (`IdDoc`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `Roles`
--
ALTER TABLE `Roles`
  ADD CONSTRAINT `Roles_Nodes` FOREIGN KEY (`IdRole`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `Section`
--
ALTER TABLE `Section`
  ADD CONSTRAINT `Section_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Section_SectionTypes` FOREIGN KEY (`idSectionType`) REFERENCES `SectionTypes` (`idSectionType`);

--
-- Table filters `SectionTypes`
--
ALTER TABLE `SectionTypes`
  ADD CONSTRAINT `SectionTypes_NodeTypes` FOREIGN KEY (`idNodeType`) REFERENCES `NodeTypes` (`IdNodeType`) ON UPDATE CASCADE;

--
-- Table filters `SemanticTags`
--
ALTER TABLE `SemanticTags`
  ADD CONSTRAINT `SemanticTags_SemanticNamespaces` FOREIGN KEY (`IdNamespace`) REFERENCES `SemanticNamespaces` (`idNamespace`);

--
-- Table filters `ServerFrames`
--
ALTER TABLE `ServerFrames`
  ADD CONSTRAINT `ServerFrames_Batchs_Down` FOREIGN KEY (`IdBatchDown`) REFERENCES `Batchs` (`IdBatch`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ServerFrames_Batchs_Up` FOREIGN KEY (`IdBatchUp`) REFERENCES `Batchs` (`IdBatch`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ServerFrames_ChannelFrames` FOREIGN KEY (`IdChannelFrame`) REFERENCES `ChannelFrames` (`IdChannelFrame`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ServerFrames_Channels` FOREIGN KEY (`ChannelId`) REFERENCES `Channels` (`IdChannel`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ServerFrames_NodeFrames` FOREIGN KEY (`IdNodeFrame`) REFERENCES `NodeFrames` (`IdNodeFrame`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ServerFrames_Nodes` FOREIGN KEY (`NodeId`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ServerFrames_PortalFrames` FOREIGN KEY (`IdPortalFrame`) REFERENCES `PortalFrames` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ServerFrames_Pumpers` FOREIGN KEY (`PumperId`) REFERENCES `Pumpers` (`PumperId`),
  ADD CONSTRAINT `ServerFrames_Servers` FOREIGN KEY (`IdServer`) REFERENCES `Servers` (`IdServer`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `Servers`
--
ALTER TABLE `Servers`
  ADD CONSTRAINT `Servers_Encodes` FOREIGN KEY (`idEncode`) REFERENCES `Encodes` (`IdEncode`),
  ADD CONSTRAINT `Servers_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Servers_Protocols` FOREIGN KEY (`IdProtocol`) REFERENCES `Protocols` (`IdProtocol`);

--
-- Table filters `StructuredDocuments`
--
ALTER TABLE `StructuredDocuments`
  ADD CONSTRAINT `StructuredDocuments_Languages` FOREIGN KEY (`IdLanguage`) REFERENCES `Languages` (`IdLanguage`) ON UPDATE CASCADE,
  ADD CONSTRAINT `StructuredDocuments_Nodes` FOREIGN KEY (`IdDoc`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `StructuredDocuments_StructuredDocuments` FOREIGN KEY (`TargetLink`) REFERENCES `StructuredDocuments` (`IdDoc`) ON UPDATE CASCADE,
  ADD CONSTRAINT `StructuredDocuments_Templates` FOREIGN KEY (`IdTemplate`) REFERENCES `Nodes` (`IdNode`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `StructuredDocuments_Users` FOREIGN KEY (`IdCreator`) REFERENCES `Users` (`IdUser`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Table filters `Transitions`
--
ALTER TABLE `Transitions`
  ADD CONSTRAINT `Transitions_Transitions` FOREIGN KEY (`previousTransitionId`) REFERENCES `Transitions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Table filters `TransitionsCache`
--
ALTER TABLE `TransitionsCache`
  ADD CONSTRAINT `TransitionsCache_Channels` FOREIGN KEY (`channelId`) REFERENCES `Channels` (`IdChannel`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `TransitionsCache_Transitions` FOREIGN KEY (`transitionId`) REFERENCES `Transitions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `TransitionsCache_Versions` FOREIGN KEY (`versionId`) REFERENCES `Versions` (`IdVersion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `Users`
--
ALTER TABLE `Users`
  ADD CONSTRAINT `Users_Locales` FOREIGN KEY (`Locale`) REFERENCES `Locales` (`Code`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Users_Nodes` FOREIGN KEY (`IdUser`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `Versions`
--
ALTER TABLE `Versions`
  ADD CONSTRAINT `Versions_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Versions_ServerFrames` FOREIGN KEY (`IdSync`) REFERENCES `ServerFrames` (`IdSync`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Versions_Users` FOREIGN KEY (`IdUser`) REFERENCES `Users` (`IdUser`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Table filters `Workflow`
--
ALTER TABLE `Workflow`
  ADD CONSTRAINT `Workflow_Nodes` FOREIGN KEY (`id`) REFERENCES `Nodes` (`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Table filters `WorkflowStatus`
--
ALTER TABLE `WorkflowStatus`
  ADD CONSTRAINT `WorkflowStatus_Workflow` FOREIGN KEY (`workflowId`) REFERENCES `Workflow` (`id`) ON UPDATE CASCADE;
