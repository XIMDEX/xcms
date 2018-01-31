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

class Factory
{

    private $_path = null;
    private $_root_name = null;
    private $_error = null;

    /**
     * @param $path string
     * @param $root_name string
     */
    public function __construct($path, $root_name)
    {
        $this->_path = rtrim($path, '/');
        $this->_root_name = $root_name;

    }
    /**
     * Return an instance of $type Class or null if:
     *
     *  - the file is not found
     *  - the file don't contains the class $type
     *
     * @param string $type
     * @param array $args
     * @return mixed
     */
    public function instantiate($type = NULL, $args = null, $namespace = '\Ximdex\MVC\Render' )
    {

        $classname = ltrim($this->_root_name,'\\');
        $namespace = trim($namespace,'\\');

        if (!is_null($type)) {
            $classname .= $type;
        }

        $class =  '\\'. $classname;
        if(!empty($namespace)) {
            $nsClass =  '\\'.$namespace  .$class;

            if ( class_exists( $nsClass )) {
                return new $nsClass( $args ) ;
            }
        }

        if (!class_exists($class)) {
            $old_class_path = $this->_path . "/$classname.class.php";
            $class_path = $this->_path . "/$classname.php";


            if (file_exists($old_class_path) && is_readable($old_class_path)) {
                require_once($old_class_path);
            } else if (file_exists($class_path) && is_readable($class_path)) {
                require_once($class_path);
            } else {
                $this->_setError("Factory::instantiate(): Unable to read $class_path ");
                Logger::error("Factory::instantiate(): Unable to read $class_path ");
                return NULL;
            }
        }
        if (!class_exists($class)) {
            $this->_setError("Factory::instantiate(): '$class' class not found in file $class_path");
            return NULL;
        }


        if ( is_null( $args) ) {
            $obj = new $class();
        } else {
            $obj = new $class($args);
        }



        if (!is_object($obj)) {
            Logger::fatal("Could'nt instanciate the class $class");
            return null ;
        }
        return $obj;
    }

    private function _setError($msg)
    {
        Logger::error($msg);
        $this->_error = $msg;
    }

    /**
     * Returns the last error message
     *
     * @return string
     */

    public function getError()
    {
        return $this->_error;
    }

}
