<?php

/**
 *  Logger para el updater
 *
 *  @author Jose Manuel Gomez <josemanuel.gomez@ximetrix.com>
 *  @version $Revision$
 *
 *	Extiende la clase Loggeable y le solicita el updatedb logger.
 *
 *  $Id$
 */

if (!defined("XIMDEX_ROOT_PATH")) {
	define ("XIMDEX_ROOT_PATH", realpath(dirname(__FILE__)."/../../../"));
}

require_once(XIMDEX_ROOT_PATH."/inc/log/Loggeable.class.php" );

class UpdateDb_log extends Loggeable {
	public static 	function DB_Log() {
		die("Use DB_Log::error() instead; Construction not allowed!");
	}

	public static function write($msg, $level=LOGGER_LEVEL_INFO) {
		parent::write($msg, "updatedb_logger", $level);
	}

	public static 	function debug($msg) {
		parent::debug($msg, "updatedb_logger");
	}

	public static 	function info($msg) {
		parent::info($msg, "updatedb_logger");
	}

	public static 	function warning($msg) {
		parent::warning($msg, "updatedb_logger");
	}

	public static 	function error($msg) {
		parent::error($msg, "updatedb_logger");
	}

	public function fatal($msg) {
		parent::fatal($msg, "updatedb_logger");
	}
}
?>
