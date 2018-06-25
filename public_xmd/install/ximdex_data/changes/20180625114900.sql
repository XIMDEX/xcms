ALTER TABLE `PortalVersions` ENGINE = InnoDB;
ALTER TABLE `PortalVersions` ADD INDEX(`IdPortal`);

ALTER TABLE `Users` ENGINE = InnoDB;

ALTER TABLE `Batchs` CHANGE `State` `State` ENUM('Waiting', 'InTime', 'Closing', 'Ended') 
    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Waiting';
ALTER TABLE `Batchs` ADD FOREIGN KEY (`IdNodeGenerator`) REFERENCES `Nodes`(`IdNode`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `Batchs` ADD FOREIGN KEY (`IdPortalVersion`) REFERENCES `PortalVersions`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `Batchs` ADD FOREIGN KEY (`UserId`) REFERENCES `Users`(`IdUser`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `Pumpers` CHANGE `State` `State` ENUM('New', 'Starting', 'Started', 'Ended') 
    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'New';
ALTER TABLE `Pumpers` ADD INDEX(`State`);
ALTER TABLE `Pumpers` ADD FOREIGN KEY (`IdServer`) REFERENCES `Servers`(`IdServer`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `Nodes` CHANGE `IdParent` `IdParent` INT(12) UNSIGNED NULL DEFAULT NULL;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6300;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6304;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6314;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6317;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6318;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6319;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6320;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6321;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6322;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6323;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6324;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6325;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6326;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6327;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6328;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6329;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6330;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 6331;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 8117;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 8119;
DELETE FROM `Nodes` WHERE `Nodes`.`IdNode` = 8120;
ALTER TABLE `Nodes` ADD FOREIGN KEY (`IdParent`) REFERENCES `Nodes`(`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;