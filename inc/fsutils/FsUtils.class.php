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



if (!defined("XIMDEX_ROOT_PATH")) {
	define("XIMDEX_ROOT_PATH", realpath(dirname (__FILE__)."/../../"));
}

require_once(XIMDEX_ROOT_PATH . '/inc/log/XMD_log.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/helper/Utils.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/log/MN_log.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/DiskUtils.class.php');

class FsUtils {

	private function __construct() {

	}

	/**
	 *
	 * @param $file
	 * @return unknown_type
	 */
	static public function get_mime_type($file) {

		if (!is_file($file)) {
			return NULL;
		}

		$command = "file -b --mime-type " . escapeshellarg($file);
/* in others systems:
$command = "file -b -i " .escapeshellarg($file)."|cut -d ';' -f 1,1"; */

		$result = exec($command);

		return str_replace('\012- ' , '', $result);
	}

	static public function getFolderFromFile($file) {
		if (preg_match('/(.*)\/[^\/]+/', $file, $matches)) {
			if (is_dir($matches[1])) {
				return $matches[1];
			}
		}
		return null;
	}

	/**
	 * Check for available free space on disk and send mail notifications if any limit is exceeded.
	 * @return boolean TRUE if no limits are exceeded, FALSE otherwise.
	 */
	static public function notifyDiskspace($file) {

		$ret = true;
		$freeSpaceBytes = DiskUtils::disk_free_space('B', self::getFolderFromFile($file));
		$limits = include(XIMDEX_ROOT_PATH . '/conf/diskspace.conf');
		$aux = array();

		foreach ($limits as $key=>$limit) {
			$matches = null;
			$matchesCount = preg_match_all('/(\d+)(KB|MB|GB)?/', $limit, $matches, PREG_SET_ORDER);
			if ($matchesCount > 0) {
				$aux[$key] = array(
					'limit' => (float) $matches[0][1],
					'unit' => $matches[0][2],
					'space' => DiskUtils::transformUnits($freeSpaceBytes, $matches[0][2])
				);
				$aux[$key]['notify'] = ($aux[$key]['space'] <= $aux[$key]['limit']);
			}
		}

		$limits = $aux;
		$msg = sprintf(_('Warning from the server %s (instance %s): The free space in disk is %s MB'),php_uname("n"),Config::getValue('AppRoot'), DiskUtils::transformUnits($freeSpaceBytes, 'MB'));

		if (isset($limits['fatal_limit']) && $limits['fatal_limit']['notify']) {

			$ret = false;
			MN_Log::error($msg);
			XMD_Log::fatal($msg);
		} else if (isset($limits['error_limit']) && $limits['error_limit']['notify']) {

			MN_Log::error($msg);
			XMD_Log::error($msg);
		} else if (isset($limits['warning_limit']) && $limits['warning_limit']['notify']) {

			MN_Log::warning($msg);
			XMD_Log::warning($msg);
		}

		return $ret;
	}

	/**
	 *
	 * @param $filename
	 * @param $data
	 * @param $flags
	 * @param $context
	 * @return unknown_type
	 */
	static public function file_put_contents($filename, $data, $flags = NULL, $context = NULL) {

		if (!self::notifyDiskspace($filename)) {
			return false;
		}

		if (!function_exists('file_put_contents')) {
			$hnd = fopen($filename, "w");

			if ($hnd) {
				$result = fwrite($hnd, $data);
				fclose($hnd);
			}
		} else {
		  if(!empty($filename) ) {
			 $result = file_put_contents($filename, $data, $flags, $context);
		  }else{
			 $result = false;
		  }
		}

		if ($result === false) {
			$backtrace = debug_backtrace();
			XMD_Log::debug(sprintf(_("Error writing in file [inc/fsutils/FsUtils.class.php] script: %s file: %s line: %s file: %s"),
						$_SERVER['SCRIPT_FILENAME'],
						$backtrace[0]['file'],
						$backtrace[0]['line'],
						$filename));
			return false;
		}
		XMD_Log::debug("file_put_contents: input: $filename");

		return true;
	}

	/**
	 *
	 * @param $path
	 * @param $mode
	 * @param $recursive
	 * @param $context
	 * @return unknown_type
	 */
	static public function mkdir($path, $mode = 0755, $recursive = false, $context = NULL) {
		if (!function_exists('mkdir')) {
			// implement a solution.
			return false;
		} else {
			if (is_dir($path)) {
				return true;
			} else {
				if ($recursive) {
					if (dirname($path) == $path) {
						return true;
					}
					preg_match('/(.*)\/(.*)\/?$/', $path, $matches);
					if (empty($matches[1])) { // We got the beginning, we go out
						return true;
					}
					return FsUtils::mkdir($matches[1], $mode, true) && mkdir($path, $mode);
				}
				// PHP4 mode
				return mkdir($path, $mode);
				// PHP5 mode
				//mkdir($path, $mode, $recursive);
			}
		}
	}

	/**
	 *
	 * @param $filename
	 * @param $use_include_path
	 * @param $context
	 * @return unknown_type
	 */
	static public function file_get_contents($filename, $use_include_path = false, $context = NULL) {
		if (!is_file($filename)) {
			$backtrace = debug_backtrace();
			XMD_Log::debug(sprintf(_('Trying to obating the content of a nonexistent file [inc/fsutils/FsUtils.class.php] script: %s file: %s line: %s nonexistent_file: %s'),
						$_SERVER['SCRIPT_FILENAME'],
						$backtrace[0]['file'],
						$backtrace[0]['line'],
						$filename));
			return NULL;
		}

		if (!function_exists('file_get_contents')) {

			// Not evaluating resource context yet.

			if (false === $hnd = fopen($filename, 'rb', $incpath)) {
				// trigger error.
				return false;
			}

			if ($fsize = @filesize($filename)) {
				$data = fread($hnd, $fsize);
			} else {
				$data = '';
				while (!feof($hnd)) {
					$data .= fread($hnd, 8192);
				}
			}

			fclose($hnd);

			return $data;
		}

		// PHP4 format
		//return file_get_contents($filename, $use_include_path);

		// PHP5 format
		return file_get_contents($filename, $use_include_path, $context);
	}

	/**
	 *
	 * @param $file
	 * @return unknown_type
	 */
	static public function get_name($file) {
		$ext = self::get_extension($file);
		$len_ext = strlen($ext)+1;

		if(false!= $ext) {
			$file = substr($file, 0, -$len_ext );
			return $file;
		}
		return $file;
	}

	/**
	 *
	 * @param $file
	 * @return unknown_type
	 */
	static public function get_extension($file) {

		if (empty($file)) {
			return false;
		}

		if (!(preg_match('/\.([^\.]*)$/', $file, $matches) > 0)) {
			return false;
		}

		return isset($matches[1]) ? $matches[1] : false;
	}

	/**
	 *
	 * @param $path
	 * @param $callback
	 * @param $args
	 * @param $recursive
	 * @return unknown_type
	 */
	static public function walk_dir($path, $callback, $args = NULL, $recursive = true) {

		$dh = @opendir($path);

		if(false === $dh) {
			return false;
		}

		while($file = readdir($dh)) {

			if("." == $file || ".." == $file){
				continue;
			}

			call_user_func($callback, "{$path}/{$file}", $args);

			if(false !== $recursive && is_dir( "{$path}/{$file}")) {
				FsUtils::walk_dir("{$path}/{$file}", $callback, $args, $recursive);
			}
		}

		closedir( $dh );

		return true;
	}
		/**
	 *
	 * @param $path
	 * @param $callback
	 * @param $args
	 * @param $recursive
	 * @return unknown_type
	 */
	static public function readFolder($path, $recursive = true, $excluded = array()) {

		if (!is_dir($path)) {
			return null;
		}

//		assert($recursive);
		if (!is_array($excluded)) $excluded = array($excluded);
		$excluded = array_merge(array('.', '..', '.svn'), $excluded);
		$files = scandir($path);
		$files = array_values(array_diff($files, $excluded));

		if ($recursive) {
			foreach ($files as $file) {
				$dir = $path . '/' . $file;
				if (is_dir($dir)) {
					$aux = self::readFolder($dir, $recursive, $excluded);
					$files = array_merge($files, $aux);
				}
			}
		}

		return $files;
	}

	/**
	 * Static function
	 * Function which deletes a folder and all its content (as deltree command of msdos)
	 *
	 * @param string $folder
	 * @return boolean
	 */
	static public function deltree($folder) {
		$backtrace = debug_backtrace();
		XMD_Log::debug(sprintf(_('It has been applied to delete recursively a folder [inc/fsutils/FsUtils.class.php] script: %s file: %s line: %s folder: %s'),
					$_SERVER['SCRIPT_FILENAME'],
					$backtrace[0]['file'],
					$backtrace[0]['line'],
					$folder));

		if (!is_dir($folder)) {
			XMD_Log::error(sprintf(_("Error estimating folder %s"), $folder));
			return false;
		}

		if (!($handler = opendir($folder))) {
			error_log(sprintf(_("It was not possible to open the folder %s %s, %s"), $folder, __FILE__, __LINE__));
			return false;
		}

		while ($file = readdir($handler)) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			$pathToElement = sprintf("%s/%s", $folder, $file);
			if (is_dir($pathToElement)) {
				if (!FsUtils::deltree($pathToElement)) {
					return false;
				}
			} else {
				FsUtils::delete($pathToElement);
			}
		}

		closedir($handler);

		if (rmdir($folder)) {
			return true;
		}
		return false;
	}

	/**
	 *
	 * @param $file
	 * @return unknown_type
	 */
	static public function delete($file) {
		if (!is_file($file)) {
			$backtrace = debug_backtrace();
			XMD_Log::debug(sprintf(_('It has been applied to delete a nonexistent file %s [inc/fsutils/FsUtils.class.php] script: %s file: %s line: %s'),
						$file,
						$_SERVER['SCRIPT_FILENAME'],
						$backtrace[0]['file'],
						$backtrace[0]['line']));
			return false;
		}
		if (!unlink($file)) {
			$backtrace = debug_backtrace();
			XMD_Log::debug(sprintf(_('It has been applied to delete a file which could not been deleted %s [inc/fsutils/FsUtils.class.php] script: %s file: %s line: %s'),
						$file,
						$_SERVER['SCRIPT_FILENAME'],
						$backtrace[0]['file'],
						$backtrace[0]['line']));
			return false;
		}
		return true;
	}

	/**
	 *
	 * @param $containerFolder
	 * @param $sufix
	 * @param $prefix
	 * @return unknown_type
	 */
	static public function getUniqueFile($containerFolder, $sufix = '', $prefix = '') {
/*		tempnam has a bug and even if it receive a folder in the first param, it creates a file in /tmp
		Even, in linux, the environment var tmp has more prevalence than the received as param folder
		if (empty($sufix)) {
			return tempnam($containerFolder, $prefix);
		}
*/
		do {
			//$fileName = Utils::generateRandomChars(8);
			$fileName = Utils::generateUniqueID();
			$tmpFile = sprintf("%s/%s%s%s", $containerFolder, $prefix, $fileName, $sufix);
		} while (is_file($tmpFile));
		XMD_Log::debug("getUniqueFile: return: $fileName | container: $containerFolder");
		return $fileName;
	}

	/**
	 *
	 * @param $containerFolder
	 * @param $sufix
	 * @param $prefix
	 * @return unknown_type
	 */
	static public function getUniqueFolder($containerFolder, $sufix = '', $prefix = '') {
		do {
			$tmpFolder = sprintf("%s/%s%s%s/", $containerFolder, $prefix, Utils::generateRandomChars(8), $sufix);
		} while (is_dir($tmpFolder));
		return $tmpFolder;
	}

	static public function copy($sourceFile, $destFile) {
		if(!empty($sourceFile) && !empty($destFile) ) {
		  $result = copy($sourceFile, $destFile);
		}else {
		  $result = false;
		}

		if (!$result) {
		 		XMD_Log::error(sprintf('An error occurred while trying to copy from %s to %s', $sourceFile, $destFile));
		}
		return $result;
	}

}
?>
