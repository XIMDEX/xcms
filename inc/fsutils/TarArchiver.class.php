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
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../"));

require_once(XIMDEX_ROOT_PATH . '/inc/helper/Messages.class.php');
/**
 * Constant definition
 *
 */

define('TAR_COMPRESSION', 'compression');
	define('TAR_COMPRESSION_BZIP2', '--bzip2');
	define('TAR_COMPRESSION_GZIP', '--gzip');
	
define('TAR_TYPE', 'type');
	define('TAR_TYPE_FULL', '');
	define('TAR_TYPE_INCREMENTAL', '--listed-incremental');

define ('COPY', 0);
define ('NO_COPY', 1);


/**
* Class to compress and decompress files using the linux command tar
*
*/
class TarArchiver {
	/**
	 * File we are working with
	 *
	 * @var string
	 */
	var $fileName = '';
	/**
	 * 
	 * @var unknown_type
	 */
	var $extension = 'tar';
	/**
	 * Options which affects to the tar creation
	 *
	 * @var array
	 */
	var $options = NULL;
	/**
	 * Array which contains a file list which contain the tar in case of edition, files to insert in case of creation
	 *
	 * @var array
	 */
	var $files = NULL;

	/**
	 * Container of message returned by the class
	 *
	 * @var $messages Messages
	 */
	var $messages = NULL;
	/**
	 * Class Construct
	 *
	 * @param string $fileName
	 * @param array $options
	 * @return TarArchiver
	 */
	function TarArchiver($fileName = '', $options = NULL) {
		$this->messages = new Messages();
		$validCompressions = array(TAR_COMPRESSION_BZIP2, TAR_COMPRESSION_GZIP);
		$validTypes = array(TAR_TYPE_FULL, TAR_TYPE_INCREMENTAL);
		
		$this->fileName = $fileName;
		$this->files = $this->_getFiles();
		
		if (!is_array($options)) {
			$this->options = array();
			return ;
		}
		
		$this->extension = 'tar';
		reset($options);
		while(list($type, $option) = each($options)) {
			if ($type == TAR_COMPRESSION) {
				if (in_array($option, $validCompressions)) {
					$this->options[$type] = $option;
					if ($this->options[$type] == TAR_COMPRESSION_BZIP2) {
						$this->extension = 'tar.bz2';
					} else {
						$this->extension = 'tar.gz';
					}
				} else {
					$this->messages->add(sprintf(_("The compression option %s is not valid"), $type), MSG_TYPE_WARNING);
				}
			}
			if ($type == TAR_TYPE) {
				if (in_array($option, $validTypes)) {
					$this->options[$type] = $option;
				} else {
					$this->messages->add(sprintf(_("The file type %s is not valid, a complete copy will be used by default."), $type), MSG_TYPE_WARNING);
				}
			}
		}
		
	}
		
	/**
	 * 
	 * @return unknown_type
	 */
	function _getFiles() {

		if (!is_file($this->fileName.$this->extension)) return array();
			
		$files = array();
		exec(sprintf('tar -tf %s', $this->fileName.$this->extension), $tarContents);
		reset($tarContents);
		while(list(, $line) = each($tarContents)) {
			if (!in_array($line, $files)) $files[] = $line;
		}

		return $files;
	}
		
	/**
	 * Adding files to the tar list
	 *
	 * @param string/array $elements element/s which are going to be added
	 */
	function addEntity($elements) {

		if (!is_array($elements)) $elements = array($elements);
			
		// List of elements which are going to be aded to a tar (new or added)
		reset($elements);
		while (list(, $element) = each($elements)) {
			if (in_array(TAR_TYPE_INCREMENTAL, $this->options)) {
				if (is_dir($element)) {
					$this->files[] = $element;
				} else {
					$this->messages->add(sprintf(_("In incremental copies just folders are allowed, the element %s will be ignored"), $element), MSG_TYPE_WARNING);
				}
			} else {
				if (is_file($element) && !in_array($element, $this->files)) {
					$this->files[] = $element;
				}
			}
		}
	}
		
	/**
	 * Function to compress files
	 *
	 */ 
 	function pack($dirName = '', $tarDirName = '') {

		if (!empty($dirName) && !is_dir($dirName)) {
			$command = sprintf("mkdir %s", $dirName);
			exec ($command);
		}
		
		$mode = is_dir($dirName) ? COPY : NO_COPY;
		
		if ($mode == COPY) {
			if (!is_dir($dirName)) {
				if (!mkdir($dirName)) {
					$this->messages->add(sprintf(_("Temporal folder %s could not been created"), $dirName), MSG_TYPE_ERROR);
					return false;
				}
			}
			
			reset($this->files);
			while (list(, $file) = each($this->files)) {
				// It can be done through php copy
				$command = sprintf("cp %s %s", $file, $dirName);
				exec ($command);
			}
			$fileString = '.';
			$options[] = sprintf('--directory=%s', $dirName);
		} elseif ($mode == NO_COPY) {
			$fileString = implode(' ', $this->files);
		}
		
		$options = array();
		// If compression is incremental
		if (isset($this->options[TAR_TYPE]) 
			&& ($this->options[TAR_TYPE] == TAR_TYPE_INCREMENTAL)) {
			$pathInfo = pathinfo($this->fileName);
			$baseName = $pathInfo['basename'];
			$dirName = $pathInfo['dirname'];
			$i = 0;
			do {
				$completePath = sprintf("%s/%s_%'05d.%s", $dirName, $baseName, $i, $this->extension);
				$i++;
			} while(is_file($completePath));
			
			$options[] = '--create';
			$options[] = sprintf('%s=%s.TarArchiver.control',TAR_TYPE_INCREMENTAL, $this->fileName);
		} else {
			$completePath = sprintf('%s/%s.%s', $tarDirName, $this->fileName, $this->extension);
			if (is_file($completePath)) {
				$options[] = '--update';
			} else {
				$options[] = '--create';
			}
			if (!empty($dirName)) {
				$options[] = sprintf('--directory=%s', $dirName);
			}
		}
		
		if (isset($this->options[TAR_COMPRESSION])) {
			$options[] = $this->options[TAR_COMPRESSION];
		}
		
		$options[] = '--preserve-permissions';
		$options[] = sprintf('--file=%s', $completePath);
		
		$options = implode(' ', $options);
		$command = sprintf('tar %s %s', $options, $fileString);
		exec ($command, $result);
		return $completePath;
	}
		
	/**
	 * 
	 * @param $dest
	 * @return unknown_type
	 */
	function unpack($dest = '') {

		//We try to create destiny folder if it does not exist
		if (!is_dir($dest) && !empty($dest)) {
			if (!mkdir($dest, 0755)) {
				return false;
			}
		}
		
		$options = array();
		// If compression is incremental
		if (isset($this->options[TAR_TYPE]) 
			&& ($this->options[TAR_TYPE] == TAR_TYPE_INCREMENTAL)) {
			$pathInfo = pathinfo($this->fileName);
			$baseName = $pathInfo['basename'];
			$dirName = $pathInfo['dirname'];
			$i = 0;
			do {
				$completePath = sprintf("%s/%s_%'05d.%s", $dirName, $baseName, $i, $this->extension);
				$result = is_file($completePath);
				if (!$result) {
					$i--;
					$completePath = sprintf("%s/%s_%'05d.%s", $dirName, $baseName, $i, $this->extension);
				} else {
					$i++;
				}
			} while($result);
			
			$options[] = '--incremental';
		} else {
			// TODO  Workaround until solving the extension problem
			$completePath = is_file($this->fileName) ? $this->fileName : $this->fileName . $this->extension;
		}
		
		if (isset($this->options[TAR_COMPRESSION])) {
			$options[] = $this->options[TAR_COMPRESSION];
		}
		
		$options[] = '--extract';
		$options[] = '--preserve-permissions';
		$options[] = sprintf('--file=%s', $completePath);
		
		$options = implode(' ', $options);
		if (!empty($dest)) {
			$command = sprintf('tar %s -C %s', $options, $dest);
		} else {
			$command = sprintf('tar %s', $options);
		}
		exec ($command, $result);
		return true;
	}
}
?>
