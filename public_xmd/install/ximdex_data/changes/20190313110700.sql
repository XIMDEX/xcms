ALTER TABLE `RelMetadataGroupMetadata` ADD `enabled` BOOLEAN NOT NULL DEFAULT TRUE AFTER `readonly`;

ALTER TABLE `MetadataGroup` ADD UNIQUE (`idMetadataScheme`, `name`);
