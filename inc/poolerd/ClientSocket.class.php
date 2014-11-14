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



require_once(XIMDEX_ROOT_PATH . '/inc/serializer/Serializer.class.php');

class ClientSocket {

	protected $conf = null;
	protected $socket = null;

	public function __construct(&$conf) {
		$this->conf =& $conf;
	}

	public function __destruct() {
		$this->close();
	}

	/**
	 * Cierra el socket, llamar cuando se necesite terminar la ejecucion
	 */
	public function close() {
		if (!is_resource($this->socket)) return;
		socket_close($this->socket);
		$this->socket = null;
	}

	protected function isClosed() {
		return !is_resource($this->socket);
	}

	/**
	 * Gestiona la comunicacion con el cliente
	 */
	public function handle(&$socket) {

		if (is_resource($this->socket)) return;
		$this->socket =& $socket;

		$pid = posix_getpid();

		$bufferSize = $this->conf->server['clientSocketBufferSize'];
		$data = '';

		do {
			$aux = socket_read($socket, $bufferSize, PHP_BINARY_READ);
//			error_log(sprintf('--> %s', strlen($aux)));
			$data .= $aux;
		} while (strlen($aux) == $bufferSize);

		$response = null;

		if (strlen($data) > 0) {

			$data = explode("\n\r", $data);
//			error_log(trim($data[1]), 3, '/tmp/xml.xml');
			$response = Serializer::decode(SZR_XMLRPC, trim($data[1]));
		}

		if (!is_array($response)) {
			$response = array(
				array(
					'queue' => null,
					'data' => array()
				)
			);
		}

		$this->close();
		return $response[0];
	}

}

?>