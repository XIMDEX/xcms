-- ALTER TABLE `ServerErrorByPumper` ENGINE = InnoDB;

-- ALTER TABLE `ServerErrorByPumper` ADD CONSTRAINT `ServerErrorByPumper_Pumpers` FOREIGN KEY (`PumperId`) 
--     REFERENCES `Pumpers`(`PumperId`) ON DELETE CASCADE ON UPDATE CASCADE;
-- ALTER TABLE `ServerErrorByPumper` ADD CONSTRAINT `ServerErrorByPumper_Servers` FOREIGN KEY (`ServerId`) 
--     REFERENCES `Servers`(`IdServer`) ON DELETE CASCADE ON UPDATE CASCADE;
-- ALTER TABLE `ServerErrorByPumper` ADD INDEX(`WithError`);

ALTER TABLE `NodeFrames` ADD CONSTRAINT `NodeFrames_Nodes` FOREIGN KEY (`NodeId`) 
    REFERENCES `Nodes`(`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `Versions` ENGINE = InnoDB;

ALTER TABLE `NodeFrames` ADD CONSTRAINT `NodeFrames_Versions` FOREIGN KEY (`VersionId`) 
    REFERENCES `Versions`(`IdVersion`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `Versions` ADD CONSTRAINT `Versions_Nodes` FOREIGN KEY (`IdNode`) 
    REFERENCES `Nodes`(`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `Versions` ADD CONSTRAINT `Versions_Users` FOREIGN KEY (`IdUser`) 
    REFERENCES `Users`(`IdUser`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `Versions` ADD CONSTRAINT `Versions_ServerFrames` FOREIGN KEY (`IdSync`) 
    REFERENCES `ServerFrames`(`IdSync`) ON DELETE SET NULL ON UPDATE CASCADE;

UPDATE ServerFrames SET `ErrorLevel` = NULL WHERE `ErrorLevel` = 0;
ALTER TABLE `ServerFrames` CHANGE `ErrorLevel` `ErrorLevel` VARCHAR(1) NULL DEFAULT NULL;
ALTER TABLE `ServerFrames` CHANGE `ErrorLevel` `ErrorLevel` ENUM('1','2') NULL DEFAULT NULL COMMENT 'Errors: 1 Soft, 2 Hard';
