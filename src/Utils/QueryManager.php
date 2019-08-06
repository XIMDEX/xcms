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

namespace Ximdex\Utils;

use Ximdex\Runtime\App;

class QueryManager
{
    public $queryContent = null;
    
    private $levels;

    public function __construct(bool $preload = true)
    {
        $this->queryContent = array();
        if ($preload === false) {
            return;
        }
        $queryString = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '' ;
        parse_str($queryString, $this->queryContent);
    }

    public function add(string $key, $value = null)
    {
        $this->queryContent[$key] = $value;
    }

    public function get(string $key)
    {
        return isset($this->queryContent[$key]) ? $this->queryContent[$key] : NULL;
    }

    public function delete(string $key)
    {
        if (isset($this->queryContent)) {
            unset($this->queryContent[$key]);
        }
    }

    public function build()
    {
        return $this->_buildQuery($this->queryContent);
    }

    public function buildWith(array $extraParams = null)
    {
        $fullQuery = $this->queryContent;
        if (! is_array($extraParams)) {
            $extraParams = array();
        }
        foreach ($extraParams as $key => $value) {
            $fullQuery[$key] = $value;
        }
        return $this->_buildQuery($fullQuery);
    }

    private function _buildQuery(array $queryParams = null)
    {
        if (is_array($queryParams)) {
            $queryPreImploded = array();
            foreach($queryParams as $key => $value) {
                /*
                if (empty($value)) {
                    continue;
                }
                */
                if (is_array($value)) {
                    $this->levels = array();
                    array_push($this->levels, urlencode($key));
                    $queryPreImploded = array_merge($queryPreImploded, $this->_buildSubQuery($value));
                } else {
                    $queryPreImploded[] = sprintf('%s=%s', urlencode($key), urlencode($value));
                }
            }
            return sprintf('?%s', implode('&', $queryPreImploded));
        }
        return '';
    }

    private function _buildSubQuery(array $queryParams = null)
    {
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

    public function getPage(bool $host = true)
    {
        if ($host) {
            
            // Changed getPage method to use UrlHost + UrlRoot value obtained from database table Config value
            $url = App::getValue('UrlHost');
        } else {
            $url = '';
        }
        
        // NOTE: We add / diretory separator at the end
        $url .= App::getValue('UrlRoot') . '/';
        return $url;
    }
}
