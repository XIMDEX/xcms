UPDATE StructuredDocuments SET IdCreator = NULL WHERE IdCreator = 0;
UPDATE StructuredDocuments SET IdLanguage = NULL WHERE IdLanguage = 0;
ALTER TABLE `StructuredDocuments` CHANGE `IdCreator` `IdCreator` INT(12) UNSIGNED NULL DEFAULT NULL, 
    CHANGE `IdLanguage` `IdLanguage` INT(12) NULL DEFAULT NULL;
UPDATE StructuredDocuments SET IdTemplate = NULL WHERE IdTemplate = 0;
ALTER TABLE `StructuredDocuments` CHANGE `IdTemplate` `IdTemplate` INT(12) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE StructuredDocuments ENGINE = InnoDB;
ALTER TABLE `StructuredDocuments` ADD CONSTRAINT `StructuredDocuments_Users` FOREIGN KEY (`IdCreator`) 
    REFERENCES `Users`(`IdUser`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE Languages ENGINE = InnoDB;
ALTER TABLE `StructuredDocuments` CHANGE `IdLanguage` `IdLanguage` INT(12) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `Languages` CHANGE `Enabled` `Enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `StructuredDocuments` ADD CONSTRAINT `StructuredDocuments_Languages` FOREIGN KEY (`IdLanguage`) 
    REFERENCES `Languages`(`IdLanguage`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `StructuredDocuments` ADD CONSTRAINT `StructuredDocuments_Templates` FOREIGN KEY (`IdTemplate`) 
    REFERENCES `Nodes`(`IdNode`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `StructuredDocuments` ADD CONSTRAINT `StructuredDocuments_StructuredDocuments` FOREIGN KEY (`TargetLink`) 
    REFERENCES `StructuredDocuments`(`IdDoc`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `PipeStatus` ENGINE = InnoDB;
ALTER TABLE `PipeStatus` ADD UNIQUE(`Name`);
ALTER TABLE `PipeStatus` CHANGE `Description` `Description` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
UPDATE `PipeStatus` set `Description` = NULL WHERE `Description` = '';
ALTER TABLE `PipeStatus` CHANGE `id` `id` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `Nodes` CHANGE `IdNodeType` `IdNodeType` INT(12) UNSIGNED NOT NULL;
ALTER TABLE `Nodes` CHANGE `Name` `Name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `Nodes` CHANGE `IdState` `IdState` INT(12) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `Nodes` CHANGE `BlockTime` `BlockTime` INT(12) NULL DEFAULT NULL;
ALTER TABLE `Nodes` CHANGE `CreationDate` `CreationDate` INT(12) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `Nodes` CHANGE `ModificationDate` `ModificationDate` INT(12) UNSIGNED NULL DEFAULT NULL;
UPDATE `Nodes` SET IdState = NULL WHERE IdState = 0;
ALTER TABLE `Nodes` ADD CONSTRAINT `Nodes_PipeStatus` FOREIGN KEY (`IdState`) REFERENCES `PipeStatus`(`id`) 
    ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `Nodes` ADD CONSTRAINT `Nodes_Users` FOREIGN KEY (`BlockUser`) REFERENCES `Users`(`IdUser`) 
    ON DELETE SET NULL ON UPDATE CASCADE;
