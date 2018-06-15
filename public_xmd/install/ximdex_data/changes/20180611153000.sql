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

/********** TABLE METADATAGROUP ***********/
CREATE TABLE `MetadataGroup`(
  `idMetadataGroup` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY(`idMetadataGroup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Available groups for metadata';

-- UNIQUE
ALTER TABLE `MetadataGroup` ADD UNIQUE(`name`);

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
CREATE TABLE `RelMetadataGroupNodeType`(
  `idRelMetadataGroupNodeType` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `idMetadataGroup` int(12) unsigned NOT NULL,
  `idNodeType` int(12) unsigned NOT NULL,
  PRIMARY KEY(`idRelMetadataGroupNodeType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Relation between group and nodetype';

-- UNIQUE
ALTER TABLE `RelMetadataGroupNodeType` ADD UNIQUE(`idMetadataGroup`, `idNodeType`);

-- FOREIGN KEY
ALTER TABLE `RelMetadataGroupNodeType` ADD FOREIGN KEY (`idMetadataGroup`) REFERENCES `MetadataGroup`(`idMetadataGroup`)
  ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `RelMetadataGroupNodeType` ADD FOREIGN KEY (`idNodeType`) REFERENCES `NodeTypes`(`IdNodeType`)
  ON DELETE CASCADE ON UPDATE CASCADE ;

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

INSERT INTO `RelRolesActions` (`IdRel`, `IdRol`, `IdAction`, `IdState`, `IdContext`, `IdPipeline`)
  VALUES  (2127,201,6211,8,1,3), (2128,201,6211,7,1,3);
