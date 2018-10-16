ALTER TABLE `Batchs` CHANGE `MajorCycle` `Cycles` INT(12) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `Batchs` DROP `MinorCycle`;
ALTER TABLE `Batchs` CHANGE `Priority` `Priority` FLOAT(3,2) UNSIGNED NOT NULL DEFAULT '0.50';
ALTER TABLE `Batchs` CHANGE `State` `State` ENUM('Waiting', 'InTime', 'Closing', 'Ended', 'NoFrames', 'Stopped') 
    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Waiting';

ALTER TABLE `Pumpers` ADD `VacancyLevel` INT(12) NULL DEFAULT NULL, ADD `Pace` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `Pumpers` ADD `ProcessedTasks` INT(12) UNSIGNED NOT NULL DEFAULT '0';
