ALTER TABLE `Metadata` CHANGE `type` `type` ENUM('integer','float','text','boolean','date','array','image','link','file') 
CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'text';
