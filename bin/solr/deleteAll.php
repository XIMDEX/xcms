<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';

\Ximdex\Modules\Manager::file('/src/SolrExporter.php', 'XSearch');

$exporter = new SolrExporter();

$exporter->DeleteAll();