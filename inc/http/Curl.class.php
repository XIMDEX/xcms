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



class Curl {

	const HTTP_OK = 200;

	/**
	 * 
	 * @param $method
	 * @param $url
	 * @param $postBody
	 * @param $headers
	 * @param $ua
	 * @return unknown_type
	 */
	protected function doRequest($method, $url, $postBody = NULL, $headers = NULL, $ua = 'ximDEX') {

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		if ($method == 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
		} else {
			curl_setopt($ch, CURLOPT_HTTPGET, 1);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $ua);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, true);
		if (!is_null($headers) && is_array($headers)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

		$data = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$errno = @curl_errno($ch);
		$error = @curl_error($ch);
		curl_close($ch);

		if ($errno != CURLE_OK) {
			throw new ErrorException("HTTP Error: " . $error);
		}

		list($raw_response_headers, $response_body) = explode("\r\n\r\n", $data, 2);
		$response_header_lines = explode("\r\n", $raw_response_headers);
		array_shift($response_header_lines);

		$response_header_array = array();
		$response_headers = array();
		foreach($response_header_lines as $header_line) {
			list($header, $value) = explode(': ', $header_line, 2);
			if (isset($response_header_array[$header])) {
				$response_header_array[$header] .= "\n" . $value;
			} else {
				$response_header_array[$header] = $value;
			}
		}

		return array('http_code' => $http_code, 'data' => $response_body, 'headers' => $response_header_array);
	}

	/**
	 * 
	 * @param $url
	 * @param $headers
	 * @return unknown_type
	 */
	public function get($url, $headers=null) {
		return $this->doRequest('GET', $url, NULL, $headers);
	}

	/**
	 * 
	 * @param $url
	 * @param $vars
	 * @param $headers
	 * @return unknown_type
	 */
	public function post($url, $vars, $headers=null) {
		return $this->doRequest('POST', $url, $vars, $headers);
	}

}