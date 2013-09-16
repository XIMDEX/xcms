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




// Include defined rest service providers.
require_once(XIMDEX_ROOT_PATH . '/inc/rest/providers/google_translate/GoogleTranslate.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/rest/providers/ximNOTA/REST_ximNOTA.class.php');
// Include utility class.
require_once(XIMDEX_ROOT_PATH . '/inc/http/Curl.class.php');

class REST_Provider {

	protected $http_provider;

	public function __construct() {
		$this->http_provider = new Curl();
	}

	public function getHttp_provider(){
		return $this->http_provider;
	}
}
?>
