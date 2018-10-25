ALTER TABLE `PortalFrames` ADD `SFdelayed` INT(12) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `PortalFrames` ADD `SFstopped` INT(12) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `PortalFrames` ADD `ScheduledTime` INT(12) NOT NULL AFTER `CreatedBy`, ADD INDEX (`ScheduledTime`);

UPDATE `Config` SET `ConfigValue` = 'prefix' WHERE `IdConfig` = 52;
