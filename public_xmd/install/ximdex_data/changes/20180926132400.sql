DELETE FROM `FastTraverse` WHERE IdNode NOT IN (SELECT n.IdNode FROM Nodes n WHERE n.IdNode = `FastTraverse`.IdNode);
DELETE FROM `FastTraverse` WHERE IdChild NOT IN (SELECT IdNode FROM Nodes WHERE IdNode = IdChild);
ALTER TABLE `FastTraverse` ENGINE = InnoDB;
ALTER TABLE `FastTraverse` ADD CONSTRAINT `FastTraverse_Nodes_parent` FOREIGN KEY (`IdNode`) REFERENCES `Nodes`(`IdNode`) 
    ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `FastTraverse` ADD CONSTRAINT `FastTraverse_Nodes_child` FOREIGN KEY (`IdChild`) REFERENCES `Nodes`(`IdNode`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `DependenceTypes` ENGINE = InnoDB;

ALTER TABLE `Dependencies` ENGINE = InnoDB;
DELETE FROM `Dependencies` WHERE IdNodeMaster NOT IN (SELECT IdNode FROM Nodes WHERE IdNodeMaster = IdNode);
DELETE FROM `Dependencies` WHERE IdNodeDependent NOT IN (SELECT IdNode FROM Nodes WHERE IdNodeDependent = IdNode);
ALTER TABLE `Dependencies` CHANGE `DepType` `DepType` INT(6) UNSIGNED NOT NULL;
ALTER TABLE `Dependencies` ADD CONSTRAINT `Dependencies_DependenceTypes` FOREIGN KEY (`DepType`) 
    REFERENCES `DependenceTypes`(`IdDepType`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `Dependencies` ADD CONSTRAINT `Dependencies_Nodes_dep` FOREIGN KEY (`IdNodeDependent`) 
    REFERENCES `Nodes`(`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `Dependencies` ADD CONSTRAINT `Dependencies_Nodes_master` FOREIGN KEY (`IdNodeMaster`) 
    REFERENCES `Nodes`(`IdNode`) ON DELETE CASCADE ON UPDATE CASCADE;
