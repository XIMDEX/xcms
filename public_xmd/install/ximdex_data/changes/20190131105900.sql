INSERT INTO `RelMetadataSectionNodeType` (`idMetadataSection`, `idNodeType`) VALUES ('1', '5104');

ALTER TABLE `RelRolesActions` DROP FOREIGN KEY `RelRolesActions_Status`;
ALTER TABLE `RelRolesActions` ADD CONSTRAINT `RelRolesActions_Status` FOREIGN KEY (`IdState`) 
    REFERENCES `WorkflowStatus`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
