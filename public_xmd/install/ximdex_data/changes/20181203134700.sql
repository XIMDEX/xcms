ALTER TABLE NodesToPublish ENGINE = InnoDB;
ALTER TABLE `NodesToPublish` ADD  CONSTRAINT `NodesToPublish_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes`(`IdNode`) 
    ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `NodesToPublish` CHANGE `IdNodeGenerator` `IdNodeGenerator` INT(12) UNSIGNED NOT NULL;
ALTER TABLE `NodesToPublish` ADD CONSTRAINT `NodesToPublish_Nodes_Generator` FOREIGN KEY (`IdNodeGenerator`) REFERENCES `Nodes`(`IdNode`) 
    ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `NodesToPublish` ADD CONSTRAINT `NodesToPublish_Users` FOREIGN KEY (`UserId`) REFERENCES `Users`(`IdUser`) 
    ON DELETE SET NULL ON UPDATE RESTRICT;

ALTER TABLE `Versions` CHANGE `IdNode` `IdNode` INT(12) UNSIGNED NOT NULL, CHANGE `IdUser` `IdUser` INT(12) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `Versions` CHANGE `Comment` `Comment` TEXT NULL DEFAULT NULL;

UPDATE `Actions` SET `IdNodeType` = '5104' WHERE `Actions`.`IdAction` = 7455;
UPDATE `Actions` SET `Command` = 'metadata' WHERE `Actions`.`IdAction` = 7455;
UPDATE `Actions` SET `Icon` = 'add_xml.png' WHERE `Actions`.`IdAction` = 7455;
UPDATE `Actions` SET `Description` = 'Manage metadata for HTML document' WHERE `Actions`.`IdAction` = 7455;
UPDATE `Actions` SET `Sort` = '82' WHERE `Actions`.`IdAction` = 7455;
UPDATE `Actions` SET `Sort` = '82' WHERE `Actions`.`IdAction` = 9510;

UPDATE `RelRolesActions` SET `IdState` = '7' WHERE `RelRolesActions`.`IdRel` = 8089;
UPDATE `RelRolesActions` SET `IdState` = '7' WHERE `RelRolesActions`.`IdRel` = 9118;

ALTER TABLE `Versions` CHANGE `File` `File` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
