<?php

if (!defined('XIMDEX_ROOT_PATH')) {
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));
}

require_once XIMDEX_ROOT_PATH . '/inc/cli/CliParser.class.php';
require_once XIMDEX_ROOT_PATH . '/modules/ximNOTA/cli/inc/ximNota.cli.class.php';
require_once XIMDEX_ROOT_PATH . '/modules/ximNOTA/inc/ximNOTAServer.class.php';


$parameterCollector = new ximNota($argc, $argv);


if ($parameterCollector->messages->count(MSG_TYPE_ERROR) > 0) {
        $parameterCollector->messages->displayRaw();
        die();
}

$commands = array(
	'migratePair' => array('ruta_fich_pdf', 'ruta_fich_xml', 'destino'),
	'publicatePair' => array() //TODO
);
$command = $parameterCollector->getParameter('--command');

if (array_key_exists($command, $commands)) {
	foreach ($commands[$command] as $param) {
		$params[$param] = $parameterCollector->getParameter('--' . $param); 
	}
} else {
	echo "Command is not allowed \n";
	die();
}

if (!isset($params['ruta_fich_pdf']) || !isset($params['ruta_fich_xml']) || !isset($params['destino'])) {
	echo "Missing parameters, run with --help \n";
	die();
}

$xns = new ximNOTAServer($command, $params);
$output = $xns->callAction();
echo "\n\n Output: $output \n";


?>
