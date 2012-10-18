<?php
require_once('bootstrap.php');

$strModel = "http://xmlns.com/foaf/0.1/";
//$strModel = "http://www.geonames.org/ontology#";
$dbModel = $mysqlDb->getModel($strModel);

if ($dbModel === false) {
   die('Database does not have a model ' . $strModel . "\n");
}

//http://www.w3.org/2002/07/owl#Class
$querystring = '
PREFIX rdfs:   <http://www.w3.org/2000/01/rdf-schema#>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
SELECT ?subject, ?label, ?comment
WHERE { ?subject 	
			rdf:type 	rdfs:Class;
			rdfs:label 	?label; 
			rdfs:comment	?comment }';
//rdf:type rdfs:Class;

$subQueryString = '
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
SELECT ?subject
WHERE { ?subject rdfs:domain <%s> }
';

$result = $dbModel->sparqlQuery($querystring);
var_dump($result);
$elements = array();
foreach($result as $element) {
//	var_dump($element);
	$elementNS = $element['?subject']->getURI();
	
	var_dump($elementNS);
	$elementQuery = sprintf($subQueryString, $elementNS);
	$propertiesResult = $dbModel->sparqlQuery($elementQuery);
	$properties = array();
	foreach ($propertiesResult as $property) {
		$properties[] = $property['?subject']->getURI();
	}
	$elements[$elementNS] = $properties;
	
}
var_dump($elements);
 
