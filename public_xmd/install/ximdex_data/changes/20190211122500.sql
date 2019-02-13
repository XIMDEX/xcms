ALTER TABLE `NodeFrames` DROP INDEX `NodeVersion`;

ALTER TABLE `Batchs` DROP FOREIGN KEY `Batchs_PortalFrames`;

ALTER TABLE `Batchs` ADD CONSTRAINT `Batchs_PortalFrames` FOREIGN KEY (`IdPortalFrame`) REFERENCES `PortalFrames`(`id`) 
ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `NodeFrames` DROP FOREIGN KEY `NodeFrames_PortalFrames`;

ALTER TABLE `NodeFrames` ADD CONSTRAINT `NodeFrames_PortalFrames` FOREIGN KEY (`IdPortalFrame`) REFERENCES `PortalFrames`(`id`) 
ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `ServerFrames` DROP FOREIGN KEY `ServerFrames_Batchs_down`;

ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_Batchs_down` FOREIGN KEY (`IdBatchDown`) REFERENCES `Batchs`(`IdBatch`) 
ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `ServerFrames` DROP FOREIGN KEY `ServerFrames_PortalFrames`;

ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_PortalFrames` FOREIGN KEY (`IdPortalFrame`) REFERENCES `PortalFrames`(`id`) 
ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `ServerFrames` DROP FOREIGN KEY `ServerFrames_Batchs_Up`;

ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_Batchs_Up` FOREIGN KEY (`IdBatchUp`) REFERENCES `Batchs`(`IdBatch`) 
ON DELETE RESTRICT ON UPDATE CASCADE;
