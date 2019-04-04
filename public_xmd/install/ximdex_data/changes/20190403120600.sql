DELETE FROM `NodeNameTranslations` WHERE Name IS NULL;

ALTER TABLE `NodeNameTranslations` CHANGE `IdNode` `IdNode` INT(12) UNSIGNED NOT NULL, 
CHANGE `IdLanguage` `IdLanguage` INT(12) UNSIGNED NOT NULL, 
CHANGE `Name` `Name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `RelSemanticTagsNodes` CHANGE `Node` `Node` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `RelSemanticTagsNodes` ADD CONSTRAINT `RelSemanticTagsNodes_Nodes` FOREIGN KEY (`Node`) REFERENCES `Nodes`(`IdNode`) 
ON DELETE CASCADE ON UPDATE CASCADE;
