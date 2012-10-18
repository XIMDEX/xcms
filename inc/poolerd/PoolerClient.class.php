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




if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', dirname(__FILE__).'/../');

require_once(XIMDEX_ROOT_PATH . '/inc/poolerd/PoolerConf.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/serializer/Serializer.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/http/Curl.class.php');

class PoolerClient {

	private function __construct() {
	}

	/**
	 * Sends a XML-RPC request to the pooler server.
	 * @param string queue Queue name
	 * @param array data Asociative arrays of data, indexed by parameter name
	 */
	static function request($queue, $data) {

		$conf = new PoolerConf(XIMDEX_ROOT_PATH . '/conf/poolerd.xml');

		$data = !is_array($data) ? array() : $data;
		$data = array(
			'method' => $queue,
			'params' => array(
				'queue' => $queue,
				'data' => $data
			)
		);
		$data = Serializer::encode(SZR_XMLRPC, $data);

		$curl = new Curl();
		try {
			$ret = $curl->post(
				$conf->client['url'],
				$data,
				array(
					'Content-Type: text/xml',
					'Expect:'	// Important!
				)
			);
		} catch (ErrorException $e) {
			XMD_Log::error(sprintf('curl_error: %s', $e->getMessage()));
			throw new Exception($e->getMessage());
		}

		return $ret;
	}

}

?>
