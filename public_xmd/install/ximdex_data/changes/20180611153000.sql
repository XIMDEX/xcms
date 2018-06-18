ALTER TABLE Nodes ENGINE = InnoDB; -- SET ENGINE TO INNODB FOR NODES
ALTER TABLE NodeTypes ENGINE = InnoDB; -- SET ENGINE TO INNODB FORT NODETYPES

ALTER TABLE `Nodes` ADD FOREIGN KEY (`IdNodeType`) REFERENCES `NodeTypes`(`IdNodeType`)
  ON DELETE RESTRICT ON UPDATE CASCADE ;

/************************************************************************************
 *                                METATADA SCHEMA
 ************************************************************************************/

/********** TABLE METADATA ***********/
CREATE TABLE `Metadata`(
  `idMetadata` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `defaultValue` text,
  `type` ENUM('integer', 'float', 'text', 'boolean', 'date') DEFAULT 'text',
  PRIMARY KEY(`idMetadata`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Available metadata for nodes';

-- UNIQUE
ALTER TABLE `Metadata` ADD UNIQUE(`name`);

/********** TABLE METADATASECTION ***********/
CREATE TABLE `MetadataSection`(
  `idMetadataSection` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY(`idMetadataSection`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Available sections for metadata';

-- UNIQUE
ALTER TABLE `MetadataSection` ADD UNIQUE(`name`);

/********** TABLE METADATAGROUP ***********/
CREATE TABLE `MetadataGroup`(
  `idMetadataGroup` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `idMetadataSection` int(12) unsigned,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY(`idMetadataGroup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Available groups for metadata';


-- FOREIGN KEY
ALTER TABLE `MetadataGroup` ADD FOREIGN KEY (`idMetadataSection`) REFERENCES
  `MetadataSection`(`idMetadataSection`) ON DELETE CASCADE ON UPDATE CASCADE ;

/********** TABLE METADATAGROUP-METADATA ***********/
CREATE TABLE `RelMetadataGroupMetadata`(
  `idRelMetadataGroupMetadata` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `idMetadataGroup` int(12) unsigned NOT NULL,
  `idMetadata` int(12) unsigned NOT NULL,
  `required` TINYINT(1) NOT NULL DEFAULT false,
  PRIMARY KEY(`idRelMetadataGroupMetadata`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Relation between metadata and metadata group';

-- UNIQUE
ALTER TABLE `RelMetadataGroupMetadata` ADD UNIQUE(`idMetadataGroup`, `idMetadata`);

-- FOREIGN KEY
ALTER TABLE `RelMetadataGroupMetadata` ADD FOREIGN KEY (`idMetadataGroup`) REFERENCES `MetadataGroup`(`idMetadataGroup`)
  ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `RelMetadataGroupMetadata` ADD FOREIGN KEY (`idMetadata`) REFERENCES `Metadata`(`idMetadata`)
  ON DELETE CASCADE ON UPDATE CASCADE ;

/********** TABLE METADATAGROUP-NODETYPE ***********/
CREATE TABLE `RelMetadataSectionNodeType`(
  `idMetadataSection` int(12) unsigned NOT NULL,
  `idNodeType` int(12) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Relation between section and nodetype';

-- UNIQUE
ALTER TABLE `RelMetadataSectionNodeType` ADD UNIQUE(`idMetadataSection`, `idNodeType`);

-- FOREIGN KEY
ALTER TABLE `RelMetadataSectionNodeType` ADD FOREIGN KEY (`idMetadataSection`) REFERENCES
  `MetadataSection`(`idMetadataSection`) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `RelMetadataSectionNodeType` ADD FOREIGN KEY (`idNodeType`) REFERENCES `NodeTypes`(`IdNodeType`)
  ON DELETE CASCADE ON UPDATE CASCADE;

/********** TABLE METADATA VALUE ***********/
CREATE TABLE `MetadataValue`(
  `idNode` int(12) unsigned NOT NULL,
  `idRelMetadataGroupMetadata` int(12) unsigned NOT NULL,
  `value` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Metadata value for node';

-- UNIQUE
ALTER TABLE `MetadataValue` ADD UNIQUE(`idNode`, `idRelMetadataGroupMetadata`);

-- FOREIGN KEY
ALTER TABLE `MetadataValue` ADD FOREIGN KEY (`idNode`) REFERENCES `Nodes`(`IdNode`)
  ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `MetadataValue` ADD FOREIGN KEY (`idRelMetadataGroupMetadata`) REFERENCES `RelMetadataGroupMetadata`
  (`idRelMetadataGroupMetadata`) ON DELETE CASCADE ON UPDATE CASCADE ;


/************ INSERT ACTIONS ****************/
INSERT INTO `Actions` (`IdAction`, `IdNodeType`, `Name`, `Command`, `Icon`, `Description`, `Sort`, `Module`, `Multiple`,
  `Params`, `IsBulk`) VALUES (9510, 5041 ,'Manage metadata','metadata','add_xml.png', 'Manage metadata for node', 15,
  NULL, 0, '', 0);

INSERT INTO `RelRolesActions` (`IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`)
  VALUES  (201, 9510, 8, 1, 3), (201, 9510, 7, 1, 3);

/************ INSERT METADATA ****************/


INSERT INTO `MetadataSection` (`idMetadataSection`, `name`) VALUES (1, 'General');

INSERT INTO `MetadataGroup` (`idMetadataGroup`,`idMetadataSection`,`name`) VALUES (1, 1,'General');

INSERT INTO `Metadata` (`idMetadata`, `name`, `defaultValue`, `type`) VALUES
  (1, 'date', 'now', 'date'), (2, 'author', 'No author', 'text'), (3, 'language', 'es', 'text');

INSERT INTO `RelMetadataSectionNodeType` (`idMetadataSection`, `idNodeType`) VALUES ('1', '5041');

INSERT INTO `RelMetadataGroupMetadata` (`idRelMetadataGroupMetadata`, `idMetadataGroup`, `idMetadata`, `required`)
  VALUES (NULL, '1', '1', '1'), (NULL, '1', '2', '1'), (NULL, '1', '3', '1');