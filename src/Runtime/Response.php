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

namespace Ximdex\Runtime;

use Ximdex\Behaviours\AssociativeArray;

class Response
{
    /**
     * @var \Ximdex\Behaviours\AssociativeArray
     */
    private $_headers;
    
    private $_content;

    /**
     * Response constructor
     */
    public function __construct()
    {
        $this->_headers = new AssociativeArray();
        ob_start();
        foreach ($_SERVER as $key => $value) {
            if (preg_match('/^HTTP_(.*)$/', $key)) {
                $key = str_replace('_', ' ', substr($key, 5));
                $key = str_replace(' ', '-', ucwords(strtolower($key)));
                $this->_headers->add($key, $value);
            }
        }
    }

    public function set(string $key, string $value)
    {
        $this->_headers->set($key, $value);
    }

    public function sendHeaders()
    {
        echo trim(ob_get_clean()); // asegura que no ha habido escritura antes de enviar las cabeceras
        $keys = $this->_headers->getKeys();
        foreach ($keys as $key) {
            $values = $this->get($key);
            if (is_array($values)) {
                foreach ($values as $value) {
                    header($key . ":" . $value);
                }
            } else {
                header($key . ": " . $values);
            }
        }
    }

    public function get(string $key)
    {
        return $this->_headers->get($key);
    }

    public function sendStatus(string $string, string $replace = null, int $status = null)
    {
        echo ob_get_clean(); // asegura que no ha habido escritura antes de enviar las cabeceras
        if (is_numeric($status)) {
            header($string, $replace, $status);
        }
    }

    public function getContent()
    {
        return $this->_content;
    }

    public function setContent(string $content)
    {
        $this->_content = $content;
    }

    /**
     * Sends the header with the specified status code
     * 
     * @param int $statusCode
     */
    public function header_status(int $statusCode)
    {
        static $status_codes = null;
        if ($status_codes === null) {
            $status_codes = array(
                100 => 'Continue',
                101 => 'Switching Protocols',
                102 => 'Processing',
                200 => 'OK',
                201 => 'Created',
                202 => 'Accepted',
                203 => 'Non-Authoritative Information',
                204 => 'No Content',
                205 => 'Reset Content',
                206 => 'Partial Content',
                207 => 'Multi-Status',
                300 => 'Multiple Choices',
                301 => 'Moved Permanently',
                302 => 'Found',
                303 => 'See Other',
                304 => 'Not Modified',
                305 => 'Use Proxy',
                307 => 'Temporary Redirect',
                400 => 'Bad Request',
                401 => 'Unauthorized',
                402 => 'Payment Required',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                406 => 'Not Acceptable',
                407 => 'Proxy Authentication Required',
                408 => 'Request Timeout',
                409 => 'Conflict',
                410 => 'Gone',
                411 => 'Length Required',
                412 => 'Precondition Failed',
                413 => 'Request Entity Too Large',
                414 => 'Request-URI Too Long',
                415 => 'Unsupported Media Type',
                416 => 'Requested Range Not Satisfiable',
                417 => 'Expectation Failed',
                422 => 'Unprocessable Entity',
                423 => 'Locked',
                424 => 'Failed Dependency',
                426 => 'Upgrade Required',
                500 => 'Internal Server Error',
                501 => 'Not Implemented',
                502 => 'Bad Gateway',
                503 => 'Service Unavailable',
                504 => 'Gateway Timeout',
                505 => 'HTTP Version Not Supported',
                506 => 'Variant Also Negotiates',
                507 => 'Insufficient Storage',
                509 => 'Bandwidth Limit Exceeded',
                510 => 'Not Extended'
            );
        }
        if ($status_codes[$statusCode] !== null) {
            $status_string = $statusCode . ' ' . $status_codes[$statusCode];
            header($_SERVER['SERVER_PROTOCOL'] . ' ' . $status_string, true, $statusCode);
        }
    }
}
