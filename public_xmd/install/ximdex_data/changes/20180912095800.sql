RENAME TABLE `PortalVersions` TO `PortalFrames`;

ALTER TABLE `Batchs` CHANGE `IdPortalVersion` `IdPortalFrame` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `PublishingReport` CHANGE `IdPortalVersion` `IdPortalFrame` INT(12) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `RelFramesPortal` CHANGE `IdPortalVersion` `IdPortalFrame` INT(12) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `PortalFrames` CHANGE `TimeStamp` `CreationTime` INT(12) UNSIGNED NOT NULL;
ALTER TABLE `PortalFrames` CHANGE `IdPortal` `IdPortal` INT(12) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `PortalFrames` CHANGE `Version` `Version` INT(12) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `PortalFrames` ADD `PublishingType` ENUM('Up','Down') NOT NULL;
ALTER TABLE `PortalFrames` ADD `CreatedBy` INT(12) UNSIGNED NULL DEFAULT NULL; 
ALTER TABLE `PortalFrames` ADD `StartTime` INT(12) NULL DEFAULT NULL; 
ALTER TABLE `PortalFrames` ADD `EndTime` INT(12) NULL DEFAULT NULL;
ALTER TABLE `PortalFrames` ADD `Status` ENUM('Created','Active','Ended') NULL DEFAULT NULL;
ALTER TABLE `PortalFrames` ADD `StatusTime` INT(12) NULL DEFAULT NULL`;
ALTER TABLE `PortalFrames` ADD `SFactive` INT(12) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `PortalFrames` ADD `SFpending` INT(12) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `PortalFrames` ADD `SFprocessed` INT(12) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `PortalFrames` ADD `SFerrored` INT(12) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `PortalFrames` ADD INDEX(`CreatedBy`);

ALTER TABLE `PortalFrames` ADD FOREIGN KEY (`CreatedBy`) REFERENCES `Users`(`IdUser`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `PortalFrames` ADD FOREIGN KEY (`IdPortal`) REFERENCES `Nodes`(`IdNode`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `ServerFrames` ADD `IdPortalFrame` INT(12) UNSIGNED NOT NULL, ADD INDEX (`IdPortalFrame`);
UPDATE `ServerFrames` sf SET sf.IdPortalFrame = (SELECT IdPortalFrame from Batchs ba WHERE ba.IdBatch = sf.IdBatchUp)
WHERE sf.IdBatchDown IS NULL;
UPDATE `ServerFrames` sf SET sf.IdPortalFrame = (SELECT IdPortalFrame from Batchs ba WHERE ba.IdBatch = sf.IdBatchDown)
WHERE NOT sf.IdBatchDown IS NULL;
ALTER TABLE `ServerFrames` ADD FOREIGN KEY (`IdPortalFrame`) REFERENCES `PortalFrames`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
