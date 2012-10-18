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




class DBpedia extends SPARQL_Provider {

	const URL_ENDPOINT = "http://dbpedia.org/sparql";

	public function __construct($endpoint = self::URL_ENDPOINT) {
		parent::__construct($endpoint);
	}

	public function query($values) {

		$queryString = "select distinct ?Concept where {[] a ?Concept}";
		$this->sparql_courier->query($queryString);
		//$this->sparql_client->setOutputFormat("xml");
		$response = $this->sparql_client->query($this->sparql_courier);

//		var_dump($response);
	}

	public function q($values) {
 
		$queryString = 	"SELECT distinct ?a ?label ?b ?same ?abstract " .
				"?skos ?clase WHERE {  ?a " .
				"foaf:page <@@MACRO@@> . " .
				"?a rdfs:label ?label .  " .
				"?a owl:sameAs ?same .  ?b owl:sameAs ?a .  " .
				"?a dbpprop:abstract ?abstract .  ?a skos:subject ?skos . " .
				"?a rdf:type ?clase .  FILTER (lang(?abstract) = \"en\") . " .
				"FILTER (lang(?label) = \"en\") ." .
				"}";

		$return_array = array();

		foreach ($values as $idx => $val) {
			$val_qs = str_replace("@@MACRO@@", $val, $queryString);
			$this->sparql_courier->query($val_qs);
			$response = $this->sparql_client->query($this->sparql_courier);
			$return_array[$idx] = $response;
		}

		return $return_array;
	}


}

?>
