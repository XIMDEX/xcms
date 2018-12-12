ALTER TABLE `Metadata` CHANGE `type` `type` ENUM('integer','float','text','boolean','date','array','image','link','file') 
    CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'text';

INSERT INTO `MetadataSection` (`name`) VALUES ('HTML');
INSERT INTO `MetadataGroup` (`idMetadataSection`, `name`) 
    SELECT MetadataSection.idMetadataSection, 'SEO' FROM  MetadataSection WHERE name = 'HTML';

UPDATE `Metadata` SET `defaultValue` = '@@@MDximdex.user()@@@' WHERE `Metadata`.`idMetadata` = 2;
UPDATE `Metadata` SET `defaultValue` = '@@@MDximdex.curdate()@@@' WHERE `Metadata`.`idMetadata` = 1;

INSERT INTO `Metadata` (`name`, `defaultValue`, `type`) VALUES 
    ('description', NULL, 'text'), 
    ('keywords', NULL, 'text'),
    ('robots', NULL, 'text'), 
    ('viewport', NULL, 'text'),
    ('image', NULL, 'image'),
    ('title', '@@@MDximdex.curname(this)@@@', 'text');

INSERT INTO `RelMetadataGroupMetadata` (`idMetadataGroup`, `idMetadata`, `required`) 
    SELECT MetadataGroup.idMetadataGroup, Metadata.idMetadata, '0'
    FROM Metadata, MetadataGroup
    WHERE Metadata.name = 'description'
        and MetadataGroup.name = 'SEO';

INSERT INTO `RelMetadataGroupMetadata` (`idMetadataGroup`, `idMetadata`, `required`) 
    SELECT MetadataGroup.idMetadataGroup, Metadata.idMetadata, '0'
    FROM Metadata, MetadataGroup
    WHERE Metadata.name = 'keywords'
        and MetadataGroup.name = 'SEO';

INSERT INTO `RelMetadataGroupMetadata` (`idMetadataGroup`, `idMetadata`, `required`) 
    SELECT MetadataGroup.idMetadataGroup, Metadata.idMetadata, '0' 
    FROM Metadata, MetadataGroup
    WHERE Metadata.name = 'robots'
        and MetadataGroup.name = 'SEO';

INSERT INTO `RelMetadataGroupMetadata` (`idMetadataGroup`, `idMetadata`, `required`) 
    SELECT MetadataGroup.idMetadataGroup, Metadata.idMetadata, '0'
    FROM Metadata, MetadataGroup
    WHERE Metadata.name = 'viewport'
        and MetadataGroup.name = 'SEO';

INSERT INTO `RelMetadataGroupMetadata` (`idMetadataGroup`, `idMetadata`, `required`) 
    SELECT MetadataGroup.idMetadataGroup, Metadata.idMetadata, '0'
    FROM Metadata, MetadataGroup
    WHERE Metadata.name = 'title'
        and MetadataGroup.name = 'GENERAL';

INSERT INTO `RelMetadataGroupMetadata` (`idMetadataGroup`, `idMetadata`, `required`) 
    SELECT MetadataGroup.idMetadataGroup, Metadata.idMetadata, '0'
    FROM Metadata, MetadataGroup
    WHERE Metadata.name = 'image'
        and MetadataGroup.name = 'GENERAL';

INSERT INTO `RelMetadataSectionNodeType` (`idMetadataSection`, `idNodeType`)
    SELECT  MetadataSection.idMetadataSection, '5104'
    FROM  MetadataSection 
    WHERE MetadataSection.name = 'HTML';
