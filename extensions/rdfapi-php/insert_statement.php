<?php
require_once('bootstrap.php');

$model = ModelFactory::getDbModel($mysqlDb); 
$statement = new Statement(new Resource('http://test.com/name'), new Resource('http://test.com/relation'), new Literal('value for testing'));

$model->add($statement);
