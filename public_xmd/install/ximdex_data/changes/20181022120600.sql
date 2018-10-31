ALTER TABLE `PortalFrames` ADD `SFdelayed` INT(12) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `PortalFrames` ADD `SFstopped` INT(12) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `PortalFrames` ADD `ScheduledTime` INT(12) NOT NULL AFTER `CreatedBy`, ADD INDEX (`ScheduledTime`);
ALTER TABLE `PortalFrames` ADD `Playing` BOOLEAN NOT NULL DEFAULT FALSE, ADD INDEX (`Playing`);

UPDATE `Config` SET `ConfigValue` = 'prefix' WHERE `IdConfig` = 52;

ALTER TABLE `Batchs` DROP `Playing`;

DROP TABLE `PublishingReport`;
