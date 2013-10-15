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


ModulesManager::file('/inc/rest/REST_Provider.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/persistence/Config.class.php');

/**
 * 
 * Conection Module with Apache Stanbol
 * 
 * @author fjcarretero
 *
 */
class TagSuggester extends REST_Provider {

	const ENCODING = "UTF-8";
	const URL_STRING = "";
	//Default response format
	const RESPONSE_FORMAT = "application/json";
	
	public function __construct() {
		parent::__construct();
	}

	/**
	 * 
	 * Query the server with the default response format (application/json)
	 * 
	 * @param unknown_type $text
	 */
	public function suggest($text) {
		return $this->query($text, self::RESPONSE_FORMAT);
	}

	/**
	 * Send petition to stanbol server and returns the parsed response
	 * 
	 * @param unknown_type $text
	 * @param unknown_type $format 
	 */
	private function query($text, $format) {

		$headers = array(
			//To remove HTTP 100 Continue messages
			'Expect:',
			//Response Format
			'Accept: '.$format,
			'Content-type: text/plain');
		
		//$data = urlencode($text);
		
		$response = $this->http_provider->post(Config::getValue("Xowl_location"), $text, $headers);

		if ($response['http_code'] != Curl::HTTP_OK) {
			return NULL;
		}
		
		$data = $response['data'];
		
		$data = $this->parseData($data);
		
		return $data;
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
	 * Parse response data from stanbol server. JSON Format default.
	 * 
	 * @param unknown_type $data
	 */
	private function parseData($data){
		if(function_exists('json_decode')){
			$data = json_decode($data,true);
		}
		else{
			return NULL;
		}
		
		//NOT USED RIGHT NOW!
		//$typeString = "http://purl.org/dc/terms/type";
		//$relationString = "http://purl.org/dc/terms/relation";
		//$selectedTextString = "http://fise.iks-project.eu/ontology/selected-text";
		//$entityLabelString = "http://fise.iks-project.eu/ontology/entity-label";
		//$entityTypeString = "http://fise.iks-project.eu/ontology/entity-type";
		//$entityReferenceString = "http://fise.iks-project.eu/ontology/entity-reference";
		
		//$place = "http://dbpedia.org/ontology/Place";
		//$person = "http://dbpedia.org/ontology/Person";
		//$organisation = "http://dbpedia.org/ontology/Organisation";
		
		$result=array();
		$result['status']="ok";
		$result['people']=array();
		$result['places']=array();
		$result['orgs']=array();
		foreach($data as $key => $values){
			if(strcmp("@graph",$key)==0){ //only analyze the @graph object
				foreach($values as $key => $value){
					if(!empty($value['dc:type'])){
						switch($value['dc:type']){
							case "dbp-ont:Person": 
								if($this->search($value['enhancer:selected-text']['@value'],$result['people'])==-1){
								$result['people'][]=$this->getPerson($value);	
								}
								break;
							case "dbp-ont:Place": 
								if($this->search($value['enhancer:selected-text']['@value'],$result['places'])==-1){
								$result['places'][]=$this->getPlace($value);	
								}
								break;
							case "dbp-ont:Organisation": 
								if($this->search($value['enhancer:selected-text']['@value'],$result['orgs'])==-1){
								$result['orgs'][]=$this->getOrg($value);	
								}
								break;
						}
					}
				}
			}
		}
	
		return json_encode($result);
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
	
	/**
	 * 
	 * Search for a value in the results array
	 * 
	 * @param unknown_type $value
	 * @param unknown_type $array
	 * @param unknown_type $type
	 */
	private function search($value, $array){
		$notexists = -1;
//error_log("BUSQUEDA".print_r($array,true));
		foreach($array as $key => $values){
			if($values['selected-text']==$value){
				return $key;
			}
		}
		return $notexists;
	}

	private function getPerson($match){	
		$people=array();
		if($match['dc:type']!="LinguisticSystem" || strpos($match['enhancer:selected-text']['@value'],"\n")!==false){
				//Here comes the data we export to the front end
				$people['selected-text']=$match['enhancer:selected-text']['@value'];
				$people['type']=$match['dc:type'];
				$people['lang']=$match['enhancer:selected-text']['@language'];
			}
		return $people;
	}

	private function getPlace($match){
		$place=array();
		if($match['dc:type']!="LinguisticSystem" || strpos($match['enhancer:selected-text']['@value'],"\n")!==false){
			//Here comes the data we export to the front end
			$place['selected-text']=$match['enhancer:selected-text']['@value'];
			$place['type']=$match['dc:type'];
			$place['lang']=$match['enhancer:selected-text']['@language'];
		}
		return $place;
	}

	private function getOrg($match){
		$org=array();
		if($match['dc:type']!="LinguisticSystem" || strpos($match['enhancer:selected-text']['@value'],"\n")!==false){
			//Here comes the data we export to the front end
			$org['selected-text']=$match['enhancer:selected-text']['@value'];
			$org['type']=$match['dc:type'];
			$org['lang']=$match['enhancer:selected-text']['@language'];
		}
		return $org;

	}

}

?>
