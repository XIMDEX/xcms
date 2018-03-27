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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\Utils;

use Ximdex\Logger;

class FsUtils
{
    /**
     * @param $file
     * @return mixed|null
     */
    static public function get_mime_type($file)
    {
        if (!is_file($file)) {
            return NULL;
        }
        $command = "file -b --mime-type " . escapeshellarg($file);
        $result = exec($command);
        return str_replace('\012- ', '', $result);
    }

    /**
     * @param $file
     * @return null
     */
    static public function getFolderFromFile($file)
    {
        if (preg_match('/(.*)\/[^\/]+/', $file, $matches)) {
            if (is_dir($matches[1])) {
                return $matches[1];
            }
        }
        return null;
    }

    /**
     * Check for available free space on disk and send mail notifications if any limit is exceeded.
     * 
     * @return boolean TRUE if no limits are exceeded, FALSE otherwise.
     * @param $file
     */
    static public function notifyDiskspace($file)
    {
        return true;
    }

    /**
     * @param $filename
     * @param $data
     * @param null $flags
     * @param null $context
     * @return bool
     */
    static public function file_put_contents($filename, $data, $flags = NULL, $context = NULL)
    {
        $result = false;
        $error = null;
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
            if (!empty($filename) && !is_dir($filename) && is_writable(dirname($filename))) {
                $result = file_put_contents($filename, $data, $flags, $context);
            } else {
                $result = false;
                if (empty($filename)) {
                    $error = 'File name has not been specified';
                }
                elseif (is_dir($filename)) {
                    $error = $filename . ' is a directory';
                }
                elseif (!is_writable(dirname($filename))) {
                    $error = 'Directory ' . dirname($filename) . ' is not writable';
                }
            }
        }
        if ($result === false) {
            $backtrace = debug_backtrace();
            Logger::error(sprintf(_("Error writing in file [inc/fsutils/FsUtils.class.php] script: %s file: %s line: %s file: %s"),
                $_SERVER['SCRIPT_FILENAME'],
                $backtrace[0]['file'],
                $backtrace[0]['line'],
                $filename));
            if ($error)
                Logger::error($error);
            return false;
        }
        Logger::debug("file_put_contents: input: $filename");
        return true;
    }

    /**
     * @param $path
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    static public function mkdir($path, $mode = 0755, $recursive = false)
    {
        if (is_dir($path)) {
            return true;
        }
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
        return mkdir($path, $mode, $recursive);
    }

    /**
     * @param $filename
     * @param bool $use_include_path
     * @param null $context
     * @return null|string
     */
    static public function file_get_contents($filename, $use_include_path = false, $context = NULL)
    {
        if (!is_file($filename)) {            
            $backtrace = debug_backtrace();
            Logger::error(sprintf(_('Trying to obtaing the content of a nonexistent file [lib/Ximdex/Utils/FsUtils.php] script: %s file: %s line: %s nonexistent_file: %s'),
                $_SERVER['SCRIPT_FILENAME'],
                $backtrace[0]['file'],
                $backtrace[0]['line'],
                $filename));
            return false;
        }
        return file_get_contents($filename, $use_include_path, $context);
    }

    /**
     * @param $file
     * @return string
     */
    static public function get_name($file)
    {
        $ext = self::get_extension($file);
        $len_ext = strlen($ext) + 1;
        if (false != $ext) {
            $file = substr($file, 0, -$len_ext);
            return $file;
        }
        return $file;
    }

    /**
     * @param $file
     * @return bool
     */
    static public function get_extension($file)
    {
        if (empty($file)) {
            return false;
        }
        if (!(preg_match('/\.([^\.]*)$/', $file, $matches) > 0)) {
            return false;
        }
        return isset($matches[1]) ? $matches[1] : false;
    }

    /**
     * @param $path
     * @param $callback
     * @param null $args
     * @param bool $recursive
     * @return bool
     */
    static public function walk_dir($path, $callback, $args = NULL, $recursive = true)
    {
        $dh = @opendir($path);
        if (false === $dh) {
            return false;
        }
        while ($file = readdir($dh)) {
            if ("." == $file || ".." == $file) {
                continue;
            }
            call_user_func($callback, "{$path}/{$file}", $args);
            if (false !== $recursive && is_dir("{$path}/{$file}")) {
                FsUtils::walk_dir("{$path}/{$file}", $callback, $args, $recursive);
            }
        }
        closedir($dh);
        return true;
    }

    /**
     * @param $path
     * @param bool $recursive
     * @param array $excluded
     * @return array|null
     */
    static public function readFolder($path, $recursive = true, $excluded = array())
    {
        if (!is_dir($path)) {
            return null;
        }
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
    static public function deltree($folder)
    {
        $backtrace = debug_backtrace();
        Logger::debug(sprintf(_('It has been applied to delete recursively a folder [inc/fsutils/FsUtils.class.php] script: %s file: %s line: %s folder: %s'),
            $_SERVER['SCRIPT_FILENAME'],
            $backtrace[0]['file'],
            $backtrace[0]['line'],
            $folder));
        if (!is_dir($folder)) {
            Logger::error(sprintf(_("Error estimating folder %s"), $folder));
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
     * @param $file
     * @return bool
     */
    static public function delete($file)
    {
        if (!is_file($file)) {
            $backtrace = debug_backtrace();
            Logger::debug(sprintf(_('It has been applied to delete a nonexistent file %s [inc/fsutils/FsUtils.class.php] script: %s file: %s line: %s'),
                $file,
                $_SERVER['SCRIPT_FILENAME'],
                $backtrace[0]['file'],
                $backtrace[0]['line']));
            return false;
        }
        if (!@unlink($file)) {
            $backtrace = debug_backtrace();
            Logger::warning(sprintf(_('It has been applied to delete a file which could not been deleted %s [inc/fsutils/FsUtils.class.php] script: %s file: %s line: %s'),
                $file,
                $_SERVER['SCRIPT_FILENAME'],
                $backtrace[0]['file'],
                $backtrace[0]['line']));
            return false;
        }
        return true;
    }

    /**
     * @param $containerFolder
     * @param string $sufix
     * @param string $prefix
     * @return string
     */
    static public function getUniqueFile($containerFolder, $sufix = '', $prefix = '')
    {
        do {
            $fileName = Strings::generateUniqueID();
            $tmpFile = sprintf("%s/%s%s%s", $containerFolder, $prefix, $fileName, $sufix);
        } while (is_file($tmpFile));
        Logger::debug("getUniqueFile: return: $fileName | container: $containerFolder");
        return $fileName;
    }

    /**
     * @param $containerFolder
     * @param string $sufix
     * @param string $prefix
     * @return string
     */
    static public function getUniqueFolder($containerFolder, $sufix = '', $prefix = '')
    {
        do {
            $tmpFolder = sprintf("%s/%s%s%s/", $containerFolder, $prefix,  Strings::generateRandomChars(8), $sufix);
        } while (is_dir($tmpFolder));
        return $tmpFolder;
    }
    
    /**
     * Copy a file to a specified destination file
     * If the parameter $move is true, the original file will be deleted
     * 
     * @param string $sourceFile
     * @param string $destFile
     * @param bool $move
     * @return bool
     */
    static public function copy($sourceFile, $destFile, bool $move = false) : bool
    {
        if (!empty($sourceFile) && !empty($destFile)) {     
            $result = copy($sourceFile, $destFile);
            if ($move)
            {
                if (!@unlink($sourceFile))
                    Logger::warning('Cannot delete the source file: ' . $sourceFile);
            }
        }
        else {
            $result = false;
        }
        if (!$result) {
            Logger::error(sprintf('An error occurred while trying to copy from %s to %s', $sourceFile, $destFile));
        }
        return $result;
    }

    /**
     * Get the files in the folder (and descendant) with an extension.
     * 
     * @param $path string Folder to read
     * @param $extensions array Extension to file
     * @param $recursive boolean Indicate if has to recursive read of path folder
     * @return array Found files.
     */
    static public function getFolderFilesByExtension($path, $extensions = array(), $recursive = true)
    {
        if (!is_dir($path)) {
            return null;
        }
        $excluded = array('.', '..', '.svn');
        $files = scandir($path);
        $files = array_values(array_diff($files, $excluded));
        foreach ($files as $key => $file) {
            $dotPos = strrpos($file, ".");
            $fileExtension = substr($file, $dotPos + 1);
            if (!in_array($fileExtension, $extensions)) {
                unset($files[$key]);
            }
            if ($recursive) {
                $dir = $path . '/' . $file;
                if (is_dir($dir)) {
                    $aux = self::readFolder($dir, $recursive, $excluded);
                    $files = array_merge($files, $aux);
                }
            }
        }
        return array_values($files);
    }
    
    /**
     * Return the complete URL path to the URL parameter given, ending in /
     * 
     * @param string $url
     */
    public static function get_url_path($url)
    {
        $data = @parse_url($url);
        if ($data === false)
        {
            Logger::error('can\'t load URL path from: ' . $url);
            return false;
        }
        $urlPath = $data['scheme'] . '://' . $data['host'];
        if (isset($data['port']) and $data['port']) {
            $urlPath .= ':' . $data['port'];
        }
        $urlPath .= dirname($data['path']) . '/';
        return $urlPath;
    }
    
    /**
     * Return the file name and the extension of an URL given (only for files with extension) or null otherwise
     *  
     * @param string $url
     * @return boolean|string|null
     */
    public static function get_url_file($url)
    {
        $data = @parse_url($url);
        if ($data === false)
        {
            Logger::error('can\'t load URL path from: ' . $url);
            return false;
        }
        return basename($data['path']);
    }
    
    /**
     * Return true is the string given is a complete URL (http...) or false is not
     * @param string $url
     * @return boolean
     */
    public static function is_url($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    static private function computeFolder($folder)
    {
        return is_null($folder) ? XIMDEX_ROOT_PATH : $folder;
    }

    /**
     * @param string $unit
     * @param null $directory
     * @return bool|float
     */
    static public function disk_total_space($unit = 'B', $directory = null)
    {
        $directory = self::computeFolder($directory);
        $bytes = disk_total_space($directory);
        return self::transformUnits($bytes, $unit);
    }

    /**
     * @param string $unit
     * @param null $directory
     * @return bool|float
     */
    static public function disk_free_space($unit = 'B', $directory = null)
    {
        $directory = self::computeFolder($directory);
        $bytes = disk_free_space($directory);
        return self::transformUnits($bytes, $unit);
    }

    /**
     * @param null $target
     * @param string $unit
     * @return bool|float|int
     */
    static public function transform($target = null, $unit = "B")
    {
        if (empty($target)) {
            return 0;
        }
        preg_match('/(\d*)(\w*)/', $target, $out);
        return self::transformToUnits($out[1], $out[2], $unit);
    }

    /**
     * @param $bytes
     * @param $unit
     * @return bool|float
     */
    static public function transformUnits($bytes, $unit)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $count = array_search($unit, $units);
        if ($count === false) {
            Logger::error('It is being tried to transform a value to a invalid units: ' . $unit);
            return false;
        }
        if ($count === 0) {
            return $bytes;
        }
        $bytes = round($bytes / pow(1024, $count), 2);
        return $bytes;
    }

    /**
     * @param $target
     * @param $unit_from
     * @param $unit_to
     * @return bool|float
     */
    static public function transformToUnits($target, $unit_from, $unit_to)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $ini = array_search($unit_from, $units);
        $end = array_search($unit_to, $units);
        $diff = $ini - $end;
        if ($ini === false || $end === false) {
            Logger::error('It is being tried to transform a value to a invalids units');
            return false;
        }
        if ($diff === 0) {
            return $target;
        }
        if ($diff > 0) {
            return round($target * pow(1024, $diff), 2);
        } else {
            $diff = -$diff;
            return round($target / pow(1024, $diff), 2);
        }
    }
}