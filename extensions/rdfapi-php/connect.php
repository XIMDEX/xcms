<?php
require_once('bootstrap.php');
 
// Create tables for MySQL
$mysqlDb->createTables('MySQL'); 
$mysqlDb->close(); 
