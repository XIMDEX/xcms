#/**
# *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
# *
# *  Ximdex a Semantic Content Management System (CMS)
# *
# *  This program is free software: you can redistribute it and/or modify
# *  it under the terms of the GNU Affero General Public License as published
# *  by the Free Software Foundation, either version 3 of the License, or
# *  (at your option) any later version.
# *
# *  This program is distributed in the hope that it will be useful,
# *  but WITHOUT ANY WARRANTY; without even the implied warranty of
# *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# *  GNU Affero General Public License for more details.
# *
# *  See the Affero GNU General Public License for more details.
# *  You should have received a copy of the Affero GNU General Public License
# *  version 3 along with Ximdex (see LICENSE file).
# *
# *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
# *
# *  @author Ximdex DevTeam <dev@ximdex.com>
# *  @version $Revision$
# */

-- TABLAS DE ximSYNC
--
-- Table structure for `Batchs` table
--

CREATE TABLE `Batchs` (
  `IdBatch` int(12) unsigned NOT NULL auto_increment,
  `TimeOn` int(12) NOT NULL,
  `State` varchar(255) NOT NULL,
  `ServerFramesTotal` int(12) unsigned default '0',
  `ServerFramesSucess` int(12) unsigned default '0',
  `ServerFramesError` int(12) unsigned default '0',
  `Playing` int(12) unsigned default NULL,
  `Type` varchar(255) NOT NULL default '0',
  `IdBatchDown` int(12) unsigned default NULL,
  `IdNodeGenerator` int(12) unsigned default NULL,
  `Priority` float(3,2) unsigned default '0',
  `MajorCycle` int(12) unsigned default '0',
  `MinorCycle` int(12) unsigned default '0',
  `IdPortalVersion` int(12) unsigned NOT NULL,
  `UserId` int(12) unsigned default NULL,
  PRIMARY KEY  (`IdBatch`),
  KEY `IdBatchDown` (`IdBatchDown`),
  KEY `IdNodeGenerator` (`IdNodeGenerator`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
--  Table structure for`ChannelFrames` table
--

CREATE TABLE `ChannelFrames` (
  `IdChannelFrame` int(12) unsigned NOT NULL auto_increment,
  `ChannelId` int(12) unsigned default '0',
  `NodeId` int(12) unsigned default '0',
  `IdBatchUp` int(12) unsigned default '0',
  PRIMARY KEY  (`IdChannelFrame`),
  KEY `ChannelId` (`ChannelId`),
  KEY `NodeId` (`NodeId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
--  Table Structure for `NodeFrames` table
--

CREATE TABLE `NodeFrames` (
  `IdNodeFrame` int(12) unsigned NOT NULL auto_increment,
  `NodeId` int(12) unsigned default '0',
  `VersionId` int(12) unsigned default '0',
  `TimeUp` int(12) unsigned default '0',
  `TimeDown` int(12) unsigned default NULL,
  `Active` int(12) unsigned default '0',
  `GetActivityFrom` int(12) default '0',
  `IsProcessUp` int(12) default '0',
  `IsProcessDown` int(12) default '0',
  `Name` varchar(255) NOT NULL,
  PRIMARY KEY  (`IdNodeFrame`),
  KEY `NodeId` (`NodeId`),
  KEY `VersionId` (`VersionId`),
  UNIQUE KEY `NodeVersion` (`NodeId`,`VersionId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
--  Table Structure for `Pumpers` table
--

CREATE TABLE `Pumpers` (
  `PumperId` int(12) unsigned NOT NULL auto_increment,
  `IdServer` int(12) unsigned NOT NULL,
  `State` varchar(255) NOT NULL,
  `StartTime` int(12) unsigned default '0',
  `CheckTime` int(12) unsigned default '0',
  `ProcessId` varchar(255) NOT NULL,
  PRIMARY KEY  (`PumperId`),
  KEY `IdServer` (`IdServer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table Structure for `ServerErrorByPumper` table
--

CREATE TABLE `ServerErrorByPumper` (
  `ErrorId` int(12) unsigned NOT NULL auto_increment,
  `PumperId` int(12) unsigned NOT NULL,
  `ServerId` int(12) unsigned NOT NULL,
  `WithError` int(12) unsigned NOT NULL,
  `UnactivityCycles` int(12) unsigned NOT NULL,
  PRIMARY KEY  (`ErrorId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
--  Table Structure for  `ServerFrames` table
--

CREATE TABLE `ServerFrames` (
  `IdSync` int(12) unsigned NOT NULL auto_increment,
  `IdServer` int(12) unsigned NOT NULL default '0',
  `DateUp` int(14) unsigned NOT NULL default '0',
  `DateDown` int(14) unsigned default '0',
  `State` varchar(255) default 'DUE',
  `Error` varchar(255) default NULL,
  `ErrorLevel` tinyint(3) unsigned NOT NULL default '0',
  `RemotePath` blob,
  `FileName` varchar(255) NOT NULL default '',
  `Retry` int(12) unsigned default '0',
  `Linked` tinyint(3) unsigned NOT NULL default '1',
  `IdNodeFrame` int(12) unsigned default '0',
  `IdBatchUp` int(12) unsigned default '0',
  `PumperId` int(12) default '0',
  `IdChannelFrame` int(12) default '0',
  `FileSize` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdSync`),
  UNIQUE KEY `IdSync` (`IdSync`),
  KEY `IdSync_2` (`IdSync`,`IdServer`,`DateUp`,`DateDown`,`State`),
  KEY `IdNodeFrame` (`IdNodeFrame`),
  KEY `IdBatchUp` (`IdBatchUp`),
  KEY `IdServer` (`IdServer`),
  KEY `PumperId` (`PumperId`),
  KEY `IdChannelFrame` (`IdChannelFrame`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='XimDEX Table Synchronization';

-- --------------------------------------------------------

--
-- Table Structure for  `SynchronizerStats` table
--

CREATE TABLE `SynchronizerStats` (
  `IdStat` int(11) unsigned NOT NULL auto_increment,
  `BatchId` int(11) unsigned default NULL,
  `NodeFrameId` int(11) unsigned default NULL,
  `ChannelFrameId` int(11) unsigned default NULL,
  `ServerFrameId` int(11) unsigned default NULL,
  `PumperId` int(11) unsigned default NULL,
  `Class` varchar(255) default NULL,
  `Method` varchar(255) default NULL,
  `File` varchar(255) default NULL,
  `Line` varchar(255) default NULL,
  `Type` varchar(255) NOT NULL,
  `Level` int(11) unsigned NOT NULL,
  `Time` int(11) unsigned NOT NULL,
  `Comment` varchar(255) NOT NULL,
  PRIMARY KEY  (`IdStat`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Statistics module synchronization';


-- --------------------------------------------------------

-- 
-- Table Structure for `SyncReport` 
-- 

CREATE TABLE `PublishingReport` ( 
  `IdReport` int(11) unsigned NOT NULL auto_increment, 
  `IdSection` int(11) unsigned default NULL, 
  `IdNode` int(11) unsigned default NULL, 
  `IdChannel` int(11) unsigned default NULL, 
  `IdSyncServer` int(11) unsigned default NULL, 
  `IdPortalVersion` int(11) unsigned default NULL, 
  `PubTime` int(11) unsigned NOT NULL, 
  `State` varchar(255) default NULL, 
  `Progress` varchar(255) default NULL, 
  `FileName` varchar(255) default NULL, 
  `FilePath` varchar(255) default NULL, 
  `IdSync` int(11) default NULL, 
  `IdBatch` int(11) default NULL, 
  `IdParentServer` int(11) default NULL, 
PRIMARY KEY  (`IdReport`) 
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Publishing report'; 

--
-- Table Structure for `NodesToPublish` table
--

CREATE TABLE `NodesToPublish` (
  `Id` int(11) unsigned NOT NULL auto_increment,
  `IdNode` int(11) unsigned NOT NULL,
  `IdNodeGenerator` int(11) NOT NULL,
  `Version` int(12) default NULL,
  `Subversion` int(12) default NULL,
  `DateUp` int(14) unsigned NOT NULL default '0',
  `DateDown` int(14) unsigned default '0',
  `State` tinyint(3) unsigned NOT NULL default '0',
  `UserId` int(12) unsigned default NULL,
  `ForcePublication` tinyint(3) unsigned NOT NULL default '0',
  `DeepLevel` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Nodes to push into publishing pool';


-- Action "Publish a server massively"
INSERT INTO Actions (IdAction, IdNodeType, Name, Command, Icon, Description, Sort, Module, Multiple) VALUES (7228, 5014, 'Publish server', 'publicatesection', 'publicate_section.png', 'Publish a server massively', -100, NULL, 0);
/*!40000 ALTER TABLE `Actions` ENABLE KEYS */;

-- Add field ActiveForPumping on Servers table
ALTER TABLE `Servers` ADD `ActiveForPumping` tinyint(3) unsigned Default '1';

-- Add RolesActions
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6376, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7228, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7228, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 7228, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 7228, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7228, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7228, 7, 1, 3);
