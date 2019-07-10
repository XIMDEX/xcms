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

namespace XimdexApi\core;

use Ximdex\Runtime\ResponseBuilder;

abstract class AbstractAPIAction
{
    const USER_PARAM = 'XimUser';
    
    /**
     * @var bool
     */
    public $secure = false;
    
    /**
     * ResponseBuilder instance
     *
     * @var \Ximdex\Runtime\ResponseBuilder
     */
    protected $responseBuilder;
    
    /**
     * Returns true/false
     * 
     * @return bool
     */
    public function isSecure()
    {
        return $this->secure ;
    }

    /**
     * Default constructor
     * Initializes the ResponseBuilder
     */
    public function __construct()
    {
        $this->responseBuilder = new ResponseBuilder();
    }

    /**
     * Default method of the actions
     * Need to be overridden
     * 
     * @param Request $request
     * @param Response $response
     */
    public abstract function index(Request $request, Response $response);

    /**
     * Sends an error response with the specified status code and message
     * 
     * @param string $message
     * @param int $status_code
     */
    protected function createErrorResponse(string $message, int $status_code = 400)
    {
        $this->responseBuilder->error($message, $status_code);
        $this->responseBuilder->build();
    }

    /**
     * Gets the ResponseBuilder object
     * 
     * @return ResponseBuilder
     */
    public function getResponseBuilder()
    {
        return $this->responseBuilder;
    }

    /**
     * Sets the ResponseBuilder instance to use
     * 
     * @param ResponseBuilder $responseBuilder
     */
    public function setResponseBuilder(ResponseBuilder $responseBuilder)
    {
        $this->responseBuilder = $responseBuilder;
    }
}
