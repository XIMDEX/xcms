<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';

ModulesManager::file('/src/SolrExporter.php', 'XSearch');

$exporter = new SolrExporter();


$command = [
    'add-field' => [
        ["name" => 'idversion', "type" => 'int'],
        ["name" => 'version', "type" => 'int'],
        ["name" => 'subversion', "type" => 'int'],
        ["name" => 'date', "type" => 'int'],
        ["name" => 'idnode', "type" => 'int'],
        ["name" => 'idnodetype', "type" => 'int'],
        ["name" => 'idparent', "type" => 'int'],
        ["name" => 'name', "type" => 'text_es'],
        ["name" => 'path', "type" => 'string'],
        ["name" => 'content', "type" => 'text_en']
    ],
    'add-dynamic-field' => [

        ["name" => '*_metadata_es', "type" => 'text_es'],
        ["name" => '*_metadata_en', "type" => 'text_en']
    ]
];

echo json_encode($command);

print_R( $exporter->launchCommand("/schema", $command) ) ;

