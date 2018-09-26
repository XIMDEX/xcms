ALTER TABLE `ServerFrames` DROP FOREIGN KEY `ServerFrames_Batchs_up`;
ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_Batchs_up` FOREIGN KEY (`IdBatchUp`) 
    REFERENCES `Batchs`(`IdBatch`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ServerFrames` DROP FOREIGN KEY `ServerFrames_Batchs_down`;
ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_Batchs_down` FOREIGN KEY (`IdBatchDown`) 
    REFERENCES `Batchs`(`IdBatch`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `Batchs` DROP FOREIGN KEY `Batchs_Batchs`;
ALTER TABLE `Batchs` ADD CONSTRAINT `Batchs_Batchs` FOREIGN KEY (`IdBatchDown`) 
    REFERENCES `Batchs`(`IdBatch`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `Batchs` CHANGE `State` `State` ENUM('Waiting','InTime','Closing','Ended', 'NoFrames') 
    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Waiting';
