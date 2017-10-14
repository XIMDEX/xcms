<?php

use Ximdex\Runtime\Cli\CliParser;



class ximNota extends CliParser  {
	var $_metadata = array(
		array (	'name' => '--command',
				'mandatory' => true,
				'message' => 'Comando a ejecutar (migratepair � publicatepair)',
				'type' => TYPE_STRING),
		array (	'name' => '--ruta_fich_pdf',
				'mandatory' => false,
				'message' => 'Ruta del archivo pdf a importar',
				'type' => TYPE_STRING),
		array (	'name' => '--ruta_fich_xml',
				'mandatory' => false,
				'message' => 'Ruta del archivo xml a importar',
				'type' => TYPE_STRING),
		array (	'name' => '--destino',
				'mandatory' => false,
				'message' => 'Ruta destino del archivo',
				'type' => TYPE_STRING)
	);
}

?>