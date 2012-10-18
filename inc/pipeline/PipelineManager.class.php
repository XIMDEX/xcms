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

require_once(XIMDEX_ROOT_PATH . '/inc/pipeline/PipeProcess.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/pipeline/PipeCache.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/App.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/Server.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/orm/RelServersChannels_ORM.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/Versions.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/model/node.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/graphs/GraphManager.class.php');
/**
 * 
 * @brief Manager for the pipeline's system
 * 
 * Manager for the pipeline's system, support actions like get caches and delete caches
 *
 */
class PipelineManager {
	var $messages;
	/**
	 * Constructor, initialize the graphs
	 * 
	 * @return void
	 */
	function PipelineManager() {
		$this->messages =& App::get('Messages');
		GraphManager::createGraph('PipelineGraph', NULL, NULL, 'Gráfica de cache hits de pipelines', 'SeriesToBars');
		GraphManager::createSerie('PipelineGraph', 'Cache request');
		GraphManager::createSerie('PipelineGraph', 'Cache miss');
	}
	
	/**
	 * Get a cache for a document version, transition and parameters, return a file pointer or false on error
	 * 
	 * @param $idVersion
	 * @param $idTransition
	 * @param $args
	 * @return file
	 */
	function getCacheFromTransition($idVersion, $idTransition, $args) {
		GraphManager::createSerieValue('PipelineGraph', 'Cache request', $idVersion, $idTransition);
		if(!isset($args['DISABLE_CACHE']) || $args['DISABLE_CACHE'] === false) {
			if (!($idVersion > 0)) {
				XMD_Log::error('Se ha solicitado una versión inexistente');
				return false;
			}
			if (!$this->_checkChannelIsEnabled($idVersion, $args)) {
				return NULL;
			}
		}	
		$cache = new PipeCache();
		return $cache->load($idVersion, $idTransition, $args, 0);
	}
	
	/**
	 * Wrapper for the method getCacheFromTransition to return string instead of a file pointer
	 * 
	 * @param $idVersion
	 * @param $idTransition
	 * @param $args
	 * @return string
	 */
	function getCacheFromTransitionAsContent($idVersion, $idTransition, $args) {
		return FsUtils::file_get_contents($this->getCacheFromTransition($idVersion, $idTransition, $args));
	}
	
	/**
	 * Get a transformed document who is the result of transform it across a process, returns a file pointer or false on error
	 * 
	 * @param $idVersion
	 * @param $processName
	 * @param $args
	 * @return file
	 */
	function getCacheFromProcess($idVersion, $processName, $args) {
		// TODO Comprobar que el proceso solicitado pertenece a un pipeline que este nodo tiene registrado
		if(!isset($args['DISABLE_CACHE']) || $args['DISABLE_CACHE'] === false) {
			if (!($idVersion > 0)) {
				XMD_Log::error('Se ha solicitado una versión inexistente');
				return false;
			}
		}	
		$process = new PipeProcess();
		if (!$process->loadByName($processName)) {
			$this->messages->add(_('No se ha encontrado el proceso especificado ' . $processName), MSG_TYPE_ERROR);
			XMD_log::fatal('No se ha encontrado el proceso especificado ' .  $processName);
		}
		if (!$process->get('id') > 0) {
			XMD_Log::fatal('No se ha encontrado ningún proceso con el nombre especificado: ' . $processName);
		}
		if (!($process->transitions->count() > 0)) {
			XMD_Log::fatal('El proceso cargado no tiene asociada ninguna transición: '. $processName);
		}

		$lastTransition = $process->transitions->last();
		$idLastTransition = $lastTransition->get('id');
		return $this->getCacheFromTransition($idVersion, $idLastTransition, $args);
	}
	
	/**
	 *  Wrapper for the method getCacheFromProcess to return string instead of a file pointer
	 * 
	 * @param $idVersion
	 * @param $processName
	 * @param $args
	 * @return unknown_type
	 */
	function getCacheFromProcessAsContent($idVersion, $processName, $args) {
		return FsUtils::file_get_contents($this->getCacheFromProcess($idVersion, $processName, $args));
	}
	
	/**
	 * Deletes all caches for a given node version
	 * 
	 * @param $idVersion
	 * @return boolean
	 */
	function deleteCache($idVersion) {
		$version = new Version($idVersion);
		if (!($version->get('IdVersion') > 0)) {
			XMD_Log::error("Se ha solicitado eliminar la cache de la version $idVersion, pero la versión no existe");
			return false;
		}
		
		$pipeCache = new PipeCache();
		$result = $pipeCache->find('id', 'IdVersion = %s', array($idVersion), MONO);
		
		// No se han encontrado caches para la version, por lo que no ha ocurrido ningun error
		if (empty($result)) {
			XMD_Log::info("Se ha solicitado eliminar la cache de la version $idVersion, pero la versión no tiene caches asociadas");
			return true;
		}
		
		reset($result);
		while (list(, $idCache) = each($result)) {

			$pipeCache = new PipeCache($idCache);
			if (!($pipeCache->get('id') > 0)) {
				XMD_Log::error("No hay cache para la versión $idVersion");
				return false;
			}
		
			if (!$pipeCache->delete()) {
				XMD_Log::error("Ha sucedido un error al eliminar la cache $idCache");
				$result = false;
			}
		}
		return isset($result) ? $result : true;
	}
	
	private function _checkChannelIsEnabled($idVersion, $args) {
		// Si no tenemos canal continuamos
		if (!is_array($args)) {
			return true;
		}
		
		if (!(isset($args['CHANNEL']))) {
			return true;
		}
		
		$idChannel = $args['CHANNEL'];
		
		// Si no tenemos nodo abortamos
		$version = new Version($idVersion);
		$idNode = $version->get('IdNode');
		
		if (!($idNode > 0)) {
			XMD_Log::error("Se ha solicitado una cache de la versión $idVersion cuyo nodo asociado no existe");
			return false;
		}
		
		$node = new Node($idNode);
		if (!($node->get('IdNode') > 0)) {
			XMD_Log::error("Se ha solicitado una cache de la versión $idVersion y no se ha podido cargar el nodo");
			return false;
		}
		
		// Si no encontramos un servidor logico abortamos
		$idServer = $node->getServer();
		$server = new Server();
		$result = $server->find('IdServer, Enabled',
			'IdNode = %s',
			array($idServer));
		if (!(count($result) > 0)) {
			XMD_Log::error("Se ha solicitado una cache de la versión $idVersion pero no hay servidores logicos");
			return false;
		}
		
		// Si el servidor está habilitado y existe para un canal generamos la cache
		reset($result);
		while (list(, $serverInfo) = each($result)) {
			if (!$serverInfo['Enabled']) {
				continue;
			}
			if (!$serverInfo['IdServer']) {
				continue;
			}
			$relServerChannel = new RelServersChannels_ORM();
			$relations = $relServerChannel->find('IdRel',
				'IdServer = %s AND IdChannel = %s',
				array($serverInfo['IdServer'], $idChannel),
				MONO);
			
			if (count($relations) > 0) {
				return true;
			}
		}
		XMD_Log::error("No se generará la caché por que no ha ningún canal habilitado para la versión $idVersion");
		return false;
	}

}

?>
