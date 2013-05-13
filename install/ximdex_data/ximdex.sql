-- MySQL dump 10.9
--
-- \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
--
-- Ximdex a Semantic Content Management System (CMS)
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU Affero General Public License as published
-- by the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU Affero General Public License for more details.
--
-- See the Affero GNU General Public License for more details.
-- You should have received a copy of the Affero GNU General Public License
-- version 3 along with Ximdex (see LICENSE file).
--
-- If not, visit http://gnu.org/licenses/agpl-3.0.html.
--
-- @author Ximdex DevTeam <dev@ximdex.com>
-- @version $Revision$
--

-- Host: localhost    Database: ximdex
-- ------------------------------------------------------
-- Server version	4.1.10a-Debian_2-log
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,MYSQL323' */;

--
-- Table structure for table 'Actions'
--

DROP TABLE IF EXISTS `Actions`;
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
) ENGINE=MYISAM COMMENT='Commands which can be executed in a node';


--
-- Dumping data for table `Actions`
--


/*!40000 ALTER TABLE `Actions` DISABLE KEYS */;
LOCK TABLES `Actions` WRITE;
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6001,5003,'Add new user','createuser','add_user.png','Create a new Ximdex user',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6002,5009,'Modify user data','modifyuser','modify_user.png','Change user data',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6003,5009,'Remove user','deleteuser','delete_user.png','Remove an user from system',80,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6004,5009,'Manage groups','modifygruposusuario','manage_user_groups.png','Enroll, disenroll, and change user role in groups where he/she belongs to',40,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6005,5010,'Modify role','modifyrole','modify_rol.png','Manage role attributions',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6006,5004,'Add new group','creategroup','add_group.png','Create a new group',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6008,5007,'Modify node type','modifynodetype','modify_nodetype.png','Modify a node type',-60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6009,5006,'Add node type','createnodetype','create_type_node.png','Add a node type',-10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6010,5005,'Add new role','createrole','create_rol.png','Create a new role',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6011,5012,'Add new project','addfoldernode','create_proyect.png','Create a new node with project type',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6012,5013,'Add new server','addfoldernode','create_server.png','Create a new server',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6013,5014,'Add new section','addsectionnode','add_section.png','Create a new section',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6014,5015,'Add new section','addsectionnode','add_section.png','Create a new section',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6015,5016,'Add new image folder','addfoldernode','add_folder_images.png','Create a new image folder',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6016,5017,'Add new image folder','addfoldernode','add_folder_images.png','Create a new image folder',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6018,5020,'Add ximclude folder','addfoldernode','add_import.png','Create a new import folder',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6019,5021,'Add ximclude folder','addfoldernode','add_import.png','Create a nex import folder',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6020,5022,'Add common folder','addfoldernode','add_folder_common.png','Create a new common folder',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6021,5023,'Add common folder','addfoldernode','add_folder_common.png','Create a new common folder',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6022,5024,'Add CSS folder','addfoldernode','add_folder_css.png','Create a new CSS folder',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6023,5025,'Add CSS folder','addfoldernode','add_folder_css.png','Create a new CSS folder',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6024,5027,'Add template folder','addfoldernode','foldergray.png','Create a new template folder',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6025,5015,'Change section name','renamenode','change_name_section.png','Change a section name',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6026,5013,'Change project name','renamenode','change_name_proyect.png','Change a project name',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6027,5014,'Change server name','renamenode','change_name_server.png','Change a server name',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6028,5017,'Change name','renamenode','change_name_folder_images.png','Change name of selected folder',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6030,5021,'Change name','renamenode','change_name_folder_import.png','Change name of selected folder',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6031,5023,'Change name','renamenode','change_name_folder_common.png','Change name of selected folder',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6032,5025,'Change name','renamenode','change_name_folder_css.png','Change folder name',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6033,5027,'Change name','renamenode','xix.png','Change node name',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6034,5013,'Delete project','deletenode','delete_proyect.png','Delete a project',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6035,5014,'Delete server','deletenode','delete_server.png','Delete a server',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6036,5015,'Delete section','deletenode','delete_section.png','Delete a section',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6037,5017,'Delete folder','deletenode','delete_folder_images.png','Delete selected folder',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6039,5021,'Delete folder','deletenode','delete_folder_import.png','Delete selected folder',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6040,5023,'Delete folder','deletenode','delete_folder_common.png','Delete selected folder',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6041,5025,'Delete folder','deletenode','delete_folder_css.png','Delete selected folder',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6042,5027,'Delete','deletenode','delete.png','Delete a node',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6108,5011,'Delete group','deletenode','delete_group.png','Delete select group',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6044,5018,'Add new document','createxmlcontainer','add_xml.png','Create a new document structured in several languages',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6045,5031,'Add new language','addlangxmlcontainer','add_language_xml.png','Add a document with a different language',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6046,5079,'Manage workflow','modifystates','manage_states.png','Add a new status to the workflow',40,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6047,5030,'Add new language','createlanguage','add_language.png','Add a new language',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6048,5029,'Add new channel','createchannel','add_channel.png','Add a new channel to Ximdex',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6123,5014,'Associated groups','modifygroupsnode','groups_server.png','Manage associations of groups with this node',50,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6049,5041,'Download file','filedownload','download_file_txt_bin.png','Download selected file',20,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6050,5040,'Download image','filedownload','download_image.png','Download an image to a local hard disk',20,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6051,5039,'Download file','filedownload','download_file_txt_bin.png','Download a file to a local hard disk',20,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6052,5040,'Image preview','filepreview','view_image.png','Preview an image',15,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6064,5031,'Change name of XML document','renamenode','change_name_xml.png','Change the document name and all its idiomatic versions',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6063,5031,'Delete XML container','deletenode','delete_xml.png','Delete XML document in all its languages',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6065,5032,'Edit XML document','xmleditor2','edit_file_xml.png','Edit content of XML document',1,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6103,5028,'Change name','renamenode','change_name_file_css.png','Change file name on import folder',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6104,5028,'Download file','filedownload','download_file_css.png','Download a file to a local hard disk',20,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6067,5008,'Delete action','deletenode','delete.png','Delete the action',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6074,5015,'Associated groups','modifygroupsnode','groups_section.png','Manage associations of groups with this node',50,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6079,5053,'Add new editor template','fileupload','add_template_editor.png','Add a new view template and its default view',10,NULL,0,'type=pvd');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6080,5045,'Delete template','deletenode','delete_template_view.png','Delete selected file',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6081,5010,'Modify associated workflow status','modifystatesrole','modify_state_workflow-rol.png','Modify associated status with this role',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6082,5036,'Modify associated roles','modifyrolesstate','manage_states-rol.png','Modify associated roles with this status',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6068,5053,'Add new template image','fileupload','add_template_image.png','Add a imagen for ED templates',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6083,5041,'Change name','renamenode','change_name_file_txt_bin.png','Change name of selected file',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6084,5041,'Delete file','deletenode','delete_file_txt_bin.png','Delete selected file',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6085,5040,'Change image name','renamenode','change_name_image.png','Change file name',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6086,5040,'Delete image','deletenode','delete_image.png','Delete an image',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6087,5039,'Change name','renamenode','change_name_file_txt_bin.png','Change file name on import folder',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6088,5039,'Delete','deletenode','delete_file_txt_bin.png','Delete file of import folder',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6069,5050,'Add new link category','addfoldernode','add_links_category.png','Create a new link category',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6070,5048,'Add new link category','addfoldernode','add_links_category.png','Create a new link category',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6071,5050,'Add new external link','createlink','add_external_link.png','Create a new external link to Ximdex',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6072,5048,'Add new external link','createlink','add_external_link.png','Create a new external link to Ximdex',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6094,5039,'Edit file','edittext','edit_file_txt.png','Edit content of text document',1,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6095,5033,'Channel properties','modifychannel','properties_channel.png','Modify channel properties',25,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6096,5011,'Group properties','modifygroup','properties_group.png','Modify group properties',25,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6097,5034,'Modify language','modifylanguage','modify_language.png','Modify data of a language',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6098,5032,'Move to next state','workflow_forward','change_next_state.png','Move to the next state',72,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6099,5032,'Move to previous state','workflow_backward','change_last_state.png','Move to the previous state',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6100,5032,'Version repository','versionmanager','repository_versions.png','Version repository',0,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6101,5011,'Change users','modifyusuariosgrupo','modify_users.png','Modify list of users that integrate this group',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6107,5033,'Delete channel','deletenode','delete_channel.png','Delete a channel if it has not associated documents',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6122,5013,'Associated groups','modifygroupsnode','groups_project.png','Manage associations of groups with this node',50,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6102,5034,'Delete','deletenode','delete_language.png','Delete a language from the system',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6105,5028,'Edit','edittext','edit_file_css.png','Edit content of text document',1,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6106,5028,'Delete','deletenode','delete_file_css.png','Delete file of import folder',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6073,5049,'Modify properties','modifylink','modify_link.png','Modify properties of external link',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6109,5010,'Delete role','deletenode','delete_role.png','Delete a selected role if it is not in use',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6121,5036,'Delete','deletenode','delete_state.png','Delete a state if it is not an initial or final one and it is not in use',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6110,5044,'Change name','renamenode','modify_template_ptd.png','Change template name',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6111,5044,'Delete template','deletenode','delete_template_ptd.png','Delete a template',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6112,5044,'Download template','filedownload','download_template_ptd.png','Download a template',20,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6113,5044,'Edit template','edittext','edit_template_ptd.png','Edit a template',1,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6114,5045,'Change name','renamenode','modify_template_view.png','Change template name',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6115,5045,'Download template','filedownload','download_template_view.png','Download selected template',20,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6116,5045,'Edit template','edittext','edit_template_view.png','Edit a template',1,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6118,5048,'Change name','renamenode','modify_link_folder.png','Change name of selected folder',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6119,5048,'Delete folder','deletenode','delete_link_folder.png','Delete selected folder',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6120,5049,'Delete link','deletenode','delete_link.png','Delete selected link',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6124,5018,'Modify view template','changetemplateview','xix.png','Modify template for all documents of the directory',0,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6125,5014,'Modify sync data','modifyserver','modify_sinc_data.png','Modify connection data with the production environment',60,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6126,5039,'Move to next state','workflow_forward','change_next_state.png','Move a text document to the next state',72,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6127,5039,'Move to previous state','workflow_backward','change_last_state.png','Move a text document to the previous state',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6128,5041,'Move to next state','workflow_forward','change_next_state.png','Move a text document to the next state',72,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6129,5041,'Move to previous state','workflow_backward','change_last_state.png','Move a text document to the previous state',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6130,5040,'Move to next state','workflow_forward','change_next_state.png','Move a text document to the next state',72,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6131,5040,'Move to previous state','workflow_backward','change_last_state.png','Move a text document to the previous state',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6132,5028,'Move to next state','workflow_forward','change_next_state.png','Move a text document to the next state',72,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6133,5028,'Move to previous state','workflow_backward','change_last_state.png','Move a text document to the previous state',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6134,5032,'Delete document','deletenode','delete_file_xml.png','Delete selected XML document',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6135,5032,'Symbolic link','xmlsetlink','file_xml_symbolic.png','Modify document which borrows the content',30,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6136,5032,'Edit in text mode','edittext','edit_file_xml_txt.png','Edit content of structured document at a low-level',3,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6137,5054,'Add new ximlet folder','addfoldernode','add_folder_ximlet.png','Create a new ximlet folder',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6138,5054,'Add new ximlet','createxmlcontainer','add_xml.png','Create a new document structured in several languages',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6139,5055,'Add new ximlet folder','addfoldernode','add_folder_ximlet.png','Create a new ximlet folder',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6140,5055,'Add new ximlet','createxmlcontainer','add_xml.png','Create a new document structured in several languages',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6141,5055,'Delete folder','deletenode','delete_folder_ximlet.png','Delete selected folder',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6142,5055,'Change name','renamenode','change_name_folder_ximlet.png','Change folder name',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6144,5040,'Version manager','manageversions','manage_versions.png','Manage version repository',77,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6145,5041,'Version manager','manageversions','manage_versions.png','Manage repository of versions',77,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6146,5028,'Version manager','manageversions','manage_versions.png','Manage repository of versions',77,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6147,5044,'Version manager','manageversions','manage_versions.png','Manage repository of versions',77,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6148,5039,'Version manager','manageversions','manage_versions.png','Manage repository of versions',77,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6149,5045,'Version manager','manageversions','manage_versions.png','Manage repository of versions',77,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6150,5057,'Version manager','manageversions','manage_versions.png','Manage repository of versions',77,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6143,5032,'Version manager','manageversions','manage_versions.png','Manage repository of versions',77,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6151,5056,'Add new language','addlangxmlcontainer','add_language_xml.png','Add a new language to ximlet',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6152,5056,'Delete ximlet','deletenode','delete_xml.png','Delete a ximlet permanently from system',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6153,5056,'Change ximlet name','renamenode','change_name_xml.png','Change ximlet name',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6154,5057,'Edit ximlet','xmleditor2','edit_file_xml.png','Modify content of a ximlet',1,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6155,5057,'Delete document','deletenode','delete_file_xml.png','Delete a ximlet and all its dependencies',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6156,5057,'Symbolic link','xmlsetlink','file_xml_symbolic.png','Modify the ximlet which borrows the content',30,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6157,5057,'Edit in text mode','edittext','edit_file_xml_txt.png','Edit ximlet with text mode',3,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6168,5040,'Replace image','fileupload','replace_image.png','Replace an existing image updating version history',90,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6170,5014,'Move node','movenode','move_node.png','Move a node',90,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6171,5015,'Move node','movenode','move_node.png','Move a node',90,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6172,5017,'Move node','movenode','move_node.png','Move a node',90,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6173,5021,'Move node','movenode','move_node.png','Move a node',90,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6174,5023,'Move node','movenode','move_node.png','Move a node',90,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6175,5028,'Move node','movenode','move_node.png','Move a node',90,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6176,5031,'Move node','movenode','move_node.png','Move a node',90,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6177,5039,'Move node','movenode','move_node.png','Move a node',90,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6178,5040,'Move node','movenode','move_node.png','Move a node',90,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6179,5041,'Move node','movenode','move_node.png','Move a node',90,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6180,5044,'Move node','movenode','move_node.png','Move a node',90,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6181,5048,'Move node','movenode','move_node.png','Move a node',90,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6182,5049,'Move node','movenode','move_node.png','Move a node',90,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6183,5056,'Move node','movenode','move_node.png','Move a node',90,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6184,5015,'Associate a ximlet with a section','addximlet','asociate_ximlet_folder.png','Associate a ximlet with a section',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7011,5075,'Delete relation','deleteximrel','remove_SIR_relation.png','Delete a ximSir relation',-10,'ximSIR',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6190,5041,'Replace file','updatefile','replace_file.png','Replace a file',90,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6191,5039,'Replace file','updatefile','replace_txt.png','Replace a file',90,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6343,5016,'Add images','fileupload_common_multiple','upload_image.png','Add an image set to the server',10,NULL,0,'type=image');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6344,5017,'Add images','fileupload_common_multiple','upload_image.png','Add an image set to the server',10,NULL,0,'type=image');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6345,5026,'Add PTD templates','fileupload_common_multiple','add_template_ptd.png','Add a set of PTD templates to the server',10,NULL,0,'type=ptd');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6346,5022,'Add files','fileupload_common_multiple','add_file_common.png','Add a set of files to the server',10,NULL,0,'type=common');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6347,5023,'Add files','fileupload_common_multiple','add_file_common.png','Add a set of files to the server',10,NULL,0,'type=common');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6348,5024,'Add style sheets','fileupload_common_multiple','add_file_css.png','Add a set of style sheets to the server',10,NULL,0,'type=css');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6349,5025,'Add style sheets','fileupload_common_multiple','add_file_css.png','Add a set of style sheets to the server',10,NULL,0,'type=css');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6350,5020,'Add new text files','fileupload_common_multiple','add_file_text_import.png','Add a set of text files to import',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6351,5021,'Add HTML files','fileupload_common_multiple','add_nodes_ht.png','Add multiple HTML files',10,NULL,0,'type=html');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6202,5014,'Associate ximlet with server','addximlet','asociate_ximlet_server.png','Associate a ximlet with a server',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6204,5015,'Publish section','publicatesection','publicate_section.png','Publish a section massively',10,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6205,5015,'Expire section','expiresection','expire_section.png','Expire a section',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6227,5076,'Edit in text mode','edittext','edit_html_txt_file.png','Edit content of text document',11,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6228,5076,'Download file','filedownload','download_html_file.png','Download selected file',20,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6229,5076,'Edit HTML','htmleditor','edit_html_file.png','Edita el documento html',10,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6230,5076,'Change name','renamenode','change_name_html_file.png','Change name of selected file',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6231,5076,'Version manager','manageversions','manage_html_versions.png','Manage repository of versions',77,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6232,5076,'Delete file','deletenode','delete_html_file.png','Delete HTML file',80,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6233,5076,'Move file','movenode','move_node.png','Move a node',90,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6234,5076,'Replace file','updatefile','replace_html_file.png','Replace a file',90,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6236,5020,'Add HTML files','fileupload_common_multiple','add_nodes_ht.png','Add multiple HTML files',10,NULL,0,'type=html');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6500,5012,'Export','serializeNodeXML','xix.png','Export all projects',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6501,5013,'Export','serializeNodeXML','xix.png','Export a complete project',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6502,5050,'Export','serializeNodeXML','xix.png','Export a ximlink',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6503,5048,'Export','serializeNodeXML','xix.png','Export a link folder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6504,5022,'Export','serializeNodeXML','xix.png','Export a complete common folder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6505,5023,'Export','serializeNodeXML','xix.png','Export a common subfolder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6506,5024,'Export','serializeNodeXML','xix.png','Export a complete CSS folder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6507,5025,'Export','serializeNodeXML','xix.png','Export a CSS subfolder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6508,5016,'Export','serializeNodeXML','xix.png','Export a complete image folder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6509,5017,'Export','serializeNodeXML','xix.png','Export a image subfolder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6510,5020,'Export','serializeNodeXML','xix.png','Export a complete ximclude folder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6511,5021,'Export','serializeNodeXML','xix.png','Export a ximclude subfolder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6512,5018,'Export','serializeNodeXML','xix.png','Export a complete ximdoc folder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6513,5031,'Export','serializeNodeXML','xix.png','Export a XML container',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6514,5054,'Export','serializeNodeXML','xix.png','Export a complete ximlet folder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6515,5055,'Export','serializeNodeXML','xix.png','Export a ximlet subfolder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6516,5056,'Export','serializeNodeXML','xix.png','Export a ximlet container',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6517,5014,'Export','serializeNodeXML','xix.png','Export a complete server',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6518,5015,'Export','serializeNodeXML','xix.png','Export a complete section',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6519,5053,'Export','serializeNodeXML','xix.png','Export a complete ximpvd',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6520,5026,'Export','serializeNodeXML','xix.png','Export a complete ximptd',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6521,5027,'Export','serializeNodeXML','xix.png','Export a template subfolder',-91,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6550,5012,'Import','deserializeNodeXML','xix.png','Import all projects',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6551,5013,'Import','deserializeNodeXML','xix.png','Import a complete project',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6552,5050,'Import','deserializeNodeXML','xix.png','Import a ximlink',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6553,5048,'Import','deserializeNodeXML','xix.png','Import a link folder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6554,5022,'Import','deserializeNodeXML','xix.png','Import a complete common folder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6555,5023,'Import','deserializeNodeXML','xix.png','Import a common subfolder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6556,5024,'Import','deserializeNodeXML','xix.png','Import a complete CSS folder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6557,5025,'Import','deserializeNodeXML','xix.png','Import a CSS subfolder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6558,5016,'Import','deserializeNodeXML','xix.png','Import a complete image folder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6559,5017,'Import','deserializeNodeXML','xix.png','Import a image subfolder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6560,5020,'Import','deserializeNodeXML','xix.png','Import a complete ximclude folder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6561,5021,'Import','deserializeNodeXML','xix.png','Import a ximclude subfolder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6562,5018,'Import','deserializeNodeXML','xix.png','Import a complete ximdoc folder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6563,5031,'Import','deserializeNodeXML','xix.png','Import a XML container',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6564,5054,'Import','deserializeNodeXML','xix.png','Import a complete ximlet folder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6565,5055,'Import','deserializeNodeXML','xix.png','Import a ximlet subfolder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6566,5056,'Import','deserializeNodeXML','xix.png','Import a ximlet container',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6567,5014,'Import','deserializeNodeXML','xix.png','Import a complete server',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6568,5015,'Import','deserializeNodeXML','xix.png','Import a complete section',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6569,5053,'Import','deserializeNodeXML','xix.png','Import a complete ximpvd',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6570,5026,'Import','deserializeNodeXML','xix.png','Import a complete ximptd',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6571,5027,'Import','deserializeNodeXML','xix.png','Import a template subfolder',-92,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6600,5013,'Copy','copy','Copy_proyecto.png','Copy a complete project',93,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6601,5050,'Copy','copyNode','copiar_documento.png','Copy a ximlink',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6602,5048,'Copy','copyNode','copiar_seccion.png','Copy a link folder',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6603,5022,'Copy','copyNode','copiar_seccion.png','Copy a complete common folder',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6604,5023,'Copy','copyNode','copiar_carpeta_common.png','Copy a common subfolder',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6605,5024,'Copy','copyNode','copiar_seccion.png','Copy a complete CSS folder',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6606,5025,'Copy','copyNode','copiar_seccion.png','Copy a CSS subfolder',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6607,5016,'Copy','copyNode','copiar_carpeta_images.png','Copy a complete image folder',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6608,5017,'Copy','copyNode','copiar_carpeta_images.png','Copy a image subfolder',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6609,5020,'Copy','copyNode','copiar_carpeta_ximclude.png','Copy a complete ximclude folder',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6610,5021,'Copy','copyNode','copiar_carpeta_ximclude.png','Copy a ximclude subfolder ',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6611,5018,'Copy','copyNode','copiar_carpeta_ximdoc.png','Copy a complete ximdoc folder',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6612,5031,'Copy','copyNode','copiar_carpeta_ximdoc.png','Copy a XML container',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6613,5054,'Copy','copyNode','copiar_carpeta_ximlet.png','Copy a complete ximlet folder',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6614,5055,'Copy','copyNode','copiar_carpeta_ximlet.png','Copy a ximlet subfolder',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6615,5056,'Copy','copyNode','copiar_carpeta_ximlet.png','Copy a ximlet container',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6616,5049,'Copy','copyNode','copiar_documento.png','Copy a link',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6617,5057,'Copy','copyNode','copiar_documento.png','Copy a ximlet',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6619,5014,'Copy','copyNode','copiar_servidor.png','Copy a complete server',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6620,5015,'Copy','copyNode','copiar_seccion.png','Copy a complete section',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6621,5053,'Copy','copyNode','copiar_seccion.png','Copy a complete ximpvd',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6622,5026,'Copy','copyNode','copiar_carpeta_ximptd.png','Copy a complete ximptd',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6623,5027,'Copy','copyNode','copiar_seccion.png','Copy a template subfolder',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6624,5044,'Copy','copyNode','copiar_seccion.png','Copy a template',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6625,5045,'Copy','copyNode','copiar_seccion.png','Copy a view template',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6629,5028,'Copy','copyNode','copiar_documento.png','Copy a style sheet',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6626,5039,'Copy','copyNode','copiar_documento.png','Copy a text file',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6627,5040,'Copy','copyNode','copiar_documento.png','Copy a image file',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6628,5041,'Copy','copyNode','copiar_documento.png','Copy a binary file',-93,'ximIO',0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7016, 5057, 'Publish ximlet', 'publicateximlet', 'xix.png', 'Publish documents associated with a ximlet', 30, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6352, 5018, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6353, 5304, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6354, 5022, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6355, 5023, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6356, 5050, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6357, 5048, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6360, 5016, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6361, 5017, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6363, 5020, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6364, 5021, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6365, 5024, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6366, 5025, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6367, 5026, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6368, 5027, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6369, 5042, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6370, 5043, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6372, 5047, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6373, 5051, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6374, 5053, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6375, 5054, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6376, 5055, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6377, 5300, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6378, 5301, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6379, 5302, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6380, 5306, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6381, 5310, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6382, 5320, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6383, 5015, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6384, 5014, 'Ximsearch', 'browser', 'browser.png', 'Ximsearch', -2, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6385, 5032, 'Preview', 'preview', 'xix.png', 'Preview of the document', 100, NULL, 0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7200, 5040, 'Copy', 'copy', 'copiar_documento.png', 'Copy a image to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7201, 5017, 'Copy', 'copy', 'copiar_carpeta_images.png', 'Copy a image subfolder to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7202, 5016, 'Copy', 'copy', 'copiar_carpeta_images.png', 'Copy a image folder to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7203, 5014, 'Copy', 'copy', 'copiar_servidor.png', 'Copy a server to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7204, 5018, 'Copy', 'copy', 'copiar_carpeta_ximdoc.png', 'Copy a ximdoc folder to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7205, 5020, 'Copy', 'copy', 'copiar_carpeta_ximclude.png', 'Copy a ximclude folder to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7206, 5021, 'Copy', 'copy', 'copiar_carpeta_ximclude.png', 'Copy a ximclude subfolder to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7207, 5022, 'Copy', 'copy', 'copiar_carpeta_common.png', 'Copy a common folder to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7208, 5023, 'Copy', 'copy', 'copiar_carpeta_common.png', 'Copia a common subfolder to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7209, 5024, 'Copy', 'copy', 'copiar_seccion.png', 'Copy a CSS folder to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7210, 5025, 'Copy', 'copy', 'copiar_seccion.png', 'Copy a CSS subfolder to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7211, 5026, 'Copy', 'copy', 'copiar_carpeta_ximptd.png', 'Copy a ximptd folde to another destination', '-30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7212, 5028, 'Copy', 'copy', 'copiar_documento.png', 'Copy a CSS document to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7213, 5031, 'Copy', 'copy', 'copiar_carpeta_ximdoc.png', 'Copy a document container to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES('7215', '5039', 'Copy', 'copy', 'copiar_documento.png', 'Copy a text document to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES('7216', '5041', 'Copy', 'copy', 'copiar_documento.png', 'Copy a binary document to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES('7217', '5048', 'Copy', 'copy', 'copiar_carpeta_ximlink.png', 'Copy a link subfolder to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES('7218', '5049', 'Copy', 'copy', 'copiar_documento.png', 'Copy a link to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES('7219', '5050', 'Copy', 'copy', 'copiar_carpeta_ximlink.png', 'Copy a ximlink folder to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES('7220', '5054', 'Copy', 'copy', 'copiar_carpeta_ximlet.png', 'Copy a ximlet folder to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES('7221', '5055', 'Copy', 'copy', 'copiar_carpeta_ximlet.png', 'Copy a ximlet subfolder to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES('7222', '5056', 'Copy', 'copy', 'copiar_carpeta_ximdoc.png', 'Copy a container of ximlet documents to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES('7223', '5057', 'Copy', 'copy', 'copiar_carpeta_ximlet.png', 'Copy a ximlet document to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES('7224', '5076', 'Copy', 'copy', 'copiar_documento.png', 'Copy a HTML document to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES('7225', '5015', 'Copy', 'copy', 'copiar_seccion.png', 'Copy a section to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES('7226', '5053', 'Copy', 'copy', 'copiar_carpeta_ximdoc.png', 'Copy a ximpvd folder to another destination', '-30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES('7227', '5045', 'Copy', 'copy', 'copiar_documento.png', 'Copy a PVD template to another destination', '30', NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7236, 5057, 'Manage associations', 'showassocnodes', 'xix.png', 'Manage node associations with ximlet', 1, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7237, 5015, 'Associate schema with a section', 'relsectionschema', 'xix.png', 'Associate view templates with a section', 11, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7238, 5013, 'Associate schema with a project', 'relsectionschema', 'xix.png', 'Associate view templates with a project',11, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7239, '0', 'Action report', 'actionsstats', 'xix.png', 'Show action report', 1, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7301, 5035, 'Add workflow', 'addworkflow', 'xix.png', 'Add a new workflow', -1, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7304, 5321, 'Edit RNG', 'edittext', 'edit_template_view.png', 'Edit RNG schema', 1, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7305, 5077, 'Delete XSL template', 'deletenode', 'delete_template_ptd.png', 'Delete a XSL template', 1, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7306, 5077, 'Edit XSL template', 'edittext', 'edit_template_ptd.png', 'Edit a XSL template', 2, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`) VALUES (7307, 5013, 'Modify heritable properties', 'manageproperties', 'xix.png', 'Modify properties of a project', 10, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`) VALUES (7308, 5014, 'Modify heritable properties', 'manageproperties', 'xix.png', 'Modify properties of a server', 10, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`) VALUES (7309, 5015, 'Modify heritable properties', 'manageproperties', 'xix.png', 'Modify properties of a section', 10, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`) VALUES (7310, 5018, 'Modify heritable properties', 'manageproperties', 'xix.png', 'Modify properties of a ximdoc folder', 10, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`) VALUES (7311, 5031, 'Modify heritable properties', 'manageproperties', 'xix.png', 'Modify properties', 10, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`) VALUES (7312, 5032, 'Modify heritable properties', 'manageproperties', 'xix.png', 'Modify properties of a document', 10, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`) VALUES (7317, 5082, 'Modify heritable properties', 'manageproperties', 'xix.png', 'Modify global properties', -10, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (7318, 5078, 'Delete RNG template', 'deletenode', 'delete_template_view.png', 'Delete a RNG template', 1, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (7319, 5078, 'Edit RNG template', 'edittext', 'edit_template_view.png', 'Edit a RNG template', 2, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (7320, 0, 'Charts', 'charts', 'xix.png', 'Graphic representation', 1, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`) VALUES (7321, 5078, 'Properties of RNG template', 'renamenode', 'modify_template_view', 'Modify properties of RNG', '20', NULL , '0', NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`,`Description`,`Sort`,`Module`,`Multiple`,`Params`) VALUES (7229, 5048, 'Check links', 'linkreport', 'xix.png', 'Check broken links', '10', NULL,0,NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`,`Description`,`Sort`,`Module`,`Multiple`,`Params`) VALUES (7231, 5050, 'Check links', 'linkreport', 'xix.png', 'Check broken links', '10', NULL,0,NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8101, 5016, 'Publish section', 'publicatesection', 'publicate_section.png', 'Publish a section massively', 10, NULL, 1, '');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8102, 5017, 'Publish section', 'publicatesection', 'publicate_section.png', 'Publish a section massively', 10, NULL, 1, '');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8103, 5024, 'Publish section', 'publicatesection', 'publicate_section.png', 'Publish a section massively', 10, NULL, 1, '');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8104, 5025, 'Publish section', 'publicatesection', 'publicate_section.png', 'Publish a section massively', 10, NULL, 1, '');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8105, 5022, 'Publish section', 'publicatesection', 'publicate_section.png', 'Publish a section massively', 10, NULL, 1, '');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8106, 5023, 'Publish section', 'publicatesection', 'publicate_section.png', 'Publish a section massively', 10, NULL, 1, '');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8107,5016,'Download all images','filedownload_multiple','download_image.png','Download all images',20,NULL,0,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8108,5017,'Download all images','filedownload_multiple','download_image.png','Download all images',20,NULL,0,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8109,5024,'Download all style sheets','filedownload_multiple','download_file_css.png','Download all style sheets',20,NULL,0,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8110,5025,'Download all style sheets','filedownload_multiple','download_file_css.png','Download all style sheets',20,NULL,0,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8111,5026,'Download all templates','filedownload_multiple','download_template_ptd.png','Download all templates',20,NULL,0,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8112,5053,'Download all templates','filedownload_multiple','download_template_view.png','Download all templates',20,NULL,0,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8113,5022,'Download all files','filedownload_multiple','download_html_file.png','Download all files',20,NULL,0,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8114,5023,'Download all files','filedownload_multiple','download_html_file.png','Download all files',20,NULL,0,'',1);
INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES (8115, 5053, 'Add PVD templates', 'fileupload_common_multiple', 'add_template_pvd.png', 'Add a set of PVD templates to the server', 10, NULL, 0, 'type=pvd', 0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8117,5077,'Change name','renamenode','modify_template_ptd.png','Change template name',70,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8118,5077,'Download template','filedownload','download_template_ptd.png','Download a template',20,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8119,5077,'Version manager','manageversions','manage_versions.png','Manage repository of versions',77,NULL,0,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8120,5077,'Move node','movenode','move_node.png','Move a node',90,NULL,1,'');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (8121,5026,'Delete templates','deletetemplates','delete_template_ptd.png','Delete selected templates',90,NULL,0,'');

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`,`IsBulk`)  VALUES (8122,5022,'Add empty file','newemptynode','add_file_common.png','Create a new empty file',95,NULL,0,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`,`IsBulk`)  VALUES (8123,5023,'Add empty file','newemptynode','add_file_common.png','Create a new empty file',95,NULL,0,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`,`IsBulk`)  VALUES (8124,5024,'Add empty file','newemptynode','add_file_common.png','Create a new empty file',95,NULL,0,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`,`IsBulk`)  VALUES (8125,5025,'Add empty file','newemptynode','add_file_common.png','Create a new empty file',95,NULL,0,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`,`IsBulk`)  VALUES (8126,5026,'Add empty file','newemptynode','add_file_common.png','Create a new empty file',95,NULL,0,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`,`IsBulk`)  VALUES (8127,5053,'Add empty file','newemptynode','add_file_common.png','Create a new empty file',95,NULL,0,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`,`IsBulk`)  VALUES (8128,5020,'Add empty file','newemptynode','add_file_common.png','Create a new empty file',95,NULL,0,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`,`IsBulk`)  VALUES (8129,5021,'Add empty file','newemptynode','add_file_common.png','Create a new empty file',95,NULL,0,'',0);

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) VALUES(8130, 5016, 'Image viewer', 'filepreview', 'view_image.png', 'Preview the images', 15, NULL, 0, 'method=showAll', 0);
INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`,`Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`, `IsBulk`)  VALUES (8131, 5017, 'Image viewer', 'filepreview', 'view_image.png', 'Preview the images', 15, NULL, 0, 'method=showAll',0);

UNLOCK TABLES;


/*!40000 ALTER TABLE `Actions` ENABLE KEYS */;
-- No actions for nodes
DROP TABLE IF EXISTS `NoActionsInNode`;
CREATE TABLE `NoActionsInNode` (
`IdNode` INT NOT NULL ,
`IdAction` INT NOT NULL COMMENT 'Action dont allowed for IdNode',
PRIMARY KEY ( `IdNode` , `IdAction` )
) ENGINE = MYISAM COMMENT = 'List Actions dont allowed in IdNode';

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
	PRIMARY KEY  (`IdChannel`)
) ENGINE=MYISAM COMMENT='Distribution channels';

--
-- Dumping data for table `Channels`
--


/*!40000 ALTER TABLE `Channels` DISABLE KEYS */;
LOCK TABLES `Channels` WRITE;
INSERT INTO `Channels` (`IdChannel`, `Name`, `Description`, `DefaultExtension`, `Format`, `Filter`, `RenderMode`) VALUES(10001, 'html', 'Html channel', 'html', NULL, NULL, 'ximdex');
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
) ENGINE=MYISAM COMMENT='Table with configuration parameters of Ximdex';

--
-- Dumping data for table `Config`
--


/*!40000 ALTER TABLE `Config` DISABLE KEYS */;
LOCK TABLES `Config` WRITE;
INSERT INTO `Config` VALUES (20,'PreviewInServer','0');
INSERT INTO `Config` VALUES (9,'GeneratorCommand','/modules/dexT/dexTdin_xmd25.pl');
INSERT INTO `Config` VALUES (2,'NodeRoot','/data/nodes');
INSERT INTO `Config` VALUES (3,'TempRoot','/data/tmp');
INSERT INTO `Config` VALUES (7,'GeneralGroup','101');
INSERT INTO `Config` VALUES (6,'UrlRoot','');
INSERT INTO `Config` VALUES (5,'FileRoot','/data/files');
INSERT INTO `Config` VALUES (8,'ProjectsNode','10000');
INSERT INTO `Config` VALUES (11,'DoctypeTag','<!DOCTYPE docxap [\n<!ENTITY Ntilde \"_MAPGENcode_Ntilde_\">\n<!ENTITY ntilde \"_MAPGENcode_ntilde_\">\n<!ENTITY aacute \"_MAPGENcode_aacute_\">\n<!ENTITY eacute \"_MAPGENcode_eacute_\">\n<!ENTITY iacute \"_MAPGENcode_iacute_\">\n<!ENTITY oacute \"_MAPGENcode_oacute_\">\n<!ENTITY uacute \"_MAPGENcode_uacute_\">\n<!ENTITY Aacute \"_MAPGENcode_Aacute_\">\n<!ENTITY Eacute \"_MAPGENcode_Eacute_\">\n<!ENTITY Iacute \"_MAPGENcode_Iacute_\">\n <!ENTITY Oacute \"_MAPGENcode_Oacute_\">\n<!ENTITY Uacute \"_MAPGENcode_Uacute_\">\n<!ENTITY agrave \"_MAPGENcode_agrave_\">\n <!ENTITY egrave \"_MAPGENcode_egrave_\">\n<!ENTITY igrave \"_MAPGENcode_igrave_\">\n <!ENTITY ograve \"_MAPGENcode_ograve_\">\n<!ENTITY ugrave \"_MAPGENcode_ugrave_\">\n<!ENTITY Agrave \"_MAPGENcode_Agrave_\">\n<!ENTITY Egrave \"_MAPGENcode_Egrave_\">\n<!ENTITY Igrave \"_MAPGENcode_Igrave_\">\n<!ENTITY Ograve \"_MAPGENcode_Ograve_\">\n<!ENTITY Ugrave \"_MAPGENcode_Ugrave_\">\n<!ENTITY auml   \"_MAPGENcode_auml_\">\n<!ENTITY euml   \"_MAPGENcode_euml_\">\n<!ENTITY iuml   \"_MAPGENcode_iuml_\">\n<!ENTITY ouml   \"_MAPGENcode_ouml_\">\n<!ENTITY uuml   \"_MAPGENcode_uuml_\">\n<!ENTITY Auml   \"_MAPGENcode_Auml_\">\n<!ENTITY Euml   \"_MAPGENcode_Euml_\">\n<!ENTITY Iuml   \"_MAPGENcode_Iuml_\">\n<!ENTITY Ouml   \"_MAPGENcode_Ouml_\">\n<!ENTITY Uuml   \"_MAPGENcode_Uuml_\">\n<!ENTITY Ccedil \"_MAPGENcode_Ccedil_\">\n<!ENTITY ccedil \"_MAPGENcode_ccedil_\">\n<!ENTITY ordf   \"_MAPGENcode_ordf_\">\n<!ENTITY ordm   \"_MAPGENcode_ordm_\">\n<!ENTITY iquest \"_MAPGENcode_iquest_\">\n<!ENTITY iexcl  \"_MAPGENcode_iexcl_\">\n<!ENTITY nbsp   \"_MAPGENcode_nbsp_\">\n<!ENTITY middot \"_MAPGENcode_middot_\">\n<!ENTITY acute  \"_MAPGENcode_acute_\">\n<!ENTITY copy  \"_MAPGENcode_copy_\">\n]>\n');
INSERT INTO `Config` VALUES (10,'EncodingTag','<?xml version=\"1.0\" encoding=\"UTF-8\"?>');
INSERT INTO `Config` VALUES (1,'AppRoot','');
INSERT INTO `Config` VALUES (12,'DefaultLanguage','es');
INSERT INTO `Config` VALUES (13,'BlockExpireTime','120');
INSERT INTO `Config` VALUES (14,'MaximunGapSizeTolerance','180');
INSERT INTO `Config` VALUES (15,'SynchronizerCommand','/modules/synchronizer/ximCRON.pl');
INSERT INTO `Config` VALUES (4,'SyncRoot','/data/sync');
INSERT INTO `Config` VALUES (16,'VisualTemplateDir','ximpvd');
INSERT INTO `Config` VALUES (17,'GeneratorTemplateDir','ximptd');
INSERT INTO `Config` VALUES (18,'PurgeSubversionsOnNewVersion','1');
INSERT INTO `Config` VALUES (19,'MaxSubVersionsAllowed','4');
INSERT INTO `Config` VALUES (21,'PurgeVersionsOnNewVersion','0');
INSERT INTO `Config` VALUES (22,'MaxVersionsAllowed','3');
INSERT INTO `Config` VALUES (23,'ximid','-');
INSERT INTO `Config` VALUES (24,'VersionName','Ximdex 3.3');
INSERT INTO `Config` VALUES (25,'UTFLevel','0');
INSERT INTO `Config` VALUES (26,'EmptyHrefCode','/404.html');
INSERT INTO `Config` VALUES (27, 'defaultPVD', NULL);
INSERT INTO `Config` VALUES (28, 'defaultChannel', NULL);
INSERT INTO `Config` VALUES (29, 'dexCache', NULL);
INSERT INTO `Config` VALUES (30, 'PublishOnDisabledServers', NULL);
INSERT INTO `Config` VALUES (31, 'defaultWebdavPVD', NULL);
INSERT INTO `Config` VALUES (32, 'locale', 'en_US');
INSERT INTO `Config` VALUES (33, 'displayEncoding', 'ISO-8859-1');
INSERT INTO `Config` VALUES (34, 'dbEncoding', 'ISO-8859-1');
INSERT INTO `Config` VALUES (35, 'dataEncoding', 'UTF-8');
INSERT INTO `Config` VALUES (36, 'workingEncoding', 'UTF-8');
INSERT INTO `Config` VALUES (NULL, 'ActionsStats', 0);
INSERT INTO `Config` VALUES (NULL, 'IdDefaultWorkflow', 403);
INSERT INTO `Config` VALUES (NULL, 'DefaultInitialStatus', 'Edición');
INSERT INTO `Config` VALUES (NULL, 'DefaultFinalStatus', 'Publicación');
INSERT INTO `Config` VALUES (NULL, 'PullMode', 0);
INSERT INTO `Config` VALUES (NULL, 'EnricherKey', '');
INSERT INTO `Config` VALUES (NULL, 'AddVersionUsesPool', '0');
INSERT INTO `Config` VALUES (NULL, 'StructuralDeps', 'css,asset,script');
INSERT INTO `Config` VALUES (NULL, 'xplorer', '1');
INSERT INTO `Config` VALUES (NULL, 'SyncStats', '0');
INSERT INTO `Config` VALUES (NULL, 'XslIncludesOnServer', '0');
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
  `DepType` varchar(10) NOT NULL default '0',
  `version` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdDep`),
  KEY `IdNodeMaster` (`IdNodeMaster`),
  KEY `IdNodeDependent` (`IdNodeDependent`),
  KEY `DepType` (`DepType`)
) ENGINE=MYISAM;

--
-- Dumping data for table `Dependencies`
--


/*!40000 ALTER TABLE `Dependencies` DISABLE KEYS */;
LOCK TABLES `Dependencies` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `Dependencies` ENABLE KEYS */;

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
) ENGINE=MYISAM COMMENT='Table of fast scan of node hierarchies';

--
-- Dumping data for table `FastTraverse`
--


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
) ENGINE=MYISAM COMMENT='Table of user groups of system';

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
) ENGINE=MYISAM COMMENT='Table of suggestions of ISO codes of languages';

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
) ENGINE=MYISAM  COMMENT='Ximdex Locales';


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
) ENGINE=MYISAM COMMENT='Table of Ximdex languages';

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
) ENGINE=MYISAM COMMENT='Table of link manager of Ximdex';

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
  `Description` varchar(255) NOT NULL,
  PRIMARY KEY  (`IdRel`),
  UNIQUE KEY `Description` (`IdLink`, `Description`)
) ENGINE=MYISAM COMMENT='Table of descriptions of Ximdex links';

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
  `FechaHora` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`IdMessage`)
) ENGINE=MYISAM COMMENT='Table of messages. One table for all users and folders. ';

--
-- Dumping data for table `Messages`
--


/*!40000 ALTER TABLE `Messages` DISABLE KEYS */;
LOCK TABLES `Messages` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `Messages` ENABLE KEYS */;

--
-- Table structure for table `NodeAllowedContents`
--

DROP TABLE IF EXISTS `NodeAllowedContents`;
CREATE TABLE `NodeAllowedContents` (
  `IdNodeAllowedContent` int(12) unsigned NOT NULL auto_increment,
  `IdNodeType` int(12) unsigned NOT NULL default '0',
  `NodeType` int(12) unsigned NOT NULL default '0',
  `Amount` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdNodeAllowedContent`),
  UNIQUE KEY `UniqeAmmount` (`IdNodeType`,`NodeType`)
) ENGINE=MYISAM COMMENT='Allowed node types into each type of node';

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
INSERT INTO `NodeAllowedContents` VALUES (43,5018,5031,0);
INSERT INTO `NodeAllowedContents` VALUES (46,5020,5021,0);
INSERT INTO `NodeAllowedContents` VALUES (47,5020,5039,0);
INSERT INTO `NodeAllowedContents` VALUES (48,5021,5039,0);
INSERT INTO `NodeAllowedContents` VALUES (49,5021,5021,0);
INSERT INTO `NodeAllowedContents` VALUES (41,5017,5017,0);
INSERT INTO `NodeAllowedContents` VALUES (50,5022,5023,0);
INSERT INTO `NodeAllowedContents` VALUES (51,5022,5039,0);
INSERT INTO `NodeAllowedContents` VALUES (52,5022,5040,0);
INSERT INTO `NodeAllowedContents` VALUES (53,5022,5041,0);
INSERT INTO `NodeAllowedContents` VALUES (54,5023,5023,0);
INSERT INTO `NodeAllowedContents` VALUES (55,5023,5039,0);
INSERT INTO `NodeAllowedContents` VALUES (56,5023,5040,0);
INSERT INTO `NodeAllowedContents` VALUES (57,5023,5041,0);
INSERT INTO `NodeAllowedContents` VALUES (60,5026,5044,0);
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
INSERT INTO `NodeAllowedContents` VALUES (84,5053,5045,0);
INSERT INTO `NodeAllowedContents` VALUES (85,5020,5076,0);
INSERT INTO `NodeAllowedContents` VALUES (86,5014,5054,1);
INSERT INTO `NodeAllowedContents` VALUES (87,5015,5054,1);
INSERT INTO `NodeAllowedContents` VALUES (88,5054,5056,0);
INSERT INTO `NodeAllowedContents` VALUES (89,5053,5040,0);
INSERT INTO `NodeAllowedContents` VALUES (90,5018,5032,0);
INSERT INTO `NodeAllowedContents` VALUES (91,5054,5055,0);
INSERT INTO `NodeAllowedContents` VALUES (92,5055,5055,0);
INSERT INTO `NodeAllowedContents` VALUES (101,5021,5076,0);
INSERT INTO `NodeAllowedContents` VALUES (102,5055,5056,0);
INSERT INTO `NodeAllowedContents` VALUES (null,5026,5077,0);
INSERT INTO `NodeAllowedContents` VALUES (null,5053,5078,0);
INSERT INTO `NodeAllowedContents` VALUES (null,5035,5079,0);
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
) ENGINE=MYISAM COMMENT='Default content of each node';

--
-- Dumping data for table `NodeDefaultContents`
--


/*!40000 ALTER TABLE `NodeDefaultContents` DISABLE KEYS */;
LOCK TABLES `NodeDefaultContents` WRITE;
INSERT INTO `NodeDefaultContents` VALUES (1,5015,5016,'images',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (3,5015,5018,'ximdoc',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (4,5015,5026,'ximptd',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (5,5015,5022,'common',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (6,5015,5020,'ximclude',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (7,5014,5024,'css',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (8,5014,5016,'images',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (10,5014,5018,'ximdoc',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (11,5014,5026,'ximptd',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (12,5014,5022,'common',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (13,5014,5020,'ximclude',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (14,5013,5026,'ximptd',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (16,5013,5050,'ximlink',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (17,5013,5053,'ximpvd',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (18,5014,5054,'ximlet',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (19,5015,5054,'ximlet',NULL,NULL);
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
) ENGINE=MYISAM COMMENT='Table of node dependencies of Ximdex';

--
-- Dumping data for table `NodeDependencies`
--


/*!40000 ALTER TABLE `NodeDependencies` DISABLE KEYS */;
LOCK TABLES `NodeDependencies` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `NodeDependencies` ENABLE KEYS */;

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
) ENGINE=MYISAM COMMENT='Node names in other language';

--
-- Dumping data for table `NodeNameTranslations`
--


/*!40000 ALTER TABLE `NodeNameTranslations` DISABLE KEYS */;
LOCK TABLES `NodeNameTranslations` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `NodeNameTranslations` ENABLE KEYS */;

--
-- Table structure for table `NodeRelations`
--

DROP TABLE IF EXISTS `NodeRelations`;
CREATE TABLE `NodeRelations` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdNodeMaster` int(12) unsigned NOT NULL default '0',
  `IdNodeDependent` int(12) unsigned NOT NULL default '0',
  `IdNodeRelation` int(12) unsigned NOT NULL default '0',
  `version` tinyint(3) unsigned default '0',
  PRIMARY KEY  (`IdRel`)
) ENGINE=MYISAM;

--
-- Dumping data for table `NodeRelations`
--


/*!40000 ALTER TABLE `NodeRelations` DISABLE KEYS */;
LOCK TABLES `NodeRelations` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `NodeRelations` ENABLE KEYS */;

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
  `IsPublicable` int(1) unsigned default NULL,
  `CanDenyDeletion` int(1) unsigned default NULL,
  `isGenerator` TINYINT( 1 ) NULL,
  `IsEnriching` TINYINT( 1 ) NULL,
  `System` int(1) unsigned default NULL,
  `Module` varchar(255) default NULL,
  PRIMARY KEY  (`IdNodeType`),
  UNIQUE KEY `IdType` (`Name`),
  KEY `IdType_2` (`IdNodeType`)
) ENGINE=MYISAM COMMENT='Node types of system';

--
-- Dumping data for table `NodeTypes`
--


/*!40000 ALTER TABLE `NodeTypes` DISABLE KEYS */;
LOCK TABLES `NodeTypes` WRITE;
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5001,'Root','root','root.png','Root node of Ximdex',1,0,0,0,0,1,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5002,'ControlCenter','root','controlcenter.png','Ximdex control center',0,0,1,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5003,'UserManager','root','user.png','User manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5004,'GroupManager','root','group.png','Group manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5005,'RoleManager','root','rol.png','Roles manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5006,'NodeTypeManager','root','nodetype.png','Type of node manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5007,'NodeType','nodetypenode','nodetype.png','Definition of node type of Ximdex',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5008,'Action','actionnode','action.png','Action run on node type of Ximdex',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5009,'User','usernode','user.png','Ximdex user',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5010,'Role','rolenode','rol.png','Role on user group of Ximdex',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5011,'Group','groupnode','group.png','User group of Ximdex',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5013,'Project','foldernode','nodetype.png','Ximdex project',1,1,1,0,0,1,0,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5012,'Projects','projects','projects.png','Root of Ximdex projects',1,0,1,0,1,1,0,0,0,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5032,'XmlDocument','xmldocumentnode','doc.png','XML document',1,1,0,0,0,0,0,1,1,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5014,'Server','servernode','server.png','Content server of Ximdex',1,1,1,1,1,1,0,0,1,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5015,'Section','sectionnode','folder.png','Ximdex section',1,1,1,1,1,0,0,0,1,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5016,'ImagesRootFolder','foldernode','folder_images.png','Root of image folder',1,1,0,0,1,0,0,0,1,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5017,'ImagesFolder','foldernode','folder_images.png','Image folder',1,1,0,0,1,0,0,0,1,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5018,'XmlRootFolder','foldernode','folder_xml.png','Root of XML folder',1,1,0,0,1,1,0,0,0,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5020,'ImportRootFolder','foldernode','folder_import.png','Root of import folder',1,1,0,0,1,0,0,0,0,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5021,'ImportFolder','foldernode','folder_import.png','Import folder',1,1,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5022,'CommonRootFolder','foldernode','folder_common.png','Root of common folder',1,1,0,0,1,0,0,0,1,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5023,'CommonFolder','foldernode','folder_common.png','Common folder',1,1,0,0,1,0,0,0,1,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5024,'CssRootFolder','foldernode','folder_css.png','Root of CSS folder',1,1,0,0,1,0,0,0,1,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5025,'CssFolder','foldernode','folder_css.png','CSS folder',1,1,0,0,1,0,0,0,1,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5026,'TemplatesRootFolder','foldernode','folder_templates.png','Root of template folder',1,1,0,0,1,0,0,0,0,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5031,'XmlContainer','Xmlcontainernode','contenedordoc.png','Container of XML docs',1,0,0,0,1,1,0,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5029,'ChannelManager','root','channel.png','Channel manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5030,'LanguageManager','root','language.png','Language manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5033,'Channel','channelnode','channel.png','Channel',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5034,'Language','languagenode','language.png','Language',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5035,'WorkflowManager','root','workflow.png','Workflow manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5036,'WorkflowState','statenode','workflow.png','Workflow status',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5037,'PermissionManager','root','permissions.png','Permits manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5038,'Permission','root','permission.png','Permit',0,0,0,0,0,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5039,'TextFile','filenode','text_file.png','Text file',1,1,0,0,0,0,1,0,1,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5040,'ImageFile','filenode','image.png','Image file',1,1,0,0,0,0,1,0,1,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5041,'BinaryFile','filenode','binary_file.png','Binary file',1,1,0,0,0,0,1,0,1,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5043,'ErrorFolder','foldernode','foldergray.png','Output error folder',1,1,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5044,'Template','templatenode','xml_document.png','Transformation template of Ximdex',1,1,0,0,0,0,1,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5045,'VisualTemplate','VisualTemplateNode','xml_document.png','Edition template of Ximdex',1,1,0,0,0,0,1,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5050,'LinkManager','foldernode','folder_links.png','Root of link manager',0,0,0,0,1,0,0,0,0,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5048,'LinkFolder','foldernode','folder_links.png','Category of link manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5028,'CssFile','filenode','css_document.png','Style sheet',1,1,0,0,0,0,1,0,1,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5049,'Link','linknode','link.png','Ximdex link',0,0,0,0,0,0,0,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5053,'TemplateViewFolder','foldernode','folder_template_view.png','Folder of view template',1,1,0,0,1,0,0,0,0,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5054,'XimletRootFolder','foldernode','folder_ximlet.png','Root folder of ximlets in sections',0,0,0,0,1,1,0,0,0,1,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5055,'XimletFolder','foldernode','folder_ximlet.png','Ximlets folder',0,0,0,0,1,1,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5056,'XimletContainer','Xmlcontainernode','contenedordoc.png','Ximlet container',0,0,0,0,1,1,0,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5057,'Ximlet','XimletNode','doc.png','ximlet',0,0,0,0,0,0,0,1,1,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5058,'PropertiesManager','root','folder_system_properties.png','Property manager',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5059,'Property','propertynode','property.png','Property',0,0,0,0,0,0,0,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5060,'ProjectPropFolder','foldernode','foldergray.png','Folder of project properties',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5061,'SystemProperty','propertynode','property.png','System properties',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5063,'ProjectDicFolder','foldernode','folder.png','Folder of project dictionary',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5064,'Dictionary','dicnode','foldergray.png','Dictionary',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5065,'DicValue','dicvaluenode','foldergray.png','Value of a dictionary',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5066,'DicFolder','foldernode','foldergray.png','Folder of dictionary values',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5067,'ximPORTA','root','folder_links.png','Folder of ximPORTA links',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5068,'PropSet','foldernode','foldergray.png','Folder of property set',NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5076,'NodeHt','filenode','nodeht_file.png','Html node',1,1,0,0,0,0,1,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5077,'XslTemplate','xsltnode','xml_document.png','Template of XSLT transformation',1,1,0,0,0,0,1,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5078,'RngVisualTemplate','rngvisualtemplatenode','xml_document.png','Template of RNG edition of Ximdex',1,1,0,0,0,0,1,0,0,0,0,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5079,'Workflow','workflow_process','workflow.png','Workflow for documents',0,0,0,0,0,0,0,0,0,0,1,'');
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5080,'ModulesFolder','foldernode','modulesconfig.png','Container of module settings',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5081,'ModuleInfoContainer','foldernode','modulesconfig.png','Container of a module settings',0,0,0,0,1,0,0,0,0,0,1,NULL);
INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES (5082, 'InheritableProperties', 'foldernode', 'modulesconfig.png', 'Heritable properties', 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 1, NULL);

UNLOCK TABLES;
/*!40000 ALTER TABLE `NodeTypes` ENABLE KEYS */;

--
-- Table structure for table `Nodes`
--
DROP TABLE IF EXISTS `Nodes`;
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
  `Path` text NOT NULL default '',
  PRIMARY KEY  (`IdNode`),
  UNIQUE KEY `IdNode` (`IdNode`,`IdParent`),
  KEY `IdNode_2` (`IdNode`,`IdParent`)
) ENGINE=MYISAM COMMENT='Table of system nodes';

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
INSERT INTO `Nodes` VALUES (12,2,5080,'Configuration manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (13, 12, 5082, 'Heritable properties', 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);
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
INSERT INTO `Nodes` VALUES (5027,6,5007,'TemplatesFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
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
INSERT INTO `Nodes` VALUES (5044,6,5007,'Template',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5045,6,5007,'VisualTemplate',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5047,6,5007,'TemplateImages',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5048,6,5007,'LinkFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5049,6,5007,'Link',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5050,6,5007,'LinkManager',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5051,6,5007,'XmlNewsFolder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (5052,6,5007,'XmlNews',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
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
INSERT INTO `Nodes` VALUES (5067,6,5007,'ximPORTA',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
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
INSERT INTO `Nodes` VALUES (6024,5027,5008,'Add template folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6025,5015,5008,'Change section name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6026,5013,5008,'Change project name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6027,5014,5008,'Change server name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6028,5017,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6030,5021,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6031,5023,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6032,5025,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6033,5027,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6034,5013,5008,'Delete proyect',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6035,5014,5008,'Delete server',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6036,5015,5008,'Delete section',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6037,5017,5008,'Delete folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6039,5021,5008,'Delete folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6040,5023,5008,'Delete folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6041,5025,5008,'Delete folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6042,5027,5008,'Delete',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6044,5018,5008,'Add new XML',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6045,5031,5008,'Add language',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6046,5035,5008,'Manage status',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6047,5030,5008,'Add language',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6048,5029,5008,'Add Channel',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6049,5041,5008,'Download file',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6050,5040,5008,'Download image',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6051,5039,5008,'Download file',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6052,5040,5008,'Image preview',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6063,5031,5008,'Delete XML container',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6064,5031,5008,'Change XML',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6065,5032,5008,'Edit XML',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6067,5008,5008,'Delete action',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6068,5053,5008,'Add template image',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6069,5050,5008,'Add category',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6070,5048,5008,'Add category',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6071,5050,5008,'Add external link',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6072,5048,5008,'Add external link',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6073,5049,5008,'Edit properties',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6074,5015,5008,'Asocciated groups',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6079,5053,5008,'Add editor template',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6080,5045,5008,'Delete template',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
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
INSERT INTO `Nodes` VALUES (6100,5032,5008,'Version repository',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6101,5011,5008,'Change users',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6102,5034,5008,'Delete',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6103,5028,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6104,5028,5008,'Download file',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6105,5028,5008,'Edit',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6106,5028,5008,'Delete',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6107,5033,5008,'Delete channel',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6108,5011,5008,'Delete group',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6109,5010,5008,'Delete role',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6110,5044,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6111,5044,5008,'Delete template',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6112,5044,5008,'Download template',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6113,5044,5008,'Edit template',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6114,5045,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6115,5045,5008,'Download template',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6116,5045,5008,'Edit template',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6117,5032,5008,'Generate',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6118,5048,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6119,5048,5008,'Delete folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6120,5049,5008,'Delete link',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6121,5036,5008,'Delete',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6122,5013,5008,'Associated groups',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6123,5014,5008,'Associated groups',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6124,5018,5008,'Modify view template',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6125,5014,5008,'Modify sync data',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
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
INSERT INTO `Nodes` VALUES (6147,5044,5008,'Version manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6148,5039,5008,'Version manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6149,5045,5008,'Version manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
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
INSERT INTO `Nodes` VALUES (6180,5044,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
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
INSERT INTO `Nodes` VALUES (6345,5026,5008,'Add PTD template',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6346,5022,5008,'Add files',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6347,5023,5008,'Add files',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6348,5024,5008,'Add style sheets',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6349,5025,5008,'Add style Sheets',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6350,5020,5008,'Add text files',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6351,5021,5008,'Add text files',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6352,5018,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6353,5304,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6354,5022,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6355,5023,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6356,5050,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6357,5048,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6360,5016,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6361,5017,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6363,5020,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6364,5021,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6365,5024,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6366,5025,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6367,5026,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6368,5027,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6369,5042,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6370,5043,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6372,5047,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6373,5051,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6374,5053,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6375,5054,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6376,5055,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6377,5300,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6378,5301,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6379,5302,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6380,5306,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6381,5310,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6382,5320,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6383,5015,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6384,5014,5008,'Ximsearch',0,0,NULL,0,0,NULL,NULL,DEFAULT);
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
INSERT INTO `Nodes` VALUES (6502,5050,5008,'Export a ximlink',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6503,5048,5008,'Export a link folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6504,5022,5008,'Export a complete common folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6505,5023,5008,'Export a common subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6506,5024,5008,'Export a complete CSS folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6507,5025,5008,'Export a CSS subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6508,5016,5008,'Export a complete image folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6509,5017,5008,'Export a image subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6510,5020,5008,'Export a complete ximclude folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6511,5021,5008,'Export a ximclude subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6512,5018,5008,'Export a complete ximdoc folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6513,5031,5008,'Export a XML container',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6514,5054,5008,'Export a complete ximlet folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6515,5055,5008,'Export a ximlet subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6516,5056,5008,'Export a ximlet container',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6517,5014,5008,'Export a complete server',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6518,5015,5008,'Export a complete section',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6519,5053,5008,'Export a complete ximpvd',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6520,5026,5008,'Export a complete ximptd',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6521,5027,5008,'Export a template subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6550,5012,5008,'Import all projects',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6551,5013,5008,'Import a complete project',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6552,5050,5008,'Import a ximlink',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6553,5048,5008,'Import a complete link folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6554,5022,5008,'Import a complete common folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6555,5023,5008,'Import a common subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6556,5024,5008,'Import a complete CSS folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6557,5025,5008,'Import a CSS subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6558,5016,5008,'Import a complete image folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6559,5017,5008,'Import a image subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6560,5020,5008,'Import a complete ximclude folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6561,5021,5008,'Import a ximclude subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6562,5018,5008,'Import a complete ximdoc folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6563,5031,5008,'Import a XML container',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6564,5054,5008,'Import a complete ximlet folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6565,5055,5008,'Import a ximlet subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6566,5056,5008,'Import a ximlet container',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6567,5014,5008,'Import a complete server',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6568,5015,5008,'Import a complete section',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6569,5053,5008,'Import a complete ximpvd',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6570,5026,5008,'Import a complete ximptd',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6571,5027,5008,'Import a template subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6600,5013,5008,'Copy a complete project',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6601,5050,5008,'Copy a ximlink',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6602,5048,5008,'Copy a link folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6603,5022,5008,'Copy a complete common folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6604,5023,5008,'Copy a common subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6605,5024,5008,'Copy a complete CSS folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6606,5025,5008,'Copy a CSS subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6607,5016,5008,'Copy a complete image folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6608,5017,5008,'Copy a image subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6609,5020,5008,'Copy a complete ximclude folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6610,5021,5008,'Copy a ximclude subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6611,5018,5008,'Copy a complete ximdoc folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6612,5031,5008,'Copy a XML container',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6613,5054,5008,'Copy a complete ximlet folder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6614,5055,5008,'Copy a ximlet subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6615,5056,5008,'Copy a ximlet container',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6616,5049,5008,'Copy a link',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6617,5057,5008,'Copy a ximlet',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6619,5014,5008,'Copy a complete server',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6620,5015,5008,'Copy a complete section',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6621,5053,5008,'Copy a complete ximpvd',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6622,5026,5008,'Copy a complete ximptd',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6623,5027,5008,'Copy template subfolder',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6624,5044,5008,'Copy a template',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6625,5045,5008,'Copy a view template',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6626,5039,5008,'Copy a text file',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6627,5040,5008,'Copy a binary file',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6628,5041,5008,'Copy a binary file',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (6629,5028,5008,'Copy a style sheet',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7229, 5048, 5008, 'Check links', 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7231, 5050, 5008, 'Check links', 0, 0, NULL, 0, 0, NULL, NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7232,5034,5008,'Associate projects with language',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7233,5013,5008,'Associate languages with projects',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7234,5033,5008,'Associate projects with channel',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7235,5013,5008,'Associate channels with project',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7236,5057,5008,'Manage associations',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7237,5015,5008,'Associate schema with section',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (7238,5013,5008,'Associate schema with project',0,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8117,5077,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8118,5044,5008,'Download template',NULL,NULL,NULL,NULL,NULL,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8119,5077,5008,'Version manager',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8120,5077,5008,'Move node',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);
INSERT INTO `Nodes` VALUES (8121,5026,5008,'Delete templates',NULL,0,NULL,0,0,NULL,NULL,DEFAULT);

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
) ENGINE=MYISAM COMMENT='Table of system permits';

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
) ENGINE=MYISAM COMMENT='Table of sync protocols of Ximdex';

--
-- Dumping data for table `Protocols`
--


/*!40000 ALTER TABLE `Protocols` DISABLE KEYS */;
LOCK TABLES `Protocols` WRITE;
INSERT INTO `Protocols` VALUES ('SSH',22,'Secure transfer protocol',1);
INSERT INTO `Protocols` VALUES ('LOCAL',NULL,'Local synchronization',0);
INSERT INTO `Protocols` VALUES ('FTP',21,'FTP synchronization',1);
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
) ENGINE=MYISAM COMMENT='Association of user groups with nodes';

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
-- Table structure for table `RelPvdRole`
--

DROP TABLE IF EXISTS RelPvdRole;
CREATE TABLE RelPvdRole (
  IdRel int(12) unsigned NOT NULL auto_increment,
  IdXimLetNode int(12) unsigned NOT NULL default '0',
  IdSectionNode int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (IdRel)
) ENGINE=MYISAM;

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
) ENGINE=MYISAM COMMENT='Assignment of default command of each role';

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
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8112,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7226,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6374,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6068,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6079,0,1,3);
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
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6149,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6116,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6115,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6114,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6080,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6180,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6147,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6113,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6112,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6111,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6110,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,8117,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6370,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7216,8,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7216,7,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6190,8,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6190,7,1,3);
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
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6168,8,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6168,7,1,3);
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
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6191,8,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6191,7,1,3);
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
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6350,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6018,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7310,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7204,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6352,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6124,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6044,0,1,3);
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
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7237,8,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7237,7,1,3);
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
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7238,0,1,3);
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
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7229,NULL,1,NULL);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,7231,NULL,1,NULL);
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
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7238,0,1,3);
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
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7237,7,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,7237,8,1,3);
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
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6350,0,1,3);
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
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6079,0,1,3);
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6068,0,1,3);
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
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,204,6234,0,1,3);
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
 INSERT INTO `RelRolesActions`(`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL,201,6234,0,1,3);
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


INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL, 201, 8130, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES (NULL, 201, 8131, 0, 1, 3);

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
) ENGINE=MYISAM COMMENT='Association of roles and permits';

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
) ENGINE=MYISAM COMMENT='Association of roles with status transitions';

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
) ENGINE=MYISAM COMMENT='Table which associates physical servers with channels .';

--
-- Dumping data for table `RelServersChannels`
--


/*!40000 ALTER TABLE `RelServersChannels` DISABLE KEYS */;
LOCK TABLES `RelServersChannels` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `RelServersChannels` ENABLE KEYS */;

--
-- Table structure for table `RelServersNodes`
--

DROP TABLE IF EXISTS `RelServersNodes`;
CREATE TABLE `RelServersNodes` (
  `IdServer` int(12) unsigned NOT NULL default '0',
  `IdNode` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdServer`,`IdNode`)
) ENGINE=MYISAM COMMENT='Table which associates servers with view templates';

--
-- Dumping data for table `RelServersNodes`
--


/*!40000 ALTER TABLE `RelServersNodes` DISABLE KEYS */;
LOCK TABLES `RelServersNodes` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `RelServersNodes` ENABLE KEYS */;

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
) ENGINE=MYISAM COMMENT='Table which associates servers with workflow status';

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
  PRIMARY KEY  (`IdRel`)
) ENGINE=MYISAM COMMENT='Association between structured documents and their channels';

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
) ENGINE=MYISAM COMMENT='Associate template with container';

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
  `IdRel` int(12) unsigned zerofill NOT NULL auto_increment,
  `IdUser` int(12) unsigned NOT NULL default '0',
  `IdGroup` int(12) unsigned NOT NULL default '0',
  `IdRole` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdRel`),
  KEY `IdUSer` (`IdUser`),
  KEY `IdGroup` (`IdGroup`)
) ENGINE=MYISAM COMMENT='Assing users to a group with a role';

--
-- Dumping data for table `RelUsersGroups`
--


/*!40000 ALTER TABLE `RelUsersGroups` DISABLE KEYS */;
LOCK TABLES `RelUsersGroups` WRITE;
INSERT INTO `RelUsersGroups` VALUES (000000000001,301,101,201);
UNLOCK TABLES;
/*!40000 ALTER TABLE `RelUsersGroups` ENABLE KEYS */;


--
-- Table structure for table `RelXimletNode`
--

DROP TABLE IF EXISTS RelXimletNode;
CREATE TABLE RelXimletNode (
  IdRel int(12) unsigned NOT NULL auto_increment,
  IdXimLetNode int(12) unsigned NOT NULL default '0',
  IdSectionNode int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (IdRel)
) ENGINE=MYISAM;


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
) ENGINE=MYISAM COMMENT='Table of roles that an user can play into a group';

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
) ENGINE=MYISAM COMMENT='Table with info about Ximdex servers';

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
) ENGINE=MYISAM COMMENT='Table of Workflow status';

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
  `CreationDate` timestamp NOT NULL,
  `UpdateDate` timestamp NOT NULL default '0000-00-00 00:00:00',
  `IdLanguage` int(12) default '0',
  `IdTemplate` int(12) unsigned NOT NULL default '0',
  `TargetLink` int(12) unsigned default NULL,
  PRIMARY KEY  (`IdDoc`)
) ENGINE=MYISAM COMMENT='Table of strutured documents of Ximdex';

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
) ENGINE=MYISAM DELAY_KEY_WRITE=1 COMMENT='Table of sync of Ximdex';

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
) ENGINE=MYISAM DELAY_KEY_WRITE=1 COMMENT='Table of dependencies of publication windows of Ximdex';

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
) ENGINE=MYISAM DELAY_KEY_WRITE=1 COMMENT='Table of sync history of Ximdex';

--
-- Table structure for table `SynchronizerDependenciesHistory`
--

DROP TABLE IF EXISTS `SynchronizerDependenciesHistory`;
CREATE TABLE `SynchronizerDependenciesHistory` (
  `IdSync` int(12) unsigned NOT NULL default '0',
  `IdResource` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdSync`,`IdResource`)
) ENGINE=MYISAM DELAY_KEY_WRITE=1 COMMENT='Table of dependece hystory of publication windows of Ximdex';


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
) ENGINE=MYISAM COMMENT='Table of sharing workflow between nodes';

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
) ENGINE=MYISAM;

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
  PRIMARY KEY  (`IdUser`),
  UNIQUE KEY `login` (`Login`)
) ENGINE=MYISAM COMMENT='Tabla de Usuarios del sistema';


--
-- Dumping data for table `Users`
--


/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
LOCK TABLES `Users` WRITE;
INSERT INTO `Users` VALUES (301,'ximdex','$1$qSGCbgO1$yqxywUuVs1w5pd7capSQV.','Administrador de ximdex','notify@ximdex.org', NULL);
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
  `SubVersion` tinyint(3) unsigned NOT NULL default '0',
  `File` varchar(255) NOT NULL default '',
  `IdUser` int(12) unsigned default '0',
  `Date` int(14) unsigned default '0',
  `Comment` blob,
  `IdSync` int(12) unsigned default NULL,
  PRIMARY KEY  (`IdVersion`),
  KEY `Version` (`SubVersion`,`IdNode`,`Version`),
  KEY `IdNode` (`IdNode`)
) ENGINE=MYISAM COMMENT='Table of contents and version management';

--
-- Dumping data for table `Versions`
--


/*!40000 ALTER TABLE `Versions` DISABLE KEYS */;
LOCK TABLES `Versions` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `Versions` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
DROP TABLE IF EXISTS `NodeConstructors`;
CREATE TABLE `NodeConstructors` (
`IdNodeConstructor` int(11) NOT NULL auto_increment,
`IdNodeType` int(11) NOT NULL,
`IdAction` int(11) NOT NULL,
PRIMARY KEY  (`IdNodeConstructor`)
);

--
-- Dumping data for table `NodeConstructors`
--

INSERT INTO `NodeConstructors` (`IdNodeConstructor`, `IdNodeType`, `IdAction`) VALUES
(1, 5026, 6012),
(2, 5050, 6011),
(3, 5053, 6011),
(4, 5014, 6012),
(5, 5048, 6070),
(6, 5049, 6072),
(7, 5045, 6079),
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
  `mimeString` varchar(255) NOT NULL default '',
  `extension` char(20) NULL,
  `filter` char(50) NULL,
  PRIMARY KEY  (`idRelNodeTypeMimeType`)
) ENGINE=MyISAM COMMENT='Relation between nodetypes and mime-types' AUTO_INCREMENT= 141 ;

--
-- Dumping data for table `RelNodeTypeMimeType`
--

INSERT INTO `RelNodeTypeMimeType` VALUES (1, 5001, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (2, 5002, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (3, 5003, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (4, 5004, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (5, 5005, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (6, 5006, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (7, 5007, 'text/xml', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (8, 5008, 'text/xml', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (9, 5009, 'text/xml', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (10, 5010, 'text/xml', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (11, 5011, 'text/xml', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (12, 5012, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (13, 5013, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (14, 5014, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (15, 5015, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (16, 5016, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (17, 5017, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (18, 5018, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (20, 5020, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (21, 5021, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (22, 5022, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (23, 5023, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (24, 5024, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (25, 5025, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (26, 5026, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (27, 5027, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (28, 5028, 'text/css|text/x-c', ';css;', 'css');
INSERT INTO `RelNodeTypeMimeType` VALUES (29, 5029, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (30, 5030, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (31, 5031, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (32, 5032, 'text/xml', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (33, 5033, 'text/xml', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (34, 5034, 'text/xml', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (35, 5035, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (36, 5036, 'text/xml', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (37, 5037, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (38, 5038, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (39, 5039, 'text/plain', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (40, 5040, 'image/jpeg | image/png | image/gif | image/x-icon', ';jpeg;jpg;gif;png;ico;', 'image');
INSERT INTO `RelNodeTypeMimeType` VALUES (41, 5041, 'query/file', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (42, 5042, 'text/html', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (43, 5043, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (44, 5044, 'text/xml', ';xml;', 'ptd');
INSERT INTO `RelNodeTypeMimeType` VALUES (45, 5045, 'text/xml', ';xml;', 'pvd');
INSERT INTO `RelNodeTypeMimeType` VALUES (47, 5047, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (48, 5048, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (49, 5049, 'text/plain', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (50, 5050, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (51, 5051, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (52, 5052, 'text/plain', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (53, 5053, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (54, 5054, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (55, 5055, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (56, 5056, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (57, 5057, 'text/xml', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (58, 5058, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (59, 5059, 'text/xml', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (60, 5060, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (61, 5061, 'text/xml', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (62, 5062, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (63, 5063, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (64, 5064, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (65, 5065, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (66, 5066, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (67, 5067, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (68, 5068, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (69, 5069, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (70, 5070, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (71, 5071, 'text/xml', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (72, 5072, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (73, 5073, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (74, 5074, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (75, 5075, 'text/xml', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (76, 5076, 'text/html', ';ht;html;htm;', 'html');
INSERT INTO `RelNodeTypeMimeType` VALUES (77, 5300, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (78, 5301, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (79, 5302, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (80, 5303, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (81, 5304, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (82, 5305, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (83, 5306, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (84, 5307, 'image/jpeg | image/png | image/gif', ';jpeg;jpg;gif;png;', 'ximnewsimage');
INSERT INTO `RelNodeTypeMimeType` VALUES (85, 5308, 'text/xml', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (86, 5309, 'text/xml', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (87, 5310, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (88, 5320, 'httpd/unix-directory', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (89, 80000, 'application/msword', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (90, 80000, 'text/rtf', 'rtf', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (91, 80000, 'application/x-zip', 'odt', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (92, 5032, 'application/vnd.oasis.opendocument.text', 'odt', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (93, 5032, 'application/vnd.sun.xml.writer', 'sxw', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (94, 5032, 'application/rtf', 'rtf', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (95, 5032, 'application/x-rtf', 'rtf', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (96, 5032, 'text/rtf', 'rtf', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (97, 5032, 'text/richtext', 'rtf', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (98, 5032, 'application/msword', 'rtf', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (99, 5032, 'application/doc', 'rtf', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (100, 5032, 'application/x-soffice', 'rtf', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (101, 5032, 'application/msword', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (102, 5032, 'application/doc', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (103, 5032, 'appl/text', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (104, 5032, 'application/vnd.msword', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (105, 5032, 'application/vnd.ms-word', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (106, 5032, 'application/winword', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (107, 5032, 'application/word', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (108, 5032, 'application/x-msw6', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (109, 5032, 'application/x-msword', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (110, 5032, 'zz-application/zz-winassoc-doc', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (111, 5032, '', 'wpd', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (112, 5032, 'text/html ', 'htm', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (113, 5032, 'text/html ', 'html', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (114, 5032, 'text/html ', 'xhtml', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (115, 5076, 'application/vnd.oasis.opendocument.text', 'odt', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (116, 5076, 'application/vnd.sun.xml.writer', 'sxw', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (117, 5076, 'application/rtf', 'rtf', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (118, 5076, 'application/x-rtf', 'rtf', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (119, 5076, 'text/rtf', 'rtf', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (120, 5076, 'text/richtext', 'rtf', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (121, 5076, 'application/msword', 'rtf', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (122, 5076, 'application/doc', 'rtf', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (123, 5076, 'application/x-soffice', 'rtf', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (124, 5076, 'application/msword', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (125, 5076, 'application/doc', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (126, 5076, 'appl/text', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (127, 5076, 'application/vnd.msword', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (128, 5076, 'application/vnd.ms-word', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (129, 5076, 'application/winword', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (130, 5076, 'application/word', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (131, 5076, 'application/x-msw6', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (132, 5076, 'application/x-msword', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (133, 5076, 'zz-application/zz-winassoc-doc', 'doc', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (134, 5076, '', 'wpd', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (135, 5076, 'text/html ', 'htm', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (136, 5076, 'text/html ', 'html', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (137, 5076, 'text/html ', 'xhtml', 'XIMPORTA');
INSERT INTO `RelNodeTypeMimeType` VALUES (138, 5028, 'text/plain', ';css;', 'css');
INSERT INTO `RelNodeTypeMimeType` VALUES (139, 5032, 'text/plain', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (140, 5055, 'text/plain', '', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (141, 5041, 'application/pdf', ';pdf;', '');
INSERT INTO `RelNodeTypeMimeType` VALUES (142, 5044, 'text/plain', ';xml;', 'ptd');
INSERT INTO `RelNodeTypeMimeType` VALUES (143, 5044, 'text/html', ';xml;', 'ptd');
INSERT INTO `RelNodeTypeMimeType` VALUES (144, 5044, 'application/xml', ';xml;', 'ptd');
INSERT INTO `RelNodeTypeMimeType` VALUES (145, 5045, 'text/plain', ';xml;', 'pvd');
INSERT INTO `RelNodeTypeMimeType` VALUES (146, 5045, 'text/html', ';xml;', 'pvd');
INSERT INTO `RelNodeTypeMimeType` VALUES (147, 5077, 'text/plain', ';xsl;', 'ptd');
INSERT INTO `RelNodeTypeMimeType` VALUES (148, 5077, 'application/xml', ';xsl;', 'ptd');
INSERT INTO `RelNodeTypeMimeType` VALUES (149, 5077, 'text/html', ';xsl;', 'ptd');
INSERT INTO `RelNodeTypeMimeType` VALUES (150, 5045, 'application/xml', ';xml;', 'pvd');
INSERT INTO `RelNodeTypeMimeType` VALUES (151, 5078, 'text/xml', ';xml;', 'pvd');

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
INSERT INTO `SectionTypes` ( `idSectionType` , `sectionType` , `idNodeType`  , `module` ) VALUES ('3', 'ximPDF', 8000, 'ximPDF');



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
(6, 7, 8, 4, 0, 'Edición_to_Publicación', '-'),
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

--  Table for storage of aditional properties of nodes
DROP TABLE IF EXISTS `NodeProperties`;
CREATE TABLE `NodeProperties` (
`IdNodeProperty` INT NULL AUTO_INCREMENT,
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
(161, 5044, 'C', NULL),
(162, 5044, 'R', NULL),
(163, 5044, 'U', NULL),
(164, 5044, 'D', NULL),
(165, 5045, 'C', 6079),
(166, 5045, 'R', NULL),
(167, 5045, 'U', 6116),
(168, 5045, 'D', 6080),
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
(237, 5067, 'C', NULL),
(238, 5067, 'R', NULL),
(239, 5067, 'U', NULL),
(240, 5067, 'D', NULL),
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Actions use stats';

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
) ENGINE=MYISAM COMMENT='Tabla de posibles codificaciones de publicación de xmiDEX';

--
-- Dumping data for table `Encodes`
--

LOCK TABLES `Encodes` WRITE;
INSERT INTO `Encodes` VALUES ('UTF-8','Codificación Utf-8');
INSERT INTO `Encodes` VALUES ('ISO-8859-1','Codificación Iso-8859-1');
UNLOCK TABLES;

-- Table structure for table RelStrdocNode

DROP TABLE IF EXISTS `RelStrdocNode`;
CREATE TABLE RelStrdocNode (
	id int(12) unsigned NOT NULL auto_increment,
	source int(12) unsigned NOT NULL default '0',
	target int(12) unsigned NOT NULL default '0',
	PRIMARY KEY (id),
	UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MYISAM;

-- Table structure for table RelStrdocXimlet

DROP TABLE IF EXISTS `RelStrdocXimlet`;
CREATE TABLE RelStrdocXimlet (
	id int(12) unsigned NOT NULL auto_increment,
	source int(12) unsigned NOT NULL default '0',
	target int(12) unsigned NOT NULL default '0',
	PRIMARY KEY (id),
	UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MYISAM;

-- Table structure for table RelStrdocTemplate

DROP TABLE IF EXISTS `RelStrdocTemplate`;
CREATE TABLE RelStrdocTemplate (
	id int(12) unsigned NOT NULL auto_increment,
	source int(12) unsigned NOT NULL default '0',
	target int(12) unsigned NOT NULL default '0',
	PRIMARY KEY (id),
	UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MYISAM;

-- Table structure for table RelSectionXimlet

DROP TABLE IF EXISTS `RelSectionXimlet`;
CREATE TABLE RelSectionXimlet (
	id int(12) unsigned NOT NULL auto_increment,
	source int(12) unsigned NOT NULL default '0',
	target int(12) unsigned NOT NULL default '0',
	PRIMARY KEY (id),
	UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MYISAM;

-- Table structure for table RelBulletinXimlet

DROP TABLE IF EXISTS `RelBulletinXimlet`;
CREATE TABLE RelBulletinXimlet (
	id int(12) unsigned NOT NULL auto_increment,
	source int(12) unsigned NOT NULL default '0',
	target int(12) unsigned NOT NULL default '0',
	PRIMARY KEY (id),
	UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MYISAM;

-- Table structure for table RelStrdocAsset

DROP TABLE IF EXISTS `RelStrdocAsset`;
CREATE TABLE RelStrdocAsset (
	id int(12) unsigned NOT NULL auto_increment,
	source int(12) unsigned NOT NULL default '0',
	target int(12) unsigned NOT NULL default '0',
	PRIMARY KEY (id),
	UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MYISAM;

-- Table structure for table RelStrdocCss

DROP TABLE IF EXISTS `RelStrdocCss`;
CREATE TABLE RelStrdocCss (
	id int(12) unsigned NOT NULL auto_increment,
	source int(12) unsigned NOT NULL default '0',
	target int(12) unsigned NOT NULL default '0',
	PRIMARY KEY (id),
	UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MYISAM;

-- Table structure for table RelStrdocScript

DROP TABLE IF EXISTS `RelStrdocScript`;
CREATE TABLE RelStrdocScript (
	id int(12) unsigned NOT NULL auto_increment,
	source int(12) unsigned NOT NULL default '0',
	target int(12) unsigned NOT NULL default '0',
	PRIMARY KEY (id),
	UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MYISAM;

-- Table structure for table RelStrdocStructure

DROP TABLE IF EXISTS `RelStrdocStructure`;
CREATE TABLE RelStrdocStructure (
	id int(12) unsigned NOT NULL auto_increment,
	source int(12) unsigned NOT NULL default '0',
	target int(12) unsigned NOT NULL default '0',
	PRIMARY KEY (id),
	UNIQUE KEY `rel` (`source`,`target`)
) ENGINE=MYISAM;

-- --------------------------------------------------------

--
-- Table structure for table `Graphs`
--

DROP TABLE IF EXISTS `Graphs`;
CREATE TABLE IF NOT EXISTS `Graphs` (
  `id` int(11) NOT NULL auto_increment,
  `label` varchar(255) NOT NULL,
  `width` int(11) default NULL,
  `height` int(11) default NULL,
  `description` varchar(255) default NULL,
  `callback` varchar(255) default NULL,
  `series` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `GraphSerieProperties`
--

DROP TABLE IF EXISTS `GraphSerieProperties`;
CREATE TABLE IF NOT EXISTS `GraphSerieProperties` (
  `id` int(11) NOT NULL auto_increment,
  `IdGraphSerie` int(11) NOT NULL,
  `property` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `GraphSeries`
--

DROP TABLE IF EXISTS `GraphSeries`;
CREATE TABLE IF NOT EXISTS `GraphSeries` (
  `id` int(11) NOT NULL auto_increment,
  `IdGraph` int(11) NOT NULL,
  `Label` varchar(255) NOT NULL,
  `SerieRepresentation` int(11) default NULL,
  `SerieType` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `GraphSerieValues`
--

DROP TABLE IF EXISTS `GraphSerieValues`;
CREATE TABLE IF NOT EXISTS `GraphSerieValues` (
  `id` int(11) NOT NULL auto_increment,
  `IdGraphSerie` int(11) NOT NULL,
  `x` double default NULL,
  `y` double default NULL,
  `TimeStamp` timestamp NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `List`;
CREATE TABLE `List` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`IdList` INT NOT NULL ,
	`Name` VARCHAR( 250 ) NOT NULL ,
	`Description` VARCHAR( 250 ) NULL ,
	PRIMARY KEY ( `id` )
) ENGINE = MYISAM;


DROP TABLE IF EXISTS `List_Label`;
CREATE TABLE `List_Label` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`Name` VARCHAR( 250 ) NOT NULL ,
	`Description` VARCHAR( 250 ) NULL ,
	PRIMARY KEY ( `id` )
) ENGINE = MYISAM;


DROP TABLE IF EXISTS `RelVersionsLabel`;
CREATE TABLE `RelVersionsLabel` (
	`id` int(12) unsigned NOT NULL auto_increment,
	`idVersion` int(12) unsigned default '0',
	`idLabel` int(12) unsigned default '0',
	PRIMARY KEY  (`id`),
	UNIQUE KEY `VersionsLabelRest` (`idVersion`,`idLabel`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


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

