ALTER TABLE `Batchs` DROP FOREIGN KEY `Batchs_PortalVersions`;

ALTER TABLE `Batchs` ADD CONSTRAINT `Batchs_PortalFrames` FOREIGN KEY (`IdPortalFrame`) REFERENCES `PortalFrames`(`id`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `Batchs` DROP FOREIGN KEY `Batchs_Servers`;

ALTER TABLE `Batchs` ADD CONSTRAINT `Batchs_Servers` FOREIGN KEY (`ServerId`) REFERENCES `Servers`(`IdServer`) 
ON DELETE CASCADE ON UPDATE CASCADE;

UPDATE `Actions` SET `Name` = 'Check link' WHERE `Actions`.`IdAction` = 7230;

ALTER TABLE `Languages` DROP INDEX `IdLanguage`;

ALTER TABLE `Languages` DROP INDEX `IdLanguage_2`;

ALTER TABLE `IsoCodes` CHANGE `Iso2` `Iso2` CHAR(2) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `Languages` CHANGE `IsoName` `IsoName` CHAR(2) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `IsoCodes` ENGINE = InnoDB;

ALTER TABLE `Languages` ADD CONSTRAINT `Languages_IsoCodes` FOREIGN KEY (`IsoName`) REFERENCES `IsoCodes`(`Iso2`) 
ON DELETE RESTRICT ON UPDATE CASCADE;

UPDATE Users SET NumAccess = 0 WHERE NumAccess IS NULL;

ALTER TABLE `Users` CHANGE `NumAccess` `NumAccess` INT(12) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `Config` ADD `Description` VARCHAR(255) NULL DEFAULT NULL AFTER `ConfigValue`;

ALTER TABLE `Config` CHANGE `ConfigKey` `ConfigKey` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

INSERT INTO `Config` (`IdConfig`, `ConfigKey`, `ConfigValue`, `Description`) 
VALUES (NULL, 'RenderizeAll', '0', 'Renderize all nodes in the data-nodes folder, not only the XLS template files.');

ALTER TABLE `NodeAllowedContents` ENGINE = InnoDB;

ALTER TABLE `NodeAllowedContents` CHANGE `IdNodeType` `IdNodeType` INT(12) UNSIGNED NOT NULL, 
CHANGE `NodeType` `NodeType` INT(12) UNSIGNED NOT NULL;

delete from NodeAllowedContents where IdNodeType in (
        select distinct nac.IdNodeType from NodeAllowedContents nac
        left join NodeTypes nt on nac.IdNodeType = nt.IdNodeType
        where nt.IdNodeType is null
);

ALTER TABLE `NodeAllowedContents` ADD CONSTRAINT `NodeAllowedContents_Nodetype_Container` FOREIGN KEY (`IdNodeType`) 
REFERENCES `NodeTypes`(`IdNodeType`) ON DELETE CASCADE ON UPDATE CASCADE;

delete from NodeAllowedContents where NodeType in (
        select distinct nac.NodeType from NodeAllowedContents nac
        left join NodeTypes nt on nac.NodeType = nt.IdNodeType
        where nt.IdNodeType is null
);

ALTER TABLE `NodeAllowedContents` ADD CONSTRAINT `NodeAllowedContents_Nodetype_Content` FOREIGN KEY (`NodeType`) 
REFERENCES `NodeTypes`(`IdNodeType`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelNodeTypeMimeType` ENGINE = InnoDB;

ALTER TABLE `RelNodeTypeMimeType` CHANGE `idNodeType` `idNodeType` INT(12) UNSIGNED NOT NULL; 

delete from RelNodeTypeMimeType where idNodeType in (
        select distinct rel.idNodeType from RelNodeTypeMimeType rel
        left join NodeTypes nt on rel.idNodeType = nt.IdNodeType
        where nt.IdNodeType is null
);

ALTER TABLE `RelNodeTypeMimeType` ADD CONSTRAINT `RelNodeTypeMimeType_NodeTypes` FOREIGN KEY (`idNodeType`) 
REFERENCES `NodeTypes`(`IdNodeType`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `RelNodeTypeMimeType` CHANGE `extension` `extension` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;

DELETE FROM `RelNodeTypeMimeType` WHERE extension = '';

UPDATE `RelNodeTypeMimeType` SET filter = NULL WHERE filter = '';

ALTER TABLE `RelNodeTypeMimeType` ADD UNIQUE(`idNodeType`);
