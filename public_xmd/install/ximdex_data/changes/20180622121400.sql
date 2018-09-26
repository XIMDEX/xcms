ALTER TABLE `ServerFrames` CHANGE `State` `State` 
    ENUM('Pending', 'Due2In', 'Due2In_', 'Due2Out', 'Due2Out_', 'Pumped', 'Out', 'Closing', 'In', 'Replaced', 'Removed', 'Canceled'
    , 'Due2InWithError', 'Due2OutWithError', 'Outdated') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Pending';

ALTER TABLE `NodeFrames` ENGINE = InnoDB;
ALTER TABLE `Pumpers` ENGINE = InnoDB;

ALTER TABLE `ServerFrames` CHANGE `IdServer` `IdServer` INT(12) UNSIGNED NOT NULL;
ALTER TABLE `ServerFrames` CHANGE `PumperId` `PumperId` INT(12) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `ServerFrames` CHANGE `DateUp` `DateUp` INT(14) UNSIGNED NOT NULL;
ALTER TABLE `ServerFrames` CHANGE `DateDown` `DateDown` INT(14) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `ServerFrames` CHANGE `IdNodeFrame` `IdNodeFrame` INT(12) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `ServerFrames` CHANGE `IdBatchUp` `IdBatchUp` INT(12) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_NodeFrames` FOREIGN KEY (`IdNodeFrame`) 
    REFERENCES `NodeFrames`(`IdNodeFrame`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_Pumpers` FOREIGN KEY (`IdServer`) 
    REFERENCES `Servers`(`IdServer`) ON DELETE CASCADE ON UPDATE CASCADE;
