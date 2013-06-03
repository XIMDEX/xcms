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

require_once(XIMDEX_ROOT_PATH . '/inc/pipeline/iterators/I_PipeTransitions.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/orm/PipeProcess_ORM.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/pipeline/PipeStatus.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/App.class.php');
/**
 * 
 * @brief Describes one Pipeline process
 * 
 * Describes one Pipeline process, one pipeline is actually formed by various
 * processes, it is reccomended every transition for a single processes to have same cache status.
 *
 */
class PipeProcess extends PipeProcess_ORM {
	var $transitions;
	/**
	 * Constructor
	 * @param $id
	 */
	function PipeProcess($id = NULL) {
		parent::GenericData($id);
		if ($this->get('id') > 0) {
			$this->transitions = new I_PipeTransitions('IdPipeProcess = %s', array($id));
		}
	}
	
	/**
	 * Load a pipeline by name instead of by id
	 * 
	 * @param $name
	 * @return boolean
	 */
	function loadByName($name) {
		if (empty($name)) {
			XMD_Log::error('Se ha solicitado la carga de un proceso sin introducir su nombre');
			return false;
		}
		
		$result = $this->find('id', 'Name = %s', array($name), MONO);
		if (count($result) == 1) {
			$this->PipeProcess($result[0]);
			return true;
		}
		return false;
	}
	
	/**
	 * Load the previous process in list
	 * 
	 * @return previous process id or null
	 */
	function getPreviousProcess() {
		//Obtenemos la transición anterior
		$result = $this->find('id', 'IdPipeline = %s AND IdTransitionTo = %s',
					array($this->get('IdPipeline'), $this->get('IdTransitionFrom')), MONO);
		$resultsCount = count($result);
		//Si son muchas error (No previsto, creo que ni siquiera lo soporta el modelo)
		if ($resultsCount > 1) {
			XMD_Log::fatal('No se ha podido determinar el proceso anterior a uno dado');
			return false;
		}
		
		if ($resultsCount == 1) {
			return $result[0];
		}
		return NULL;
	}
	
	/**
	 * Removes a intermediate status from a sequence
	 * 
	 * @param $idStatus
	 * @return boolean
	 */
	function removeStatus($idStatus) {
		if (!($this->get('id') > 0)) {
			XMD_Log::error('No se ha podido encontrar el proceso de workflow');
			$this->messages->add(_('Ha ocurrido un error no recuperable durante la gestión de estados de workflow, consulte con su administrador'), 
				MSG_TYPE_ERROR);
			return false;
		}
		
		
		$transitionFrom = NULL;
		$transitionTo = NULL;
		$this->transitions->reset();
		while($transition = $this->transitions->next()) {
			if ($transition->get('IdStatusFrom') == $idStatus) {
				$transitionFrom = $transition;
			}
			if ($transition->get('IdStatusTo') == $idStatus) {
				$transitionTo = $transition;
			}
		}

		if (!(is_object($transitionFrom) && is_object($transitionTo))) {
			$this->messages->add(_('No se han podido determinar las transiciones de un estado para su eliminación, esto es normal si el estado es estado inicial o final'), MSG_TYPE_WARNING);
			XMD_Log::warning('No se han podido determinar las transiciones de un estado para su eliminación, esto es normal si el estado es estado inicial o final');
			return false;
		}
		
		$transitionTo->set('IdStatusTo', $transitionFrom->get('IdStatusTo'));
		$transitionTo->update();
		
		$transitionFrom->delete();
		
		// Check if there is any back reference to the status, else remove it
		
		$results = $this->find('id', 'IdStatusFrom = %s OR IdStatusTo = %s', array($idStatus, $idStatus), MONO);
		if (count($results) == 0) {
			$pipeStatus = new PipeStatus($idStatus);
			$pipeStatus->delete();
		}
		return true;
	}
	
	/**
	 * Return true if the given status is the first in the pipeline
	 * 
	 * @param $idStatus
	 * @return boolean
	 */
	function isStatusFirst($idStatus) {
		$transition = $this->transitions->first();
		
		return ($transition->get('IdStatusFrom') == $idStatus);

	}
	
	/**
	 * Return true if the given status is the last in the pipeline
	 * 
	 * @param $idStatus
	 * @return boolean
	 */
	function isStatusLast($idStatus) {
		$transition = $this->transitions->last();
		
		return ($transition->get('IdStatusTo') == $idStatus);
	}
	
	/**
	 * Return the first status in the pipeline
	 * 
	 * @return integer
	 */
	function getFirstStatus() {
		$transition = $this->transitions->first();
		return $transition->get('IdStatusFrom');		
	}
	
	/**
	 * Return the last status in the pipeline
	 * 
	 * @return integer
	 */
	function getLastStatus() {
		$transition = $this->transitions->last();
		return $transition->get('IdStatusTo');	
	}
	
	/**
	 * Return the next status from the given one
	 * 
	 * @param $idStatus
	 * @return integer
	 */
	function getNextStatus($idStatus) {
		$this->transitions->reset();
		 
		while ($transition = $this->transitions->next()) {
			if ($transition->get('IdStatusFrom') == $idStatus) {
				return $transition->get('IdStatusTo');
			}
		}
		return $this->getFirstStatus();
	}
	
	/**
	 * Return the previous status from the given on
	 * 
	 * @param $idStatus
	 * @return integer
	 */
	function getPreviousStatus($idStatus) {
		$this->transitions->reset(); 
		while ($transition = $this->transitions->next()) {
			if ($transition->get('IdStatusTo') == $idStatus) {
				return $transition->get('IdStatusFrom');
			}
		}
		return $this->getFirstStatus();
	}

	/**
	 * Return all the status from this pipeline sorted
	 * 
	 * @return array
	 */
	function getAllStatus() {
		$allStatus = array();
		$this->transitions->reset();
		while ($transition = $this->transitions->next()) {
			$pipeStatus = new PipeStatus($transition->get('IdStatusFrom'));
			$allStatus[] = $pipeStatus->get('id');
			$lastTransition = $transition;
		}
		$pipeStatus = new PipeStatus($lastTransition->get('IdStatusTo'));
		$allStatus[] = $pipeStatus->get('id');
		return $allStatus;
	}
	
	/**
	 * Return one transition in which the given status is first for this pipeline
	 * 
	 * @param $idStatusFrom
	 * @return integer
	 */
	function getTransition($idStatusFrom) {
		$this->transitions->reset();
		while ($localTransition = $this->transitions->next()) {
			if ($localTransition->get('IdStatusFrom') == $idStatusFrom) {
				return $localTransition->get('id');
			}
		}
		return NULL;
	}
/*	
	function addTransition($name, $description) {
		if (!($this->get('id')) > 0) {
			return false;
		}
		
		$pipeStatus = new PipeStatus();
		$pipeStatus->set('Name', $name);
		$pipeStatus->set('Description', $description);
		$idPipeStatus = $pipeStatus->add();
		
		if (!$idPipeStatus > 0) {
			$this->messages->add(_('No se ha podido insertar el estado'), MSG_TYPE_ERROR);
			return false;
		}
		
		$pipeTransition = new PipeTransition();
		$pipeTransition->set('IdStatusTo', $idPipeStatus);
		$pipeTransition->set('IdPipeProcess', $this->get('id'));
		$pipeTransition->set('Cacheable', 0);
		$pipeTransition->set('Name', $this->get('Name') . '_0');
		$pipeTransition->set('Callback', '-');
		$idPipeTransition = $pipeTransition->add();
		
		$this->set('IdTransitionTo', $idPipeTransition);
		return $this->update();
	}
*/
}

?>
