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


ModulesManager::file('/services/Xowl/searchers/AbstractSearcherStrategy.class.php');
class AnnotationSearcherStrategy extends AbstractSearcherStrategy{

	
	const ENCODING = "UTF-8";
	const URL_STRING = "";
	//Default response format
	const RESPONSE_FORMAT = "application/json";
	private static $IS_SEMANTIC = 1;
	
	public function __construct(){
		parent::__construct();
	}

	/**
	 * <p>Query the server with the default response format (application/json)</p>
	 * @param unknown_type $text
	 */
	public function suggest($text) {
		return $this->query($text, self::RESPONSE_FORMAT);
	}

	/**
	 * <p>Send petition to stanbol server and returns the parsed response	</p>
	 * @param unknown_type $text
	 * @param unknown_type $format
	 * @return this. 
	 */
	private function query($text, $format) {

		$headers = array(
			//To remove HTTP 100 Continue messages
			'Expect:',
			//Response Format
			'Accept: '.$format,
			'Content-type: text/plain');
		
		//$data = urlencode($text);
		
		$response = $this->restProvider->getHttp_provider()->post(Config::getValue("Xowl_location"), $text, $headers);

		if ($response['http_code'] != Curl::HTTP_OK) {
			return NULL;
		}
		
		$data = $response['data'];		
		$this->data = $this->parseData($data);
		return $this;
	}
	
	/**
	 * 
	 * Check parsed data
	 * 
	 * @param unknown_type $data
	 */
	private function checkData($data){
		$correct = true;
		if(json_last_error()){
			$correct = false;
		} 
		return $correct;
	}

	/**
	 * 
	 * <p>Parse response data from stanbol server. JSON Format default.</p>
	 * @param unknown_type $data
	 */
	private function parseData($data){
		if(function_exists('json_decode')){
			$data = json_decode($data,true);
		}
		else{
			return NULL;
		}
		
		//$place = "http://dbpedia.org/ontology/Place";
		//$person = "http://dbpedia.org/ontology/Person";
		//$organisation = "http://dbpedia.org/ontology/Organisation";
		
		$result=array();
		$result['people']=array();
		$result['places']=array();
		$result['orgs']=array();
		foreach($data as $key => $values){
			if(strcmp("@graph",$key)==0){ //only analyze the @graph object
				foreach($values as $key => $value){
					if(!empty($value['dc:type'])){
						$ximdexType="custom";
						switch($value['dc:type']){
							case "dbp-ont:Person":
								$dcType="people"; 
								$ximdexType = self::$XIMDEX_TYPE_DPERSON;
								break;
							case "dbp-ont:Place":
								$dcType = "places";
								$ximdexType = self::$XIMDEX_TYPE_DPLACE;
								break;
							case "dbp-ont:Organisation":
							default:
								$dcType = "orgs";
								$ximdexType = self::$XIMDEX_TYPE_DORGANISATION;
								break;
						}
						if (isset($value['enhancer:selected-text'])){
                                                        $selectedText = $value['enhancer:selected-text']['@value'];
							$confidence  = $value['enhancer:confidence']?$value['enhancer:confidence']:0;
							$result[$dcType][$selectedText]["confidence"][]=$confidence;
							$result[$dcType][$selectedText]["type"] = $ximdexType;
							$result[$dcType][$selectedText]["isSemantic"] = self::$IS_SEMANTIC;
							$result[$dcType][$selectedText]["others"][]=$value;
						}
					}
				}
			}
		}
		$result = $this->estimateConfidence($result);
		return $result;
	}

	/**
	*	<p>Re-calculate the confidence </p>
	*/
	private function estimateConfidence (&$result){
		foreach($result as $key => $dcType){
			
			if (is_array($dcType)){
				foreach($dcType as $key2 => $resource){
					$acum = 0;
					$cont = 0;
					foreach($resource["confidence"] as $confidence){					
						$acum += $confidence;
						$cont++;
					}
					//unset($result[$key][$key2]["confidence"]);
					$result[$key][$key2]["confidence"] = number_format(($acum/$cont)*100, 2, ',', '');
				}
			}
		}

		return $result;
	}
	
	/**
	 * UNUSED! 
	 * Returns the short  type (places, people or organisations)
	 * @param unknown_type $tipo
	 */

	private function getTipo($tipo){
		$place = "http://dbpedia.org/ontology/Place";
		$person = "http://dbpedia.org/ontology/Person";
		$organisation = "http://dbpedia.org/ontology/Organisation";
		
		$tipos[$place] = "places";
		$tipos[$person] = "people";
		$tipos[$organisation] = "organisations";
		
		return $tipos[$tipo];
	}
	
}

?>