ALTER TABLE `ServerFrames` DROP FOREIGN KEY `ServerFrames_ibfk_7`;
ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_ibfk_7` FOREIGN KEY (`PumperId`) 
    REFERENCES `Pumpers`(`PumperId`) ON DELETE RESTRICT ON UPDATE RESTRICT;