<?php

// Include RAP
define("RDFAPI_INCLUDE_DIR", "./api/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");

// Connect to MySQL database with user defined connection settings
$mysqlDb = ModelFactory::getDbStore('MySQL', 'localhost', 'tt', 'root', '12345');
?>
