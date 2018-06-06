INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) 
    VALUES ('6350', '5041', 'Replace file', 'replacefile', 'add_file_common.png', 'Replace the binary file for the current node'
    , '62', NULL, '0', '', '0');

INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES 
    (NULL, '201', '6350', 7, 1, 3), 
    (NULL, '201', '6350', 8, 1, 3);

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) 
    VALUES ('6351', '5040', 'Replace file', 'replacefile', 'add_file_common.png', 'Replace the image file for the current node'
    , '62', NULL, '0', '', '0');

INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES 
    (NULL, '201', '6351', 7, 1, 3), 
    (NULL, '201', '6351', 8, 1, 3);
    
INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) 
    VALUES ('6352', '5028', 'Replace file', 'replacefile', 'add_file_common.png', 'Replace the CSS file for the current node'
    , '62', NULL, '0', '', '0');

INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES 
    (NULL, '201', '6352', 7, 1, 3), 
    (NULL, '201', '6352', 8, 1, 3);

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) 
    VALUES ('6353', '5092', 'Replace file', 'replacefile', 'add_file_common.png', 'Replace the javascript file for the current node'
    , '62', NULL, '0', '', '0');

INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`) VALUES 
    (NULL, '201', '6353', 7, 1, 3), 
    (NULL, '201', '6353', 8, 1, 3);