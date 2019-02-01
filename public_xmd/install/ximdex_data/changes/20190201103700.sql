ALTER TABLE `ServerFrames` DROP FOREIGN KEY `ServerFrames_PortalFrames`;
ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_PortalFrames` FOREIGN KEY (`IdPortalFrame`) 
    REFERENCES `PortalFrames`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
