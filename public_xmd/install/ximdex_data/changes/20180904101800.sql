-- Metadata document: 5085

DELETE FROM `Actions` WHERE `Actions`.`IdAction` = 6209;
DELETE FROM `Actions` WHERE `Actions`.`IdAction` = 6210;

DELETE FROM `NodeAllowedContents` WHERE `NodeAllowedContents`.`IdNodeAllowedContent` = 30;

DELETE FROM `Nodes` WHERE `Nodes`.`IdNodeType` = 5085;

DELETE FROM `RelNodeTypeMimeType` WHERE `RelNodeTypeMimeType`.`idRelNodeTypeMimeType` = 91;

DELETE FROM `NodeTypes` WHERE `NodeTypes`.`IdNodeType` = 5085;


-- Metadata container: 5084

DELETE FROM `Actions` WHERE `Actions`.`IdAction` = 6207;
DELETE FROM `Actions` WHERE `Actions`.`IdAction` = 6208;

DELETE FROM `RelNodeTypeMimeType` WHERE `RelNodeTypeMimeType`.`idRelNodeTypeMimeType` = 90;

DELETE FROM `Nodes` WHERE `Nodes`.`IdNodeType` = 5084;

DELETE FROM `NodeTypes` WHERE `NodeTypes`.`IdNodeType` = 5084;


-- Metadata section: 5083

DELETE FROM `NodeDefaultContents` WHERE `NodeDefaultContents`.`IdNodeDefaultContent` = 20;

DELETE FROM `Actions` WHERE `Actions`.`IdAction` = 6206;

DELETE FROM `NodeAllowedContents` WHERE `NodeAllowedContents`.`IdNodeAllowedContent` = 1;
DELETE FROM `NodeAllowedContents` WHERE `NodeAllowedContents`.`IdNodeAllowedContent` = 31;
DELETE FROM `NodeAllowedContents` WHERE `NodeAllowedContents`.`IdNodeAllowedContent` = 32;

DELETE FROM `Nodes` WHERE `Nodes`.`IdNodeType` = 5083;

DELETE FROM `RelNodeTypeMimeType` WHERE `RelNodeTypeMimeType`.`idRelNodeTypeMimeType` = 89;

DELETE FROM `NodeTypes` WHERE `NodeTypes`.`IdNodeType` = 5083;


-- Drop tables

DROP TABLE `RelNodeTypeMetadata`;
DROP TABLE `RelNodeMetadata`;
DROP TABLE `RelNodeVersionMetadataVersion`;
