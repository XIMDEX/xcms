<?php

define("RDFAPI_INCLUDE_DIR", "./api/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
require_once('../Services_JSON/Services_JSON.class.php');

$database = ModelFactory::getDbStore('Mysql', 'localhost', 'tt', 'root', '12345');

$models = array("http://xmlns.com/foaf/0.1/",
	"http://www.geonames.org/ontology");
$elements = array();
function getURIInfo($model) {
}
foreach($models as $strModel) {
	$dbModel = $database->getModel($strModel);

	if ($dbModel === false) {
	   die('Database does not have a model ' . $strModel . "\n");
	}

	$querystring = '
	PREFIX rdfs:   <http://www.w3.org/2000/01/rdf-schema#>
	PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
	
	SELECT ?subject ?label ?comment
	WHERE { ?subject	rdf:type 	rdfs:Class;
			rdfs:label 	?label; 
			rdfs:comment	?comment }';

	$subQueryString = '
	PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
	SELECT ?subject ?label ?comment
	WHERE { ?subject 	rdfs:domain 	<%s>;
				rdfs:label	?label;
				rdfs:comment	?comment
	}';

	$result = $dbModel->sparqlQuery($querystring);
	foreach($result as $element) {
		$elementNS = $element['?subject']->getURI();
		$elements[$strModel][$elementNS]['info'] = array(
			'label' => $element['?label']->getLabel(), 
			'comment' => $element['?comment']->getLabel());
		
		$elementQuery = sprintf($subQueryString, $elementNS);
		$propertiesResult = $dbModel->sparqlQuery($elementQuery);
		$properties = array();
		foreach ($propertiesResult as $property) {
			$properties[$property['?subject']->getURI()] = array(
				'info' => array(
					'label' => $property['?label']->getLabel(), 
					'comment' => $property['?comment']->getLabel()
				)
			);
		}
		$elements[$strModel][$elementNS]['properties'] = $properties;
	}
} 
$sJson = new Services_JSON();
echo $sJson->encode($elements);
