RENAME TABLE `MetadataSection` TO `MetadataScheme`;

ALTER TABLE `MetadataScheme` CHANGE `idMetadataSection` `idMetadataScheme` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT;

RENAME TABLE `RelMetadataSectionNodeType` TO `RelMetadataSchemeNodeType`;

ALTER TABLE `RelMetadataSchemeNodeType` CHANGE `idMetadataSection` `idMetadataScheme` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `MetadataGroup` CHANGE `idMetadataSection` `idMetadataScheme` INT(12) UNSIGNED NULL DEFAULT NULL;

UPDATE `MetadataGroup` SET `name` = 'General type metadata' WHERE `MetadataGroup`.`idMetadataGroup` = 1;

UPDATE `MetadataGroup` SET `name` = 'Search engine optimization' WHERE `MetadataGroup`.`idMetadataGroup` = 2;

ALTER TABLE `RelMetadataGroupMetadata` ADD `readonly` BOOLEAN NOT NULL DEFAULT FALSE AFTER `required`;

INSERT INTO `RelMetadataSchemeNodeType` (`idMetadataScheme`, `idNodeType`) VALUES ('1', '5040'), ('1', '5039'), ('1', '5042'), ('1', '5032');

UPDATE `Actions` SET `Name` = 'Edit metadata' WHERE `Actions`.`IdAction` = 9510;

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) 
VALUES (NULL, '5032', 'Edit metadata', 'metadata', 'add_xml.png', 'Manage metadata for XML document', '82', NULL, '0', NULL, '0');

INSERT INTO `RelRolesActions` (IdRol, IdAction, IdState) VALUES ('201', LAST_INSERT_ID(), '7');

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) 
VALUES (NULL, '5039', 'Edit metadata', 'metadata', 'add_xml.png', 'Manage metadata for text file', '82', NULL, '0', NULL, '0');

INSERT INTO `RelRolesActions` (IdRol, IdAction, IdState) VALUES ('201', LAST_INSERT_ID(), '7');

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) 
VALUES (NULL, '5040', 'Edit metadata', 'metadata', 'add_xml.png', 'Manage metadata for image file', '82', NULL, '0', NULL, '0');

INSERT INTO `RelRolesActions` (IdRol, IdAction, IdState) VALUES ('201', LAST_INSERT_ID(), '7');

INSERT INTO `RelRolesActions` (IdRol, IdAction, IdState) VALUES ('201', LAST_INSERT_ID(), '7');

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) 
VALUES (NULL, '5042', 'Edit metadata', 'metadata', 'add_xml.png', 'Manage metadata for video file', '82', NULL, '0', NULL, '0');

INSERT INTO `RelRolesActions` (IdRol, IdAction, IdState) VALUES ('201', LAST_INSERT_ID(), '7');

DELETE FROM `NodeProperties` WHERE `IdNodeProperty` = 2;

ALTER TABLE `NodeProperties` 
CHANGE `Property` `Property` ENUM('Transformer', 'SchemaType', 'DefaultServerLanguage', 'channel', 'language', 'metadata') 
CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `TransitionsCache` ADD `channelId` INT(12) UNSIGNED NULL DEFAULT NULL AFTER `file`;

ALTER TABLE `TransitionsCache` ADD CONSTRAINT `TransitionsCache_Channels` FOREIGN KEY (`channelId`) REFERENCES `Channels`(`IdChannel`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `TransitionsCache` DROP INDEX `IdVersion`, ADD UNIQUE `IdVersion` (`versionId`, `transitionId`, `channelId`) USING BTREE;

INSERT INTO `Config` (`IdConfig`, `ConfigKey`, `ConfigValue`, `Description`) VALUES (NULL, 'LogoText', '', NULL);

INSERT INTO `Nodes` (`IdNode`, `IdParent`, `IdNodeType`, `Name`, `IdState`, `BlockTime`, `BlockUser`, `CreationDate`, `ModificationDate`
, `Description`, `SharedWorkflow`, `Path`, `ActiveNF`, `sortorder`, `deleted`) 
VALUES ('5019', '6', '5007', 'MetadataManager', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/Ximdex/Control center/Type of node manager'
, NULL, '0', '0');

INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`
, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `IsHidden`, `CanDenyDeletion`
, `isGenerator`, `IsEnriching`, `System`, `Module`, `workflowId`) 
VALUES ('5019', 'MetadataManager', 'root', 'metadata', 'Metadata manager', '0', '0', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0'
, '0', '1', NULL, NULL);

INSERT INTO `Nodes` (`IdNode`, `IdParent`, `IdNodeType`, `Name`, `IdState`, `BlockTime`, `BlockUser`, `CreationDate`, `ModificationDate`
, `Description`, `SharedWorkflow`, `Path`, `ActiveNF`, `sortorder`, `deleted`) 
VALUES (NULL, '2', '5019', 'Metadata manager', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/Ximdex/Control center', NULL, '0', '0');

ALTER TABLE `IsoCodes` ADD `NativeName` VARCHAR(50) NOT NULL AFTER `Name`;

UPDATE `IsoCodes` SET `NativeName` = 'Français' WHERE `IsoCodes`.`IdIsoCode` = 1;
UPDATE `IsoCodes` SET `NativeName` = 'Italiano' WHERE `IsoCodes`.`IdIsoCode` = 2;
UPDATE `IsoCodes` SET `NativeName` = 'Español' WHERE `IsoCodes`.`IdIsoCode` = 3;
UPDATE `IsoCodes` SET `NativeName` = 'Deutsch' WHERE `IsoCodes`.`IdIsoCode` = 4;
UPDATE `IsoCodes` SET `NativeName` = 'English' WHERE `IsoCodes`.`IdIsoCode` = 5;
UPDATE `IsoCodes` SET `NativeName` = 'Português' WHERE `IsoCodes`.`IdIsoCode` = 6;
UPDATE `IsoCodes` SET `NativeName` = 'Galego' WHERE `IsoCodes`.`IdIsoCode` = 7;
UPDATE `IsoCodes` SET `NativeName` = 'Català' WHERE `IsoCodes`.`IdIsoCode` = 8;
UPDATE `IsoCodes` SET `Name` = 'Basque', `NativeName` = 'Euskara' WHERE `IsoCodes`.`IdIsoCode` = 9;
UPDATE `IsoCodes` SET `NativeName` = 'Valencià' WHERE `IsoCodes`.`IdIsoCode` = 10;

ALTER TABLE `IsoCodes` ADD UNIQUE(`NativeName`);

INSERT INTO `ProgrammingCommand` (`id`, `description`) VALUES ('pathToCurrent', 'Path to current page');

ALTER TABLE `ProgrammingCode` CHANGE `code` `code` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

INSERT INTO `ProgrammingCode` (`id`, `idLanguage`, `idCommand`, `code`) VALUES (NULL, 'html5', 'pathToCurrent', '@@@RMximdex.pathto(THIS,,%s)@@@');

INSERT INTO `ProgrammingCode` (`id`, `idLanguage`, `idCommand`, `code`) 
VALUES (NULL, 'php', 'pathToCurrent', '<?php\n$url = \'@@@RMximdex.pathto(THIS,,%s)@@@\';\n\necho str_replace(basename($url), \'\', $url) . xim_path;\n?>');

ALTER TABLE `XimIOExportations` CHANGE `idXimIO` `idXimIO` VARCHAR(50) NOT NULL;

INSERT INTO `ProgrammingCommand` (`id`, `description`) VALUES ('const', 'Constant definition');

INSERT INTO `ProgrammingCode` (`id`, `idLanguage`, `idCommand`, `code`) 
VALUES (NULL, 'php', 'const', '<?php\n$var = \'%s\';\nif (! defined($var)) {\n    define($var, \'%s\');\n?>');

INSERT INTO `ProgrammingCode` (`id`, `idLanguage`, `idCommand`, `code`) VALUES (NULL, 'xif', 'pathToCurrent', '@@@RMximdex.pathto(THIS,,%s)@@@');

UPDATE `NodeTypes` SET `IsHidden` = '1' WHERE `NodeTypes`.`IdNodeType` = 5019;
