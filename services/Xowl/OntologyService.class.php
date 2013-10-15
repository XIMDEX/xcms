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

ModulesManager::file('/inc/model/Namespaces.class.php');
ModulesManager::file('/services/Xowl/searchers/AnnotationSearcherStrategy.class.php');
ModulesManager::file('/services/Xowl/searchers/ContentEnricherSearcherStrategy.class.php');
ModulesManager::file('/services/Xowl/searchers/XimdexSearcherStrategy.class.php');


class OntologyService {

	private $key = false;
	private $providers = array(); 
	
	/**
	*<p>Class constructor. Check if Xowl module is enabled and if exists EnricherKey;</p>
	*/
	public function __construct(){

		//Loading all defined providers
		$this->loadProviders();
	
		if(ModulesManager::isEnabled('Xowl')){		
			$key = Config::getValue('EnricherKey');

			if($key !== NULL && $key != ''){
				$this->key = $key;
			}

			
		}
	}

	/**
	* <p>Load all existing namespaces in this ximdex instance.</p>
	* @return Array<Namespaces> with all namespaces in Namespaces table.
	*/
	public static function getAllNamespaces(){
		$namespace = new Namespaces();
		return $namespace->getAll();
	}

	/**
	*<p>Suggest related terms and resources from a text</p>
	*<p> If key exists return semantic and suggested terms.</p>
	*@param $text: string to search related words.
	*@return string in json format.
	*/
	public function suggest($text, $provider=null){

		if ($this->key){
			$result = array();
			foreach ($this->providers as $type => $providerName){
				if (class_exists($providerName)){
					$provider = new $providerName;
					$result[$type] = $provider->suggest($text)->getData();
				}
			}			
			$result["status"] = "ok";
			return json_encode($result);
		}

		return $false;
	
	}


	/**
	* Load all providers and update the provider property
	*/	
	private function loadProviders(){

		//It should be loaded from DB.
		$this->providers["semantic"]="AnnotationSearcherStrategy";
		//$this->providers["content"]="ContentEnricherSearcherStrategy";
		//$this->providers["ximdex"]="XimdexSearcherStrategy";
	}
}

?>
