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

-- ------------- NEW TABLES -----------------
CREATE TABLE IF NOT EXISTS `XimTAGSTags` (
  `IdTag` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Total` mediumint(6) unsigned NOT NULL DEFAULT '1',
  `IdNamespace` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`IdTag`),
  UNIQUE KEY `Name` (`Name`, `IdNamespace`),
  KEY `IdNamespace` (`IdNamespace`),
  FULLTEXT KEY `Name_2` (`Name`)
) ENGINE=MyISAM  CHARSET='utf8' COMMENT='List Tags' AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `RelTagsNodes` (
  `Node` int(10) unsigned NOT NULL DEFAULT '0',
  `TagDesc` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'RelTagsDescriptions  id ',
  PRIMARY KEY (`Node`,`TagDesc`)
) ENGINE=MyISAM  COMMENT='Tags for each node';

CREATE TABLE `RelTagsDescriptions` (
`IdTagDescription` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`Tag` int(11) UNSIGNED NOT NULL ,
`Type` ENUM( 'GENERICS', 'ORGANISATIONS', 'PLACES', 'PEOPLE' ) NOT NULL ,
`Link` VARCHAR( 250 ) NOT NULL ,
`Description` TEXT NULL
) ENGINE=MyISAM COMMENT = 'Descriptions and info for Tags';


-- -----------  ACTIONS -------------

-- -- Nodetype: 5012 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6911, 5012 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6911, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6911, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6911, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6911, 0, 1, 3);

-- -- Nodetype: 5013 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6912, 5013 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6912, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6912, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6912, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6912, 0, 1, 3);

-- -- Nodetype: 5014 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6913, 5014 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);

INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6913, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6913, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6913, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6913, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6913, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6913, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6913, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6913, 8, 1, 3);

-- -- Nodetype: 5015 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6914, 5015 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6914, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6914, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6914, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6914, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6914, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6914, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6914, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6914, 8, 1, 3);

-- -- Nodetype: 5016 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6915, 5016 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6915, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6915, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6915, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6915, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6915, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6915, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6915, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6915, 8, 1, 3);

-- -- Nodetype: 5017 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6916, 5017 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6916, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6916, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6916, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6916, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6916, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6916, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6916, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6916, 8, 1, 3);

-- -- Nodetype: 5018 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6917, 5018 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6917, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6917, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6917, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6917, 0, 1, 3);

-- -- Nodetype: 5020 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6918, 5020 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6918, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6918, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6918, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6918, 0, 1, 3);

-- -- Nodetype: 5021 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6919, 5021 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6919, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6919, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6919, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6919, 0, 1, 3);

-- -- Nodetype: 5022 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6920, 5022 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6920, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6920, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6920, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6920, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6920, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6920, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6920, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6920, 8, 1, 3);

-- -- Nodetype: 5023 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6921, 5023 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6921, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6921, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6921, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6921, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6921, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6921, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6921, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6921, 8, 1, 3);

-- -- Nodetype: 5024 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6922, 5024 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6922, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6922, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6922, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6922, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6922, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6922, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6922, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6922, 8, 1, 3);

-- -- Nodetype: 5025 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6923, 5025 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6923, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6923, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6923, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6923, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6923, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6923, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6923, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6923, 8, 1, 3);

-- -- Nodetype: 5026 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6924, 5026 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6924, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6924, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6924, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6924, 0, 1, 3);

-- -- Nodetype: 5028 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6925, 5028 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6925, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6925, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6925, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6925, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6925, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6925, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6925, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6925, 8, 1, 3);

-- -- Nodetype: 5031 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6928, 5031 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6928, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6928, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6928, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6928, 0, 1, 3);

-- -- Nodetype: 5032 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6929, 5032 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6929, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6929, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6929, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6929, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6929, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6929, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6929, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6929, 8, 1, 3);

-- -- Nodetype: 5036 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6933, 5036 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6933, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6933, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6933, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6933, 0, 1, 3);

-- -- Nodetype: 5039 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6936, 5039 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6936, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6936, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6936, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6936, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6936, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6936, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6936, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6936, 8, 1, 3);

-- -- Nodetype: 5040 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6937, 5040 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6937, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6937, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6937, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6937, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6937, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6937, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6937, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6937, 8, 1, 3);

-- -- Nodetype: 5041 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6938, 5041 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6938, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6938, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6938, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6938, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6938, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6938, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6938, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6938, 8, 1, 3);

-- -- Nodetype: 5043 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6939, 5043 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6939, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6939, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6939, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6939, 0, 1, 3);

-- -- Nodetype: 5044 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6940, 5044 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6940, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6940, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6940, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6940, 0, 1, 3);

-- -- Nodetype: 5045 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6941, 5045 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6941, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6941, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6941, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6941, 0, 1, 3);

-- -- Nodetype: 5048 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6942, 5048 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6942, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6942, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6942, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6942, 0, 1, 3);

-- -- Nodetype: 5049 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6943, 5049 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6943, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6943, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6943, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6943, 0, 1, 3);

-- -- Nodetype: 5050 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6944, 5050 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6944, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6944, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6944, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6944, 0, 1, 3);

-- -- Nodetype: 5053 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6945, 5053 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6945, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6945, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6945, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6945, 0, 1, 3);

-- -- Nodetype: 5054 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6946, 5054 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6946, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6946, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6946, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6946, 0, 1, 3);

-- -- Nodetype: 5055 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6947, 5055 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6947, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6947, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6947, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6947, 0, 1, 3);

-- -- Nodetype: 5056 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6948, 5056 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6948, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6948, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6948, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6948, 0, 1, 3);

-- -- Nodetype: 5057 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6949, 5057 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6949, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6949, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6949, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6949, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6949, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6949, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6949, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6949, 8, 1, 3);

-- -- Nodetype: 5059 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6951, 5059 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6951, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6951, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6951, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6951, 0, 1, 3);

-- -- Nodetype: 5063 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6954, 5063 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6954, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6954, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6954, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6954, 0, 1, 3);

-- -- Nodetype: 5064 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6955, 5064 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6955, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6955, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6955, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6955, 0, 1, 3);

-- -- Nodetype: 5065 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6956, 5065 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6956, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6956, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6956, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6956, 0, 1, 3);

-- -- Nodetype: 5066 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6957, 5066 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6957, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6957, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6957, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6957, 0, 1, 3);

-- -- Nodetype: 5067 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6958, 5067 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6958, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6958, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6958, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6958, 0, 1, 3);

-- -- Nodetype: 5068 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6959, 5068 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6959, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6959, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6959, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6959, 0, 1, 3);

-- -- Nodetype: 5076 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6960, 5076 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6960, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6960, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6960, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6960, 0, 1, 3);

-- -- Nodetype: 5077 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6961, 5077 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6961, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6961, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6961, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6961, 0, 1, 3);

-- -- Nodetype: 5078 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6962, 5078 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6962, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6962, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6962, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6962, 0, 1, 3);

-- -- Nodetype: 5081 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6965, 5081 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6965, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6965, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6965, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6965, 0, 1, 3);

-- -- Nodetype: 5302 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6969, 5302 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6969, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6969, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6969, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6969, 0, 1, 3);

-- -- Nodetype: 5303 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6970, 5303 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6970, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6970, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6970, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6970, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6970, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6970, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6970, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6970, 8, 1, 3);

-- -- Nodetype: 5305 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6972, 5305 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6972, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6972, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6972, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6972, 0, 1, 3);

-- -- Nodetype: 5307 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6974, 5307 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6974, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6974, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6974, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6974, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6974, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6974, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6974, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6974, 8, 1, 3);

-- -- Nodetype: 5310 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6977, 5310 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6977, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6977, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6977, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6977, 0, 1, 3);

-- -- Nodetype: 5311 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6978, 5311 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6978, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6978, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6978, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6978, 0, 1, 3);


-- -- Nodetype: 5313 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6980, 5313 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6980, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6980, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6980, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6980, 0, 1, 3);

-- -- Nodetype: 5320 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6981, 5320 ,"Semantic Tags", "setmetadata", "change_next_state.png","Managing semantic tags related to the current node.", 999, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6981, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6981, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6981, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6981, 0, 1, 3);

-- -- table Namespaces -- --
INSERT INTO `Namespaces` VALUES (NULL,'Ximdex','OntologyBrowser','structured','ontologies/json/SchemaOrg.json',0,'generic',1);

