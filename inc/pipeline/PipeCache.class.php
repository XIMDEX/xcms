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




if (!defined('XIMDEX_ROOT_PATH')) {
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}
require_once(XIMDEX_ROOT_PATH . '/inc/model/orm/PipeCaches_ORM.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/pipeline/PipeTransition.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/pipeline/iterators/I_PipePropertyValues.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/log/XMD_log.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/Versions.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/graphs/GraphManager.class.php');


define ('CACHE_FOLDER', '/data/cache/pipelines/');
define ('DATA_FOLDER', '/data/files/');
define ('TMP_FOLDER', '/data/tmp/');
/**
 *
 * @brief Support the Cache system for the pipelines
 *
 * Supports the actions load cache and delete cache, the load cache make calls to
 * the PipeTransition class to get the transition content for a concrete status or
 * load the resulting transition from cache if it is already generated.
 *
 */
class PipeCache extends PipeCaches_ORM {
	var $_args = NULL;
	var $_transition = NULL;
	/**
	 * Constructor
	 * @param $id
	 */
	function PipeCache($id = NULL) {
		parent::GenericData($id);
		if ($this->get('id') > 0) {
			$this->_transition = new PipeTransition($this->get('IdPipeTransition'));
		}
	}
	/**
	 * Devuelve el contenido de una cache, (almacenada o calculada)
	 *
	 * @param int $idVersion
	 * @param int $idTransition
	 * @param array $args
	 */
	function load($idVersion, $idTransition, $args = NULL, $depth = 0) {
		GraphManager::createSerie('PipelineGraph', 'Cache load request');
		GraphManager::createSerieValue('PipelineGraph', 'Cache load request', $idVersion, $idTransition);

		// Busqueda de la cache de lo que tenemos
		if(!isset($args['DISABLE_CACHE']) || $args['DISABLE_CACHE'] === false) {
			$this->_args = $args;
		 	$results = $this->_getCache($idVersion, $idTransition);

		 	if (count($results) > 0) {
		 		$idCache = $this->_checkPropertyValues($idTransition, $results);
		 		if ($idCache) {
		 			$this->PipeCache($idCache);
			 		if ($this->get('id') > 0) {
			 			XMD_Log::info("PipeCache: Se ha estimado la cache correctamente a partir de una regeneraci�n anterior Version: $idVersion Transition: $idTransition");
						GraphManager::createSerie('PipelineGraph', "Cache hit lvl $depth");
						GraphManager::createSerieValue('PipelineGraph', "Cache hit lvl $depth", $idVersion, $idTransition);
						return $this->_getPointer();
			 		} else {
			 			XMD_Log::fatal("PipeCache: Se ha estimado una cache que despu�s ha resultado no existir Version: $idVersion Transition: $idTransition");
			 			return NULL;
			 		}
		 		}
		 	} else {
				XMD_Log::info("PipeCache: Previous cache not found for IdVersion $idVersion, IdTransition $idTransition, arguments " . str_replace("\n", " ", print_r($args, true)));
			}
		}

		$version = new Version($idVersion);
		if ($version->get('IdVersion') > 0) {
			// Busqueda de cache en version anterior si es una version publicada (cuya minor es 0)
			$this->_transition = new PipeTransition($idTransition);
			$lastVersion = $version->get('Version');
			if ($version->get('SubVersion') == '0' && ($lastVersion >= 1)) {
				// Estimamos la �ltima versi�n
				$subVersions = $version->find('IdVersion', 'Version = %s AND IdNode = %s ORDER BY Version DESC, SubVersion DESC',
		 			array(($lastVersion - 1), $version->get('IdNode')), MONO);

		 		if (count($subVersions) > 0 && isset($subVersions[0])) {
		 			$previousVersion = $subVersions[0];
		 			XMD_Log::info('PipeCache Loading previous version for caching :' . $previousVersion);
		 			if ($this->_transition->get('id') > 0) {

		 				$caches = $this->_getCache($idVersion, $idTransition);

					 	if (count($caches) > 0) {
					 		$idCache = $this->_checkPropertyValues($idTransition, $caches);
					 		if ($idCache) {
					 			$pipeCache = new PipeCache($idCache);
						 		if ($pipeCache->get('id') > 0) {
	 								XMD_Log::info('PipeCache Successfully loading :' . $previousVersion);
	 								// copying the content to new cache
						 			$pointer = $pipeCache->_getPointer();
						 			$this->store($idVersion, $idTransition, $pointer, $args);
									GraphManager::createSerie('PipelineGraph', "Cache hit lvl $depth");
									GraphManager::createSerieValue('PipelineGraph', "Cache hit lvl $depth", $idVersion, $idTransition);
						 			return $pointer;
						 		} else {
						 			XMD_Log::info('PipeCache (1) Previous cache version is not generated, regenarating for current version. :' . $previousVersion);
						 		}
					 		} else {
						 		XMD_Log::info('PipeCache (2) Previous cache version is not generated, regenarating for current version. :' . $previousVersion);
					 		}
					 	} else {
		 					XMD_Log::warning('PipeCache (3) Previous cache version is not generated. :' .  $previousVersion);
					 	}
		 			} else {
		 				XMD_Log::warning('PipeCache Previous transition dont exists. :' . $previousVersion);
		 			}
		 		} else {
		 			XMD_Log::warning('PipeCache previous version not found');
		 		}
		 	}
		}

	 	// Si llegamos a este punto hay que regenerar la cache
	 	$this->_transition = new PipeTransition($idTransition);
	 	$previousTransition = $this->_transition->getPreviousTransition();

	 	if ($previousTransition) {
	 		$cache = new PipeCache();
			$pointer = $cache->load($idVersion, $previousTransition, $args, $depth + 1);
			if ($pointer) {
				GraphManager::createSerie('PipelineGraph', "Cache generation");
				GraphManager::createSerieValue('PipelineGraph', "Cache generation", $idVersion, $idTransition);
				return $this->_transition->generate($idVersion, $pointer, $args);
			}
	 	} else {
			if(!isset($args['DISABLE_CACHE']) || $args['DISABLE_CACHE'] === false) {
		 		$version = new Version($idVersion);
		 		$pointer = XIMDEX_ROOT_PATH . DATA_FOLDER . $version->get('File');
		 		GraphManager::createSerieValue('PipelineGraph', 'Cache miss', $idVersion, $idTransition);
			} else {
		 		$pointer = XIMDEX_ROOT_PATH . TMP_FOLDER . FsUtils::getUniqueFile(XIMDEX_ROOT_PATH . TMP_FOLDER);
				if (!isset($args['CONTENT'])) {
					XMD_Log::error('PipeCache error, no content to write.');
		 			return null;
				} else if (!FsUtils::file_put_contents($pointer, $args['CONTENT'])) {
		 			XMD_Log::error('PipeCache error writting file content');
		 			return null;
		 		}
			}
			GraphManager::createSerie('PipelineGraph', "Cache generation");
			GraphManager::createSerieValue('PipelineGraph', "Cache generation", $idVersion, $idTransition);
			return $this->_transition->generate($idVersion, $pointer, $args);
	 	}
	 }

	 private function _getCache($idVersion, $idTransition, $allCaches = false) {
	 	if ($allCaches) {
		 	$query = sprintf('Select pc.id'
		 		. ' FROM PipeCaches pc INNER JOIN PipeTransitions pt ON pc.IdPipeTransition = pt.id'
		 		. ' WHERE IdVersion = %s AND IdPipeTransition = %s', $idVersion, $idTransition);
	 	} else {
		 	$query = sprintf('Select pc.id'
		 		. ' FROM PipeCaches pc INNER JOIN PipeTransitions pt ON pc.IdPipeTransition = pt.id AND pt.Cacheable = 1'
		 		. ' WHERE IdVersion = %s AND IdPipeTransition = %s', $idVersion, $idTransition);
	 	}
	 	$result = $this->query($query, MONO, 'id');
		XMD_Log::info("PipeCache: Resultado del m�todo _getCache " . str_replace("\n", " ", print_r($result, true)));
		return $result;
	 }

	 /**
	  * Comprueba las propiedades de una cache y devuelve la cache que corresponde
	  *
	  * @param int $idTransition
	  * @param array $idCaches
	  */
	 function _checkPropertyValues($idTransition, $idCaches) {
	 	//Primero comprobamos si tenemos todos los argumenos, si no los tenemos damos un fatal
		if (empty($this->_args)) $this->_args = array();
	 	$keys = array_keys($this->_args);
	 	$propertiesIds = array();
	 	$this->_transition = new PipeTransition($idTransition);
	 	if ($this->_transition->properties->count() > 0) {
		 	$this->_transition->properties->reset();
	 		while($property = $this->_transition->properties->next()) {
		 		if (!$this->_searchKeyInArgs($property->get('Name'), $this->_args)) {
		 			return false;
		 		}
		 		$propertiesIds[] = $property->get('id');
		 		$association[$property->get('id')] = $property->get('Name');
		 	}
	 	} else {
	 		if (count($idCaches) == 1) {
	 			return $idCaches[0];
	 		} else {
	 			XMD_Log::fatal('PipeCache No se ha podido estimar una �nica cach�');
	 			return false;
	 		}
	 	}

	 	// Ahora por cada cache buscamos si tenemos un conjunto de tuplas en propertyValues que satisfagan nuestras condiciones
	 	$countProperties = count($propertiesIds);
	 	for ($i = 0; $i < $countProperties; $i++) {
	 		$queryWhere[] = 'IdPipeProperty = %s';
	 	}

	 	if (isset($queryWhere)) {
	 		$queryArray[] = "(" . implode(' OR ', $queryWhere) . ")";
	 	}

	 	reset($idCaches);

 		$queryArray[] = 'IdPipeCache = %s';
	 	while(list(, $idCache) = each($idCaches)) {
	 		$localQuery = implode(' AND ', $queryArray);
	 		$localArgs = array_merge($propertiesIds, array($idCache));
			$propertyValuesIterator = new I_PipePropertyValues($localQuery, $localArgs);

			$continue = false;
			while ($propertyValue = $propertyValuesIterator->next()) {
				if ($propertyValue->get('Value') != $this->_args[$association[$propertyValue->get('IdPipeProperty')]]) {
					$continue = true;
					break;
				}
			}
			if (!$continue) {
				return $idCache;
			}

	 	}
	 	return NULL;
	 }

	 function _searchKeyInArgs($key, $args) {
	 	if (is_array($args)) {
		 	reset($args);
		 	while(list($index, $value) = each($args)) {
		 		if ($index == $key) {
		 			return $value;
		 		}
		 	}
	 	}

	 	return NULL;
	 }

	 function _getPointer() {
	 	$cacheFile = XIMDEX_ROOT_PATH . CACHE_FOLDER . $this->get('File');
	 	if (is_file($cacheFile)) {
	 		return $cacheFile;
	 	}
	 	return NULL;
	 }

	 /**
	  * Stores a cache for the given parameters
	  *
	  * @param $idVersion
	  * @param $idTransition
	  * @param $contentFile
	  * @param $args
	  * @return boolean
	  */
	 function store($idVersion, $idTransition, $contentFile, $args) {
		XMD_Log::info("PipeCache: Trying to store cache for version $idVersion transition $idTransition args " . print_r($args, true));
	 	$this->_transition = new PipeTransition($idTransition);
	 	if (!($this->_transition->get('id') > 0)) {
	 		XMD_Log::fatal('PipeCache: Error storing cache, no se ha podido estimar la transicion a la que se va a asociar la cach�: ' . $idTransition);
	 		return false;
	 	}

	 	$caches = $this->_getCache($idVersion, $idTransition, true);
	 	$cacheFile = '';

	 	if (!empty($caches)) {
	 		if (count($caches) > 1) {
				XMD_Log::warning('PipeCache: Multiple cache found we are going to delete them (should be only one)' . print_r($caches, true));
	 			foreach($caches as $idPipeCache) {
	 				$pipeCache = new PipeCache($idPipeCache);
	 				$pipeCache->delete();
	 				unset($pipeCache);
	 			}
	 		}
	 		if (count($caches) == 1) {
				$idCache = $this->_checkPropertyValues($idTransition, $caches);
				if ($idCache > 0) {
					XMD_Log::warning('PipeCache: Cache found returning file' . print_r($caches, true));
		 			$pipeCache = new PipeCache($idCache);
		 			$cacheFile = $pipeCache->get('File');
				}
	 		}
	 	}
	 	XMD_Log::info('PipeCache: found cacheFile ' . $cacheFile);
	 	if (empty($cacheFile)) {
		 	$this->set('IdVersion', $idVersion);
		 	$this->set('IdPipeTransition', $idTransition);

		 	$cacheFile = FsUtils::getUniqueFile(XIMDEX_ROOT_PATH . CACHE_FOLDER);
		 	if (FsUtils::copy($contentFile, XIMDEX_ROOT_PATH . CACHE_FOLDER . $cacheFile)) {
			 	$this->set('File', $cacheFile);
		 	}

		 	$idCache = $this->add();
		 	if (!$idCache > 0) {
		 		XMD_Log::error("PipeCache: Ha sucedido un error al almacenar la cache");
		 		return false;
		 	}
	 	} else {

		 	if (!FsUtils::copy($contentFile, XIMDEX_ROOT_PATH . CACHE_FOLDER . $cacheFile)) {
		 		XMD_Log::error("PipeCache: Ha sucedido un error al sustituir la cache (Posible problema de permisos en data/cache/pipelines)");
				return false;
		 	}
	 	}
	 	if (!isset($idCache)) {
			XMD_Log::error("PipeCache: No se ha obtenido un idCache V�lido $idVersion $idTransition $contentFile");
			return false;
		}
	 	$this->_transition->properties->reset();
	 	if ($this->_transition->properties->count() > 0) {
	 		$this->_transition->properties->reset();
	 		while($property = $this->_transition->properties->next()) {
	 			$propertyValue = new PipePropertyValue();
	 			$propertyValue->set('IdPipeProperty', $property->get('id'));

	 			$propertyValue->set('IdPipeCache', $idCache);

	 			$propertyValue->set('Value', $this->_searchKeyInArgs($property->get('Name'), $args));
	 			if (!$propertyValue->add()) {
	 				XMD_Log::error('PipeCache: Error al intentar almacenar la propiedad');
	 			}
	 		}
	 	}

		return true;
	 }

	 /**
	  * (non-PHPdoc)
	  * @see inc/helper/GenericData#delete()
	  */
	function delete() {
		if (!($this->get('id') > 0)) {
			return false;
		}
		$db = new DB();
		$query = sprintf("DELETE FROM PipePropertiesCache WHERE IdPipeCache = %s", $db->sqlEscapeString($this->get('id')));
		$db->execute($query);

		FsUtils::delete(XIMDEX_ROOT_PATH . '/data/cache/pipelines/' . $this->get('File'));

		return parent::delete();
	}

}

?>