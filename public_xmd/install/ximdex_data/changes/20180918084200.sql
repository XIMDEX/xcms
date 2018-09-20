ALTER TABLE `Batchs` ADD `ServerFramesPending` INT NOT NULL DEFAULT '0' AFTER `ServerFramesTotal`, 
    ADD `ServerFramesActive` INT NOT NULL DEFAULT '0' AFTER `ServerFramesPending`;
ALTER TABLE `Batchs` CHANGE `ServerFramesTotal` `ServerFramesTotal` INT(12) UNSIGNED NOT NULL DEFAULT '0', 
    CHANGE `ServerFramesSucess` `ServerFramesSucess` INT(12) NOT NULL DEFAULT '0', 
    CHANGE `ServerFramesError` `ServerFramesError` INT(12) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `Batchs` CHANGE `ServerFramesPending` `ServerFramesPending` INT(12) UNSIGNED NOT NULL DEFAULT '0', 
    CHANGE `ServerFramesActive` `ServerFramesActive` INT(12) UNSIGNED NOT NULL DEFAULT '0', 
    CHANGE `ServerFramesSucess` `ServerFramesSuccess` INT(12) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `Batchs` ADD INDEX(`State`);

ALTER TABLE `PortalFrames` ADD `SFtotal` INT(12) UNSIGNED NOT NULL DEFAULT '0' AFTER `StatusTime`;
ALTER TABLE `PortalFrames` CHANGE `SFprocessed` `SFsuccess` INT(12) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `PortalFrames` CHANGE `IdPortal` `IdNodeGenerator` INT(12) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `PortalFrames` ADD UNIQUE (`IdNodeGenerator`, `Version`);
ALTER TABLE `PortalFrames` ADD INDEX(`Status`);
ALTER TABLE `PortalFrames` ADD INDEX(`PublishingType`);
