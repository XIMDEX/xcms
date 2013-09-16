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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */
if (!defined('XIMDEX_ROOT_PATH')) {
    define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}
require_once(XIMDEX_ROOT_PATH . '/inc/lang/AssociativeArray.class.php');

/**
 *
 * @brief Http response parameters container
 *
 * This class is intended to store the response parameters and send back them to the server
 *
 */
class Response {

    private $_headers;
    private $_content;

    function __construct() {

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

    /**
     * A�adimos un valor a un array
     * @param $key
     * @param $value
     * @return unknown_type
     */
    public function set($key, $value) {
        $this->_headers->set($key, $value);
    }

    /**
     *
     * @param $key
     * @return unknown_type
     */
    public function get($key) {
        return $this->_headers->get($key);
    }

    /**
     *
     * @return unknown_type
     */
    public function sendHeaders() {
        echo ob_get_clean(); // asegura que no ha habido escritura antes de enviar las cabeceras
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

    public function sendStatus($string, $replace, $status) {
        echo ob_get_clean(); // asegura que no ha habido escritura antes de enviar las cabeceras

        if (is_numeric($status)) {
            header($string, $replace, $status);
//			die();
        }
    }

    /**
     *
     * @return unknown_type
     */
    public function getContent() {
        return $this->_content;
    }

    /**
     *
     * @param $content
     * @return unknown_type
     */
    public function setContent($content) {
        $this->_content = $content;
    }

    /**
     * <p>Sends the header with the specified status code</p>
     * @staticvar string $status_codes Keeps the status codes
     * @param string $statusCode The status code to send
     */
    public function header_status($statusCode) {
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

?>
