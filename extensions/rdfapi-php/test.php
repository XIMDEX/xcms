<?php
// ----------------------------------------------------------------------------------
// PHP Script: test_StoringModelsInDatabase.php
// ----------------------------------------------------------------------------------

/*
 * This is an online demo of RAP's database backend.
 * It shows how to peristently store rdf models in a database.
 *
 * @version $Id: test_StoringModelsInDatabase.php 268 2006-05-15 05:28:09Z tgauss $
 * @author Radoslaw Oldakowski <radol@gmx.de>
 */
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Test OWL procesing</title>
</head>
<body>

<?php

// Include RAP
define("RDFAPI_INCLUDE_DIR", "./api/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
include(RDFAPI_INCLUDE_DIR . PACKAGE_ONTMODEL);
include(RDFAPI_INCLUDE_DIR . 'infModel/InfModel.php');
include(RDFAPI_INCLUDE_DIR . PACKAGE_INFMODEL);
include(RDFAPI_INCLUDE_DIR . 'ontModel/OWLVocabulary.php');

## 1. Connect to MsAccess database (via ODBC)
## ------------------------------------------

// Connect to MsAccess (rdf_db DSN) database using connection settings
// defined in constants.php :
//$rdf_database = ModelFactory::getDbStore('MySQL', 'localhost', 'rdf', 'root', '12345'); 

//$ontModel = ModelFactory::getOntModel(DBMODEL, RDFS_VOCABULARY, 'http://xmlns.com/foaf/0.1/');
$ontModel = ModelFactory::getOntModel(DBMODEL, RDFS_VOCABULARY, 'http://www.geonames.org/ontology#');
var_dump($ontModel->find(NULL, new Resource('http://www.w3.org/2004/02/skos/core#prefLabel'), NULL));
?>
</body>
</html>
