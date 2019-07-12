<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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
    static public function get_mime_type(string $file)
    {
        if (! is_file($file)) {
            return null;
        }
        $command = 'file -b --mime-type ' . escapeshellarg($file);
        $result = exec($command);
        return str_replace('\012- ', '', $result);
    }

    static public function getFolderFromFile(string $file)
    {
        $matches = [];
        if (preg_match('/(.*)\/[^\/]+/', $file, $matches)) {
            if (is_dir($matches[1])) {
                return $matches[1];
            }
        }
        return null;
    }

    /**
     * Check for available free space on disk and send mail notifications if any limit is exceeded
     * 
     * @param string $file
     * @return boolean TRUE if no limits are exceeded, FALSE otherwise
     */
    static public function notifyDiskspace(string $file)
    {
        return true;
    }

    static public function file_put_contents(string $filename, string $data, array $flags = null, $context = null) : bool
    {
        $result = false;
        $error = null;
        if (! self::notifyDiskspace($filename)) {
            return false;
        }
        if (! function_exists('file_put_contents')) {
            $hnd = fopen($filename, 'w');
            if ($hnd) {
                $result = fwrite($hnd, $data);
                fclose($hnd);
            }
        } else {
            if (! empty($filename) && ! is_dir($filename) && is_writable(dirname($filename))) {
                $result = file_put_contents($filename, $data, $flags, $context);
            } else {
                $result = false;
                if (empty($filename)) {
                    $error = 'File name has not been specified';
                }
                elseif (is_dir($filename)) {
                    $error = $filename . ' is a directory';
                }
                elseif (! is_writable(dirname($filename))) {
                    $error = 'Directory ' . dirname($filename) . ' is not writable';
                }
            }
        }
        if ($result === false) {
            $backtrace = debug_backtrace();
            Logger::error(sprintf('Error writing in file [inc/fsutils/FsUtils.class.php] script: %s file: %s line: %s file: %s',
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

    static public function mkdir(string $path, int $mode = 0755, bool $recursive = false)
    {
        if (is_dir($path)) {
            return true;
        }
        if ($recursive) {
            if (dirname($path) == $path) {
                return true;
            }
            $matches = [];
            preg_match('/(.*)\/(.*)\/?$/', $path, $matches);
            if (empty($matches[1])) {
                
                // We got the beginning, we go out
                return true;
            }
            return static::mkdir($matches[1], $mode, true) && mkdir($path, $mode);
        }
        return mkdir($path, $mode, $recursive);
    }

    static public function file_get_contents(string $filename, bool $use_include_path = false, $context = null)
    {
        if (! is_file($filename)) {            
            $backtrace = debug_backtrace();
            Logger::error(sprintf('Trying to obtain the content for a nonexistant file [lib/Ximdex/Utils/FsUtils.php]'
                . ' script: %s file: %s line: %s nonexistant_file: %s',
                $_SERVER['SCRIPT_FILENAME'],
                $backtrace[0]['file'],
                $backtrace[0]['line'],
                $filename));
            return false;
        }
        return file_get_contents($filename, $use_include_path, $context);
    }

    static public function get_name(string $file)
    {
        $ext = self::get_extension($file);
        $len_ext = strlen($ext) + 1;
        if (false != $ext) {
            $file = substr($file, 0, -$len_ext);
            return $file;
        }
        return $file;
    }

    static public function get_extension(string $file)
    {
        if (empty($file)) {
            return false;
        }
        $matches = [];
        if (! (preg_match('/\.([^\.]*)$/', $file, $matches) > 0)) {
            return false;
        }
        return isset($matches[1]) ? $matches[1] : false;
    }

    static public function walk_dir(string $path, callable $callback, array $args = null, bool $recursive = true)
    {
        $dh = @opendir($path);
        if (false === $dh) {
            return false;
        }
        while ($file = readdir($dh)) {
            if ('.' == $file || '..' == $file) {
                continue;
            }
            call_user_func($callback, "{$path}/{$file}", $args);
            if (false !== $recursive && is_dir("{$path}/{$file}")) {
                static::walk_dir("{$path}/{$file}", $callback, $args, $recursive);
            }
        }
        closedir($dh);
        return true;
    }

    static public function readFolder(string $path, bool $recursive = true, array $excluded = array())
    {
        if (! is_dir($path)) {
            return null;
        }
        if (! is_array($excluded)) {
            $excluded = array($excluded);
        }
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
    static public function deltree(string $folder)
    {
        $backtrace = debug_backtrace();
        Logger::debug(sprintf('It has been asked to delete recursively a folder [inc/fsutils/FsUtils.class.php]'
            . ' script: %s file: %s line: %s folder: %s',
            $_SERVER['SCRIPT_FILENAME'],
            $backtrace[0]['file'],
            $backtrace[0]['line'],
            $folder));
        if (! is_dir($folder)) {
            Logger::error(sprintf('Error estimating folder %s'), $folder);
            return false;
        }
        if (! ($handler = opendir($folder))) {
            Logger::error(sprintf('It was not possible to open the folder %s %s, %s', $folder, __FILE__, __LINE__));
            return false;
        }
        while ($file = readdir($handler)) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $pathToElement = sprintf('%s/%s', $folder, $file);
            if (is_dir($pathToElement)) {
                if (! static::deltree($pathToElement)) {
                    return false;
                }
            } else {
                static::delete($pathToElement);
            }
        }
        closedir($handler);
        if (rmdir($folder)) {
            return true;
        }
        return false;
    }

    static public function delete(string $file) : bool
    {
        if (! is_file($file)) {
            $backtrace = debug_backtrace();
            Logger::warning(sprintf('It has been asked to delete a nonexistant file %s [inc/fsutils/FsUtils.class.php]'
                . ' script: %s file: %s line: %s',
                $file,
                $_SERVER['SCRIPT_FILENAME'],
                $backtrace[0]['file'],
                $backtrace[0]['line']));
            return false;
        }
        if (! @unlink($file)) {
            $backtrace = debug_backtrace();
            Logger::error(sprintf('It has been asked to delete a file which could not be deleted %s [inc/fsutils/FsUtils.class.php]' 
                . ' script: %s file: %s line: %s',
                $file,
                $_SERVER['SCRIPT_FILENAME'],
                $backtrace[0]['file'],
                $backtrace[0]['line']));
            return false;
        }
        return true;
    }

    static public function getUniqueFile(string $containerFolder, string $sufix = '', string $prefix = '') : string
    {
        do {
            $fileName = Strings::generateUniqueID();
            $tmpFile = sprintf('%s/%s%s%s', $containerFolder, $prefix, $fileName, $sufix);
        } while (is_file($tmpFile));
        Logger::debug("getUniqueFile: return: $fileName | container: $containerFolder");
        return $fileName;
    }

    static public function getUniqueFolder(string $containerFolder, string $sufix = '', string $prefix = '')
    {
        do {
            $tmpFolder = sprintf('%s/%s%s%s/', $containerFolder, $prefix,  Strings::generateRandomChars(8), $sufix);
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
    static public function copy(string $sourceFile, string $destFile, bool $move = false) : bool
    {
        if (!empty($sourceFile) && !empty($destFile)) {     
            $result = copy($sourceFile, $destFile);
            if ($move) {
                if (! @unlink($sourceFile))
                    Logger::warning('Cannot delete the source file: ' . $sourceFile);
            }
        } else {
            $result = false;
        }
        if (! $result) {
            Logger::error(sprintf('An error occurred while trying to copy from %s to %s', $sourceFile, $destFile));
        }
        return $result;
    }

    /**
     * Get the files in the folder (and descendant) with an extension
     * 
     * @param $path string Folder to read
     * @param $extensions array Extension to file
     * @param $recursive boolean Indicate if has to recursive read of path folder
     * @return array Found files.
     */
    static public function getFolderFilesByExtension(string $path, array $extensions = array(), bool $recursive = true)
    {
        if (!is_dir($path)) {
            return null;
        }
        $excluded = array('.', '..', '.svn');
        $files = scandir($path);
        $files = array_values(array_diff($files, $excluded));
        foreach ($files as $key => $file) {
            $dotPos = strrpos($file, '.');
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
     * @param bool $includeHost
     * @return boolean|string
     */
    public static function get_url_path(string $url, bool $includeHost = true)
    {
        $data = @parse_url($url);
        if ($data === false) {
            Logger::error('Cannot load URL path from: ' . $url);
            return false;
        }
        if ($includeHost) {
            $urlPath = $data['scheme'] . '://' . $data['host'];
            if (isset($data['port']) and $data['port']) {
                $urlPath .= ':' . $data['port'];
            }
            $urlPath .= dirname($data['path']) . '/';
        } else {
            $urlPath = $data['path'];
        }
        return $urlPath;
    }
    
    /**
     * Return the file name and the extension of an URL given (only for files with extension) or null otherwise
     * 
     * @param string $url
     * @return boolean|string
     */
    public static function get_url_file(string $url)
    {
        $data = @parse_url($url);
        if ($data === false)
        {
            Logger::error('can not load URL path from: ' . $url);
            return false;
        }
        return basename($data['path']);
    }
    
    /**
     * Return true is the string given is a complete URL (http...) or false is not
     * 
     * @param string $url
     * @return mixed
     */
    public static function is_url(string $url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    static private function computeFolder(string $folder)
    {
        return is_null($folder) ? XIMDEX_ROOT_PATH : $folder;
    }

    static public function disk_total_space(string $unit = 'B', string $directory = null)
    {
        $directory = self::computeFolder($directory);
        $bytes = disk_total_space($directory);
        return self::transformUnits($bytes, $unit);
    }

    static public function disk_free_space(string $unit = 'B', string $directory = null)
    {
        $directory = self::computeFolder($directory);
        $bytes = disk_free_space($directory);
        return self::transformUnits($bytes, $unit);
    }

    static public function transform(string $target = null, string $unit = 'B')
    {
        if (empty($target)) {
            return 0;
        }
        $out = [];
        preg_match('/(\d*)(\w*)/', $target, $out);
        return self::transformToUnits($out[1], $out[2], $unit);
    }

    static public function transformUnits(float $bytes, string $unit)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $count = array_search($unit, $units);
        if ($count === false) {
            Logger::error('trying to transform a value to a non valid unit: ' . $unit);
            return false;
        }
        if ($count === 0) {
            return $bytes;
        }
        $bytes = round($bytes / pow(1024, $count), 2);
        return $bytes;
    }

    static public function transformToUnits(float $target, string $unit_from, string $unit_to)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $ini = array_search($unit_from, $units);
        $end = array_search($unit_to, $units);
        $diff = $ini - $end;
        if ($ini === false || $end === false) {
            Logger::error('trying to transform a value to a non valid unit');
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
    
    public static function str_replace_first(string $from, string $to, string $content) : string
    {
        $from = '/' . preg_quote($from, '/') . '/';
        return preg_replace($from, $to, $content, 1);
    }
    
    /**
     * Check if a given directory path is empty
     * 
     * @param string $dir
     * @return bool
     */
    public static function dir_is_empty(string $dir) : bool
    {
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != '.' && $entry != '..') {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Return true if a given file path exists
     * 
     * @param string $file
     * @return bool
     */
    public static function file_exists(string $file) : bool
    {
        return file_exists($file);
    }
}
