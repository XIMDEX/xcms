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




ModulesManager::file('/inc/ximNEWS_Adapter.inc', 'ximNEWS');
ModulesManager::file('/actions/createnews/baseIO.php', 'ximNEWS');


class BaseIO_News {

	function build($data) {
		$channellst=array();
		$langidlst=array();
		$nameLst=array();
		$array_news=array();

		$arg_uno["name"]=$data["parentName"];
		$arg_uno["template"]=$data[0]["template"];
		$arg_uno["nodeid"]=$data["parentID"];
		$arg_uno["colector"]=array();
		$arg_uno["colector"]=$data["colector"];

		for($i=0;$i<sizeof($data)-3;$i++){
			$channellst[]=$data[$i]["channels"][0];
			$langidlst[]=$data[$i]["langID"];
			$nameLst[$data[$i]["langID"]]=$data[$i]["aliasName"];
			$uno=array();
			$dos=array();
			
			$uno=$this->extract_data_news($arg_uno["template"],$data[$i]["content"]);
			$dos["area"]=$this->extract_areas($data[$i]["content"]);
			
			$array_news[$data[$i]["isoName"]]=array_merge($uno,$dos);
		}

		$ret = CreateNews($arg_uno,$channellst,$langidlst,$nameLst,$array_news);

		return $ret;
	}

	function extract_data_news($pvdID, $newsContent){
		$dataNews = array();

		$adapter = new ximNEWS_Adapter();
		$elementsPvd = $adapter->extractPvdElements($pvdID);

		foreach ($elementsPvd as $element) {
			if (!is_null($element['NAME'])) {
				$fieldsPvd[] = $element['NAME'];
			}
		}

		$domDoc = new DOMDocument();
		$domDoc->validateOnParse = true;
		$domDoc->loadXML(utf8_encode($newsContent));

		$xpath = new DOMXPath($domDoc);

		// Process nodes

		foreach ($fieldsPvd as $field) {
			$nodeList = $xpath->query('//*[local-name(.) = "'.$field.'"]');

			if ($nodeList->length == 0) {
				$nodeList = $xpath->query('//*[@'.$field.']');
			}

			foreach ($nodeList as $element) {
				$dataNews[$field][] = $element->nodeValue;
			}
		}

		return $dataNews;
	}

	function extract_areas($content){
		$areas = array();
		
		$domDoc = new DOMDocument();
		$domDoc->validateOnParse = true;
		$domDoc->loadXML(utf8_encode($content));

		$xpath = new DOMXPath($domDoc);

		$nodeList = $xpath->query('//*[local-name(.) = "area_tematica"]');

		foreach ($nodeList as $element) {
			$areas[] = $element->nodeValue;
		}

		return $areas;
	}
}

?>
