UPDATE `Nodes` SET BlockTime = NULL WHERE BlockTime = 0;

ALTER TABLE `Nodes` ADD `ActiveNF` INT(12) UNSIGNED NULL DEFAULT NULL AFTER `Path`;

ALTER TABLE `Nodes` ADD CONSTRAINT `Nodes_NodeFrames` FOREIGN KEY (`ActiveNF`) REFERENCES `NodeFrames`(`IdNodeFrame`) 
ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `Nodes` CHANGE `ActiveNF` `ActiveNF` INT(12) UNSIGNED NULL DEFAULT NULL COMMENT 'Current active node frame';

ALTER TABLE `NodeFrames` ADD `TimeStampState` INT(12) UNSIGNED NULL DEFAULT NULL AFTER `IdPortalFrame`, 
ADD `TimeStampProccesed` INT(12) UNSIGNED NULL DEFAULT NULL AFTER `TimeStampState`;

ALTER TABLE `NodeFrames` ADD `SF_Total` INT(4) UNSIGNED NOT NULL DEFAULT '0' AFTER `TimeStampProccesed`, 
ADD `SF_IN` INT(4) UNSIGNED NOT NULL DEFAULT '0' AFTER `SF_Total`;

ALTER TABLE `NodeFrames` CHANGE `SF_Total` `SF_Total` INT(4) UNSIGNED NOT NULL DEFAULT '0' 
COMMENT 'Number of total server frames related to this node frame', 
CHANGE `SF_IN` `SF_IN` INT(4) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Number of server frames in IN state related to this node frame';
-- CHANGE `SF_OUT` `SF_OUT` INT(4) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Number of server frames in OUT state related to this node frame';

ALTER TABLE `NodeFrames` CHANGE `TimeStampState` `TimeStampState` INT(12) UNSIGNED NULL DEFAULT NULL 
COMMENT 'Time when first server frame change its status to IN or OUT', 
CHANGE `TimeStampProccesed` `TimeStampProccesed` INT(12) UNSIGNED NULL DEFAULT NULL 
COMMENT 'Time when first server frame was processed from previous state';

ALTER TABLE `ServerFrames` CHANGE `IdNodeFrame` `IdNodeFrame` INT(12) UNSIGNED NOT NULL, CHANGE `NodeId` `NodeId` INT(12) UNSIGNED NOT NULL;
