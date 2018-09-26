ALTER TABLE `Servers` ENGINE = InnoDB;
ALTER TABLE `RelServersChannels` ENGINE = InnoDB;
ALTER TABLE `Protocols` ENGINE = InnoDB;
ALTER TABLE `Encodes` ENGINE = InnoDB;

ALTER TABLE `RelServersChannels` ADD CONSTRAINT `RelServersChannels_Channels` FOREIGN KEY (`IdChannel`) 
    REFERENCES `Channels`(`IdChannel`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `RelServersChannels` ADD CONSTRAINT `RelServersChannels_Servers` FOREIGN KEY (`IdServer`) 
    REFERENCES `Servers`(`IdServer`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `RelServersChannels` DROP INDEX `IdRel`;

ALTER TABLE `Servers` ADD CONSTRAINT `Servers_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes`(`IdNode`) 
    ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `Servers` ADD CONSTRAINT `Servers_Protocols` FOREIGN KEY (`IdProtocol`) REFERENCES `Protocols`(`IdProtocol`) 
    ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `Protocols` DROP INDEX `IdProtocol`;
ALTER TABLE `Protocols` DROP INDEX `IdProtocol_2`;

ALTER TABLE `Encodes` DROP INDEX `IdEncode`;
ALTER TABLE `Encodes` DROP INDEX `IdEncode_2`;

ALTER TABLE `ChannelFrames` ADD CONSTRAINT `ChannelFrames_Channels` FOREIGN KEY (`ChannelId`) REFERENCES `Channels`(`IdChannel`) 
    ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `ChannelFrames` ADD CONSTRAINT `ChannelFrames_Nodes` FOREIGN KEY (`NodeId`) REFERENCES `Nodes`(`IdNode`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ServerFrames` CHANGE `ChannelId` `ChannelId` INT(12) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `ServerFrames` CHANGE `NodeId` `NodeId` INT(12) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_Channels` FOREIGN KEY (`ChannelId`) REFERENCES `Channels`(`IdChannel`) 
    ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_Nodes` FOREIGN KEY (`NodeId`) REFERENCES `Nodes`(`IdNode`) 
    ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `ServerFrames` CHANGE `IdChannelFrame` `IdChannelFrame` INT(12) UNSIGNED NULL;
ALTER TABLE `ServerFrames` ADD CONSTRAINT `ServerFrames_ChannelFrames` FOREIGN KEY (`IdChannelFrame`) 
    REFERENCES `ChannelFrames`(`IdChannelFrame`) ON DELETE CASCADE ON UPDATE CASCADE;
