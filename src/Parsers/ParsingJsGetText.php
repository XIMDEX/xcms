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

namespace Ximdex\Parsers;

use Ximdex\Runtime\App;
use Ximdex\Logger;

class ParsingJsGetText
{
    private $PATH_CACHE;
    private $_default_lang;
    private $_lang;

    // Final file name, cached and gettexted
    private $_file;
    
    // File from which cache is going to be obtained
    private $_file_orig;
    private $_gettext_file;

    function __construct()
    {
        $this->PATH_CACHE = App::getValue('TempRoot') . "/js/";
        
        // Definimos el default lang
        if (\Ximdex\Runtime\Session::get('locale')) {
            $this->setDefaultLang(\Ximdex\Runtime\Session::get('locale'));
        }
        else {
            $this->setDefaultLang(DEFAULT_LOCALE);
        }
    }

    /**
     * Selecting a language by default to get the file translated to this langauge in case of the passed language is not existing
     * 
     * @param $_lang
     */
    public function setDefaultLang($_lang)
    {
        $this->_default_lang = $_lang;

        // Given need permits in case of cache folder has not the correct ones
        @chmod(XIMDEX_ROOT_PATH . $this->PATH_CACHE, 0777);
        
        // Checking if the asociated language folder is ixisting, if not, we create it
        $_pathname = XIMDEX_ROOT_PATH . $this->PATH_CACHE . $this->_default_lang;
        if (!is_dir($_pathname)) {
            @mkdir($_pathname, 0777);
        }
    }

    /**
     * Selecting the language in which gettext the file
     * 
     * @param $_lang
     */
    public function setLang($_lang = null)
    {
        $this->_lang = ($_lang) ? $_lang : $this->_default_lang;
        $_pathname = XIMDEX_ROOT_PATH . $this->PATH_CACHE . $this->_lang;
        if (!is_dir($_pathname)) {
            @mkdir($_pathname, 0777);
        }
    }

    /**
     * Selecting the file where apply gettext
     * 
     * @param $_js
     * @return bool
     */
    public function setFile($_js = null)
    {
        if ($_js != null) {
            $info = pathinfo($_js);
            if (!isset($info['extension']) or !$info['extension']) {
                return true;
            }
            if (!file_exists(XIMDEX_ROOT_PATH . '/' . $_js)) {
                Logger::error('The file ' . $_js . ' could not be included because of it is not existing in the path: ' . XIMDEX_ROOT_PATH . $_js);
                return false;
            }
            $this->_file_orig = $_js;
            
            // Deleting first "/", if it has it
            if ($_js[0] == '/') $_js = substr($_js, 1);

            // Putting path with "_"
            $_js = str_replace("/", "_", $_js);
            $_js = str_replace("\\", "_", $_js);
            $_js = str_replace(".", "_", $_js);
            $this->_file = \Ximdex\Utils\Strings::convertText($_js) . ".js";
        }
        return true;
    }

    /**
     * Quick and unified way to manage params in some of this class methods
     * 
     * @param $_lang
     * @param $_js
     */
    public function setParam($_lang = null, $_js = null)
    {
        if ($_js != NULL) {
            $this->setFile($_js);
        }
        $this->_lang = ($_lang) ? $_lang : $this->_lang;
        $this->_lang = ($this->_lang) ? $this->_lang : $this->_default_lang;
    }

    /**
     * Gettexting the file and returning the path to final gettexted and cached js file
     * 
     * @param $_js
     * @param $_lang
     * @return NULL|string
     */
    public function process($_js = null, $_lang = null)
    {
        $this->setParam($_lang, $_js);

        // If there is not source file we quit
        if ($this->_file_orig == null) {
            Logger::warning("ERROR, file_orig not stablished");
            return null;
        }
        
        // Opening the source file to start to gettext it
        $file_in = @fopen(XIMDEX_ROOT_PATH . $this->_file_orig, "r");
        if (!$file_in) {
            Logger::warning("ERROR, the file " . XIMDEX_ROOT_PATH . $this->_file_orig . " could not be opened");
            return null;
        }
        
        // Opening the destiny file to start to create it
        $file_out = @fopen(XIMDEX_ROOT_PATH . $this->PATH_CACHE . $this->_lang . "/" . $this->_file, "w");
        if (!$file_out) {
            Logger::error('You have not permits, or the language directory is not existing. Review permits in \'data/tmp/js\'. Error opening the file '
                . XIMDEX_ROOT_PATH . $this->PATH_CACHE . $this->_lang . "/" . $this->_file);
            return null;
        }
        Logger::debug("Caching: " . XIMDEX_ROOT_PATH . $this->_file_orig . " --> " . XIMDEX_ROOT_PATH . $this->PATH_CACHE . $this->_lang . "/" 
                . $this->_file);
        if ($file_in && $file_out) {
            while (!feof($file_in)) {
                $content = fgets($file_in);
                $content = $this->parseContent($content);
                fputs($file_out, $content);
            }
            fclose($file_in);
            fclose($file_out);
            Logger::debug('Js Cache generated ' . App::getValue('UrlRoot') . $this->PATH_CACHE . $this->_lang . "/" . $this->_file);
            return App::getXimdexUrl( $this->PATH_CACHE . $this->_lang . "/" . $this->_file );
        }
    }

    /**
     * Obtaining the name and path to the (selected) file gettexted to the specified language (if it does not exist 
     * for selected language, in the language by default
     * 
     * @param $_js
     * @param $_lang
     * @return string|NULL
     */
    public function getFile($_js = null, $_lang = null)
    {
        static $no_cacheable = null;
        if (!isset($no_cacheable) ) {
            $no_cacheable = array(App::getUrl("/assets/js/"), App::getUrl('/vendors/') );
        }
        $this->setParam($_lang, $_js);
        foreach ($no_cacheable as $n_c) {
            if (!substr_compare($_js, $n_c, 0, strlen($n_c))) {
                $no_cached_url = $_js;
                return $no_cached_url;
            }
        }
        if (!is_file(XIMDEX_ROOT_PATH . $_js)) { // dinamic call
            $no_cached_url =   $_js;
            return $no_cached_url;
        }

        // Checking if the file gettexted for the specificed langauge is existing
        if ($this->fileExists()) {
            
            // If it exists, returning its url
            return App::getXimdexUrl($this->PATH_CACHE . $this->_lang . "/" . $this->_file);
        }

        // If it does not exist, sending the file to parsing and returning
        return $this->process();
    }

    /**
     * Checking the existence of a gettexted for a specified langauge js file
     * 
     * @param $_lang
     * @param $_js
     * @return boolean
     */
    public function fileExists($_lang = null, $_js = null)
    {
        $this->setParam($_lang, $_js);
        $nombre_file_cacheado = XIMDEX_ROOT_PATH . $this->PATH_CACHE . $this->_lang . "/" . $this->_file;

        // If the cached file exists, we start checking dates
        if (file_exists($nombre_file_cacheado)) {
            
            // Checking that cached file modification date is later than the original file one
            $date_file_cache = filectime($nombre_file_cacheado);
            $date_file_nuevo = filectime(XIMDEX_ROOT_PATH . $this->_file_orig);
            $diff = $date_file_nuevo - $date_file_cache;
            if ($diff > 0) {
                @unlink($nombre_file_cacheado);
                return false;
            }
            return true;
        }
        return false;
    }

    public function getTextArrayOfJs($_arrayjs, $_lang = null)
    {
        $_files = Array();
        if (count($_arrayjs) > 0) {
            foreach ($_arrayjs as $_js) {
                $_file = $this->getFile($_js, $_lang);
                if ($_file) {
                    $_files[] = $_file;
                }
            }
        }
        return $_files;
    }

    public static function parseContent($content)
    {
        $patron = '/_\(\s*([\'"])(.*)(?<!\\\\)\1\s*(\\/[*](.*)[*]\\/)?\s*\)/Usi';
        $content = preg_replace_callback($patron,
            create_function('$coincidencias', '$_out = null; eval(\'$_out = \'.$coincidencias[0].";"); return \'"\'.$_out.\'"\';'),
            $content);
        return $content;
    }
}