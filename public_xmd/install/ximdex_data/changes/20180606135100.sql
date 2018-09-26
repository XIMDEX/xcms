ALTER TABLE `Roles` CHANGE `IdRole` `IdRole` INT(12) UNSIGNED NOT NULL;

DELETE FROM `NodeAllowedContents` WHERE `NodeAllowedContents`.`IdNodeAllowedContent` = 29;

INSERT INTO `ProgrammingCommand` (`id`, `description`) VALUES ('var', 'Initialize a variable');
INSERT INTO `ProgrammingCode` (`id`, `idLanguage`, `idCommand`, `code`) VALUES (NULL, 'php', 'var', '<?php $%s = \'%s\'; ?>');

INSERT INTO `ProgrammingCommand` (`id`, `description`) VALUES ('obstart', 'Turn on output buffering');
INSERT INTO `ProgrammingCode` (`id`, `idLanguage`, `idCommand`, `code`) VALUES (NULL, 'php', 'obstart', '<?php ob_start(); ?>');

ALTER TABLE `ProgrammingCommand` CHANGE `description` `description` VARCHAR(255) NULL DEFAULT NULL;

INSERT INTO `ProgrammingCommand` (`id`, `description`) VALUES ('obgetclean', 'Get current buffer contents and delete current output buffer');
INSERT INTO `ProgrammingCode` (`id`, `idLanguage`, `idCommand`, `code`) VALUES (NULL, 'php', 'obgetclean', '<?php $%s = ob_get_clean(); ?>');

INSERT INTO `ProgrammingCommand` (`id`, `description`) VALUES ('echo', 'Output one variable value');
INSERT INTO `ProgrammingCode` (`id`, `idLanguage`, `idCommand`, `code`) VALUES (NULL, 'php', 'echo', '<?php echo $%s; ?>');

INSERT INTO `ProgrammingCommand` (`id`, `description`) VALUES ('sprintf1', 'Return a formatted string with one parameter');
INSERT INTO `ProgrammingCode` (`idLanguage`, `idCommand`, `code`) VALUES ('php', 'sprintf1', '<?php $%s = sprintf($%s, $%s); ?>');

INSERT INTO `ProgrammingCommand` (`id`, `description`) VALUES ('sprintf2', 'Return a formatted string with two parameters');
INSERT INTO `ProgrammingCode` (`idLanguage`, `idCommand`, `code`) VALUES ('php', 'sprintf2', '<?php $%s = sprintf($%s, $%s, $%s); ?>');

ALTER TABLE RelSemanticTagsNodes DROP FOREIGN KEY `RelSemanticTagsNodes_ibfk_1`;

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) 
    VALUES ('7312', '5022', 'Modify properties', 'manageproperties', 'xix.png', 'Modify properties of a common root folder', '60'
    , NULL, '0', NULL, '0');
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES 
    (NULL, '201', '7312', 7, 1, 3), (NULL, '201', '7312', 8, 1, 3);
    
INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) 
    VALUES ('7313', '5023', 'Modify properties', 'manageproperties', 'xix.png', 'Modify properties of a common folder', '60'
    , NULL, '0', NULL, '0');
INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES 
    (NULL, '201', '7313', 7, 1, 3), (NULL, '201', '7313', 8, 1, 3);
