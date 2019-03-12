UPDATE `NodeTypes` SET `IsHidden` = '0' WHERE `NodeTypes`.`IdNodeType` = 5019;

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) 
VALUES (NULL, '5019', 'Add or update metadata', 'createmetadata', 'add_metadata.png', 'Add or update a metadata to Ximdex', '10', NULL
, '0', '', '0');

INSERT INTO `RelRolesActions` (IdRol, IdAction, IdState) VALUES ('201', LAST_INSERT_ID(), NULL);

INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) 
VALUES (NULL, '5019', 'Associate metadata to groups', 'modifymetadatagroups', 'add_metadata.png', 'Associate metadata to sections groups'
, '10', NULL, '0', '', '0');

INSERT INTO `RelRolesActions` (IdRol, IdAction, IdState) VALUES ('201', LAST_INSERT_ID(), NULL);
