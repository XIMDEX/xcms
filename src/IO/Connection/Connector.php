<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

namespace Ximdex\IO\Connection;

use Ximdex\Models\Server;

class Connector
{
    protected $isFile = false;
    protected $server;
    protected $error;
    protected $type;
    const TYPE_API = 'API';
    
    /**
     * @param string $type
     * @param Server $server
     */
    public function __construct(Server $server = null)
    {
        $this->server = $server;
        $this->error = null;
        $this->type = null;
    }
    
    /**
     * @return string|NULL
     */
    public function getError() : ?string
    {
        return $this->error;
    }
    
    /**
     * @param string $error
     */
    public function setError(?string $error) : void
    {
        $this->error = $error;
    }
    
    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }
    
    /**
     * @param string $type
     */
    public function setType(string $type) : void
    {
        $this->type = $type;
    }
    
    public function setIsFile(bool $isFile) : void
    {
        $this->isFile = $isFile;
    }
    
    public function getServer() : ?Server
    {
        return $this->server;
    }
}
