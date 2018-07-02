ALTER TABLE `ServerFrames` DROP FOREIGN KEY `ServerFrames_ibfk_1`;
ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_ibfk_1` FOREIGN KEY (`IdBatchUp`) 
    REFERENCES `Batchs`(`IdBatch`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ServerFrames` DROP FOREIGN KEY `ServerFrames_ibfk_2`;
ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_ibfk_2` FOREIGN KEY (`IdBatchDown`) 
    REFERENCES `Batchs`(`IdBatch`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `Batchs` DROP FOREIGN KEY `Batchs_ibfk_1`;
ALTER TABLE `Batchs` ADD CONSTRAINT `Batchs_ibfk_1` FOREIGN KEY (`IdBatchDown`) 
    REFERENCES `Batchs`(`IdBatch`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `Batchs` CHANGE `State` `State` ENUM('Waiting','InTime','Closing','Ended', 'NoFrames') 
    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Waiting';