ALTER TABLE `Channels` ADD COLUMN `Default_Channel` boolean NOT NULL default FALSE;
UPDATE `Channels` SET `Default_Channel`=1 WHERE `IdChannel`=10001;