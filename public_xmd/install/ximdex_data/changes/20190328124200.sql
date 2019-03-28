ALTER TABLE `DependenceTypes` CHANGE `Type` `Type` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `DependenceTypes` ADD UNIQUE (`Type`);

ALTER TABLE `NodeConstructors` CHANGE `IdNodeConstructor` `IdNodeConstructor` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT, 
CHANGE `IdNodeType` `IdNodeType` INT(12) UNSIGNED NOT NULL, CHANGE `IdAction` `IdAction` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `NodeConstructors` ENGINE = InnoDB;
