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
require_once(XIMDEX_ROOT_PATH . '/inc/pipeline/PipeCache.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/pipeline/PipeProcess.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/pipeline/iterators/I_PipeProperties.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/patterns/Factory.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/orm/PipeTransitions_ORM.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/log/XMD_log.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/App.class.php');
require_once(XIMDEX_ROOT_PATH . "/inc/helper/Timer.class.php");
require_once(XIMDEX_ROOT_PATH . '/inc/graphs/GraphManager.class.php');

define('CALLBACK_FOLDER', XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/');
/**
 * 
 * @brief Describes a Transition in the pipeline
 * 
 * Describes a transition in the pipeline, a pipeline is formed by one or more Processes,
 * each process contains one or more transitions, and one transition contains exactly two
 * status, so this class describes the transition between two status and the transformation
 * who is called in the process
 *
 */
class PipeTransition extends PipeTransitions_ORM {
	var $properties = NULL;
	/**
	 * Constructor
	 * @param $id
	 * 
	 */
	function PipeTransition($id = NULL) {
		parent::GenericData($id);
		
		$id = $this->get('id');
		if (!($id > 0)) {
			return ;
		}
		
		$this->properties = new I_PipeProperties('IdPipeTransition = %s', array($id));
	}
	
	/**
	 * Add a status to this transition, primaly used for workflow pipelines
	 * 
	 * @param $name
	 * @param $description
	 * @return integer
	 */
	function addStatus($name, $description) {
		$pipeStatus = new PipeStatus();
		$pipeStatus->set('Name', $name);
		$pipeStatus->set('Description', $description);
		$idStatus = $pipeStatus->add();
		
		if (!($idStatus > 0)) {
			$this->messages->add(_('Ha ocurrido un error insertando el estado, consulte con su administrador'), MSG_TYPE_ERROR);
			$this->messages->mergeMessages($this->messages);
			return NULL;
		}
		
		$finalStatus = new PipeStatus($this->get('IdStatusTo'));
		
		$pipeTransition = new PipeTransition();
		$pipeTransition->set('IdStatusFrom', $idStatus);
		$pipeTransition->set('IdStatusTo', $finalStatus->get('id'));
		$pipeTransition->set('Name', sprintf('%s_to_%s', $pipeStatus->get('Name'), $finalStatus->get('Name')));
		$pipeTransition->set('IdPipeProcess', $this->get('IdPipeProcess'));
		$pipeTransition->set('Cacheable', 0);
		$pipeTransition->set('Callback', '-');
		$idNewTransition = $pipeTransition->add();
		
		if (!($idNewTransition > 0)) {
			$this->messages->add(_('No se ha podido generar la nueva transición'), MSG_TYPE_ERROR);
			$this->messages->mergeMessages($pipeTransition->messages);
			return NULL;
		}
		
		$this->set('IdStatusTo', $idStatus);
		$this->set('Callback', '-');
		$this->update();
		
		return $idStatus;
		
	}
	
	/**
	 * Return the previous transition in the pipeline
	 * @return integer
	 */
	function getPreviousTransition() {
		//Obtenemos la transición anterior
		$result = $this->find('id', 'IdPipeProcess = %s AND IdStatusTo = %s',
					array($this->get('IdPipeProcess'), $this->get('IdStatusFrom')), MONO);
		$resultsCount = count($result);
		//Si son muchas error (No previsto, creo que ni siquiera lo soporta el modelo)
		if ($resultsCount > 1) {
			XMD_Log::fatal('No se ha podido determinar la transicion anterior a una dada');
			return false;
		}
		
		if ($resultsCount == 1) {
			return $result[0];
		}
		
		// Si no la encontramos en este proceso buscamos en el proceso anterior
		$id = $this->get('IdPipeProcess');
		$process = new PipeProcess($id);
		$idProcess = $process->getPreviousProcess();
		if (!$idProcess > 0) {
			return NULL;
		}
		$process = new PipeProcess($idProcess);
		$transition = $process->transitions->last();
		if (is_null($transition)) {
			return NULL;
		}
		return $transition->get('id');
	}
	
	/**
	 * Transform the given content with the transition asociated callback
	 * @param $idVersion
	 * @param $content
	 * @param $args
	 * @return pointer
	 */
	function generate($idVersion, $content, $args) {
		$v = $this->callback($idVersion, $content, $args, 'transform');
		return $v;
	}
	
	/**
	 * Reverse a transformation
	 * @param $idVersion
	 * @param $content
	 * @param $args
	 * @return pointer
	 */
	function reverse($idVersion, $content, $args) {
		return $this->callback($idVersion, $content, $args, 'reverse');
	}
	
	/**
	 * Method who contains the generate and reverse implementations, should be private
	 * 
	 * @param $idVersion
	 * @param $pointer
	 * @param $args
	 * @param $function
	 * @return pointer
	 */
	function callback($idVersion, $pointer, $args, $function) {
		$factory = new Factory(CALLBACK_FOLDER, 'View_');
		$callback = $this->get('Callback');
		GraphManager::createSerie('PipelineGraph', $callback);
		GraphManager::createSerieValue('PipelineGraph', $callback, $idVersion);
		
		$object = $factory->instantiate($callback);

		$timer = new Timer();

		$timer->start();
		if (method_exists($object, $function)) {
			$transformedPointer = $object->$function($idVersion, $pointer, $args);
		} else {
			$idTransition = $this->get('id');
			XMD_Log::warning("No se ha encontrado el método $function al llamar a una vista: IdVersion $idVersion transición $idTransition");
			$transformedPointer = $pointer;		
		}
		$timer->stop();

		XMD_Log::info("PIPETRANSITION: View_$callback time: " . $timer->display());

		if(isset($args['DISABLE_CACHE']) && $args['DISABLE_CACHE'] === true) {
			XMD_Log::info('DISABLE_CACHE activo, aunque la transición es cacheable, no se almacenará la caché.');
		} else {
			$cache = new PipeCache();
			if (!$cache->store($idVersion, $this->get('id'), $transformedPointer, $args)) {
				XMD_Log::error('No se ha podido almacenar la cache para la transición ');
			}
		}
		
		return $transformedPointer;
	}
	
	
}

?>