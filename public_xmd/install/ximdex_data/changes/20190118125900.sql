INSERT INTO `Locales` (`ID`, `Code`, `Name`, `Enabled`) VALUES (NULL, 'fr_FR', 'French', '0');

ALTER TABLE `Locales` ADD UNIQUE(`Code`);

ALTER TABLE `Locales` ADD UNIQUE(`Name`);

ALTER TABLE `Locales` CHANGE `Enabled` `Enabled` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Enabled(1)|Not Enabled(0)';

ALTER TABLE `Locales` ENGINE = InnoDB;

ALTER TABLE `Users` ADD CONSTRAINT `Users_Locales` FOREIGN KEY (`Locale`) REFERENCES `Locales`(`Code`) ON DELETE SET NULL ON UPDATE CASCADE;
