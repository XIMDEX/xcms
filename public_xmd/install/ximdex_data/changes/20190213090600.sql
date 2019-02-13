UPDATE `NodeTypes` SET `Class` = 'WorkflowProcess' WHERE `NodeTypes`.`IdNodeType` = 5079;

ALTER TABLE `Servers` CHANGE `OverrideLocalPaths` `OverrideLocalPaths` BOOLEAN NOT NULL DEFAULT FALSE, 
CHANGE `Enabled` `Enabled` BOOLEAN NOT NULL DEFAULT TRUE, 
CHANGE `Previsual` `Previsual` BOOLEAN NOT NULL DEFAULT FALSE;
