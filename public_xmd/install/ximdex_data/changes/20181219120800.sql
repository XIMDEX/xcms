CREATE TABLE `Workflow` (
    `id` int(4) UNSIGNED NOT NULL,
    `name` varchar(50) NOT NULL,
    `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `Workflow` (`id`, `name`, `description`) VALUES (404, 'Common', 'Common documents'), (403, 'Structured', 'Structured documents');

ALTER TABLE `Workflow` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

CREATE TABLE `WorkflowStatus` (
  `id` int(12) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL COMMENT 'Class and method names to call separated by @',
  `sort` int(4) UNSIGNED NOT NULL,
  `workflowId` int(4) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `WorkflowStatus` (`id`, `name`, `description`, `action`, `sort`, `workflowId`) VALUES
(7, 'Edition', 'The document is in the development phase', NULL, 0, NULL),
(8, 'Publication', 'The document is waiting to be published', NULL, 100, NULL),
(10, 'Translation', 'Send the document to translate system', 'Translator@sendTranslation', 1, 403),
(11, 'Review translation', 'State defined to check if translations are right', NULL, 2, 403);

ALTER TABLE `WorkflowStatus` ADD PRIMARY KEY (`id`), ADD KEY `WorkflowStatus_Workflow` (`workflowId`);

ALTER TABLE `WorkflowStatus` MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `WorkflowStatus` ADD CONSTRAINT `WorkflowStatus_Workflow` FOREIGN KEY (`workflowId`) REFERENCES `Workflow` (`id`) ON UPDATE CASCADE;

UPDATE `RelRolesActions` SET IdState = NULL WHERE IdState = 0;

DROP TABLE `States`;

DELETE FROM `RelRolesActions` WHERE IdState = 14;

ALTER TABLE `RelRolesActions` ADD CONSTRAINT `RelRolesActions_Status` FOREIGN KEY (`IdState`) REFERENCES `WorkflowStatus`(`id`) 
ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `Nodes` DROP FOREIGN KEY `Nodes_PipeStatus`;

ALTER TABLE `Nodes` ADD CONSTRAINT `Nodes_WorkflowStatus` FOREIGN KEY (`IdState`) REFERENCES `WorkflowStatus`(`id`) 
ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `WorkflowStatus` CHANGE `sort` `sort` INT(4) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `NodeTypes` ADD `workflowId` INT(4) UNSIGNED NULL DEFAULT NULL AFTER `Module`;

ALTER TABLE `NodeTypes` ADD CONSTRAINT `NodeTypes_Workflow` FOREIGN KEY (`workflowId`) REFERENCES `Workflow`(`id`) 
ON DELETE RESTRICT ON UPDATE CASCADE;

-- Workflow for structured documents
UPDATE `NodeTypes` SET `workflowId` = '403' WHERE `NodeTypes`.`IdNodeType` = 5032;
UPDATE `NodeTypes` SET `workflowId` = '403' WHERE `NodeTypes`.`IdNodeType` = 5057;
UPDATE `NodeTypes` SET `workflowId` = '403' WHERE `NodeTypes`.`IdNodeType` = 5104;

ALTER TABLE `RelRolesActions` DROP `IdContext`;

DROP TABLE `Contexts`;

ALTER TABLE `Workflow` ADD `master` BOOLEAN NOT NULL DEFAULT FALSE AFTER `description`;

UPDATE `Workflow` SET `master` = '1' WHERE `Workflow`.`id` = 2;

ALTER TABLE `RelRolesStates` ENGINE = InnoDB;

DELETE FROM `RelRolesStates` WHERE `RelRolesStates`.`IdRel` = 9;

ALTER TABLE `RelRolesStates` ADD CONSTRAINT `RelRolesStates_Roles` FOREIGN KEY (`IdRole`) REFERENCES `Roles`(`IdRole`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelRolesStates` ADD CONSTRAINT `RelRolesStates_WorkflowStatus` FOREIGN KEY (`IdState`) REFERENCES `WorkflowStatus`(`id`) 
ON DELETE CASCADE ON UPDATE CASCADE;

-- Workflow for common documents
UPDATE `NodeTypes` SET `workflowId` = '404' WHERE `NodeTypes`.`IdNodeType` = 5028;
UPDATE `NodeTypes` SET `workflowId` = '404' WHERE `NodeTypes`.`IdNodeType` = 5039;
UPDATE `NodeTypes` SET `workflowId` = '404' WHERE `NodeTypes`.`IdNodeType` = 5040;
UPDATE `NodeTypes` SET `workflowId` = '404' WHERE `NodeTypes`.`IdNodeType` = 5041;
UPDATE `NodeTypes` SET `workflowId` = '404' WHERE `NodeTypes`.`IdNodeType` = 5042;
UPDATE `NodeTypes` SET `workflowId` = '404' WHERE `NodeTypes`.`IdNodeType` = 5092;

ALTER TABLE `Roles` CHANGE `Name` `Name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `Roles` ADD CONSTRAINT `Roles_Nodes` FOREIGN KEY (`IdRole`) REFERENCES `Nodes`(`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `Permissions` ENGINE = InnoDB;

ALTER TABLE `Permissions` CHANGE `Name` `Name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `RelRolesPermissions` ENGINE = InnoDB;

ALTER TABLE `RelRolesPermissions` CHANGE `IdRel` `IdRel` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT, 
CHANGE `IdRole` `IdRole` INT(12) UNSIGNED NOT NULL, CHANGE `IdPermission` `IdPermission` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `RelRolesPermissions` ADD CONSTRAINT `RelRolesPermissions_Roles` FOREIGN KEY (`IdRole`) REFERENCES `Roles`(`IdRole`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelRolesPermissions` ADD CONSTRAINT `RelRolesPermissions_Permissions` FOREIGN KEY (`IdPermission`) 
REFERENCES `Permissions`(`IdPermission`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelRolesPermissions` ADD UNIQUE (`IdRole`, `IdPermission`);

ALTER TABLE `RelRolesActions` CHANGE `IdRol` `IdRol` INT(12) UNSIGNED NOT NULL, CHANGE `IdAction` `IdAction` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `RelRolesActions` DROP `IdPipeline`;

DELETE FROM `RelRolesActions` WHERE `RelRolesActions`.`IdRel` = 8203;

DELETE FROM `RelRolesActions` WHERE `RelRolesActions`.`IdRel` = 1351;
DELETE FROM `RelRolesActions` WHERE `RelRolesActions`.`IdRel` = 1352;

ALTER TABLE `RelRolesActions` ADD UNIQUE (`IdRol`, `IdAction`, `IdState`);

UPDATE Nodes node SET node.IdState = NULL WHERE (SELECT nodetype.workflowId FROM NodeTypes nodetype 
WHERE node.IdNodeType = nodetype.IdNodeType) IS NULL;

ALTER TABLE RelUsersGroups DROP FOREIGN KEY RelUsersGroups_Groups;

ALTER TABLE `Groups` CHANGE `IdGroup` `IdGroup` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `RelUsersGroups` ADD CONSTRAINT `RelUsersGroups_Groups` FOREIGN KEY (`IdGroup`) 
REFERENCES `Groups`(`IdGroup`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `Groups` ADD CONSTRAINT `Groups_Nodes` FOREIGN KEY (`IdGroup`) REFERENCES `Nodes`(`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `Groups` CHANGE `Name` `Name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

UPDATE `NodeTypes` SET `workflowId` = NULL WHERE `NodeTypes`.`IdNodeType` = 5079;

ALTER TABLE `StructuredDocuments` CHANGE `IdDoc` `IdDoc` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `StructuredDocuments` ADD CONSTRAINT `StructuredDocuments_Nodes` FOREIGN KEY (`IdDoc`) REFERENCES `Nodes`(`IdNode`) 
ON DELETE CASCADE ON UPDATE CASCADE;

UPDATE `Actions` SET `Sort` = '-60' WHERE `Actions`.`IdAction` = 6004;
