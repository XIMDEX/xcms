DELETE FROM `NodeConstructors` WHERE idNodeType NOT IN (SELECT IdNodeType FROM NodeTypes);

ALTER TABLE `NodeConstructors` ADD CONSTRAINT `NodeConstructors_Actions` FOREIGN KEY (`IdAction`) REFERENCES `Actions`(`IdAction`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `NodeConstructors` ADD CONSTRAINT `NodeConstructors_NodeTypes` FOREIGN KEY (`IdNodeType`) REFERENCES `NodeTypes`(`IdNodeType`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `NodeDefaultContents` ENGINE = InnoDB;

ALTER TABLE `NodeDefaultContents` ADD CONSTRAINT `NodeDefaultContents_NodeTypes` FOREIGN KEY (`IdNodeType`) 
REFERENCES `NodeTypes`(`IdNodeType`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `NodeDefaultContents` ADD CONSTRAINT `NodeDefaultContents_NodeTypes_target` FOREIGN KEY (`NodeType`) 
REFERENCES `NodeTypes`(`IdNodeType`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `NodeNameTranslations` ENGINE = InnoDB;

ALTER TABLE `NodeNameTranslations` ADD CONSTRAINT `NodeNameTranslations_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes`(`IdNode`) 
ON DELETE CASCADE ON UPDATE CASCADE;

DELETE FROM `NodeNameTranslations` WHERE IdLanguage NOT IN (SELECT IdLanguage FROM Languages);

ALTER TABLE `NodeNameTranslations` ADD CONSTRAINT `NodeNameTranslations_Languages` FOREIGN KEY (`IdLanguage`) 
REFERENCES `Languages`(`IdLanguage`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelTemplateContainer` CHANGE `IdTemplate` `IdTemplate` INT(12) UNSIGNED NOT NULL, 
CHANGE `IdContainer` `IdContainer` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `NodetypeModes` ADD UNIQUE (`IdNodeType`, `Mode`, `IdAction`);

ALTER TABLE `NodetypeModes` CHANGE `id` `id` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT, 
CHANGE `IdNodeType` `IdNodeType` INT(12) UNSIGNED NOT NULL, CHANGE `IdAction` `IdAction` INT(12) UNSIGNED NULL DEFAULT NULL;

DELETE FROM `NodetypeModes` WHERE IdNodeType NOT IN (SELECT IdNodeType FROM NodeTypes);

DELETE FROM `NodetypeModes` WHERE IdAction NOT IN (SELECT IdAction FROM Actions);

ALTER TABLE `NodetypeModes` ENGINE = InnoDB;

ALTER TABLE `NodetypeModes` ADD CONSTRAINT `NodetypeModes_NodeTypes` FOREIGN KEY (`IdNodeType`) REFERENCES `NodeTypes`(`IdNodeType`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `NodetypeModes` ADD CONSTRAINT `NodetypeModes_Actions` FOREIGN KEY (`IdAction`) REFERENCES `Actions`(`IdAction`) 
ON DELETE CASCADE ON UPDATE CASCADE;

DELETE FROM `RelDocumentFolderToTemplatesIncludeFile` WHERE `source` NOT IN (SELECT IdNode FROM Nodes);

DELETE FROM `RelDocumentFolderToTemplatesIncludeFile` WHERE `target` NOT IN (SELECT IdNode FROM Nodes);

ALTER TABLE `RelDocumentFolderToTemplatesIncludeFile` ENGINE = InnoDB;

ALTER TABLE `RelDocumentFolderToTemplatesIncludeFile` CHANGE `id` `id` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT, 
CHANGE `source` `source` INT(12) UNSIGNED NOT NULL COMMENT 'idNode of the XML document folder', 
CHANGE `target` `target` INT(12) UNSIGNED NOT NULL COMMENT 'idNode of the templates folder';

ALTER TABLE `RelDocumentFolderToTemplatesIncludeFile` ADD CONSTRAINT `RelDocumentFolderToTemplatesIncludeFile_Nodes_Source` 
FOREIGN KEY (`source`) REFERENCES `Nodes`(`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelDocumentFolderToTemplatesIncludeFile` ADD CONSTRAINT `RelDocumentFolderToTemplatesIncludeFile_Nodes_Target` 
FOREIGN KEY (`target`) REFERENCES `Nodes`(`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelLinkDescriptions` ENGINE = InnoDB;

ALTER TABLE `RelLinkDescriptions` ADD CONSTRAINT `RelLinkDescriptions_Links` FOREIGN KEY (`IdLink`) REFERENCES `Links`(`IdLink`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelNode2Asset` CHANGE `source` `source` INT(12) UNSIGNED NULL, CHANGE `target` `target` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `RelNode2Asset` ENGINE = InnoDB;

ALTER TABLE `RelNode2Asset` ADD CONSTRAINT `RelNode2Asset_Nodes_Source` FOREIGN KEY (`source`) REFERENCES `Nodes`(`IdNode`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelNode2Asset` ADD CONSTRAINT `RelNode2Asset_Nodes_Target` FOREIGN KEY (`target`) REFERENCES `Nodes`(`IdNode`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelStrdocTemplate` CHANGE `source` `source` INT(12) UNSIGNED NOT NULL, CHANGE `target` `target` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `RelStrdocTemplate` ENGINE = InnoDB;

DELETE FROM `RelStrdocTemplate` WHERE `source` NOT IN (SELECT IdNode FROM Nodes);

DELETE FROM `RelStrdocTemplate` WHERE `target` NOT IN (SELECT IdNode FROM Nodes);

ALTER TABLE `RelStrdocTemplate` ADD CONSTRAINT `RelStrdocTemplate_Nodes_Source` FOREIGN KEY (`source`) REFERENCES `Nodes`(`IdNode`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelStrdocTemplate` ADD CONSTRAINT `RelStrdocTemplate_Nodes_target` FOREIGN KEY (`target`) REFERENCES `Nodes`(`IdNode`) 
ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO `Config` (`IdConfig`, `ConfigKey`, `ConfigValue`, `Description`) VALUES (NULL, 'Owner', 'ximdex', 'Meta tag owner in html');

ALTER TABLE `ServerFrames` DROP FOREIGN KEY `ServerFrames_Channels`;

ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_Channels` FOREIGN KEY (`ChannelId`) REFERENCES `Channels`(`IdChannel`) 
ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `ServerFrames` DROP FOREIGN KEY `ServerFrames_ChannelFrames`;

ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_ChannelFrames` FOREIGN KEY (`IdChannelFrame`) REFERENCES `ChannelFrames`(`IdChannelFrame`) 
ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `RelTemplateContainer` ENGINE = InnoDB;

ALTER TABLE `RelTemplateContainer` ADD UNIQUE (`IdRel`, `IdTemplate`);

ALTER TABLE `RelTemplateContainer` ADD CONSTRAINT `RelTemplateConatiner_Nodes_Container` FOREIGN KEY (`IdContainer`) REFERENCES `Nodes`(`IdNode`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelTemplateContainer` ADD CONSTRAINT `RelTemplateConatiner_Nodes_Template` FOREIGN KEY (`IdTemplate`) REFERENCES `Nodes`(`IdNode`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelXml2Xml` CHANGE `source` `source` INT(12) UNSIGNED NOT NULL, CHANGE `target` `target` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `RelXml2Xml` ENGINE = InnoDB;

ALTER TABLE `RelXml2Xml` ADD CONSTRAINT `RelXml2Xml_Nodes_source` FOREIGN KEY (`source`) REFERENCES `StructuredDocuments`(`IdDoc`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelXml2Xml` ADD CONSTRAINT `RelXml2Xml_Nodes_target` FOREIGN KEY (`target`) REFERENCES `StructuredDocuments`(`IdDoc`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `SectionTypes` CHANGE `sectionType` `sectionType` ENUM('Normal','Xnews') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
CHANGE `idNodeType` `idNodeType` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `SectionTypes` ADD CONSTRAINT `SectionTypes_NodeTypes` FOREIGN KEY (`idNodeType`) REFERENCES `NodeTypes`(`IdNodeType`) 
ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `Section` CHANGE `IdNode` `IdNode` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `Section` ADD CONSTRAINT `Section_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes`(`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelServersChannels` CHANGE `IdServer` `IdServer` INT(12) UNSIGNED NULL, CHANGE `IdChannel` `IdChannel` INT(12) UNSIGNED NULL;
