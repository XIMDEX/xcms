DROP TABLE `PipeCacheTemplates`;

ALTER TABLE `PipePropertyValues` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
    CHANGE `IdPipeProperty` `IdPipeProperty` INT(11) UNSIGNED NOT NULL, CHANGE `IdPipeCache` `IdPipeCache` INT(11) UNSIGNED NOT NULL;

ALTER TABLE `PipePropertyValues` ENGINE = InnoDB;

ALTER TABLE `PipePropertyValues` ADD CONSTRAINT `PipePropertyValues_PipeCache` FOREIGN KEY (`IdPipeCache`) REFERENCES `PipeCaches`(`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `PipePropertyValues` ADD CONSTRAINT `PipePropertyValues_PipeProperties` FOREIGN KEY (`IdPipeProperty`) 
    REFERENCES `PipeProperties`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `PipeNodeTypes` ENGINE = InnoDB;

ALTER TABLE `PipeNodeTypes` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
    CHANGE `IdPipeline` `IdPipeline` INT(11) UNSIGNED NOT NULL, CHANGE `IdNodeType` `IdNodeType` INT(11) UNSIGNED NOT NULL;

ALTER TABLE `PipeNodeTypes` ADD CONSTRAINT `PipeNodeTypes_NodeTypes` FOREIGN KEY (`IdNodeType`) REFERENCES `NodeTypes`(`IdNodeType`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `PipeNodeTypes` ADD CONSTRAINT `PipeNodeTypes_Pipelines` FOREIGN KEY (`IdPipeline`) REFERENCES `Pipelines`(`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE `Transitions` (
  `id` int(12) UNSIGNED NOT NULL,
  `cacheable` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(50) NOT NULL,
  `viewClass` varchar(50) DEFAULT NULL,
  `previousTransitionId` int(12) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `Transitions` (`id`, `cacheable`, `name`, `viewClass`, `previousTransitionId`) VALUES
	(1, 1, 'ToRenderize', 'NodeToRenderizedContent', NULL),
	(2, 1, 'FromRenderToPreFilter', 'PrefilterMacros', 1),
	(3, 1, 'FromPreFilterToDexT', 'Transformer', 2),
	(4, 0, 'PublishXML', 'FilterMacros', 3),
	(5, 0, 'ToFinal', 'Common', NULL),
	(6, 0, 'EditionToPublication', NULL, NULL),
	(7, 0, 'ToRenderizeXedit', 'NodeToRenderizedContent', NULL),
	(8, 0, 'FromRenderToXedit', 'Xedit', 7),
	(9, 0, 'FromXeditToPreFilter', 'PrefilterMacros', 8),
	(10, 1, 'PrepareHTML', 'PrepareHTML', NULL),
	(11, 0, 'PublishHTML', 'FilterMacros', 10),
	(12, 1, 'PrepareXIF', 'PrepareXIF', 10),
	(13, 0, 'PublishXIF', 'FilterMacros', 12),
	(34, 0, 'Edition_to_Edition', NULL, NULL),
	(35, 0, 'Edition_to_Translating', NULL, NULL),
	(36, 0, 'Translating_to_Review translation', NULL, NULL),
    (37, 0, 'Review translation_to_Publication', NULL, NULL);

ALTER TABLE `Transitions` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `Name` (`name`), ADD KEY `Transitions_Transitions` (`previousTransitionId`);

ALTER TABLE `Transitions` MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

ALTER TABLE `Transitions` ADD CONSTRAINT `Transitions_Transitions` FOREIGN KEY (`previousTransitionId`) REFERENCES `Transitions` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE;

CREATE TABLE `TransitionsCache` (
  `id` int(12) UNSIGNED NOT NULL,
  `versionId` int(12) UNSIGNED NOT NULL,
  `transitionId` int(12) UNSIGNED NOT NULL,
  `file` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `TransitionsCache` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `IdVersion` (`versionId`,`transitionId`), 
    ADD UNIQUE KEY `versionId` (`versionId`,`file`), ADD KEY `transitionId` (`transitionId`);

ALTER TABLE `TransitionsCache` MODIFY `id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `TransitionsCache`
    ADD CONSTRAINT `TransitionsCache_Transitions` FOREIGN KEY (`transitionId`) REFERENCES `Transitions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `TransitionsCache_Versions` FOREIGN KEY (`versionId`) REFERENCES `Versions` (`IdVersion`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE PipePropertyValues DROP FOREIGN KEY PipePropertyValues_PipeCache;

DROP TABLE `PipeCaches`;

ALTER TABLE `NodeProperties` CHANGE `IdNodeProperty` `IdNodeProperty` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT, 
    CHANGE `IdNode` `IdNode` INT(12) UNSIGNED NOT NULL;

ALTER TABLE `NodeProperties` ENGINE = InnoDB;

ALTER TABLE `NodeProperties` ADD CONSTRAINT `NodeProperties_Nodes` FOREIGN KEY (`IdNode`) REFERENCES `Nodes`(`IdNode`) 
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `NodeProperties` CHANGE `Property` `Property` ENUM('Transformer', 'pipeline', 'SchemaType', 'DefaultServerLanguage'
    , 'channel','language') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `TransitionsCache` ADD UNIQUE (`file`);
