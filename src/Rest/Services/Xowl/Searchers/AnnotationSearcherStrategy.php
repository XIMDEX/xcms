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

namespace Ximdex\Rest\Services\Xowl\Searchers;

use Ximdex\Runtime\App;
use Ximdex\Utils\Curl;

class AnnotationSearcherStrategy extends AbstractSearcherStrategy
{
    const ENCODING = "UTF-8";
    const URL_STRING = "";
    
    // Default response format
    const RESPONSE_FORMAT = "application/json";
    private static $IS_SEMANTIC = 1;

    /**
     * Query the server with the default response format (application/json)
     * 
     * {@inheritDoc}
     * @see \Ximdex\Rest\Services\Xowl\Searchers\AbstractSearcherStrategy::suggest()
     */
    public function suggest($text)
    {
        return $this->query($text, self::RESPONSE_FORMAT);
    }

    /**
     * Send petition to stanbol server and returns the parsed response
     * 
     * @param $text
     * @param $format
     * @return Object
     */
    private function query($text, $format)
    {
        $headers = array(
            
            // To remove HTTP 100 Continue messages
            'Expect:',
            
            // Response Format
            'Accept: ' . $format);
        $data = array();
        if (is_string($text)) {
            $data["content"] = trim(html_entity_decode(strip_tags($text)));
            $data["token"] = App::getValue( "Xowl_token");
        } else {
            $data = $text;
        }
        $response = $this->restProvider->getHttp_provider()->post(App::getValue("Xowl_location"), $data, $headers);
        if ($response['http_code'] != Curl::HTTP_OK) {
            return NULL;
        }
        $data = $response['data'];
        $this->data = $this->parseData($data);
        return $this;
    }

    /**
     * Check parsed data
     *
     * @param $data
     */
    private function checkData($data)
    {
        $correct = true;
        if (json_last_error()) {
            $correct = false;
        }
        return $correct;
    }

    /**
     * Parse response data from stanbol server. JSON Format default.
     * @param $data
     */
    private function parseData($data)
    {
        if (function_exists('json_decode')) {
            $data = json_decode($data, true);
        } else {
            return NULL;
        }
        $result = array();
        $result['people'] = array();
        $result['places'] = array();
        $result['orgs'] = array();
        $result['creativework'] = array();
        $result['others'] = array();
        foreach ($data["semantic"] as $value) {
            if (!empty($value['dc:type'])) {
                switch ($value['dc:type']) {
                    case "dbp-ont:Person":
                        $dcType = "people";
                        $ximdexType = self::$XIMDEX_TYPE_DPERSON;
                        break;
                    case "dbp-ont:Place":
                        $dcType = "places";
                        $ximdexType = self::$XIMDEX_TYPE_DPLACE;
                        break;
                    case "dbp-ont:Work":
                        $dcType = "creativework";
                        $ximdexType = self::$XIMDEX_TYPE_DCREATIVEWORK;
                        break;
                    case "dbp-ont:Organisation":
                        $dcType = "orgs";
                        $ximdexType = self::$XIMDEX_TYPE_DORGANISATION;
                        break;
                    default:
                        $dcType = "others";
                        $ximdexType = self::$XIMDEX_TYPE_DOTHERS;
                        break;
                }
                if (isset($value['selected-text'])) {
                    $selectedText = $value['selected-text']['value'];
                    $confidence = $value['confidence'] ? $value['confidence'] : 0;
                    $result[$dcType][$selectedText]["confidence"][] = $confidence;
                    $result[$dcType][$selectedText]["type"] = $ximdexType;
                    $result[$dcType][$selectedText]["isSemantic"] = self::$IS_SEMANTIC;
                    $result[$dcType][$selectedText]["Name"] = $selectedText;
                    $result[$dcType][$selectedText]["Link"] = $value["entities"][0]["uri"] ? $value["entities"][0]["uri"] : "";
                    $result[$dcType][$selectedText]["Description"] = (isset($value["entities"][0]["rdfs:comment"]["value"]) 
                        and $value["entities"][0]["rdfs:comment"]["value"]) ? $value["entities"][0]["rdfs:comment"]["value"] : "";
                    $result[$dcType][$selectedText]["Image"] = (isset($value["entities"][0]["foaf:depiction"]) 
                        and $value["entities"][0]["foaf:depiction"]) ? $value["entities"][0]["foaf:depiction"] : "";
                }
            }
        }
        $result = $this->estimateConfidence($result);
        return $result;
    }

    /**
     * Re-calculate the confidence
     */
    private function estimateConfidence(&$result)
    {
        foreach ($result as $key => $dcType) {
            if (is_array($dcType)) {
                foreach ($dcType as $key2 => $resource) {
                    $acum = 0;
                    $cont = 0;
                    foreach ($resource["confidence"] as $confidence) {
                        $acum += $confidence;
                        $cont++;
                    }
                    $result[$key][$key2]["confidence"] = number_format(($acum / $cont) * 100, 2, ',', '');
                }
            }
        }
        return $result;
    }
}