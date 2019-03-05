-- ALTER TABLE `ServerFrames` DROP FOREIGN KEY `ServerFrames_Pumpers`;
ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_Pumpers` FOREIGN KEY (`PumperId`) 
    REFERENCES `Pumpers`(`PumperId`) ON DELETE RESTRICT ON UPDATE RESTRICT;
