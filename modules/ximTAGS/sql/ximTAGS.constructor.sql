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

# ############################################# TABLES  ######################################
CREATE TABLE IF NOT EXISTS `XimTAGSTags` (
  `IdTag` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Total` mediumint(6) unsigned NOT NULL DEFAULT '1',
  `IdNamespace` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`IdTag`),
  UNIQUE KEY `Name` (`Name`),
  KEY `IdNamespace` (`IdNamespace`),
  FULLTEXT KEY `Name_2` (`Name`)
) ENGINE=MyISAM  COMMENT='List Tags' AUTO_INCREMENT=1;

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
) ENGINE = MYISAM COMMENT = 'Descriptions and info for Tags';

# ############################################# ACTIONS  ######################################


#---- Para Nodetype: 5001 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6900, 5001 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6900, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6900, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6900, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6900, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6900,  5008, 5001, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5002 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6901, 5002 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6901, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6901, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6901, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6901, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6901, 5008, 5002, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5003 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6902, 5003 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6902, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6902, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6902, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6902, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6902, 5008, 5003, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5004 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6903, 5004 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6903, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6903, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6903, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6903, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6903, 5008, 5004, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5005 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6904, 5005 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6904, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6904, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6904, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6904, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6904, 5008, 5005, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5006 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6905, 5006 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6905, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6905, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6905, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6905, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6905, 5008, 5006, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5007 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6906, 5007 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6906, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6906, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6906, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6906, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6906, 5008, 5007,  "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5008 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6907, 5008 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6907, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6907, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6907, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6907, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6907, 5008, 5008,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5009 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6908, 5009 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6908, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6908, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6908, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6908, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6908, 5008, 5009, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5010 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6909, 5010 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6909, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6909, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6909, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6909, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6909, 5008, 5010, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5011 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6910, 5011 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6910, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6910, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6910, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6910, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6910, 5008, 5011, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5012 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6911, 5012 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6911, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6911, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6911, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6911, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6911, 5008, 5012, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5013 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6912, 5013 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6912, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6912, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6912, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6912, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6912, 5008, 5013, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5014 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6913, 5014 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);

INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6913, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6913, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6913, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6913, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6913, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6913, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6913, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6913, 8, 1, 3);

INSERT INTO `Nodes` VALUES (6913, 5008, 5014,  "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5015 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6914, 5015 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6914, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6914, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6914, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6914, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6914, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6914, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6914, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6914, 8, 1, 3);


INSERT INTO `Nodes` VALUES (6914, 5008, 5015,  "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5016 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6915, 5016 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6915, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6915, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6915, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6915, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6915, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6915, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6915, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6915, 8, 1, 3);
INSERT INTO `Nodes` VALUES (6915, 5008, 5016,  "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5017 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6916, 5017 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6916, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6916, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6916, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6916, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6916, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6916, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6916, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6916, 8, 1, 3);
INSERT INTO `Nodes` VALUES (6916, 5008, 5017,  "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5018 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6917, 5018 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6917, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6917, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6917, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6917, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6917, 5008,5018, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5020 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6918, 5020 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6918, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6918, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6918, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6918, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6918, 5008, 5020,  "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5021 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6919, 5021 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6919, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6919, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6919, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6919, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6919, 5008, 5021,  "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5022 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6920, 5022 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6920, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6920, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6920, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6920, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6920, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6920, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6920, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6920, 8, 1, 3);
INSERT INTO `Nodes` VALUES (6920,5008, 5022,  "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5023 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6921, 5023 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6921, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6921, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6921, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6921, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6921, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6921, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6921, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6921, 8, 1, 3);
INSERT INTO `Nodes` VALUES (6921, 5008, 5023, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5024 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6922, 5024 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6922, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6922, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6922, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6922, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6922, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6922, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6922, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6922, 8, 1, 3);
INSERT INTO `Nodes` VALUES (6922, 5008, 5024, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5025 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6923, 5025 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6923, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6923, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6923, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6923, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6923, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6923, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6923, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6923, 8, 1, 3);
INSERT INTO `Nodes` VALUES (6923, 5008, 5025, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5026 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6924, 5026 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6924, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6924, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6924, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6924, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6924, 5008,5026, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5028 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6925, 5028 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6925, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6925, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6925, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6925, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6925, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6925, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6925, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6925, 8, 1, 3);
INSERT INTO `Nodes` VALUES (6925, 5008, 5028, "Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5029 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6926, 5029 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6926, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6926, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6926, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6926, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6926,5008,5029,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5030 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6927, 5030 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6927, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6927, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6927, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6927, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6927, 5008, 5030,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5031 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6928, 5031 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6928, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6928, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6928, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6928, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6928, 5008, 5031,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5032 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6929, 5032 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6929, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6929, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6929, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6929, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6929, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6929, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6929, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6929, 8, 1, 3);
INSERT INTO `Nodes` VALUES (6929, 5008, 5032,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5033 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6930, 5033 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6930, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6930, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6930, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6930, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6930, 5008, 5033,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5034 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6931, 5034 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6931, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6931, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6931, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6931, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6931, 5008, 5034,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5035 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6932, 5035 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6932, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6932, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6932, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6932, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6932, 5008, 5035,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5036 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6933, 5036 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6933, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6933, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6933, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6933, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6933, 5008, 5036,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5037 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6934, 5037 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6934, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6934, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6934, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6934, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6934, 5008, 5037,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5038 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6935, 5038 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6935, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6935, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6935, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6935, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6935, 5008, 5038,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5039 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6936, 5039 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6936, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6936, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6936, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6936, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6936, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6936, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6936, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6936, 8, 1, 3);
INSERT INTO `Nodes` VALUES (6936, 5008, 5039,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5040 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6937, 5040 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6937, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6937, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6937, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6937, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6937, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6937, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6937, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6937, 8, 1, 3);
INSERT INTO `Nodes` VALUES (6937, 5008, 5040,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5041 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6938, 5041 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6938, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6938, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6938, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6938, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6938, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6938, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6938, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6938, 8, 1, 3);
INSERT INTO `Nodes` VALUES (6938, 5008, 5041,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5043 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6939, 5043 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6939, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6939, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6939, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6939, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6939, 5008, 5043,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5044 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6940, 5044 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6940, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6940, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6940, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6940, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6940, 5008,  5044,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5045 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6941, 5045 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6941, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6941, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6941, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6941, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6941, 5008, 5045,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5048 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6942, 5048 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6942, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6942, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6942, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6942, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6942, 5008, 5048,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5049 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6943, 5049 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6943, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6943, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6943, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6943, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6943, 5008, 5049,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5050 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6944, 5050 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6944, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6944, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6944, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6944, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6944, 5008, 5050,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5053 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6945, 5053 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6945, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6945, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6945, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6945, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6945, 5008, 5053,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5054 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6946, 5054 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6946, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6946, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6946, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6946, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6946, 5008, 5054,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5055 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6947, 5055 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6947, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6947, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6947, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6947, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6947, 5008, 5055,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5056 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6948, 5056 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6948, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6948, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6948, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6948, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6948, 5008, 5056,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5057 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6949, 5057 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6949, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6949, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6949, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6949, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6949, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6949, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6949, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6949, 8, 1, 3);
INSERT INTO `Nodes` VALUES (6949, 5008, 5057,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5058 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6950, 5058 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6950, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6950, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6950, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6950, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6950, 5008, 5058,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5059 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6951, 5059 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6951, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6951, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6951, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6951, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6951, 5008, 5059,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5060 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6952, 5060 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6952, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6952, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6952, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6952, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6952, 5008, 5060,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5061 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6953, 5061 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6953, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6953, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6953, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6953, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6953, 5008, 5061,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5063 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6954, 5063 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6954, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6954, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6954, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6954, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6954, 5008, 5063,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5064 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6955, 5064 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6955, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6955, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6955, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6955, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6955, 5008, 5064,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5065 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6956, 5065 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6956, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6956, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6956, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6956, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6956, 5008, 5065,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5066 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6957, 5066 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6957, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6957, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6957, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6957, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6957, 5008, 5066,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5067 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6958, 5067 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6958, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6958, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6958, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6958, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6958, 5008, 5067,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5068 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6959, 5068 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6959, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6959, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6959, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6959, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6959, 5008, 5068,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5076 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6960, 5076 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6960, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6960, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6960, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6960, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6960, 5008, 5076,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5077 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6961, 5077 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6961, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6961, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6961, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6961, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6961, 5008, 5077,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5078 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6962, 5078 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6962, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6962, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6962, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6962, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6962, 5008, 5078,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5079 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6963, 5079 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6963, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6963, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6963, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6963, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6963, 5008, 5079,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5080 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6964, 5080 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6964, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6964, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6964, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6964, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6964, 5008, 5080,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5081 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6965, 5081 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6965, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6965, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6965, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6965, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6965, 5008, 5081,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5082 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6966, 5082 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6966, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6966, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6966, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6966, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6966, 5008, 5082,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5300 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6967, 5300 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);

INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6967, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6967, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6967, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6967, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6967, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6967, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6967, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6967, 8, 1, 3);

INSERT INTO `Nodes` VALUES (6967, 5008, 5300,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5301 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6968, 5301 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6968, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6968, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6968, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6968, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6968, 5008, 5301,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5302 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6969, 5302 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6969, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6969, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6969, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6969, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6969, 5008, 5302,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5303 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6970, 5303 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6970, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6970, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6970, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6970, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6970, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6970, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6970, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6970, 8, 1, 3);
INSERT INTO `Nodes` VALUES (6970, 5008, 5303,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5304 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6971, 5304 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6971, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6971, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6971, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6971, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6971, 5008, 5304,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5305 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6972, 5305 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6972, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6972, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6972, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6972, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6972, 5008, 5305,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5306 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6973, 5306 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6973, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6973, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6973, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6973, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6973, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6973, 5008, 5306,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5307 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6974, 5307 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6974, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6974, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6974, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6974, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6974, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6974, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6974, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6974, 8, 1, 3);
INSERT INTO `Nodes` VALUES (6974, 5008, 5307,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5308 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6975, 5308 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6975, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6975, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6975, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6975, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6975, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6975, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6975, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6975, 8, 1, 3);
INSERT
 INTO `Nodes` VALUES (6975, 5008, 5308,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5309 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6976, 5309 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6976, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6976, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6976, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6976, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6976, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6976, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6976, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6976, 8, 1, 3);
INSERT INTO `Nodes` VALUES (6976, 5008, 5309,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5310 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6977, 5310 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6977, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6977, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6977, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6977, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6977, 5008, 5310,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5311 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6978, 5311 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6978, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6978, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6978, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6978, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6978, 5008, 5311,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5312 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6979, 5312 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6979, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6979, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6979, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6979, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6979, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6979, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6979, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6979, 8, 1, 3);
INSERT INTO `Nodes` VALUES (6979, 5008, 5312,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5313 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6980, 5313 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6980, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6980, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6980, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6980, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6980, 5008, 5313,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);


#---- Para Nodetype: 5320 ----
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`,`Multiple`, `Params`) VALUES ( 6981, 5320 ,"Define metadata", "setmetadata", "change_next_state.png","Define metadata", 95, "ximTAGS", 0, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6981, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6981, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6981, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6981, 0, 1, 3);
INSERT INTO `Nodes` VALUES (6981, 5008, 5320,"Define metadata", 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);
