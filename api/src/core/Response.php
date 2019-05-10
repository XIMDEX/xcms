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

class Response
{
    private $status = 0;
    
    private $response;
    
    private $message = '';

    /**
     * Sets the status code
     * 
     * @param int $status
     * @return \XimdexApi\core\Response
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Sets the message
     * 
     * @param string $message
     * @return \XimdexApi\core\Response
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Sets the response
     * 
     * @param mixed $response
     * @return \XimdexApi\core\Response
     */
    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Sends reponse and exists
	 *
     * @param array $headers
     * @return string
     */
    public function send(array $headers = null)
    {
        $oldErrorReporting = error_reporting();
        error_reporting($oldErrorReporting ^ E_WARNING);
        if (! is_null($headers)) {
            foreach ($headers as $key => $value) {
                header($key . ":" . $value);
            }
            echo $this->response;
        } else {
            $data = [
                'status' => $this->status,
                'message' => $this->message,
                'response' => $this->response,
            ];

            // TODO Check CORS and filters
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Headers: Authorization");
            header("Access-Control-Allow-Credentials: true");
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        error_reporting($oldErrorReporting);
        exit();
    }
}
