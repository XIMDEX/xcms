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



class PIDFile {

	protected $pidfile = null;

	public function __construct($pidfile) {
		$this->pidfile = $pidfile;
	}

	public function getPID() {
		$pid = null;
		if (is_file($this->pidfile)) {
			$pid = trim(@file_get_contents($this->pidfile, false));
			if (!is_numeric($pid)) $pid = null;
		}
		return $pid;
	}

	public function checkServer() {

		$ret = false;
		$pid = $this->getPID();

		if (is_null($pid)) return $ret;

		$ret = posix_kill($pid, 0);
		return $ret;
	}

	public function create($pid=null) {

		$ret = false;
		if ($this->checkServer()) {
			// Server running
			return $ret;
		}

		if ($pid === null) $pid = posix_getpid();

		if ($fp = fopen($this->pidfile, 'w')) {
			$ret = (boolean) fwrite($fp, $pid);
			fclose($fp);
		}

		return $ret;
	}

	public function delete() {

		$ret = false;
		if (file_exists($this->pidfile)) {
			$ret = unlink($this->pidfile);
		}
		return $ret;
	}

}

?>
