ALTER TABLE `NodeFrames` CHANGE `NodeId` `NodeId` INT(12) UNSIGNED NULL DEFAULT NULL, 
    CHANGE `VersionId` `VersionId` INT(12) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `PipeStatus` ENGINE = InnoDB;

ALTER TABLE `PipeProcess` ENGINE = InnoDB;
ALTER TABLE `PipeProcess` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `PipeProcess` CHANGE `IdTransitionFrom` `IdTransitionFrom` INT(11) UNSIGNED NULL DEFAULT NULL, 
    CHANGE `IdTransitionTo` `IdTransitionTo` INT(11) UNSIGNED NOT NULL, 
    CHANGE `IdPipeline` `IdPipeline` INT(11) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `PipeProcess` ADD UNIQUE(`Name`);

ALTER TABLE `PipeTransitions` ENGINE = InnoDB;
ALTER TABLE `PipeTransitions` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `PipeTransitions` CHANGE `IdPipeProcess` `IdPipeProcess` INT(11) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `PipeTransitions` ADD CONSTRAINT `PipeTransitions_PipeProcess` FOREIGN KEY (`IdPipeProcess`) REFERENCES `PipeProcess`(`id`) 
    ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `PipeTransitions` CHANGE `IdStatusFrom` `IdStatusFrom` INT(12) UNSIGNED NULL DEFAULT NULL, 
    CHANGE `IdStatusTo` `IdStatusTo` INT(12) UNSIGNED NOT NULL;
ALTER TABLE `PipeTransitions` ADD CONSTRAINT `PipeTransitions_PipeStatus_From` FOREIGN KEY (`IdStatusFrom`) REFERENCES `PipeStatus`(`id`) 
    ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `PipeTransitions` ADD CONSTRAINT `PipeTransitions_PipeStatus_To` FOREIGN KEY (`IdStatusTo`) REFERENCES `PipeStatus`(`id`) 
    ON DELETE RESTRICT ON UPDATE RESTRICT;
UPDATE `PipeTransitions` SET `Cacheable` = '1' WHERE `PipeTransitions`.`id` = 10;
UPDATE `PipeTransitions` SET `Name` = 'ToRenderizeXedit' WHERE `PipeTransitions`.`id` = 7;
ALTER TABLE `PipeTransitions` ADD UNIQUE(`Name`);
ALTER TABLE `PipeTransitions` CHANGE `Callback` `Callback` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
UPDATE `PipeTransitions` SET Callback = NULL WHERE Callback = '-';
ALTER TABLE `PipeTransitions` CHANGE `IdPipeProcess` `IdPipeProcess` INT(11) UNSIGNED NOT NULL;
ALTER TABLE `PipeTransitions` CHANGE `Cacheable` `Cacheable` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `PipeCaches` ENGINE = InnoDB;
ALTER TABLE `PipeCaches` CHANGE `id` `id` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT, CHANGE `IdVersion` `IdVersion` INT(12) UNSIGNED NOT NULL;
ALTER TABLE `PipeCaches` ADD CONSTRAINT `PipeCaches_Versions` FOREIGN KEY (`IdVersion`) REFERENCES `Versions`(`IdVersion`) 
    ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `PipeCaches` ADD UNIQUE (`IdVersion`, `IdPipeTransition`);
ALTER TABLE `PipeCaches` CHANGE `IdPipeTransition` `IdPipeTransition` INT(11) UNSIGNED NOT NULL;
ALTER TABLE `PipeCaches` ADD CONSTRAINT `PipeCaches_PipeTransitions` FOREIGN KEY (`IdPipeTransition`) REFERENCES `PipeTransitions`(`id`) 
    ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `NodeDependencies` CHANGE `IdNode` `IdNode` INT(12) UNSIGNED NOT NULL;
ALTER TABLE `NodeDependencies` CHANGE `IdResource` `IdResource` INT(12) UNSIGNED NOT NULL;
ALTER TABLE `NodeDependencies` ENGINE = InnoDB;
ALTER TABLE `NodeDependencies` ADD CONSTRAINT `NodeDependencies_Channels` FOREIGN KEY (`IdChannel`) REFERENCES `Channels`(`IdChannel`) 
    ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `NodeDependencies` ADD CONSTRAINT `NodeDependencies_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes`(`IdNode`) 
    ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `NodeDependencies` ADD CONSTRAINT `NodeDependencies_Nodes_Resource` FOREIGN KEY (`IdResource`) REFERENCES `Nodes`(`IdNode`) 
    ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `NodeDependencies` DROP PRIMARY KEY, ADD PRIMARY KEY (`IdNode`, `IdResource`, `IdChannel`) USING BTREE;

ALTER TABLE `Pipelines` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `Pipelines` ADD UNIQUE(`Pipeline`);
ALTER TABLE `Pipelines` ENGINE = InnoDB;
ALTER TABLE `Pipelines` CHANGE `IdNode` `IdNode` INT(12) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `Pipelines` ADD CONSTRAINT `Pipelines_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes`(`IdNode`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `PipeProcess` ADD CONSTRAINT `PipeProcess_Pipelines` FOREIGN KEY (`IdPipeline`) REFERENCES `Pipelines`(`id`) 
    ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `PipeProcess` ADD CONSTRAINT `PipeProcess_PipeTransitions_From` FOREIGN KEY (`IdTransitionFrom`) REFERENCES `PipeTransitions`(`id`) 
    ON DELETE RESTRICT ON UPDATE RESTRICT;

-- INSERT INTO `PipeTransitions` (`id`, `IdStatusFrom`, `IdStatusTo`, `IdPipeProcess`, `Cacheable`, `Name`, `Callback`) 
--     VALUES ('6', '7', '8', '4', '0', 'EditionToPublication', NULL);
-- ALTER TABLE `PipeTransitions` ADD CONSTRAINT `PipeTransitions_PipeProcess` FOREIGN KEY (`IdPipeProcess`) REFERENCES `PipeProcess`(`id`) 
--     ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `PipeProcess` ADD CONSTRAINT `PipeProcess_PipeTransitions_To` FOREIGN KEY (`IdTransitionTo`) REFERENCES `PipeTransitions`(`id`) 
    ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `PipeProperties` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
    CHANGE `IdPipeTransition` `IdPipeTransition` INT(11) UNSIGNED NOT NULL;
ALTER TABLE `PipeProperties` ENGINE = InnoDB;
ALTER TABLE `PipeProperties` ADD CONSTRAINT `PipeProperties_PipeTransitions` FOREIGN KEY (`IdPipeTransition`) REFERENCES `PipeTransitions`(`id`) 
    ON DELETE RESTRICT ON UPDATE RESTRICT;

INSERT INTO `PipeProcess` (`id`, `IdTransitionFrom`, `IdTransitionTo`, `IdPipeline`, `Name`) VALUES ('8', '10', '6', '5', 'HTMLToSolar');
INSERT INTO `PipeTransitions` (`id`, `IdStatusFrom`, `IdStatusTo`, `IdPipeProcess`, `Cacheable`, `Name`, `Callback`) 
    VALUES ('12', NULL, '3', '8', '1', 'PrepareSolar', NULL);
UPDATE `PipeProcess` SET `IdTransitionTo` = '12' WHERE `PipeProcess`.`id` = 8;
INSERT INTO `PipeProcess` (`id`, `IdTransitionFrom`, `IdTransitionTo`, `IdPipeline`, `Name`) VALUES (9, 12, 11, 5, 'SolarToPublished');
INSERT INTO `PipeTransitions` (`id`, `IdStatusFrom`, `IdStatusTo`, `IdPipeProcess`, `Cacheable`, `Name`, `Callback`) 
    VALUES ('13', '3', '6', '9', '0', 'PublishSolar', 'FilterMacros');
