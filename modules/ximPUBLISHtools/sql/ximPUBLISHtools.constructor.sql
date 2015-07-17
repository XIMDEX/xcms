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

-- 
-- Table structure for table `XimNewsColectorUsers`
-- 

DROP TABLE IF EXISTS `XimNewsColectorUsers`;
CREATE TABLE IF NOT EXISTS `XimNewsColectorUsers` (
  `Id` int(12) unsigned NOT NULL auto_increment,
  `IdColector` int(12) unsigned NOT NULL,
  `IdUser` int(12) unsigned NOT NULL,
  `StartGenerationTime` int(12) unsigned NOT NULL,
  `EndGenerationTime` int(12) unsigned default NULL,
  `EndPublicationTime` int(12) unsigned default NULL,
  `Progress` int(3) unsigned NOT NULL,
  `State` varchar(255) NOT NULL default 'Generated',
  PRIMARY KEY  (`Id`),
  KEY `IdColector` (`IdColector`),
  KEY `IdUser` (`IdUser`)
) ENGINE=MyISAM;

-- 
-- Table structure for table `RelNewsColectorUsers`
-- 

DROP TABLE IF EXISTS `RelNewsColectorUsers`;
CREATE TABLE IF NOT EXISTS `RelNewsColectorUsers` (
  `Id` int(12) unsigned NOT NULL auto_increment,
  `IdRelNewsColector` int(12) unsigned NOT NULL,
  `IdUser` int(12) unsigned NOT NULL,
  `Time` int(12) unsigned NOT NULL,
  PRIMARY KEY  (`Id`),
  KEY `IdRelNewsColector` (`IdRelNewsColector`),
  KEY `IdUser` (`IdUser`)
) ENGINE=MyISAM;

-- 
-- Table structure for table `RelColectorUsersBatchs`
-- 

DROP TABLE IF EXISTS `RelColectorUsersBatchs`;
CREATE TABLE IF NOT EXISTS `RelColectorUsersBatchs` (
  `Id` int(12) unsigned NOT NULL auto_increment,
  `IdColectorUser` int(12) unsigned NOT NULL,
  `IdBatch` int(12) unsigned NOT NULL,
  PRIMARY KEY  (`Id`),
  KEY `IdColectorUser` (`IdColectorUser`),
  KEY `IdBatch` (`IdBatch`)
) ENGINE=MyISAM;

LOCK TABLES `Permissions` WRITE;
INSERT INTO `Permissions` VALUES (1005,'view_publication_resume','View publication report');
UNLOCK TABLES;
