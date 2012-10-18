<?php
require_once('bootstrap.php');
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
	<title>Test Store Models in Database</title>
</head>
<body>

<?php


// Filename of an RDF document
$base="http://xmlns.com/foaf/spec/index.rdf";

// Create a new memory model
$memModel = new MemModel();

// Load and parse document
$memModel->load($base);

// Now store the model in database

// An unique modelURI will be generated
//$rdf_database->putModel($memModel);

// You can also provide an URI for the model to be stored
$modelURI = "http://xmlns.com/foaf/0.1/";
//$memModel = new MemModel();
// But then you must check if there already is a model with the same modelURI
// otherwise the method putModel() will return FALSE
if ($mysqlDb->modelExists($modelURI))
    echo "Model with the same URI: '$modelURI' already exists";
else
    $mysqlDb->putModel($memModel, $modelURI);


// Get an array with modelURI and baseURI of all models stored in rdf database
$list = $mysqlDb->listModels();

// Show the database contents
foreach ($list as $model) {
   echo "modelURI: " .$model['modelURI'] ."<br>";
   echo "baseURI : " .$model['baseURI'] ."<br><br>";
}


?>
</body>
</html>
