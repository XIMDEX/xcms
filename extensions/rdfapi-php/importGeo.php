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
	<title>Test Store Models in Database</title>
</head>
<body>

<?php
require_once('bootstrap.php');


$base="http://www.w3.org/2003/01/geo/wgs84_pos#";
$memModel = new MemModel();
$memModel->load($base);
$modelURI = "http://www.geonames.org/ontology";
if ($mysqlDb->modelExists($modelURI))
    echo "Model with the same URI: '$modelURI' already exists";
else
    $mysqlDb->putModel($memModel, $modelURI);


$list = $mysqlDb->listModels();

// Show the database contents
foreach ($list as $model) {
   echo "modelURI: " .$model['modelURI'] ."<br>";
   echo "baseURI : " .$model['baseURI'] ."<br><br>";
}


?>
</body>
</html>
