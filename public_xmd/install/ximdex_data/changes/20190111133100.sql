-- Move to previous state
UPDATE `Actions` SET `Sort` = '73' WHERE `Actions`.`IdAction` = 7461;
UPDATE `Actions` SET `Sort` = '73' WHERE `Actions`.`IdAction` = 6099;
UPDATE `Actions` SET `Sort` = '73' WHERE `Actions`.`IdAction` = 6127;
UPDATE `Actions` SET `Sort` = '73' WHERE `Actions`.`IdAction` = 6129;
UPDATE `Actions` SET `Sort` = '73' WHERE `Actions`.`IdAction` = 6131;
UPDATE `Actions` SET `Sort` = '73' WHERE `Actions`.`IdAction` = 6133;
UPDATE `Actions` SET `Sort` = '73' WHERE `Actions`.`IdAction` = 7259;
UPDATE `Actions` SET `Sort` = '73' WHERE `Actions`.`IdAction` = 7445;

-- Move to next state
UPDATE `Actions` SET `Name` = 'Move to next state', `Sort` = '72' WHERE `Actions`.`IdAction` = 6098;
UPDATE `Actions` SET `Name` = 'Move to next state', `Sort` = '72' WHERE `Actions`.`IdAction` = 6126;
UPDATE `Actions` SET `Name` = 'Move to next state', `Sort` = '72' WHERE `Actions`.`IdAction` = 6128;
UPDATE `Actions` SET `Name` = 'Move to next state', `Sort` = '72' WHERE `Actions`.`IdAction` = 6130;
UPDATE `Actions` SET `Name` = 'Move to next state', `Sort` = '72' WHERE `Actions`.`IdAction` = 6132;
UPDATE `Actions` SET `Name` = 'Move to next state', `Sort` = '72' WHERE `Actions`.`IdAction` = 7258;
UPDATE `Actions` SET `Name` = 'Move to next state', `Sort` = '72' WHERE `Actions`.`IdAction` = 7444;
UPDATE `Actions` SET `Name` = 'Move to next state', `Sort` = '72' WHERE `Actions`.`IdAction` = 7460;

-- Workflows
UPDATE `Nodes` SET `Name` = 'Workflow for structured' WHERE `Nodes`.`IdNode` = 403;
UPDATE `Nodes` SET `Name` = 'Workflow for common' WHERE `Nodes`.`IdNode` = 404;
