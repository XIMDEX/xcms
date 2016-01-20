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
use  Ximdex\Utils\Messages;

define('UNZIP', 'unzip %s -d %s');

/**
 * Class to compress and decompress files through the linux tar command
 *
 */
class ZipArchiver
{

    /**
     * File we are working with
     *
     * @var string
     */
    var $fileName = '';
    /**
     *
     * @var string
     */
    var $extension = 'tar';
    /**
     * Options which affects to tar creation
     *
     * @var array
     */
    var $options = null;
    /**
     * Array which contains the list of files which the tar contains in case of edition, files to insert in case of creation
     *
     * @var array
     */
    var $files = null;
    /**
     * Container of messages returned by the class
     *
     * @var $messages Messages
     */
    var $messages = null;


    /**
     * Class Construct
     *
     * @param string $fileName
     * @param array $options
     */
    public function __construct($fileName = '', $options = NULL)
    {
        $this->fileName = $fileName;
    }

    /**
     * File which adds files to the tar list
     *
     * @param string /array $elements element/s to add
     */
    function addEntity($elements)
    {

    }

    /**
     * Funtion to compress files
     *
     */
    /**
     * @param string $dirName
     */
    function pack($dirName = '')
    {

    }

    /**
     * Funtion to decompress files
     *
     * @param string $dest
     * @return boolean
     */
    function unpack($dest = '')
    {
        $messages = new \Ximdex\Utils\Messages();
        if (!is_file($this->fileName)) {
            $messages->add(_('Specified file could not be found'), MSG_TYPE_ERROR);
            return false;
        }

        $command = sprintf(UNZIP, $this->fileName, $dest);

        exec($command);

        return true;
    }
}