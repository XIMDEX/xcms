INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) 
VALUES (NULL, '5019', 'Associate node types to schemes', 'modifynodetypeschemes', 'add_metadata.png', 'Associate node types to metadata sections'
, '20', NULL, '0', '', '0');

INSERT INTO `RelRolesActions` (IdRol, IdAction, IdState) VALUES ('201', LAST_INSERT_ID(), NULL);

ALTER TABLE `NodeTypes` ADD `HasMetadata` BOOLEAN NOT NULL DEFAULT FALSE AFTER `workflowId`;

UPDATE `NodeTypes` SET `HasMetadata` = '1' WHERE `NodeTypes`.`IdNodeType` = 5032;

UPDATE `NodeTypes` SET `HasMetadata` = '1' WHERE `NodeTypes`.`IdNodeType` = 5041;

UPDATE `NodeTypes` SET `HasMetadata` = '1' WHERE `NodeTypes`.`IdNodeType` = 5104;
