ALTER TABLE `NodeFrames` CHANGE `IdPortalFrame` `IdPortalFrame` INT(12) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `NodeFrames` DROP FOREIGN KEY `NodeFrames_PortalFrames`;

ALTER TABLE `NodeFrames` ADD CONSTRAINT `NodeFrames_PortalFrames` FOREIGN KEY (`IdPortalFrame`) REFERENCES `PortalFrames`(`id`) 
ON DELETE SET NULL ON UPDATE CASCADE;
