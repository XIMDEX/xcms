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
 *  
 */
class Mutex {

	private $file_name;
	private $file_ptr;
	private $is_acquired;

	public function __construct($file_name = NULL) {

		if (is_null($file_name)) {
			return NULL;
		}
		
		$this->file_name = $file_name;
	}

	private function lock() {

		if (flock($this->file_ptr, LOCK_EX+LOCK_NB)) {
                        fwrite($this->file_ptr, posix_getpid() . "\n");
                        flush($this->file_ptr);
                        fclose($this->file_ptr);
                        $this->is_acquired = true;
                        return true;
                } 
		return false;
	}

	public function acquire() {

                // detect if old process/ is dead
		$old_pid = NULL;
		if (is_file($this->file_name)) {
			$old_pid = file_get_contents($this->file_name);
		}

		if (!empty($old_pid) && posix_kill(trim($old_pid), 0)) {
			//old process is alive.
			$this->is_acquired = false;
			return false;
		} else {
			//old process is dead.
			if (($handler = fopen($this->file_name, "w+")) == false) {
				fclose($handler);
				$this->is_acquired = false;
				return false;
			}
	
			$this->file_ptr = $handler;
	
			if ($this->lock()) {
				return true;
			}
	
			$this->is_acquired = false;
			return false;
		}
	}

	public function release() {

		if (!$this->is_acquired) {
			return true;
		}
                
		if (($this->file_ptr = fopen($this->file_name, "w+")) == false) {
			fclose($this->file_ptr);
			return false;
		}

		if (flock($this->file_ptr, LOCK_UN) == false) {
			return false;
		}
		
		fclose($this->file_ptr);

		// Erase all content
		$this->file_ptr = fopen($this->file_name, "w"); 
		fclose($this->file_ptr);

		return true;
	}

	
}

?>
