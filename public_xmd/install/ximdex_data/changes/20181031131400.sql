ALTER TABLE `PortalFrames` 
    ADD `Priority` FLOAT NOT NULL DEFAULT '1', 
    ADD `Boost` ENUM('1','2','4') NOT NULL DEFAULT '1', 
    ADD INDEX (`Priority`);
ALTER TABLE `PortalFrames` ADD `Cycles` FLOAT NOT NULL DEFAULT '0', ADD INDEX (`Cycles`);
ALTER TABLE `PortalFrames` CHANGE `Priority` `SuccessRate` FLOAT NOT NULL DEFAULT '1';
ALTER TABLE `PortalFrames` ADD `Visits` INT(12) NOT NULL DEFAULT '0';

INSERT INTO `Config` (`IdConfig`, `ConfigKey`, `ConfigValue`) VALUES (NULL, 'SchedulerPriority', 'batchs');
