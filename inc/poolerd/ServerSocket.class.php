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
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));

if (!defined('POOLER_ROOT_PATH'))
	define('POOLER_ROOT_PATH', realpath(dirname(__FILE__)));

//if (!defined('POOLER_SERVER_SOCKET'))
//	define('POOLER_SERVER_SOCKET', 100);

/**
 * El socket que crea esta clase se puede abrir en modo bloqueante o no bloqueante.
 * En el primer caso el proceso queda dormido hasta que se recibe una conexion entrante
 * a traves del socket, pero se bloquean todas las señales enviadas al proceso y no
 * se pueden gestionar.
 *
 * En el segundo caso las señales son gestionadas sin problemas, pero el bucle en handle()
 * hace que la CPU se dispare al 100%.
 *
 * La solucion es abrir el socket en modo bloqueante y usar la funcion socket_select(), esta
 * bloquea el proceso pero da la posibilidad de establecer un timeout. De esta forma las señales
 * son gestionadas sin problemas y el proceso queda dormido sin disparar la CPU.
 */
class ServerSocket {

	protected $conf = null;
	protected $socket = null;
	protected $stop = null;
	protected $children = null;

	public function __construct(&$conf) {

		$this->conf =& $conf;
		$this->children = array();
		$this->stop = false;
	}

	public function __destruct() {
		$this->close();
	}

	/**
	 * Creates the socket and binds it to an address and port
	 */
	protected function bind() {

		if (is_resource($this->socket)) return true;

		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if (false === $socket) {
			echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
			return false;
		}

		// NOTE: Ver notas al inicio del fichero
//			$ret = socket_set_nonblock($socket);
//			if (false === $ret) {
//				echo "socket_set_nonblock() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
//				return false;
//			}

		$ret = socket_bind($socket, $this->conf->server['address'], $this->conf->server['port']);
		if (false === $ret) {
			echo "socket_bind() failed: " . socket_strerror(socket_last_error()) . "\n";
			return false;
		}

		$ret = socket_listen($socket, 0);
		if (false === $ret) {
			echo "socket_listen() failed: " . socket_strerror(socket_last_error()) . "\n";
			return false;
		}

		$this->socket =& $socket;
		return true;
	}

	/**
	 * Close the server socket and kills all child process
	 */
	public function close() {
		@socket_close(&$this->socket);
		$this->stop = true;
		$this->socket = null;
	}

	/**
	 * Opens the socket and wait for connections
	 */
	public function handle() {

		if (!$this->bind()) return 1;
		$socket =& $this->socket;

		// Se crea un socket bloqueante para que la CPU no se dispare al 100%.
		// Usando socket_select() se consigue un timeout para que el proceso
		// pueda gestionar señales.
		$clientSck = null;
		while (is_resource($socket) && !$this->stop) {

			// NOTE: Ver notas al inicio del fichero
			$ret = @socket_select($r = array(&$socket), $w = array(&$socket), $e = array(&$socket), 1);
			switch($ret) {
				case 1:

					@$clientSck =& socket_accept($socket);

					// TODO: fork client sockets
					if (is_resource($clientSck)) {
						$cli = new ClientSocket($this->conf);
						$response = $cli->handle($clientSck);
						$this->addToQueue($response);
						$cli = null;
						$clientSck = null;
					}

					break;
				case 2:
					// Connection refused
					break;
				case 0:
					// Connection timed out
					break;
			}
		}

		return 0;
	}

	protected function addToQueue($data) {
		$q = $this->conf->getQueueByName($data['queue']);
		if ($q !== null) {
			$q->push($data['data']);
			posix_kill($q->getPID(), SIGUSR1);
		}
	}

}

?>
