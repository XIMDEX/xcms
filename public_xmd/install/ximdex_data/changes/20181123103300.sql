ALTER TABLE `NodeFrames` DROP INDEX `NodeVersion`, ADD UNIQUE `NodeVersion` (`NodeId`, `VersionId`, `IdPortalFrame`) USING BTREE;

ALTER TABLE `Batchs` CHANGE `State` `State` ENUM('Creating', 'Waiting','InTime','Closing','Ended','NoFrames','Stopped') 
    CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Creating';
