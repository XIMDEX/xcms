ALTER TABLE `RelRolesActions` ENGINE = InnoDB;
ALTER TABLE `Roles` ENGINE = InnoDB;

DELETE FROM RelRolesActions 
    WHERE `IdRel` IN (SELECT `IdRel` FROM RelRolesActions rel
    LEFT JOIN Actions actions ON actions.IdAction = rel.IdAction
    WHERE actions.IdAction IS NULL);

ALTER TABLE `RelRolesActions` CHANGE `IdAction` `IdAction` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `RelRolesActions` ADD CONSTRAINT `RelRolesActions_Actions` FOREIGN KEY (`IdAction`) 
    REFERENCES `Actions`(`IdAction`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `RelRolesActions` ADD CONSTRAINT `RelRoldesActions_Rol` FOREIGN KEY (`IdRol`) 
    REFERENCES `Roles`(`IdRole`) ON DELETE CASCADE ON UPDATE CASCADE;
