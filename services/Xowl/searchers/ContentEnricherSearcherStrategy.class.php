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

class ContentEnricherSearcherStrategy extends AbstractSearcherStrategy{


	const ENCODING = "UTF-8";
	const URL_STRING = "http://api.zemanta.com/services/rest/0.0/";
	const IS_SEMANTIC = 0;

	public function __construct() {
		parent::__construct();
	}

	/**
	* Implement the abstract method
	* @param $text source for search the related terms.
	*/
	public function suggest($text) {


		//Key is mandatory for the service
		$key = \App::getValue( 'EnricherKey');
		if(!$key)
			return false;
		$xmlData = $this->query('zemanta.suggest', $key, $text, 'xml');
		$this->data = $this->parseData($xmlData);
		return $this;
	}

	/**
	* <p>Search the related terms usign a rest providerName</p>
	* @param $method. service method_exists	
	* @param $key. unique id key for enable the services
	* @param $text. the source content
	* @param $format. Output format.
	* @return. A xml string.
	*/
	private function query($method, $key, $text, $format) {

		//Preparing the args
		$args = array(
			'method' => $method,
			'api_key' => $key,
			'text' => $text,
			'format' => $format );
		$data = "";
		foreach($args as $key=>$value) {
			$data .= ($data != "")?"&":"";
			$data .= urlencode($key)."=".urlencode($value);
		}

		//Making rest call
		$response = $this->restProvider->getHttp_provider()->post(self::URL_STRING, $data);

		if ($response['http_code'] != Curl::HTTP_OK) {
			return NULL;
		}

		//return just the xml content.
		return substr($response["data"],strpos($response["data"],"<rsp>"));
	}


	/**
	* <p>Transform the xml with the found terms to an array.</p>
	* @param $xml. XML string with the service response.
	* @return Array with the response	
	*/
	private function parseData($xml){
			$result = array();
			$domDoc = new DOMDocument();
			$domDoc->preserveWhiteSpace = false;
			$domDoc->validateOnParse = true;
			$domDoc->formatOutput = true;
			if ($domDoc->loadXML($xml)){
				$xpathObj = new DOMXPath($domDoc);
				//each category has its own format.
				$result["articles"] = $this->parseArticleData($xpathObj);
				$result["images"] = $this->parseImageData($xpathObj);
				$result["links"] = $this->parseLinkData($xpathObj);
			}
			return $result;
			
	}

	/**
	*<p>Parse tags to an array for this category</p>
	*@param $xpathObj. DOMXPath object for the current XML.
	*@return an array.
	*/
	private function parseArticleData($xpathObj){
		$result = array();
		$nodeList0 = $xpathObj->query('/rsp/articles/article');
		for($i = 0; $i < $nodeList0->length; $i++){
			$nodeArticle = $nodeList0->item($i);
			$articleArray = array();	
			foreach ($nodeArticle->childNodes as $child) {						
				
				switch(strtolower($child->nodeName)){
					case "title":
						$name=$child->nodeValue;
						break;
					case "url":								
						$articleArray["others"]["url"]=$child->nodeValue;
						break;
					case "confidence": 
						$articleArray["confidence"]=$child->nodeValue;
						break;
				}
			}
			$articleArray["isSemantic"] = self::IS_SEMANTIC;
			$articleArray["type"] = "zArticle";
			$result[$name]=$articleArray;
			}

			return $result;
	}

	/**
	*<p>Parse tags to an array for this category</p>
	*@param $xpathObj. DOMXPath object for the current XML.
	*@return an array.
	*/
	private function parseLinkData($xpathObj){		
		$result = array();
		$nodeList0 = $xpathObj->query('/rsp/markup/links/link');
		for($i = 0; $i < $nodeList0->length; $i++){
			$nodeLink = $nodeList0->item($i);
			$linkArray = array();
			$others = array();
			foreach ($nodeLink->childNodes as $child) {				
				switch(strtolower($child->nodeName)){
					case "target":
						$others = $this->getLinkDataDetails($child);
						if (count($others)){
							$linkArray["others"]["target"][] = $others;						
							$name = $others["title"];								
						}
						break;
					case "confidence": 
						$linkArray["confidence"]=$child->nodeValue;
						break;
					default:
						$linkArray["others"][$child->nodeName] = $child->nodeValue;
				}
			}
			$linkArray["isSemantic"] = self::IS_SEMANTIC;
			$linkArray["type"] = "zLink";

			if ($name)
				$result[$name]=$linkArray;
		}

		return $result;
	}

	/**
	*<p>Get the different target for a link</p>
	*@param $target. Object DOMNode target for the link.
	*@return an array.
	*/
	private function getLinkDataDetails($target){

		$result = array();
		foreach ($target->childNodes as $child) {
			$result[$child->nodeName] = $child->nodeValue;			
		}

		return $result;
	}

	/**
	*<p>Parse tags to an array for this category</p>
	*@param $xpathObj. DOMXPath object for the current XML.
	*@return an array.
	*/
	private function parseImageData($xpathObj){

		$result = array();
		$nodeList0 = $xpathObj->query('/rsp/images/image');
		for($i = 0; $i < $nodeList0->length; $i++){
			$nodeImage = $nodeList0->item($i);
			$imageArray = array();
			$others = array();
			foreach ($nodeImage->childNodes as $child){
				switch($child->nodeName){
					case "confidence":
						$imageArray["confidence"] = $child->nodeValue;
						break;
					case "description":
						$name = $child->nodeValue;
						break;
				}
				
				$others[$child->nodeName] = $child->nodeValue;
			}
			$imageArray["isSemantic"] = self::IS_SEMANTIC;
			$imageArray["type"] = "zImage";
			$imageArray["others"] = $others;
			$result[$name] = $imageArray;
		}

		return $result;
	}
}

?>