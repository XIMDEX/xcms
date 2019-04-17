ALTER TABLE `Servers` ADD `Indexable` BOOLEAN NOT NULL DEFAULT FALSE AFTER `Token`;

ALTER TABLE `Servers` ADD `LastSitemapGenerationTime` INT(12) NULL DEFAULT NULL AFTER `Indexable`;

INSERT INTO `Config` (`IdConfig`, `ConfigKey`, `ConfigValue`, `Description`) VALUES (NULL, 'SitemapInterval', '3600', NULL);

INSERT INTO `Nodes` (`IdNode`, `IdParent`, `IdNodeType`, `Name`, `IdState`, `BlockTime`, `BlockUser`, `CreationDate`, `ModificationDate`
, `Description`, `SharedWorkflow`, `Path`, `ActiveNF`, `sortorder`, `deleted`) 
VALUES (5111, '6', '5007', 'Sitemap', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/Ximdex/Control center/Type of node manager', NULL, '0', '0');

INSERT INTO `NodeTypes` (`IdNodeType`, `Name`, `Class`, `Icon`, `Description`, `IsRenderizable`, `HasFSEntity`, `CanAttachGroups`
, `IsSection`, `IsFolder`, `IsVirtualFolder`, `IsPlainFile`, `IsStructuredDocument`, `IsPublishable`, `IsHidden`, `CanDenyDeletion`
, `isGenerator`, `IsEnriching`, `System`, `Module`, `workflowId`, `HasMetadata`) 
VALUES (5111, 'Sitemap', 'FileNode', 'sitemap_document', 'Sitemap XML', '0', '0', '0', '0', '0', '0', '1', '0', '1', '1', '0'
, '0', '0', '0', NULL, NULL, '0');

INSERT INTO `NodeAllowedContents` (`IdNodeAllowedContent`, `IdNodeType`, `NodeType`, `Amount`) VALUES (NULL, '5014', '5111', '0');
