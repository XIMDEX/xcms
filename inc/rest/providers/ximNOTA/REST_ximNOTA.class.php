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



//

class REST_ximNOTA extends REST_Provider {

	const ENCODING = 'UTF-8';
	
	protected $URL_STRING = '';

	public function __construct() {
		parent::__construct();
		$this->URL_STRING = sprintf('%s'.ModulesManager::path('ximNOTA').'/rest/index.php', \App::getValue( 'UrlRoot'));
#		$this->URL_STRING = sprintf('%s/xmd/loadaction.php', \App::getValue( 'UrlRoot'));
	}

	public function migratePair($pathToFile, $pathToXml, $pathToXimdex) {
		return $this->query('migratePair', $pathToFile, $pathToXml, $pathToXimdex);
	}

	public function publicatePair($pathToFile, $pathToXml, $pathToXimdex) {
		return $this->query('publicatePair', $pathToFile, $pathToXml, $pathToXimdex);
	}

	protected function query($method, $pathToFile, $pathToXml, $pathToXimdex) {

		$args = array(
#			'mod=ximNOTA',	// Usar si el servicio es una accion de ximdex
#			'action=rest',	// Usar si el servicio es una accion de ximdex
			'command='.$method,
			'pathToFile='.$pathToFile,
			'pathToXml='.$pathToXml,
			'pathToXimdex='.$pathToXimdex
		);

		$data = implode('&', $args);
		$response = $this->http_provider->post($this->URL_STRING, $data);

		if ($response['http_code'] != Curl::HTTP_OK) {
			return NULL;
		}

		// TODO: Check valid response

		return $response['data'];
	}

}

?>