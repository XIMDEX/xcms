UPDATE `Actions` SET `Sort` = '80' WHERE `IdNodeType` = 5032 AND `Name` = 'Version manager';

UPDATE `Actions` SET `Sort` = '80' WHERE `IdNodeType` = 5104 AND `Name` = 'Version manager';

-- ALTER TABLE `NodeDependencies` CHANGE `IdChannel` `IdChannel` INT(12) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `NoActionsInNode` ENGINE = InnoDB;

ALTER TABLE `NoActionsInNode` CHANGE `IdNode` `IdNode` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `NoActionsInNode` ADD CONSTRAINT `NoActionsInNode_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes`(`IdNode`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `NoActionsInNode` CHANGE `IdAction` `IdAction` INT(12) UNSIGNED NOT NULL COMMENT 'Actions not allowed for a Node';

ALTER TABLE `NoActionsInNode` ADD CONSTRAINT `NoActionsInNode_Actions` FOREIGN KEY (`IdAction`) REFERENCES `Actions`(`IdAction`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE ``NodeDependencies`` DROP PRIMARY KEY;

ALTER TABLE `NodeDependencies` ADD `id` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);

ALTER TABLE `NodeDependencies` ADD UNIQUE (`IdNode`, `IdResource`, `IdChannel`);

ALTER TABLE `NodeDependencies` CHANGE `IdChannel` `IdChannel` INT(12) UNSIGNED NULL DEFAULT NULL;
