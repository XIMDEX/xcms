INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`, `Params`, `IsBulk`) 
VALUES (NULL, '5042', 'Replace file', 'replacefile', 'add_file_common.png', 'Replace the video file for the current node', '62'
, NULL, '0', '', '0');

INSERT INTO `RelRolesActions` (IdRol, IdAction, IdState) VALUES ('201', LAST_INSERT_ID(), 7);
