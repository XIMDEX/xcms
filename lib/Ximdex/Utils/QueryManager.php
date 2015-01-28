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

namespace Ximdex\Utils ;


class QueryManager {

    var $queryContent = NULL;
    private $levels;


    /**
     * @param bool $preload
     */
    public function __construct($preload = true) {

        $this->queryContent = array();

        if ($preload === false ) {
            return ;
        }
        $queryString = (isset( $_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '' ;
        parse_str($queryString, $this->queryContent);
    }

    /**
     *
     * @param $key
     * @param $value
     */
    function add($key, $value) {
        $this->queryContent[$key] = $value;
    }

    /**
     *
     * @param $key
     * @return mixed
     */
    function get($key) {
        return isset($this->queryContent[$key]) ? $this->queryContent[$key] : NULL;
    }

    /**
     *
     * @param $key
     */
    function delete($key) {
        if (isset($this->queryContent)) {
            unset($this->queryContent[$key]);
        }
    }

    /**
     *
     * @return mixed
     */
    function build() {
        return $this->_buildQuery($this->queryContent);
    }

    /**
     *
     * @param $extraParams
     * @return mixed
     */
    function buildWith($extraParams=null) {
        $fullQuery = $this->queryContent;
        if (!is_array($extraParams)) $extraParams = array();
        foreach ($extraParams as $key => $value) {
            $fullQuery[$key] = $value;
        }
        return $this->_buildQuery($fullQuery);
    }

    /**
     *
     * @param $queryParams
     * @return string
     */
    function _buildQuery($queryParams) {

        if (is_array($queryParams)) {

            $queryPreImploded = array();
            foreach($queryParams as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                if (is_array($value)) {
                    $this->levels = array();
                    array_push($this->levels, urlencode($key));
                    $queryPreImploded = array_merge($queryPreImploded, $this->_buildSubQuery($value));
                } else {
                    $queryPreImploded[] = sprintf('%s=%s', urlencode($key), urlencode($value));
                }
            }

            return sprintf('?%s', implode('&', (array) $queryPreImploded));
        }

        return '';
    }

    /**
     *
     * @param $queryParams
     * @return array
     */
    function _buildSubQuery($queryParams) {
        $storedValues = array();
        if (is_array($queryParams)) {
            foreach ($queryParams as $key => $value) {
                array_push($this->levels, urlencode($key));
                if (is_array($value)) {
                    $storedValues = array_merge($storedValues, $this->_buildSubQuery($value));
                    array_pop($this->levels);
                } else {
                    $key = '' . implode('[' , $this->levels) . ']';
                    $value = urlencode($value);
                    $storedValues[] = sprintf("%s=%s", $key, $value);
                    array_pop($this->levels);
                }
            }
            return $storedValues;
        }
        return array();
    }

    /**
     *
     * @return string
     */
    function getPage() {
        $sapi_type = php_sapi_name();
        $https = isset($_SERVER['HTTPS']) ? "s" : "";
        if (substr($sapi_type, 0, 3) == 'cgi') {
            return 'httpi'.$https.'://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }
        return 'http'.$https.'://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

    }
}
