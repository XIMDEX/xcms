INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) 
VALUES ('6237', '5021', 'Add files', 'fileupload_common_multiple', 'add_nodes_ht.png', 'Add multiple files', '10', NULL, '0', '', '0');

INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`) VALUES (NULL, '201', '6237', NULL);
