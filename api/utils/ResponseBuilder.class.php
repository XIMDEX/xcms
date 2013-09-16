<?php

/**
 *  \details &copy; 2013  Open Ximdex Evolution SL [http://www.ximdex.org]
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

/**
 * <p>Builder class to create responses</p>
 * <p>Using fluent API</p>
 */
class ResponseBuilder {
    
    /**
     * <p>The Response instance to be created or used</p>
     */
    
    private $response;
    
    /**
     * <p>The array representation of the response content</p>
     * <p>Usually, it will be:
     *  <ul>
     *      <li>array('error' => 1 , 'message' => 'error_message') if any error occurred</li>
     *      <li>array('error' => 0 , 'data' => ACTION_SPECIFIC_FORMAT) if no error occurred</li>
     *  </ul>
     * </p>
     */
    private $responseArray;
 
    /**
     * <p>Response builder using the response given as parameter or creating a new one</p>
     * @param Response $response 
     */
    public function __construct(Response $response = null) {
        $this->response = $response == NULL ? new Response() : $response;
        $this->responseArray = array('error' => 0 , 'data' => array());
    }
    
    /**
     * <p>Sets an OK (200) as status response code and clear error</p>
     * @return ResponseBuilder
     */
    public function ok() {
        $this->response->header_status(200);
        $this->responseArray['error'] = 0;
        unset($this->responseArray['message']);
        return $this;
    }
    
    /**
     * <p>Sets an error response with the specified message and status code if provided</p>
     * @param string $message the error message
     * @param int $statusErrorCode the error status code or 400 if no status code is provided
     * @return ResponseBuilder 
     */
    public function error($message, $statusErrorCode = 400) {
        $this->response->header_status($statusErrorCode);
        $error = array('error' => 1, 'message' => $message);
        $this->response->setContent($error);
        return $this;
    }
    
    /**
     * <p>Sets the response content</p
     * @param mixed $content the content. It can be a string, array or number
     * @return ResponseBuilder 
     */
    public function content($content) {
        $this->responseArray['data'] = $content;
        $this->response->setContent($this->responseArray);
        return $this;
    }
    
    /**
     * <p>Builds the response and return it</p>
     * @return Response the created or modified response object
     */
    public function build() {
        return $this->response;
    }
    
    /**
     * <p>Sets the Response instance to be used to generate the response</p>
     * @param Response $response the response
     * @return ResponseBuilder
     */
    public function setResponse(Response $response) {
        $this->response = $response;
        return $this;
    }
}

?>
