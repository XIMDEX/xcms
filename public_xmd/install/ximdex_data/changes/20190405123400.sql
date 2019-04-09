DELETE FROM `RelLinkDescriptions` WHERE `Description` IS NULL OR `Description` = '';

ALTER TABLE `RelLinkDescriptions` CHANGE `Description` `Description` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
