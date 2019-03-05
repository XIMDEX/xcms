ALTER TABLE `NodeFrames` ADD CONSTRAINT `NodeFrames_Nodes` FOREIGN KEY (`NodeId`) 
    REFERENCES `Nodes`(`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `Versions` ENGINE = InnoDB;

ALTER TABLE `NodeFrames` ADD CONSTRAINT `NodeFrames_Versions` FOREIGN KEY (`VersionId`) 
    REFERENCES `Versions`(`IdVersion`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `Versions` ADD CONSTRAINT `Versions_Nodes` FOREIGN KEY (`IdNode`) 
    REFERENCES `Nodes`(`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `Versions` ADD CONSTRAINT `Versions_Users` FOREIGN KEY (`IdUser`) 
    REFERENCES `Users`(`IdUser`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `Versions` ADD CONSTRAINT `Versions_ServerFrames` FOREIGN KEY (`IdSync`) 
    REFERENCES `ServerFrames`(`IdSync`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `ServerFrames` CHANGE `ErrorLevel` `ErrorLevel` VARCHAR(1) NULL DEFAULT NULL;
UPDATE ServerFrames SET `ErrorLevel` = NULL WHERE `ErrorLevel` = 0;
ALTER TABLE `ServerFrames` CHANGE `ErrorLevel` `ErrorLevel` ENUM('1','2') NULL DEFAULT NULL COMMENT 'Errors: 1 Soft, 2 Hard';
ALTER TABLE `ServerFrames` DROP `Error`;

ALTER TABLE `Servers` ADD CONSTRAINT `Servers_Encodes` FOREIGN KEY (`idEncode`) 
    REFERENCES `Encodes`(`IdEncode`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `Servers` ADD `DelayTimeToEnableForPumping` INT(12) UNSIGNED NULL DEFAULT NULL AFTER `ActiveForPumping`;
ALTER TABLE `Servers` CHANGE `ActiveForPumping` `ActiveForPumping` INT(1) UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `Servers` CHANGE `Description` `Description` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `IdServer`;
ALTER TABLE `Servers` ADD `CyclesToRetryPumping` INT(12) UNSIGNED NOT NULL DEFAULT '0' AFTER `DelayTimeToEnableForPumping`;

DROP TABLE `ServerErrorByPumper`;

ALTER TABLE `Batchs` ADD `ServerId` INT(12) UNSIGNED NOT NULL AFTER `UserId`, ADD INDEX (`ServerId`);
ALTER TABLE `Batchs` ADD CONSTRAINT `Batchs_Servers` FOREIGN KEY (`ServerId`) 
    REFERENCES `Servers`(`IdServer`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `ServerFrames` DROP FOREIGN KEY `ServerFrames_PortalFrames`;
ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_PortalFrames` FOREIGN KEY (`IdPortalFrame`) 
    REFERENCES `PortalFrames`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `ServerFrames` DROP FOREIGN KEY `ServerFrames_Batchs_down`;
ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_Batchs_down` FOREIGN KEY (`IdBatchDown`) 
    REFERENCES `Batchs`(`IdBatch`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `Groups` ENGINE = InnoDB;

ALTER TABLE `RelUsersGroups` ENGINE = InnoDB;
ALTER TABLE `RelUsersGroups` ADD CONSTRAINT `RelUsersGroups_Groups` FOREIGN KEY (`IdGroup`) 
    REFERENCES `Groups`(`IdGroup`) ON DELETE CASCADE ON UPDATE CASCADE;
DELETE FROM RelUsersGroups WHERE IdRole NOT IN (SELECT IdRole FROM Roles);
ALTER TABLE `RelUsersGroups` ADD CONSTRAINT `RelUsersGroups_Roles` FOREIGN KEY (`IdRole`) 
    REFERENCES `Roles`(`IdRole`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `RelUsersGroups` ADD CONSTRAINT `RelUsersGroups_User` FOREIGN KEY (`IdUser`) 
    REFERENCES `Users`(`IdUser`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `Batchs` CHANGE `ServerFramesError` `ServerFramesFatalError` INT(12) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `Batchs` ADD `ServerFramesTemporalError` INT(12) UNSIGNED NOT NULL DEFAULT '0' AFTER `ServerFramesFatalError`;
ALTER TABLE `Batchs` CHANGE `Playing` `Playing` INT(12) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `Batchs` CHANGE `Priority` `Priority` FLOAT(3,2) UNSIGNED NULL DEFAULT '0.5';

ALTER TABLE `PortalFrames` CHANGE `SFerrored` `SFfatalError` INT(12) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `PortalFrames` ADD `SFsoftError` INT(12) UNSIGNED NOT NULL DEFAULT '0' AFTER `SFfatalError`;
