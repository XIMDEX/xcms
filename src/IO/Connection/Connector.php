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

namespace Ximdex\IO\Connection;

use Ximdex\Models\Server;

class Connector
{
    protected $isFile = false;
    protected $server;
    protected $error;
    protected $code;
    protected $type;
    const TYPE_API = 'API';
    
    public function __construct(Server $server = null)
    {
        $this->server = $server;
    }

    public function getError() : ?string
    {
        return $this->error;
    }

    public function setError(string $error = null) : void
    {
        $this->error = $error;
    }

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
    
    public function getCode() : ?int
    {
        return $this->code;
    }
}
