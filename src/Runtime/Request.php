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


namespace Ximdex\Runtime;

use Ximdex\Behaviours\AssociativeArray;


/**
 *
 * @brief Http request parameters container
 *
 * This class is intended to store the request parameters
 *
 */

/**
 * Class Request
 * @package Ximdex\Runtime
 */
class Request
{

    /**
     * @var AssociativeArray
     */
    var $params;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->params = new  AssociativeArray();
    }

    /**
     * @param $name
     * @return null
     */
    public static function get($name)
    {

        return isset($_GET[$name]) ? $_GET[$name] : NULL;
    }

    /**
     * @param $name
     * @return null
     */
    public static function post($name)
    {

        return isset($_POST[$name]) ? $_POST[$name] : NULL;
    }

    /**
     * @param $name
     * @return null
     */
    public static function request($name)
    {

        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : NULL;
    }

    /**
     * @param $key
     * @param $value
     * @param string $defValue
     */
    function add($key, $value, $defValue = "")
    {

        $value = isset ($value) ? $value : $defValue;
        $this->params->add($key, $value);
    }

    /**
     * @param $vars
     */
    function setParameters($vars)
    {
        if (!empty($vars) > 0) {
            foreach ($vars as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $this->setParam($key, $value);
                } else {
                    $this->setParam($key, trim($value));
                }
            }
        }
    }

    /**
     * @param $key
     * @param $value
     * @param string $defValue
     */
    function setParam($key, $value, $defValue = "")
    {

        $value = isset ($value) ? $value : $defValue;

        $this->params->set($key, $value);
    }

    /**
     * @param $key
     * @return mixed
     */
    function & getParam($key)
    {
        return $this->params->get($key);
    }

    /**
     * @return array
     */
    function &  getRequests()
    {
        return $this->params->getArray();
    }

    /**
     * @return bool
     */
    function isGet()
    {
        return (!empty($_GET));
    }


    // Transitional methods. You MUST use Request object returned from ApplicationController.

    /**
     * @return bool
     */
    function isPost()
    {
        return (!empty($_POST));
    }

    /**
     * @return bool
     */
    function isCookie()
    {
        return (!empty($_COOKIE));
    }

    /**
     * @return bool
     */
    function isFile()
    {
        return (!empty($_FILES));
    }


}