<?php
/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */



/**
 * XIMDEX_ROOT_PATH
 */
if (!defined('XIMDEX_ROOT_PATH'))
        define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));
require_once(XIMDEX_ROOT_PATH . '/inc/helper/Messages.class.php');
define('FILE_VERSION','0.1');

class Shell {

	/**
	 *
	 * @var unknown_type
	 */
	var $messages;

	//TODO: Descriptor 2 should be XIMDEX_ROOT_PATH . "/logs/shell_stderr.log" or assigned from construct.
	/**
	 *
	 * @var unknown_type
	 */
	var $descriptors = array (
                        0 => array("pipe", "r"),
                        1 => array("pipe", "w"),
                        2 => array("file", "stderr.log", "a")
	);


	/**
	 * Construct
	 * @param $messages
	 * @return unknown_type
	 */
	function Shell ($messages = NULL) {

		if (isset($messages) && is_null ($messages)) {
			$this->messages = new Messages ();
		} else {
			$this->messages = $messages;
		}
	}

	/**
	 *
	 * @param $cmd
	 * @return unknown_type
	 */
	function exec_proc($cmd) {

		$output = array();

		$proc = proc_open($cmd, $this->descriptors, $pipes);

		// PHP5 nice feature.
		/*
		$proc_status = proc_get_status($proc);
		if( $proc_status['exitcode'] != 0 ) {
		$proc_failed = true;
		}
		*/

		if( is_resource($proc) ) {
			while (!feof($pipes[1])) {
				$line = fgets($pipes[1]);
				$output[] = rtrim($line);
			}
		} else {
			$this->messages->add (_("Error of proc_open"), MSG_TYPE_ERROR);
			return false;
		}

		fclose($pipes[0]);
		fclose($pipes[1]);

		$ret = proc_close($proc);

		$this->messages->add ($ret, MSG_TYPE_NOTICE);

		if ($proc_failed) {
			$ret = shell_exec($cmd);
			$this->messages->add ($ret, MSG_TYPE_NOTICE);
		} else {
			return true;
		}
	}

	/**
	 *
	 * @param $cmd
	 * @param $mayReturnNothing
	 * @return unknown_type
	 */
	function exec($cmd, $mayReturnNothing = false) {

		$output = array ();
		$err = false;

		$c = $cmd;

		// Try to run the command normally
		if ($handle = popen($c, "r")) {
			$firstline = true;
			while (!feof($handle)) {
				$line = fgets($handle);
				if ($firstline && $line == "" && !$mayReturnNothing) {
					$err = true;
				}
				$firstline = false;
				$output[] = rtrim($line);
			}

			pclose($handle);
			if (!$err) {
				return $output;
			}
		}

//		$this->messages->add ($cmd, MSG_TYPE_ERROR);

		// Rerun the command, this time grabbing the error information
		$output = shell_exec($c);
		if (!empty($output)) {
			$this->messages->add ($output, MSG_TYPE_NOTICE);
			return true;
		} else {
			return NULL;
		}
	}


}
?>