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


class Enricher extends REST_Provider {

	const ENCODING = "UTF-8";
	const URL_STRING = "http://api.zemanta.com/services/rest/0.0/";

	public function __construct() {
		parent::__construct();
	}

	public function suggest($text, $key, $format = 'xml') {

		return $this->query('zemanta.suggest', $key, $text, $format);
	}

	private function query($method, $key, $text, $format) {

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

		$response = $this->http_provider->post(self::URL_STRING, $data);

		if ($response['http_code'] != Curl::HTTP_OK) {
			return NULL;
		}

		// TODO: Check valid response

		return $response['data'];
	}

}

?>