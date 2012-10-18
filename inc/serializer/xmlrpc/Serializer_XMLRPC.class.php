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



class Serializer_XMLRPC {

	public function __construct() {
	}

	public function encode($data) {

		if (!is_array($data) || !isset($data['method']) || !isset($data['params'])) {
			XMD_Log::error('Los parametros enviados no son validos: Serializer_XMLRPC::encode()');
		}

		$encoded = null;
		if (function_exists('xmlrpc_encode_request')) {
			// NOTE: La extension XML-RPC es experimental y esta sujeta a cambios.
			// En PHP4 la extension esta habilitada por defecto
			$encoded = @xmlrpc_encode_request($data['method'], $data['params']);
		} else {
			XMD_Log::warning('Se esta intentando serializar usando la funcion inexistente xmlrpc_encode_request()');
		}
		return $encoded;
	}

	public function decode($xmlrpc) {
		$decoded = null;
		if (function_exists('xmlrpc_decode_request')) {
			// NOTE: La extension XML-RPC es experimental y esta sujeta a cambios.
			// En PHP4 la extension esta habilitada por defecto
			$decoded = @xmlrpc_decode_request($xmlrpc, $method);
		} else {
			XMD_Log::warning('Se esta intentando deserializar usando la funcion inexistente xmlrpc_decode()');
		}
		return $decoded;
	}

}

?>
