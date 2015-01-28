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



if(!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../../"));

require_once("Server.php");
require_once(XIMDEX_ROOT_PATH . "/inc/dav/DAV_Log.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/auth/Authenticator.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/xvfs/XVFS.class.php");
require_once(XIMDEX_ROOT_PATH . "/inc/repository/nodeviews/View_XmlDocument.class.php");


class ximDEX_webDAV_Server extends HTTP_WebDAV_Server {

	/**
	 * String to be used in "X-Dav-Powered-By" header
	 *
	 * @var string
	 */
	var $dav_powered_by = "ximDEX webDAV Server";
	/**
	 * Realm string to be used in authentification popups
	 *
	 * @var string
	 */
	var $http_auth_realm = "ximDEX webDAV Zone";
	/**
	 * 
	 * @var unknown_type
	 */
	var $base = "";

	/**
	 * 
	 * @return unknown_type
	 */
	function ximDEX_webDAV_Server() {

		//printf("ximdex_webDAV_Server::__construct() <br/>\n");
		parent::HTTP_WebDAV_Server();
	}

	/**
	 * 
	 * @param $user
	 * @param $passwd
	 * @return unknown_type
	 */
	function mount_vfs($user, $passwd) {

		require_once XIMDEX_ROOT_PATH . '/conf/webdav.conf';

		if (!isset($davconfig)) $davconfig = array();
		if (count($davconfig) == 0) {
			// configuracion por defecto
			$davconfig[0] = array();
			$davconfig[0]['mountpoint'] = '/';
			$davconfig[0]['uri'] = "xnodes://$user:$passwd@localhost/Proyectos";
		}

		foreach ($davconfig as $item) {

			$mp = $item['mountpoint'];
			$uri = $item['uri'];
			$_uri = preg_replace('#://user:passwd@#mi', "://$user:$passwd@", $uri);
			$ret = XVFS::mount($mp, $_uri);

//			if ($ret) {
//				DAV_Log::info("DAV_Server::mount_vfs() - Se ha montado el recurso $uri correctamente sobre $mp");
//			}
			if (!$ret) {
				DAV_Log::error("DAV_Server::mount_vfs() - Ocurrio un error al intentar montar el recurso $uri sobre $mp");
			}
		}
	}

	/**
	 * 
	 * @return unknown_type
	 */
	function serveRequest() {
		//$req = apache_request_headers();

		$method = $_SERVER['REQUEST_METHOD'] . ' ' .
			  $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . ' ' .
			  $_SERVER['SERVER_PROTOCOL'];

		$this->base = "";

		// base class work
		parent::ServeRequest();
	}

	/**
	 * Authentication process.
	 *
	 * @access private
	 * @param  string  HTTP Authentication type (Basic, Digest, ...)
	 * @param  string  Username
	 * @param  string  Password
	 * @return bool    true on successful authentication
	 */
	function check_auth($type, $username, $password) {
		$params = array( 'type' => $type, 'username' => $username, 'password' => $password );

		$ret = false;

		if (!session_is_registered("dav")) {

			// Connect to authentication system.

	        $authenticator =& new Authenticator();
			$isValid = $authenticator->login($username, $password);

			if ($isValid) {

				// Create new session
				\Ximdex\Utils\Session::start("dav");
				\Ximdex\Utils\Session::set('user', $username);
				// Mount VFS
				$this->mount_vfs($username, $password);

				$ret = true;

			}
		}

		if ($ret) {
			DAV_Log::info("DAV_Server::check_auth() - Sesion iniciada por $username.");
		} else {
			DAV_Log::error("DAV_Server::check_auth() - Fallo en el inicio de sesion por $username.");
		}

		return $ret;
	}

	/**
	 * PROPFIND method handler
	 *
	 * @param  array  general parameter passing array
	 * @param  array  return array for file properties
	 * @return bool   true on success
	 */
	function PROPFIND(&$options, &$files) {

		// Path relativo
		$vfspath = $options["path"];

		$dir =& XVFS::read($vfspath);

		if (is_null($dir)) {
			DAV_Log::warning("DAV_Server::PROPFIND($vfspath) - No se encontro el nodo solicitado.");
			return false;
		}

		// store information for requested path
		$files["files"] = array();
		$files["files"][] = $this->fileinfo($vfspath);

		// information for contained resources requested?
		if (!empty($options['depth'])) {

			// recursive call to fileinfo...
			//
			// Se puede consultar si el recurso es un directorio usando XVFS,
			// pero es mas rapido preguntarle al objeto XVFS_Entity...
			if ($dir->get('isdir')) {

				$collection = $dir->getCollection();

				foreach ($collection as $child_path) {

					// El objeto XVFS_Entity almacena una coleccion de paths
					$files['files'][] = $this->fileinfo($child_path);
				}
			}
		}

		DAV_Log::info("DAV_Server::PROPFIND($vfspath) - Informacion solicitada.");
		return true;
	}

	/**
	 * Obtiene informacion de un recurso
	 *
	 * @param string vfspath Ruta del recurso
	 * @return array Informacion obtenida
	 */
	function fileinfo($vfspath) {

		// create result array
		$info = array();
		// properties array
		$info["props"] = array();

		$entity = XVFS::read($vfspath);

		// Si entity es NULL sera porque no es fichero regular, directorio o enlace,
		// se puede dar el caso, por ejemplo, en un Backend_File cuando se esta
		// intentando obtener informacion de un fichero tipo 'Socket' o 'Named Pipe'.
		// El resto de recursos que llegan desde PROPFIND deben existir todos.
		if (is_null($entity)) return null;

		// get normalized path.
		$info['path'] = $entity->get('bpath');

		$descriptor		= $entity->getDescriptor();
		$mime			= $entity->getMIME();
		$resourcetype	= $entity->get('isdir') ? 'collection' : '';
		$creationdate	= $entity->get('creationdate');
		$lastmodified	= $entity->get('lastmodified');

		$contentlength	= (false !== $descriptor) ? filesize($descriptor) : null;

		$info['props'][] = $this->mkprop('displayname', strtoupper($info['path']));
		$info['props'][] = $this->mkprop('resourcetype', $resourcetype);
		$info['props'][] = $this->mkprop('getcontenttype', $mime);
		$info['props'][] = $this->mkprop('creationdate', $creationdate);
		$info['props'][] = $this->mkprop('getlastmodified', $lastmodified);

		if (!is_null($contentlength)) $info["props"][] = $this->mkprop('getcontentlength', $contentlength);

		return $info;
	}

	/**
	 * PROPPATCH method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function PROPPATCH(&$options) {
		//global $prefs, $tab;

//	require(realpath(dirname(__FILE__)) . "/../../../devel/tests/common_config.php");
//	logdump($options);

		$msg = "";

		$path = $options["path"];

		$dir = dirname($path)."/";
		$base = basename($path);

		foreach($options["props"] as $key => $prop) {
			if ($prop["ns"] == "DAV:") {
				$options["props"][$key]['status'] = "403 Forbidden";
			} else {
				if (isset($prop["val"])) {
					// Replace prop['name'] property in the namespace $prop['ns'] del path $options['path']

				} else {
					// Delete prop['name'] property in the namespace $prop['ns'] del path $options['path']

				}

				// update props !
			}
		}

		return "";
	}

	/**
	 * GET method handler
	 *
	 * @param  array  parameter passing array
	 * @return bool   true on success
	 */
	function GET(&$options) {

		$vfspath = $options['path'];

		$entity =& XVFS::read($vfspath);

		if (is_null($entity)) {
			DAV_Log::warning("DAV_Server::GET($vfspath) - No se encontro el nodo solicitado.");
			return false;
		}

		if (!XVFS::isReadable($vfspath)) {
			DAV_Log::warning("DAV_Server::GET($vfspath) - El recurso solicitado no es accesible.");
			return false;
		}

		if ($entity->get('isdir')) {
			// Si la entidad es un directorio se devuelve una representacion HTML del contenido
			return $this->getHTMLDir($entity, $options);
		}

		// Los resultados de los metodos de acceso al filesystem son cacheados por PHP
		clearstatcache();
		$descriptor = $entity->get('descriptor');

		// Procesa StructuredDocuments y la plantilla docxap
		$view = new View_XmlDocument();
		$content = $view->transform($entity->get('idnode'), null, null);
		if ($content !== false) {
			$content = str_replace('{base_uri}', $this->base_uri, $content);
			$descriptor = $descriptor . '.xml';
			FsUtils::file_put_contents($descriptor, $content);
		}


		$options['mimetype'] = $entity->get('mimetype');
		$options['mtime'] = $entity->get('lastmodified');

		if (false !== $descriptor) {
			$options['stream'] = fopen($descriptor, 'r');
			$options['size'] = filesize($descriptor);
		}

		DAV_Log::info("DAV_Server::GET($vfspath) - Informacion solicitada.");
		return true;
	}

	/**
	 * Devuelve la representacion de un recurso, tipicamente una coleccion, en HTML
	 *
	 * @param object dir XVFS_Entity
	 * @param array options Parametros generales
	 */
	function getHTMLDir(&$dir, &$options) {

		$format = "%15s  %-19s  %-s\n";
		$th_format = '<tr><th>%s</th><th>%s</th><th>%s</th><th>%s</th></tr>';
		$td_format = '<tr><td>%s</td><td>%s</td><td align="right">%s&nbsp;&nbsp;</td><td align="right">&nbsp;%s&nbsp;</td></tr>';
		$icon_format = '<img src="%s" alt="[%s]">';

		echo "<html><head><title>Index of ".htmlspecialchars($options['path'])."</title></head>\n";

		echo "<h1>Index of ".htmlspecialchars($options['path'])."</h1>\n";
		echo '<table>';

		printf($th_format, '', 'Filename', 'Last Modified', 'Size');
		echo '<tr><th colspan="4"><hr /></th></tr>';

		$icon = sprintf($icon_format, '/icons/back.gif', 'Parent Directory');
		$link = '<a href="%s">%s</a>';
		$parent = $_SERVER["REQUEST_URI"];
		$parent = str_replace(basename($parent).'/', '', $parent);
		$link = sprintf($link, $parent, 'Parent Directory');

		printf($td_format, $icon, $link, '&nbsp;', '-');

		// Coleccion de rutas de los recursos hijos
		$col = $dir->getCollection();

		foreach ($col as $path) {

			$e = XVFS::read($path);
			if (is_object($e)) {

				clearstatcache();
				$descriptor = $e->get('descriptor');

				if (false !== $descriptor) {

					$mtime = filemtime($descriptor);
					$mtime = date('d-M-Y', $mtime) . ' ' . date('H:m:s', $mtime);
					$fsize = filesize($descriptor);
					$unit = 'Bytes';

					if ($fsize > pow(1024, 3)) {
						$fsize /= pow(1024, 3);
						$unit = 'Gb';
					} else if ($fsize > pow(1024, 2)) {
						$fsize /= pow(1024, 2);
						$unit = 'Mb';
					} else if ($fsize > 1024) {
						$fsize /= 1024;
						$unit = 'Kb';
					}

					$fsize = sprintf('%u %s', $fsize, $unit);
				} else {

					$mtime = '-';
					$fsize = '-';
				}

				$icon = $e->get('isdir') ? '/icons/folder.gif' : '/icons/unknown.gif';
				$icon = sprintf($icon_format, $icon, $e->get('name'));
				$link = '<a href="%s">%s</a>';
				$slink = str_replace('//', '/', $_SERVER["REQUEST_URI"] . '/' . $e->get('name') . '/');
				$link = sprintf($link, $slink, $e->get('name'));

				printf($td_format, $icon, $link, $mtime, $fsize);
			}
		}

		echo '<tr><th colspan="4"><hr /></th></tr>';
		echo '</table>';
		echo '</html>';

		exit;
	}

	/**
	* PUT method handler
	*
	* @param  array  parameter passing array
	* @return bool   true on success
	*/
	function PUT(&$options) {

		$vfspath = $options['path'];

		// Es nueva creacion o actualizacion ??
		$options['new'] = !XVFS::exists($vfspath);

		if (!$options['new'] && XVFS::isDir($vfspath)) {
			$status = '409 Conflict';
			DAV_Log::error("DAV_Server::PUT($vfspath) - ($status), El nodo indicado existe y es un directorio.");
			return $status;
		}

		// Se obtiene el contenido de la entidad, se asignara via XVFS
		$stream = '';
		while (!feof($options['stream'])) {
			$stream .= fread($options['stream'], 8192);
		}


		// Crea o actualiza la entidad con el contenido obtenido anteriormente
		if ($options['new']) {
			$ret = XVFS::append($vfspath, $stream);
		} else {
			$ret = XVFS::update($vfspath, $stream);
		}

		if ($ret > 0) {

			$ret = XVFS_NONE;
			$entity =& XVFS::read($vfspath);

			// NOTE: ���Pedazo de workaround!!!
			// Procesa StructuredDocuments y la plantilla docxap
			$view = new View_XmlDocument();
			$content = $view->transform($entity->get('idnode'), $stream, null);
			if ($content !== false) {
				$ret = XVFS::update($vfspath, $content);
				// TODO: Comprobar que se guarda correctamente...
				$ret = XVFS_NONE;
			}
		}


		switch ($ret) {
			case XVFS_NOT_EXISTS:
			case XVFS_NOT_IN_REP:
				$status = '409 Conflict';
				DAV_Log::error("DAV_Server::PUT($vfspath) - ($status), Se ha intentado actualizar un recurso que no existe.");
				return $status;
				break;

			case XVFS_EXISTS:
				$status = '403 Forbidden';
				DAV_Log::error("DAV_Server::PUT($vfspath) - ($status), Se ha intentado crear un recurso que ya existe.");
				return $status;
				break;

			case XVFS_NOT_DIR:
				$status = '409 Conflict';
				DAV_Log::error("DAV_Server::PUT($vfspath) - ($status), El padre del recurso indicado no es un directorio.");
				return $status;
				break;

			case XVFS_PARENT_NOT_FOUND:
				$status = '409 Conflict';
				DAV_Log::error("DAV_Server::PUT($vfspath) - ($status), El padre del recurso indicado no existe.");
				return $status;
				break;

			case XVFS_NO_NODE_NAME:
				$status = '403 Forbidden';
				DAV_Log::error("DAV_Server::PUT($vfspath) - ($status), No se especifico el nombre del nuevo recurso.");
				return $status;
				break;

			case XVFS_BASEIO_ERROR:
				$status = '403 Forbidden';
				DAV_Log::error("DAV_Server::PUT($vfspath) - ($status), BaseIO devolvio un codigo de error.");
				return $status;
				break;

			case XVFS_INFERE_ERROR:
				$status = '403 Forbidden';
				DAV_Log::error("DAV_Server::PUT($vfspath) - ($status), No se pudo inferir el nodetype del recurso indicado.");
				return $status;
				break;

			case XVFS_NOT_WRITABLE:
				$status = '403 Forbidden';
				DAV_Log::error("DAV_Server::PUT($vfspath) - ($status), No tiene permisos para crear o actualizar el recurso.");
				return $status;
				break;

			case XVFS_NONE:
				// Todo Ok!
				$status = '201 Created';
				if ($options['new']) {
					DAV_Log::info("DAV_Server::PUT($vfspath) - ($status), El recurso indicado se ha creado correctamente.");
				} else {
					DAV_Log::info("DAV_Server::PUT($vfspath) - ($status), El recurso indicado se ha actualizado correctamente.");
				}
				return $status;
				break;

			default:
				// Error desconocido...
				$status = '403 Forbidden';
				DAV_Log::error("DAV_Server::PUT($vfspath) - ($status), Error desconocido, no fue posible crear el recurso.");
				return $status;
		}

		//
		// El contenido de la entidad ya es actualizado por XVFS::append() o XVFS::update(),
		// No es necesario escribirlo dos veces.
		// Si se devuelve un apuntador a un fichero el contenido sera escrito
		// de nuevo por la clase padre.
		//
		// Ademas, lo apropiado es que el propio backend sea quien escriba el contenido,
		// ya que es el quien conoce el medio de almacenamiento.
		//
//		if (!is_resource($fp)) $fp = fopen($f_desc, "w");

		$fp = '201 Created';

		return $fp;
	}

	/**
	 * MKCOL method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function MKCOL($options) {

		$vfspath = $options['path'];

		if (!empty($_SERVER['CONTENT_LENGTH'])) {
			$status = "415 Unsupported media type";
			DAV_Log::error("DAV_Server::MKCOL($vfspath) - ($status), 415 Unsupported media type.");
			return $status;
		}

		$ret = XVFS::mkdir($vfspath, '0777');
		if ($ret > 0) $ret = XVFS_NONE;

		switch ($ret) {
			case XVFS_NOT_IN_REP:
				$status = '409 Conflict';
				DAV_Log::error("DAV_Server::MKCOL($vfspath) - ($status), Se ha intentado actualizar un recurso que no existe.");
				return $status;
				break;

			case XVFS_EXISTS:
				$status = '403 Forbidden';
				DAV_Log::error("DAV_Server::MKCOL($vfspath) - ($status), El recurso especificado ya existe.");
				return $status;
				break;

			case XVFS_NOT_WRITABLE:
				$status = '403 Forbidden';
				DAV_Log::error("DAV_Server::MKCOL($vfspath) - ($status), No tiene permisos para crear el recurso.");
				return $status;
				break;

			case XVFS_NOT_DIR:
				$status = '409 Conflict';
				DAV_Log::error("DAV_Server::MKCOL($vfspath) - ($status), El padre del recurso indicado no es un directorio.");
				return $status;
				break;

			case XVFS_PARENT_NOT_FOUND:
				$status = '409 Conflict';
				DAV_Log::error("DAV_Server::MKCOL($vfspath) - ($status), El padre del recurso indicado no existe.");
				return $status;
				break;

			case XVFS_NO_NODE_NAME:
				$status = '403 Forbidden';
				DAV_Log::error("DAV_Server::MKCOL($vfspath) - ($status), No se especifico el nombre del nuevo recurso.");
				return $status;
				break;

			case XVFS_BASEIO_ERROR:
				$status = '403 Forbidden';
				DAV_Log::error("DAV_Server::MKCOL($vfspath) - ($status), BaseIO devolvio un codigo de error.");
				return $status;
				break;

			case XVFS_INFERE_ERROR:
				$status = '403 Forbidden';
				DAV_Log::error("DAV_Server::MKCOL($vfspath) - ($status), No se pudo inferir el nodetype del recurso indicado.");
				return $status;
				break;

			case XVFS_NONE:
				// Todo Ok!
				$status = '201 Created';
				DAV_Log::info("DAV_Server::MKCOL($vfspath) - ($status), El directorio se creo correctamente.");
				return $status;
				break;

			default:
				// Error desconocido...
				$status = '403 Forbidden';
				DAV_Log::error("DAV_Server::MKCOL($vfspath) - ($status), Error desconocido, no fue posible crear el recurso.");
				return $status;
		}

	}

	/**
	 * DELETE method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function DELETE($options) {

		$vfspath = $options["path"];

		$ret = XVFS::delete($vfspath);
		if ($ret > 0) $ret = XVFS_NONE;

		switch ($ret) {
			case XVFS_NOT_WRITABLE:
				$status = "403 Forbidden";
				DAV_Log::error("DAV_Server::DELETE($vfspath) - ($status), No tiene permisos para eliminar el recurso.");
				return $status;
				break;

			case XVFS_NOT_EXISTS:
			case XVFS_NOT_IN_REP:
				$status = "404 Not Found";
				DAV_Log::error("DAV_Server::DELETE($vfspath) - ($status), Se ha intentado borrar un recurso que no existe.");
				return $status;
				break;

			case XVFS_BASEIO_ERROR:
				$status = "403 Forbidden";
				DAV_Log::error("DAV_Server::DELETE($vfspath) - ($status), BaseIO informo de un error al intentar eliminar el recurso.");
				return $status;
				break;

			case XVFS_NONE:
				$status = '200 Ok';
				DAV_Log::info("DAV_Server::DELETE($vfspath) - ($status), El recurso indicado se elimino correctamente.");
				return $status;
				break;

			default:
				// Error desconocido...
				$status = '403 Forbidden';
				DAV_Log::error("DAV_Server::DELETE($vfspath) - ($status), Error desconocido, no fue posible eliminar el recurso.");
				return $status;
		}

	}

	/**
	 * MOVE method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function MOVE($options) {
		return $this->COPY($options, true);
	}

	/**
	 * COPY method handler
	 *
	 * @param  array  general parameter passing array
	 * @return bool   true on success
	 */
	function COPY($options, $del=false) {

		// TODO Property updates still broken (Litmus should detect this?)


		$method = $del ? 'MOVE' : 'COPY';

		// rfc2518
		// overwrite = T (true)		El destino sera borrado, si existe, antes de copiar o mover el origen
		// overwrite = F (false)	El proceso debe terminar con un codigo de error y no realizar la operacion indicada
		$overwrite = $options['overwrite'];

		$source = $options['path'];
		$dest = $options['dest'];
		$destPath = dirname($dest);


		// rfc2518: 8.8.5 Status Codes
		//
		// 201 Created:					El origen se ha copiado correctamente creando un nuevo recurso (No existia el destino).
		// 204 No Content:				El origen se ha copiado correctamente en un recurso existinte (Existia el destino).
		// 403 Forbidden:				El origen y el destino son el mismo recurso.
		// 409 Conflict:				Es necesario crear una o mas colecciones para poder crear el recurso.
		// 412 Precondition Failed:		The server was unable to maintain the liveness of the properties
		//								listed in the propertybehavior XML element or the Overwrite header
		//								is "F" and the state of the destination resource is non-null.
		// 423 Locked:					El destino esta bloqueado.
		// 502 Bad Gateway:				Ocurre cuando el destino se encuentra en otro servidor y este rechaza el recurso.
		// 507 Insufficient Storage:	No existe espacio suficiente para almacenar el estado del recurso despues de la ejecucion.
		//
		//
		// En principio basta con ejecutar el metodo copy() del handle para hacer la copia pero
		// los metodos de XVFS actualmente no devuelven codigos de estado, solo TRUE o FALSE.
		// Por este motivo se realizan algunas de estas comprobaciones aqui.


		// no body parsing yet
		if (!empty($_SERVER['CONTENT_LENGTH'])) {
			$status = '415 Unsupported media type';
			DAV_Log::error("DAV_Server::COPY($source, $dest) - ($status), 415 Unsupported media type.");
			return $status;
		}

		// no copying to different WebDAV Servers yet
		if (isset($options['dest_url'])) {
			$status = '502 bad gateway';
			DAV_Log::error("DAV_Server::COPY($source, $dest) - ($status), No es posible copiar un recurso a otro servidor webDAV.");
			return $status;
		}

		// Comprueba si el origen y el destino son el mismo recurso
		if ($source == $dest) {
			$status = '403 Forbidden';
			DAV_Log::error("DAV_Server::COPY($source, $dest) - ($status), El origen y el destino son el mismo recurso.");
			return $status;
		}

		// rfc2518 Section 9.2, last paragraph
		if (XVFS::isDir($source) && ($options['depth'] != 'infinity')) {
			$status = '400 Bad request';
			DAV_Log::error("DAV_Server::COPY($source, $dest) - ($status), Peticion erronea.");
			return $status;
		}

		// Si se sobreescribe el destino la respuesta es distinta, esto es lo que dice
		// el rfc2518, pero lo cierto es que Konqueror no entiende el codigo 204 y lo
		// trata como un error.
		$dest_exists = XVFS::exists($dest);
		$retStr = $dest_exists ? '204 No Content' /*'201 Created'*/ : '201 Created';


		// Ejecuta la accion
		switch ($method) {
			case 'COPY':

				if ($dest_exists) {
					if ($overwrite) {
						DAV_Log::info("DAV_Server::COPY($source, $dest) - El cliente solicita sobreescritura (overwrite: T).");
						$ret = XVFS::delete($dest);
					} else {
						// El cliente no indica que se sobreescriba el contenido,
						// con lo que el servidor informa que ya existe el recurso.
						$ret = XVFS_EXISTS;
						DAV_Log::info("DAV_Server::COPY($source, $dest) - El cliente no solicita sobreescritura (overwrite: F).");
						break;
					}
				}
				$ret = XVFS::copy($source, $dest);
				break;

			case 'MOVE':

				if ($dest_exists) {
					if ($overwrite) {
						DAV_Log::info("DAV_Server::MOVE($source, $dest) - El cliente solicita sobreescritura (overwrite: T).");
						$ret = XVFS::delete($dest);
					} else {
						// El cliente no indica que se sobreescriba el contenido,
						// con lo que el servidor informa que ya existe el recurso.
						$ret = XVFS_EXISTS;
						DAV_Log::info("DAV_Server::MOVE($source, $dest) - El cliente no solicita sobreescritura (overwrite: F).");
						break;
					}
				}
				$ret = XVFS::move($source, $dest);
				break;
		}

		if ($ret > 0) $ret = XVFS_NONE;

		/*
		 * TODO: rfc2518, si falla la copia de algun nivel interno del recurso indicado se debe devolver 207 Multistatus
		 * De la respuesta 207 se deben omitir los codigos 424, 201 y 204.
		 */
		switch ($ret) {
			case XVFS_NOT_EXISTS:
			case XVFS_NOT_IN_REP:
				$status = '404 Not found';
				DAV_Log::error("DAV_Server::$method($source, $dest) - ($status), Se ha intentado renombrar un recurso que no existe.");
				return $status;
				break;

			case XVFS_EXISTS:
				$status = '412 precondition failed';
				DAV_Log::error("DAV_Server::$method($source, $dest) - ($status), Ya existe un recurso con el nombre especificado.");
				return $status;
				break;

			case XVFS_NO_SOURCE:
				$status = '404 Not found';
				DAV_Log::error("DAV_Server::$method($source, $dest) - ($status), Se ha intentado copiar un recurso que no existe.");
				return $status;
				break;

			case XVFS_INVALID_SOURCE:
				$status = '403 Forbidden';
				DAV_Log::error("DAV_Server::$method($source, $dest) - ($status), No se indico un origen valido, posiblemente se indico la raiz del repositorio.");
				return $status;
				break;

			case XVFS_NO_TARGET:
				$status = '409 Conflict';
				DAV_Log::error("DAV_Server::$method($source, $dest) - ($status), Se ha indicado un contenedor de destino que no existe.");
				return $status;
				break;

			case XVFS_NONE:
				$status = $retStr;
				DAV_Log::info("DAV_Server::$method($source, $dest) - ($status), El recurso se ha copiado correctamente.");
				return $status;
				break;

			default:
				$status = '412 precondition failed';
				DAV_Log::error("DAV_Server::$method($source, $dest) - ($status), Ocurrio un error desconocido al intentar copiar el recurso.");
				return $status;
		}

	}

}
 ?>