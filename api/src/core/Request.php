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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

namespace XimdexApi\core;

class Request
{
    private $path;

    public function __construct()
    {
        $this->path = isset($_GET['_action']) ? $_GET['_action'] : "";
        $this->path = trim($this->path, "/");
    }
    
    /**
     * Get a query value from a key
     * 
     * @param string $key
     * @param bool $optional
     * @param string $default
     * @throws APIException
     * @return string
     */
    public function get(string $key, bool $optional = false, string $default = null)
    {
        if (! $optional && ! isset($_GET[$key])) {
            throw new APIException("Key {$key} not found in params", 1);
        }
        if (! isset($_GET[$key])) {
            return $default;
        }
        return $_GET[$key];
    }

    /**
     * Return the current path as a string
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
