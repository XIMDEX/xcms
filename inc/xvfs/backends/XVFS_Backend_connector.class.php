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




require_once(XIMDEX_XVFS_PATH . '/XVFS_Backend.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/xvfs/backends/XVFS_Backend_interface.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/io/connection/ConnectionManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/cli/Shell.class.php');

/**
 * @brief Backend for access to repositories via Connectors (inc/io/connection).
 *
 * Implementation of ximDEX backend.
 *
 * NOTE: Las operaciones que crean entidades no devuelven un error si el recurso
 * indicado ya existe en el repositorio, sino que devuelven un codigo indicando que
 * el metodo se ejecuto correctamente.
 * Esto es un workaround necesario cuando se llama al backend desde webDAV.
 *
 */

class XVFS_Backend_connector extends XVFS_Backend implements Backend_XVFS_interface {

	protected $connection = null;

	/**
	 * Constructor
	 * Recibe como parametro la definicion del backend, ruta fisica,
	 * punto de montaje y tipo de backend
	 *
	 * expected uri format ftp://user:passwd/server/subfolder1/subfolder2
	 * @param $uri
	 */
	protected $basePath = '';
	public function XVFS_Backend_connector($uri) {
		/**
		*		Expected format:		 
		*		array(5) {
		*		  ["scheme"]=>
		*		  string(3) "ftp"
		*		  ["host"]=>
		*		  string(9) "localhost"
		*		  ["user"]=>
		*		  string(7) "jmgomez"
		*		  ["pass"]=>
		*		  string(7) "paquito"
		*		  ["vfspath"]=>
		*		  string(2) "/a"
		 */
		$matches = array();
//		preg_match('/(\w+)\:\/\/([^\:]+)\:([^\@]+)@([^\/]+)\/(.*)/', $uri, $matches);
		if (is_array($uri)) {
			$this->_be_base = isset($uri['vfspath']) ? $uri['vfspath'] : '' ;
			$serviceName = isset($uri['scheme']) ? $uri['scheme'] : '' ;
			$serviceUser = isset($uri['user']) ? $uri['user'] : '' ;
			$servicePasswd = isset($uri['pass']) ? $uri['pass'] : '' ;
			$serviceUrl = isset($uri['host']) ? $uri['host'] : '' ;
			$serviceFolder = isset($uri['vfspath']) ? $uri['vfspath'] : '' ;
			$servicePath = isset($uri['path']) ? $uri['path'] : '' ;
			$this->connection = ConnectionManager::getConnection($serviceName);
			$this->connection->connect($serviceUrl);
			$this->connection->login($serviceUser, $servicePasswd);
			$this->connection->cd($servicePath);
			
		}
		parent::XVFS_Backend($uri);
	}


	/**
	 *  Devuelve TRUE si el recurso indicado es un directorio
	 *
	 *  @param string bpath Backendpath
	 *  @return boolean
	 */
	public function isDir($bpath) {
		return $this->connection->isDir($bpath);
	}

	/**
	 * Devuelve TRUE si el recurso indicado es un fichero
	 *
	 * @param string bpath BackendPath
	 * @return boolean
	 */
	public function isFile($bpath) {
//		$rpath = $this->getDescriptor($bpath);
		return $this->connection->isFile($bpath);
	}

	/**
	 * Devuelve TRUE si el recurso indicado existe en el backend
	 *
	 * @param string bpath BackendPath
	 * @return boolean
	 */
	public function exists($bpath) {
//		$rpath = $this->getDescriptor($bpath);
		return $this->connection->isDir($bpath) || $this->connection->isFile($bpath);
	}

	/**
	 * Devuelve TRUE si el recurso indicado puede ser leido
	 *
	 * @param string bpath BackendPath
	 * @return boolean
	 */
	
	// TODO esta función debe hacer un ls y parsear el resultado para obtener los permisos
	public function isReadable($bpath) {
		return true;
	}

	/**
	 * Devuelve TRUE si el recurso indicado se puede escribir
	 *
	 * @param string bpath BackendPath
	 * @return boolean
	 */
	// TODO esta función debe hacer un ls y parsear el resultado para obtener los permisos
	public function isWritable($bpath) {
		return true;
	}

	/**
	 * Devuelve una entidad XVFS a partir del backendpath
	 *
	 * @param string bpath Backendpath
	 * @return object
	 */
	
	public function & read($bpath) {
		$entity = null;
		if (!$this->exists($bpath)) {
			XVFS_Log::warning("Path not found in $bpath");
			return $entity;
		}
		
		if ($this->connection->isDir($bpath)) {
			$entity = new XVFS_Entity_Dir($bpath);
		} else if ($this->connection->isFile($bpath)) {
			$entity = new XVFS_Entity_File($bpath);
		}
		
		if (is_null($entity)) return null;

		// Se cargan los datos de la entidad
		$descriptor = $this->getDescriptor($bpath);
		$rpath = $this->pathInBackEnd($bpath);
		
		// Si el path coincide con el punto de montaje el nombre
		// del recurso debe ser el del punto de montaje, no el
		// el nombre real.
		
		$matches = array();
		$name = preg_match('/.*\/([^\/]+)$/', $bpath, $matches);
		if (count($matches) > 1) {
			$name = $matches[1];
		}
		
		$entity->set('path', $bpath);				// Path real en el medio de almacenamiento
		$entity->set('bpath', $rpath);				// Path en el backend
		$entity->set('rlpath', null);	// Si el recurso es un enlace simbolico, esta propiedad es el path al recurso real al que apunta el link
		$entity->set('name', $name);
		$entity->set('exists', true);
		$entity->set('descriptor', $descriptor);
		$entity->set('mimetype', $this->getMIME($bpath));
		$entity->set('creationdate', null);
		$entity->set('lastmodified', null);

		
		// Si es un directorio se cargaran los hijos del primer nivel
		if (!$entity->get('isdir')) return $entity;

		$collection = array();

		$fileList = $this->connection->ls($bpath);
		
		if (!empty($fileList)) {
			foreach ($fileList as $item) {
				if ($item != '.' && $item != '..') {
					$item_bpath = XVFS::normalizePath($this->pathInBackEnd("$bpath/$item"));
					$collection[] = $item_bpath;
				}
			}
		}
		$entity->set('collection', $collection);
		return $entity;
		
	}

	/**
	 * Obtiene el contenido de una entidad
	 *
	 * @param string bpath BackendPath
	 * @return string
	 */
	public function & getContent($bpath) {
		$content = NULL;
		
		if ($this->connection->isFile($bpath)) {
			$targetFile = XIMDEX_ROOT_PATH . '/data/tmp/ftp_tmp_file';
			$this->connection->get($bpath, $targetFile);
			if (is_file($targetFile)) {
				$content = FsUtils::file_get_contents($targetFile);
			}

			FsUtils::delete($targetFile);
		} 
		return $content;
	}

	/**
	 * Establece el contenido de una entidad.
	 * Es un alias para update().
	 *
	 * @param string bpath Backendpath
	 * @param string content Contenido a asignar
	 * @return int Longitud de la cadena content
	 */
	public function setContent($bpath, $content) {
		return $this->update($bpath, $content);
	}

	/**
	 * Devuelve el tipo mime de una entidad
	 *
	 * @param string bpath Backendpath
	 * @return string
	 */
	public function getMIME($bpath) {
		if ($this->isDir($bpath)) {
			$mime = 'httpd/unix-directory';
		} else {
			$targetFile = XIMDEX_ROOT_PATH . '/data/tmp/ftp_tmp_file';
			$this->connection->get($bpath, $targetFile);
			if ($this->connection->isFile($bpath)) {
				$mime = Shell::exec('file -bi ' . $targetFile);
				$mime = $mime[0];
			}
		}
		
		return $mime;
	}

	/**
	 * Escribe un directorio.
	 *
	 * @param string bpath Backendpath
	 * @return int ID del nuevo nodo o el codigo de error
	 */
	public function mkdir($bpath) {
		return ($this->connection->mkdir($bpath));
	}

	/**
	 * Escribe un fichero y opcionalmente se le asigna un contenido
	 *
	 * @param string bpath Backendpath
	 * @param string content
	 * @return int ID del nuevo nodo o el codigo de error
	 */
	public function append($bpath, $content=null) {
		if ($this->connection->isFile($bpath)) {
			return false;
		} 

		return $this->put($bpath, $content);
	}

	/**
	 * Actualiza el contenido de una entidad.
	 *
	 * @param string bpath Backendpath
	 * @param string content
	 * @return int ID del nodo actualizado o el codigo de error
	 */
	public function update($bpath, $content=null) {
		if (!$this->connection->isFile($bpath)) {
			return false;
		} 
		return $this->put($bpath, $content);
	}

	private function put($bpath, $content) {

		$tmpFile = XIMDEX_ROOT_PATH . '/data/tmp/ftp_tmp_file';
		FsUtils::file_put_contents($tmpFile, $content);
		$result = $this->connection->put($tmpFile, $bpath);
		FsUtils::delete($tmpFile);
		return $result;
	}

	/**
	 * Elimina una entidad
	 *
	 * @param string bpath Backendpath
	 * @return int ID del nodo eliminado o el codigo de error
	 */
	public function delete($bpath) {
		return $this->connection->rm($bpath);
	}
	
	/**
	 * Renombra una entidad.
	 *
	 * @param string bpath BackendPath
	 * @param string newName Nuevo nombre
	 * @return int ID del nodo renombrado o el codigo de error
	 */
	public function rename($bpath, $newName) {
		if ($this->exists($bpath)) {
			return $this->connection->rename($bpath, $newName);
		}
		return false;
	}

	/**
	 * Mueve una entidad
	 *
	 * @param string source BackendPath del nodo origen
	 * @param string target BackendPath del nodo de destino
	 * @return int ID del nodo movido o el codigo de error
	 */
	public function move($source, $target) {
		return $this->rename($source, $target);
	}

	/**
	 * Copia una entidad, si esta es un directorio la copia es recursiva.
	 * Cuando la copia es recursiva intenta que, ante un fallo, se copien
	 * el maximo de recursos posibles. Este comportamiento es "webDAV compliant",
	 * RFC2518 - 8.8.3 Copy for Collections.
	 *
	 * @param string source BackendPath del nodo origen
	 * @param string target BackendPath del nodo de destino
	 * @return int ID del nodo copiado o el codigo de error
	 */
	public function copy($bpath, $target) {
		if (!$this->connection->isFile($realPath)) {
			return $this->connection->rename($realpath, $rpath);
		}
		return false;
	}

	public function getDescriptor($bpath) {}
}

?>