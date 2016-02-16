<?php
include_once '../../bootstrap/start.php';

ModulesManager::file('/src/SolrExporter.php', 'XSearch');

$exporter = new SolrExporter();

$exporter->ExportAll();