ALTER TABLE PipeTransitions DROP FOREIGN KEY PipeTransitions_PipeProcess;

ALTER TABLE PipeTransitions DROP FOREIGN KEY PipeTransitions_PipeStatus_From;

ALTER TABLE PipeTransitions DROP FOREIGN KEY PipeTransitions_PipeStatus_To;

ALTER TABLE PipeProcess DROP FOREIGN KEY PipeProcess_PipeTransitions_From;

ALTER TABLE PipeProcess DROP FOREIGN KEY PipeProcess_Pipelines;

DROP TABLE `PipeProcess`;

DROP TABLE `PipeStatus`;

DROP TABLE `PipeProperties`;

DROP TABLE `PipeTransitions`;

DROP TABLE `Pipelines`;

UPDATE `Nodes` SET BlockTime = NULL WHERE BlockTime = 0;

UPDATE `Nodes` SET CreationDate = NULL WHERE CreationDate = 0;

UPDATE `Nodes` SET ModificationDate = NULL WHERE ModificationDate = 0;

ALTER TABLE `Users` 
CHANGE `Login` `Login` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
CHANGE `Pass` `Pass` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
CHANGE `Name` `Name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
CHANGE `LastLogin` `LastLogin` INT(14) UNSIGNED NULL DEFAULT NULL, 
CHANGE `NumAccess` `NumAccess` INT(12) UNSIGNED NULL DEFAULT '0';

ALTER TABLE `Users` ADD UNIQUE(`Name`);

ALTER TABLE `Users` ADD CONSTRAINT `Users_Nodes` FOREIGN KEY (`IdUser`) REFERENCES `Nodes`(`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO `Nodes` (`IdNode`, `IdParent`, `IdNodeType`, `Name`, `IdState`, `BlockTime`, `BlockUser`, `CreationDate`, `ModificationDate`
, `Description`, `SharedWorkflow`, `Path`) 
VALUES ('404', '8', '5079', 'Workflow for common files', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/Ximdex/Control center/Workflow manager');

ALTER TABLE `Workflow` ADD  CONSTRAINT `Workflow_Nodes` FOREIGN KEY (`id`) REFERENCES `Nodes`(`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

UPDATE `Nodes` SET `Name` = 'Workflow for structured documents' WHERE `Nodes`.`IdNode` = 403;

INSERT INTO `Nodes` (`IdNode`, `IdParent`, `IdNodeType`, `Name`, `IdState`, `BlockTime`, `BlockUser`, `CreationDate`, `ModificationDate`
, `Description`, `SharedWorkflow`, `Path`) VALUES 
('1004', '10', '5038', 'Delete_frames', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/Ximdex/Control center/Permit manager'),
('1005', '10', '5038', 'view_publication_resume', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/Ximdex/Control center/Permit manager'),
('1006', '10', '5038', 'Expert_mode_allowed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/Ximdex/Control center/Permit manager'),
('1007', '10', '5038', 'Ximdex_close', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/Ximdex/Control center/Permit manager'),
('1008', '10', '5038', 'Structural_publication', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/Ximdex/Control center/Permit manager'),
('1009', '10', '5038', 'Advanced_publication', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/Ximdex/Control center/Permit manager'),
('1010', '10', '5038', 'Ximedit_publication_allowed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/Ximdex/Control center/Permit manager');

ALTER TABLE `Permissions` ADD CONSTRAINT `Permissions_Nodes` FOREIGN KEY (`IdPermission`) REFERENCES `Nodes`(`IdNode`) 
ON DELETE CASCADE ON UPDATE CASCADE;

UPDATE `NodeTypes` SET IsRenderizable = 0 WHERE IsRenderizable IS NULL;
UPDATE `NodeTypes` SET HasFSEntity = 0 WHERE HasFSEntity IS NULL;
UPDATE `NodeTypes` SET CanAttachGroups = 0 WHERE CanAttachGroups IS NULL;
UPDATE `NodeTypes` SET IsSection = 0 WHERE IsSection IS NULL;
UPDATE `NodeTypes` SET IsFolder = 0 WHERE IsFolder IS NULL;
UPDATE `NodeTypes` SET IsVirtualFolder = 0 WHERE IsVirtualFolder IS NULL;
UPDATE `NodeTypes` SET IsPlainFile = 0 WHERE IsPlainFile IS NULL;
UPDATE `NodeTypes` SET IsStructuredDocument = 0 WHERE IsStructuredDocument IS NULL;
UPDATE `NodeTypes` SET IsPublishable = 0 WHERE IsPublishable IS NULL;
UPDATE `NodeTypes` SET CanDenyDeletion = 0 WHERE CanDenyDeletion IS NULL;
UPDATE `NodeTypes` SET System = 0 WHERE System IS NULL;

ALTER TABLE `NodeTypes` CHANGE `IsRenderizable` `IsRenderizable` BOOLEAN NOT NULL DEFAULT FALSE, 
CHANGE `HasFSEntity` `HasFSEntity` BOOLEAN NOT NULL DEFAULT FALSE, 
CHANGE `CanAttachGroups` `CanAttachGroups` BOOLEAN NOT NULL DEFAULT FALSE, 
CHANGE `IsSection` `IsSection` BOOLEAN NOT NULL DEFAULT FALSE, 
CHANGE `IsFolder` `IsFolder` BOOLEAN NOT NULL DEFAULT FALSE, 
CHANGE `IsVirtualFolder` `IsVirtualFolder` BOOLEAN NOT NULL DEFAULT FALSE, 
CHANGE `IsPlainFile` `IsPlainFile` BOOLEAN NOT NULL DEFAULT FALSE, 
CHANGE `IsStructuredDocument` `IsStructuredDocument` BOOLEAN NOT NULL DEFAULT FALSE, 
CHANGE `IsPublishable` `IsPublishable` BOOLEAN NOT NULL DEFAULT FALSE, 
CHANGE `CanDenyDeletion` `CanDenyDeletion` BOOLEAN NOT NULL DEFAULT FALSE, 
CHANGE `System` `System` BOOLEAN NOT NULL DEFAULT FALSE;

ALTER TABLE `NodeTypes` CHANGE `Name` `Name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

INSERT INTO `Nodes` (`IdNode`, `IdParent`, `IdNodeType`, `Name`, `IdState`, `BlockTime`, `BlockUser`, `CreationDate`, `ModificationDate`
, `Description`, `SharedWorkflow`, `Path`) VALUES 
('5044', NULL, '5007', 'ConfigFolder', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5061', NULL, '5007', 'SystemProperty', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5077', NULL, '5007', 'XslTemplate', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5078', NULL, '5007', 'RngVisualTemplate', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5079', NULL, '5007', 'Workflow', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5080', NULL, '5007', 'ModulesFolder', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5081', NULL, '5007', 'ModuleInfoContainer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5082', NULL, '5007', 'AllowedExtensions', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5090', NULL, '5007', 'JsRootFolder', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5091', NULL, '5007', 'JsFolder', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO `Nodes` (`IdNode`, `IdParent`, `IdNodeType`, `Name`, `IdState`, `BlockTime`, `BlockUser`, `CreationDate`, `ModificationDate`
, `Description`, `SharedWorkflow`, `Path`) VALUES 
('5092', NULL, '5007', 'JsFile', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5100', NULL, '5007', 'HTMLLayout', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5101', NULL, '5007', 'HTMLComponentsFolder', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5103', NULL, '5007', 'HTMLContainer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5104', NULL, '5007', 'HtmlDocument', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5105', NULL, '5007', 'HTMLlayoutsFolder', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5106', NULL, '5007', 'HTMLViewsFolder', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5107', NULL, '5007', 'HTMLComponent', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5108', NULL, '5007', 'HTMLView', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('5110', NULL, '5007', 'XOTFFolder', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

ALTER TABLE `NodeTypes` ADD CONSTRAINT `NodeTypes_Nodes` FOREIGN KEY (`IdNodeType`) REFERENCES `Nodes`(`IdNode`) 
ON DELETE CASCADE ON UPDATE CASCADE;

DROP TABLE `RelBulletinXimlet`;

ALTER TABLE `RelGroupsNodes` CHANGE `IdGroup` `IdGroup` INT(12) UNSIGNED NOT NULL, CHANGE `IdNode` `IdNode` INT(12) UNSIGNED NOT NULL;

UPDATE `RelGroupsNodes` set IdRole = NULL WHERE IdRole = 0;

ALTER TABLE `RelGroupsNodes` DROP INDEX `uniq`, ADD UNIQUE `uniq` (`IdNode`, `IdGroup`, `IdRole`) USING BTREE;

ALTER TABLE `RelUsersGroups` CHANGE `IdUser` `IdUser` INT(12) UNSIGNED NOT NULL, CHANGE `IdGroup` `IdGroup` INT(12) UNSIGNED NOT NULL, 
CHANGE `IdRole` `IdRole` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `RelGroupsNodes` ENGINE = InnoDB;

ALTER TABLE `RelGroupsNodes` ADD CONSTRAINT `RelGroupsNodes_Group` FOREIGN KEY (`IdGroup`) REFERENCES `Groups`(`IdGroup`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelGroupsNodes` ADD CONSTRAINT `RelGroupsNodes_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes`(`IdNode`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelGroupsNodes` ADD CONSTRAINT `RelGroupsNodes_Roles` FOREIGN KEY (`IdRole`) REFERENCES `Roles`(`IdRole`) 
ON DELETE CASCADE ON UPDATE CASCADE;

DROP TABLE `PipeNodeTypes`;

DROP TABLE `PipePropertyValues`;

UPDATE `Workflow` SET `master` = '1' WHERE `Workflow`.`id` = 403;
