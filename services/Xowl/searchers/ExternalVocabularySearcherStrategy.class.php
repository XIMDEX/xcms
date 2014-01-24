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
class ExternalVocabularySearcherStrategy extends AbstractSearcherStrategy{

	private $core="art";
        const LMF_URL_KEY ="LMF_url";

	public function __construct(){
		parent::__construct();
	}

	public function suggest($text){

		$headers = array(
			//To remove HTTP 100 Continue messages
			'Expect:',
			//Response Format
			'Accept: application/json',
			'Content-type: text/plain');
		
		//$data = urlencode($text);
		$query = "q=(".urlencode("nombre:{$text}* OR aka:{$text}* OR titulo:{$text}*").")";
                if (!Config::getValue(self::LMF_URL_KEY))
                    return $this;
		$uri = Config::getValue(self::LMF_URL_KEY).$this->core."/select?wt=json&$query";
		$response = $this->restProvider->getHttp_provider()->get($uri, $headers);

		if ($response['http_code'] != Curl::HTTP_OK) {
			return $this;
		}
		
		$data = $response['data'];		
		$this->data = $this->parseData($data);
		return $this;
	}

	private function getEngine(){
		return "Solr";
	}	

	private function parseData($data){
		$result = array();
		$arrayData = json_decode($data);
		$docs = $arrayData->response->docs;

		if ($docs){
			$uriParam = "lmf.uri";
			$typeParam = "lmf.type";
			foreach ($docs as $doc) {
				$docArray = array();
				
				$docArray["uri"] = $doc->$uriParam;

				$typesArray = $doc->$typeParam;
				$name = false;
				if (is_array($typesArray) && count($typesArray)){
					$docArray["type_uri"] = $typesArray[0];
					$namespace = new Namespaces();
					$inferedTypes = $namespace->getByUri($typesArray[0]);
					if (count($inferedTypes)){
						$docArray["type"] = $inferedTypes[0]->get("type");
					}else{
						$docArray["type"] = $typesArray[0];;
					}
				}
				if(property_exists($doc, "nombre")){
					$nameArray = $doc->nombre;					
					if (is_array($nameArray) && count($nameArray)){						
						$name = $nameArray[0];						
					}					
				}else if (property_exists($doc, "titulo")){
					$nameArray = $doc->titulo;
					if (is_array($nameArray) && count($nameArray)){
						$name = $nameArray[0];
					}					
				}

				if ($name){				
					$result[$name] = $docArray;
				}
			}
		}
		return $result;
	}


}