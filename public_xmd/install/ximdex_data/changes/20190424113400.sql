ALTER TABLE `PortalFrames` ADD `Hidden` BOOLEAN NOT NULL DEFAULT FALSE AFTER `CyclesTotal`;

UPDATE `NodeTypes` SET `IsPublishable` = '0' WHERE `NodeTypes`.`IdNodeType` = 5111;
