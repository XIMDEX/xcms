ALTER TABLE `RelUsersGroups` ADD UNIQUE (`IdUser`, `IdGroup`);

INSERT INTO `Config` (`IdConfig`, `ConfigKey`, `ConfigValue`, `Description`) VALUES (NULL, 'xEditCleanFilters', '0', '1: active, 0: disabled');
