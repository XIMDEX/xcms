<?php
include_once '../../bootstrap.php';

ModulesManager::file('/src/SolrExporter.php', 'XSearch');

$exporter = new SolrExporter();

$exporter->DeleteAll();