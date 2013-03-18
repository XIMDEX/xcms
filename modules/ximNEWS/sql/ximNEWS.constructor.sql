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

INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublicable`, `CanDenyDeletion`, `System`, `Module`) VALUES
(5300, 'XimNewsSection', 'sectionnode', 'folder_ximnews.png', 'XimNEWS section', 1, 1, 1, 1, 1, 0, 0, 0, 1, 0, 0, 'ximNEWS'),
(5301, 'XimNewsBulletins', 'foldernode', 'folder_bulletins.png', 'XimNEWS bulletin folder', 1, 1, 0, 0, 0, 1, 0, 0, 0, 0, 1, 'ximNEWS'),
(5304, 'XimNewsNews', 'foldernode', 'folder_news.png', 'XimNEWS news folder', 1, 1, 0, 0, 1, 0, 0, 0, 0, 0, 1, 'ximNEWS'),
(5306, 'XimNewsImages', 'foldernode', 'folder_images.png', 'XimNEWS image root folder', 1, 1, 0, 0, 1, 0, 0, 0, 1, 0, 1, 'ximNEWS'),
(5303, 'XimNewsBulletin', 'xmlcontainernode', 'contenedordoc.png', 'XimNEWS bulletin', 1, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 'ximNEWS'),
(5305, 'XimNewsNew', 'xmlcontainernode', 'contenedordoc.png', 'XimNEWS news', 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 'ximNEWS'),
(5308, 'XimNewsBulletinLanguage', 'ximnewsbulletinnodetype', 'doc.png', 'XimNEWS bulletin in a concrete language', 1, 1, 0, 0, 0, 0, 0, 1, 1, 0, 0, 'ximNEWS'),
(5307, 'XimNewsImageFile', 'ximnewsimagefile', 'image.png', 'XimNEWS image file', 1, 1, 0, 0, 0, 0, 1, 0, 1, 0, 0, 'ximNEWS'),
(5309, 'XimNewsNewLanguage', 'ximnewsnewlanguage', 'doc.png', 'Bulleting news', 1, 1, 0, 0, 0, 0, 0, 1, 1, 0, 0, 'ximNEWS'),
(5302, 'XimNewsColector', 'ximnewscolectornodetype', 'folder_bulletins.png', 'XimNEWS news colector', 1, 1, 1, 0, 0, 1, 0, 0, 0, 0, 1, 'ximNEWS'),
(5310, 'XimNewsImagesFolder', 'ximnewsimagesfolder', 'folder_images.png', 'XimNEWS image folder', 1, 1, 0, 0, 1, 0, 0, 0, 0, 0, 1, 'ximNEWS'),
(5311, 'XimNewsBulletinXimlet', 'xmlcontainernode', 'contenedordoc.png', 'Bulletin as ximlet container in ximNEWS', 1, 1, 0, 0, 1, 0, 0, 0, 0, 0, 1, 'ximNEWS'),
(5313, 'XimNewsBulletinLanguageXimletContainer', 'xmlcontainernode', 'contenedordoc.png', 'Index bulletin container', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 'ximNEWS'),
(5312, 'XimNewsBulletinLanguageXimlet', 'xmldocumentnode', 'doc.png', 'Bulletin as ximlet in ximNEWS', 1, 1, 0, 0, 0, 0, 1, 1, 1, 0, 1, 'ximNEWS'),
(5320, 'XimNewsDateSection', 'foldernode', 'folder_section_news.png', 'XimNEWS resource forlder', 1, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 'ximNEWS');

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6700,5300,'Delete ximNEWS section','deletenode','delete_section_ximnews.png','Delete a whole ximNEWS section',90,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6701,5300,'Change name','renamenode','change_name_section_ximnews.png','Change a ximNEWS section name',91,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6630,5300,'Copy ximNEWS node','copy','copiar_seccion.png','Copy a whole ximNEWS section',97,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6702,5301,'Add news colector','createcolector','add_section_news.png','Create a new news colector',90,'ximNEWS',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6704,5302,'Delete colector','deletenode','delete_cat_bulletin.png','Delete the current colector',91,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6705,5302,'Edit colector','createcolector','edit_cat.png','Edit the current colector','92','ximNEWS',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6707,5303,'Delete bulletin','deletenode','delete_bulletin.png','Delete a news bulletin',-91,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6708,5303,'Change name','renamenode','edit_bulletin.png','Change the name of the news colector',92,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6710,5304,'Create news','createnews','add_news.png','Create a new news',90,'ximNEWS',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6717,5309,'Associate news to colectors','addtocolector','link_n2b.png','Associate a news to a colector',90,'ximNEWS',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6718,5305,'Delete news','deletenode','delete_new.png','Delete a news in ximNEWS',91,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6720,5306,'Add image batch','createLote','add_folder_images.png','Create a new image folder',90,'ximNEWS',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6723,5306,'Image viewer','browserimages','view_images.png','Visualize the image batchs',-10,'ximNEWS',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6728,5307,'Download image','filedownload','download_image.png','Download an image to the local hard disk',90,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6729,5307,'Preview image','filepreview','view_image.png','Preview an image',91,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6730,5307,'Change image name','renamenode','change_name_image.png','Change the image name',92,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6731,5307,'Delete image','deletenode','delete_image.png','Delete the current image',93,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6740,5308,'Edit XML','xmleditor2','edit_file_xml.png','Edit the XML document content',90,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6745,5308,'Delete document','deletenode','delete_file_xml.png','Delete the selected XML document',93,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6747,5308,'Edit in text mode','edittext','edit_file_xml_txt.png','Edit a structured document content at a low level',97,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6744,5308,'Delete news from bulletin','unlinknews','delete_news_bulletin.png','Delete news from a bulletin',-1,'ximNEWS',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6750,5309,'Edit XML','xmleditor2','edit_file_xml.png','Edit a XML document content',90,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6755,5309,'Delete document','deletenode','delete_file_xml.png','Delete the selected XML document',95,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6757,5309,'Edit in text mode','edittext','edit_file_xml_txt.png','Edit a structured document content at a low level',97,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6721,5310,'View images','browserlote','view_images.png','See the images of a batch',-10,'ximNEWS',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6724,5310,'Change name','renamenode','change_name_folder_images.png','Change the name of the selected folder',91,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6725,5310,'Delete folder','deletenode','delete_folder_images.png','Delete the selected folder',92,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Params`) VALUES (6726,5310,'Add image','fileupload','upload_image.png','Add a new image to the folder',93,'','type=ximnewsimage');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Params`) VALUES (6727,5310,'Add images','fileupload_common_multiple','upload_image.png','Add a set of images to the folder',94,NULL,'type=ximnewsimage');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6762,5311,'Delete bulletin','deletenode','delete_bulletin.png','Delete a bulletin',-91,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6761,5311,'Change name','renamenode','edit_bulletin.png','Change the bulletin name',92,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6760,5311,'Add laguage','addlangbulletin','add_language.png','Add a new language to bulletin',93,'ximNEWS',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6764,5312,'Edit XML','xmleditor2','edit_file_xml.png','Edit the XML document content',90,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6765,5312,'Delete document','deletenode','delete_file_xml.png','Delete the selected XML document',93,'',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6767,5312,'Edit in text mode','edittext','edit_file_xml_txt.png','Edit a structured document content at a low level',97,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6768,5312,'Delete news from bulletin','unlinknews','xix.png','Delete news from a bulletin',-97,'ximNEWS',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6769,5302,'Generate bulletins','generatecolector','generate_colector.png','Generate the bulletins of a colector',94,'ximNEWS',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6772,5302,'Delete news from colector','unlinknewscolector','delete_news_bulletin.png','Delete news from a colector',93,'ximNEWS',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6773,5310,'Publish section','publicatesection','publicate_section.png','Publish a section in a row',96,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6776,5305,'Add language','addlangxmlcontainer','add_language_xml.png','Add a new language to the news',10,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6777,5309,'Delete news from colector','unlinknewscolector','delete_news_bulletin.png','Delete news from a colector',93,'ximNEWS',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6712,5300,'Associate ximlet with ximNEWS section','addximlet','asociate_ximlet_folder.png','Associate a ximlet to the section',92,NULL,1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Params`) VALUES (6778,5307,'Replace image','fileupload','replace_image.png','Replace an existing image updating the version history',97,'','type=ximnewsimage');
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6779,5305,'Change name','renamenode','change_name_section_ximnews.png','Change the name of a ximNEWS news',91,'',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6780,5309,'Version manager','manageversions','manage_versions.png','Manage the version repository',77,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6781,5302,'See associated news','viewaddednews','view_associated_news.png','Show the news associated to a colector',91,'ximNEWS',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (6782,5300,'Associated groups','modifygroupsnode','groups_section.png','Manage the association of groups with this node',95,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (7020,5300,'Add news folder','addfoldernode','xix.png','Create a new news folder',96,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (7021,5302,'Associated groups','modifygroupsnode','groups_section.png','Manage the association of groups with this node',95,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES (7252,5309,'Symbolic link','xmlsetlink','file_xml_symbolic.png','Modify the document to whom it is borrowing the content',30,NULL,0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (6743, 5308, 'Publish bulletin', 'workflow_forward', 'change_next_state.png', 'Publish a bulletin', 95, NULL, 0, NULL);
INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`) VALUES
(7313, 5300, 'Modify heritable properties', 'manageproperties', 'xix.png', 'Modify the ximNEWS folder properties', 10, NULL, 0, NULL);
INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`) VALUES
(7314, 5304, 'Modify heritable properties', 'manageproperties', 'xix.png', 'Modify heritable properties', 10, NULL, 0, NULL);
INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`) VALUES
(7315, 5305, 'Modify heritable properties', 'manageproperties', 'xix.png', 'Modify heritable properties', 10, NULL, 0, NULL);
INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`) VALUES
(7316, 5309, 'Modify heritable properties', 'manageproperties', 'xix.png', 'Modify heritable properties of a news', 10, NULL, 0, NULL);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`, `Params`)  VALUES (7322, 5300, 'Publish ximNEWS section','publicatesection','publicate_section.png','Publish a ximNEWS section',-9999,NULL,1,'');

--
-- Browser actions and nodes. Move to principal when necessary
--

INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES(7017, 5300, 'Manage categories', 'manageareas', 'manage_categories.png', 'Manage categories', 110, 'ximNEWS',0);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES(7018, 5305, 'Associate news to categories', 'addtoarea', 'asociate_news_category.png', 'Associate categories', 110, 'ximNEWS',1);
INSERT INTO `Actions`(`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`,`Sort`, `Module`, `Multiple`) VALUES(7019, 5305, 'Delete news from categories', 'deleterelnewsarea', 'delete_news_category.png', 'Delete news from categories', 130, 'ximNEWS',0);

INSERT INTO Nodes VALUES(7017, 5300, 5008, 'Manage categories', '0', '0', NULL, '0', '0', NULL, NULL, DEFAULT);
INSERT INTO Nodes VALUES(7018, 5305, 5008, 'Associate news to categories', '0', '0', NULL, '0', '0', NULL, NULL, DEFAULT);
INSERT INTO Nodes VALUES(7019, 5305, 5008, 'Delete news from categories', '0', '0', NULL, '0', '0', NULL, NULL, DEFAULT);


--
-- Associating actions to roles by default
--
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6740, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6723, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6380, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6353, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6378, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6377, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6380, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6353, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6702, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6378, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6377, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6382, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6768, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6767, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6765, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6764, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6760, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6761, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6762, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6763, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6773, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6727, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6726, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6725, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6724, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6721, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6381, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6780, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6777, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6757, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6755, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6750, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6717, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6747, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6745, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6740, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6778, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6731, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6730, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6729, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6728, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6723, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6720, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6380, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7019, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7018, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6779, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6776, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6718, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6710, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6353, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6708, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6707, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6706, 7, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6781, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6772, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6769, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6705, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6704, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6379, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6702, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6378, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7017, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6782, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6712, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6701, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6700, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6377, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6373, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6371, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6093, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6092, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6090, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6066, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7015, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7020, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7021, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7252, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7322, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6630, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7241, 0, 1, NULL);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6377, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6377, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6700, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6700, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6701, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6701, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6630, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6712, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6712, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6782, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6782, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7020, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7020, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7313, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7313, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7322, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7017, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7017, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6378, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6702, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6353, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6710, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7314, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6380, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6380, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6720, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6720, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6723, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6723, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6706, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6706, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6708, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6708, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6718, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6776, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6779, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7315, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7018, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7019, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6740, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6740, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6745, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6745, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6747, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6747, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6744, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6744, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6743, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6743, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6728, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6728, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6729, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6729, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6730, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6730, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6731, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6731, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6778, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6778, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 8116, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 8116, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6717, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6717, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6750, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6750, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6755, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6755, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6757, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6757, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6777, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6777, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6780, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6780, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7252, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7252, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7316, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7316, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6379, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6704, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6705, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6769, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6772, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6781, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 7021, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6381, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6721, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6724, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6725, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6726, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6727, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6773, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6763, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6761, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6760, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6764, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6764, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6765, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6765, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6767, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6767, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6768, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6768, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 204, 6382, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6747, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6728, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6729, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 8116, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6750, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6757, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6780, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 7252, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6379, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6781, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6381, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6721, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6764, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6767, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 202, 6382, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6723, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6706, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6706, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 7018, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 7019, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6744, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6743, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6743, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6728, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6729, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6717, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6777, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6780, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 7252, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6379, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6705, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6769, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6772, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6781, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6381, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6721, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6773, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6763, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6761, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6760, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6768, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6768, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 203, 6382, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6377, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6377, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6700, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6700, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6701, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6701, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6630, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6630, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6712, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6712, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6782, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6782, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7020, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7020, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7313, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7313, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7322, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7322, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7017, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7017, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6378, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6702, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6353, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6710, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7314, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6380, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6380, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6720, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6720, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6723, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6723, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6706, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6706, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6707, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6707, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6708, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6708, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6718, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6776, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6779, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7315, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7018, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7019, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6740, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6740, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6745, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6745, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6747, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6747, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6744, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6744, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6743, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6743, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6728, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6728, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6729, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6729, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6730, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6730, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6731, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6731, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6778, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6778, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 8116, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 8116, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6717, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6717, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6750, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6750, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6755, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6755, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6757, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6757, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6777, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6777, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6780, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6780, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7252, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7252, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7316, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7316, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6379, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6704, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6705, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6769, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6772, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6781, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 7021, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6381, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6721, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6724, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6725, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6726, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6727, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6773, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6763, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6762, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6761, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6760, 0, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6764, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6764, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6765, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6765, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6767, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6767, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6768, 7, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6768, 8, 1, 3);
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES(NULL, 201, 6382, 0, 1, 3);


INSERT INTO `Nodes`  VALUES (6700,5300,5008,'Delete ximNEWS section',NULL,NULL,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6701,5300,5008,'Change name',NULL,NULL,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6702,5301,5008,'Add news colector',NULL,NULL,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6703,5302,5008,'Create bulletin',NULL,NULL,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6704,5302,5008,'Delete colector',NULL,NULL,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6706,5303,5008,'Add news',NULL,NULL,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6708,5063,5008,'Add a news',0,0,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6709,5305,5008,'Add a news',0,0,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6710,5304,5008,'Create news',0,0,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6713,5306,5008,'Delete news',0,0,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6714,5306,5008,'Change news name',0,0,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6715,5305,5008,'Add language to bulletin',0,0,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6716,5307,5008,'Unpublish bulletin',0,0,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6717,5305,5008,'Associate news to bulletins',0,0,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6720,5306,5008,'Add image batch',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6721,5310,5008,'See images',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6723,5306,5008,'Image viewer',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6724,5310,5008,'Change name',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6725,5310,5008,'Delete folder',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6726,5310,5008,'Add image',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6727,5310,5008,'Add images',NULL,0,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6728,5307,5008,'Download image',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6729,5307,5008,'Preview images',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6730,5307,5008,'Change image name',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6731,5307,5008,'Delete image',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6740,5308,5008,'Edit XML',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6741,5308,5008,'Move to next state',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6742,5308,5008,'Move to previous state',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6745,5308,5008,'Delete document',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6747,5308,5008,'Edit in text mode',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6750,5309,5008,'Edit XML',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6751,5309,5008,'Move to next state',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6752,5309,5008,'Move to previous state',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6755,5309,5008,'Delete document',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6757,5309,5008,'Edit in text mode',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6759,5309,5008,'Copy XML',NULL,0,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (7252,5309,5008,'Symbolic link',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (6630,5300,5008,'Copy ximNEWS',NULL,0,NULL,0,0,NULL,NULL, DEFAULT);
INSERT INTO `Nodes`  VALUES (7322,5300,5008,'Publish ximNEWS section',NULL,NULL,NULL,NULL,NULL,NULL,NULL, DEFAULT);


INSERT INTO `NodeDefaultContents` VALUES (NULL,5300,5306,'images',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (NULL,5300,5301,'bulletins',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (NULL,5300,5304,'news',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (NULL,5300,5026,'ximptd',NULL,NULL);
INSERT INTO `NodeDefaultContents` VALUES (NULL,5300,5022,'common',NULL,NULL);

-- It is reserved for XimNEWS the NodeAllowedContents from 200 on --
-- NodeAllowedContents for Servers root folder
INSERT INTO `NodeAllowedContents` VALUES (NULL,5014,5300,0);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5015,5300,0);

-- NodeAllowedContents for XimNEWS root folder
INSERT INTO `NodeAllowedContents` VALUES (NULL,5300,5301,1);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5300,5022,1);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5300,5306,1);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5300,5026,1);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5300,5304,0);

-- NodeAllowedContents for bulletins folder and descendants
INSERT INTO `NodeAllowedContents` VALUES (NULL,5301,5302,0);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5302,5313,1);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5313,5312,0);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5302,5303,0);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5303,5308,0);

-- NodeAllowedContents for image folder and descendants
INSERT INTO `NodeAllowedContents` VALUES (NULL,5306,5310,0);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5306,5320,0);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5310,5307,0);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5320,5320,0);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5320,5307,0);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5320,5310,0);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5310,5320,0);

-- NodeAllowedContents for news folder and descendants
INSERT INTO `NodeAllowedContents` VALUES (NULL,5304,5305,0);
INSERT INTO `NodeAllowedContents` VALUES (NULL,5305,5309,0);

update Config set ConfigValue=0 where ConfigKey='PurgeSubversionsOnNewVersion';

--	Params related with news bellows:

INSERT INTO Config Values ('', 'RatioNewsFuelle', 10);
INSERT INTO Config Values ('', 'ToleranciaFuelle', 25);
INSERT INTO Config Values ('', 'StartCheckNoFuelle', 2);
INSERT INTO Config Values ('', 'EndCheckNoFuelle', 5);

#
# Table structure for table 'XimNewsBulletins'
#
DROP TABLE IF EXISTS `XimNewsBulletins`;

CREATE TABLE `XimNewsBulletins` (
  `IdBulletin` int(12) unsigned NOT NULL default '0',
  `IdContainer` int(12) unsigned NOT NULL default '0',
  `IdColector` int(12) unsigned NOT NULL default '0',
  `IdLote` int(12) unsigned default NULL,
  `Fecha` varchar(255) default '0',
	`SetAsoc` varchar(255) default NULL,
	`State` varchar(255) NOT NULL default 'generated',
  PRIMARY KEY  (`IdBulletin`),
  KEY `IdBulletin` (`IdContainer`)
);

#
# Table structure for table 'XimNewsNews'
#
DROP TABLE IF EXISTS `XimNewsNews`;

CREATE TABLE `XimNewsNews` (
  `IdNew` int(12) unsigned NOT NULL default '0',
  `Fecha` int(12) unsigned NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '',
  `Titular` varchar(255) NOT NULL default '',
  `TimeStamp` int(12) unsigned NOT NULL default '0',
  `IdSection` int(12) unsigned default '0',
  `AssociationAttempts` int(12) unsigned default 0,
   PRIMARY KEY  (`IdNew`)
);

#
# Table structure for table 'XimNewsAreas'
#
DROP TABLE IF EXISTS `XimNewsAreas`;

CREATE TABLE `XimNewsAreas` (
  `IdArea` int(12) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '',
  `Description` varchar(255) NOT NULL default '',
   PRIMARY KEY  (`IdArea`)
);

#
# Table structure for table 'XimNewsList'
#
DROP TABLE IF EXISTS `XimNewsList`;

CREATE TABLE `XimNewsList` (
  `IdList` int(12) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '',
   PRIMARY KEY  (`IdList`)
);


--
-- Table structure for table `RelNewsArea`
--
DROP TABLE IF EXISTS  `RelNewsArea`  ;


CREATE TABLE `RelNewsArea` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdNew` int(12) unsigned NOT NULL default '0',
  `IdArea` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdRel`),
  KEY `IdNew` (`IdArea`)
);

--
-- Table structure for table `RelNewsColector`
--
DROP TABLE IF EXISTS  `RelNewsColector`  ;
DROP TABLE IF EXISTS  `RelNewsCategory`  ;

CREATE TABLE `RelNewsColector` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdNew` int(12) unsigned NOT NULL default '0',
  `IdColector` int(12) unsigned NOT NULL default '0',
	`State` varchar(255) default 'pending',
	`SetAsoc` varchar(255) default NULL,
	`PosInSet` int(12) default NULL,
	`Page` int(12) default NULL,
	`PosInSet2` int(12) default NULL,
	`Page2` int(12) default NULL,
	`LangId` int(12) default NULL,
	`FechaIn` int(12) default NULL,
	`FechaOut` int(12) default NULL,
	`Version` int(12) default NULL,
	`SubVersion` int(12) default NULL,
	`IdCache` int(12) default NULL,
  PRIMARY KEY  (`IdRel`),
  KEY `IdNew` (`IdColector`)
);

DROP TABLE IF EXISTS `XimNewsFrameBulletin`;

#
# Table structure for table 'XimNewsFrameBulletin'
#

CREATE TABLE `XimNewsFrameBulletin` (
  IdSync int(12) unsigned NOT NULL default '0',
  BulletinID int(12) unsigned NOT NULL default '0',
  State  varchar(255) NOT NULL default '',
  KEY IdSync (IdSync)
) ENGINE=MyISAM;

--
-- Table structure for table 'XimNewsFrameVersion'
--
DROP TABLE IF EXISTS  `XimNewsFrameVersion` ;

CREATE TABLE `XimNewsFrameVersion` (
  IdSync int(12) unsigned NOT NULL default '0',
  IdVersion int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (IdSync)
) ENGINE=MyISAM;

--
-- Table structure for table `XimNewsColector`
--
-- IdXimlet changed to NULL because when the colector is created, the ximlets does not exist yet
--

DROP TABLE IF EXISTS  `XimNewsColector`;
DROP TABLE IF EXISTS  `XimNewsCategory`;

CREATE TABLE `XimNewsColector` (
  `IdColector` int(12) unsigned NOT NULL default '0',
  `Name` varchar(255) NOT NULL default '',
  `Filter` varchar(255) NOT NULL default '',
  `IdTemplate` int(12) unsigned NOT NULL default '0',
  `IdSection` int(12) unsigned NOT NULL default '0',
  `IdXimlet` int(12) unsigned NULL default NULL,
  `OrderNewsInBulletins` varchar(255) default 'asc',
    `NewsPerBulletin` int(12) default 100000,
    `TimeToGenerate` int(12) default NULL,
    `NewsToGenerate` int(12) default NULL,
    `LastGeneration` int(12) default NULL,
    `MailChannel` int(12) default NULL,
    `Locked` tinyint(1) NOT NULL default 0,
  `XslFile` varchar(255) default NULL,
  `TemplateVersion` int(12) unsigned NOT NULL default '0',
  `Inactive` int(12) unsigned NOT NULL default '0',
  `IdArea` int(12) unsigned default NULL,
    `ForceTotalGeneration` int(12) default 0,
    `Global` tinyint(1) NULL default 0,
    `State` varchar(255) NOT NULL default 'created',
   PRIMARY KEY  (`IdColector`),
   KEY `IdBulletin` (`IdColector`)
);

--
-- Table structure for table `RelNewsBulletins`
--
DROP TABLE IF EXISTS  `RelNewsBulletins`  ;


CREATE TABLE `RelNewsBulletins` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdNew` int(12) unsigned NOT NULL default '0',
  `IdBulletin` int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdRel`),
  KEY `IdNew` (`IdBulletin`)
);

--
-- Table structure for table `RelColectorList`
--
DROP TABLE IF EXISTS  `RelColectorList`;


CREATE TABLE `RelColectorList` (
  `IdRel` int(12) unsigned NOT NULL auto_increment,
  `IdColector` int(12) unsigned NOT NULL default '0',
  `IdList`  int(12) unsigned NOT NULL default '0',
  PRIMARY KEY  (`IdRel`),
  KEY `IdNew` (`IdColector`)
);

--
-- Table structure for table `XimNewsCache`
--
DROP TABLE IF EXISTS  `XimNewsCache`;

CREATE TABLE `XimNewsCache` (
  `IdCache` int(12) unsigned NOT NULL auto_increment,
  `IdNew` int(12) unsigned NOT NULL default '0',
  `IdTemplate` int(12) unsigned NOT NULL default '0',
  `IdVersion` int(12) unsigned default NULL,
  `File` varchar(255) default '0',
  `Fecha` varchar(255) default NULL,
  `Counter` int(12) unsigned default NULL,
  PRIMARY KEY  (`IdCache`)
);
