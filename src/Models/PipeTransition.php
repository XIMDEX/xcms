<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Models\ORM\PipeTransitionsOrm;
use Ximdex\Runtime\App;

define('CALLBACK_FOLDER', XIMDEX_ROOT_PATH . '/src/Nodeviews/');

/**
 * @brief Describes a Transition in the pipeline
 *
 * Describes a transition in the pipeline, a pipeline is formed by one or more Processes,
 * each process contains one or more transitions, and one transition contains exactly two
 * status, so this class describes the transition between two status and the transformation
 * who is called in the process
 */
class PipeTransition extends PipeTransitionsOrm
{
	/**
	 * @var \Ximdex\Pipeline\Iterators\IteratorPipeProperties|null
     */
	public $properties = null;

	/**
	 * Constructor
	 * @param $id
	 *
	 */
	public function __construct(int $id = null)
	{
		parent::__construct($id);
		$id = $this->get('id');
		if (! $id) {
			return;
		}
		$this->properties = new \Ximdex\Pipeline\Iterators\IteratorPipeProperties('IdPipeTransition = %s', array($id));
	}

	/**
	 * Add a status to this transition, primaly used for workflow pipelines
	 * 
	 * @param string $name
	 * @param string $description
	 * @return NULL|bool|string
	 */
	public function addStatus(string $name, string $description = null)
	{
		$pipeStatus = new PipeStatus();
		$pipeStatus->set('Name', $name);
		$pipeStatus->set('Description', $description);
		$idStatus = $pipeStatus->add();
		if (! $idStatus) {
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
		$pipeTransition->set('Callback', null);
		$idNewTransition = $pipeTransition->add();
		if (! $idNewTransition) {
			$this->messages->add(_('No se ha podido generar la nueva transición'), MSG_TYPE_ERROR);
			$this->messages->mergeMessages($pipeTransition->messages);
			return NULL;
		}
		$this->set('IdStatusTo', $idStatus);
		$this->set('Callback', null);
		$this->update();
		return $idStatus;
	}

	/**
	 * Return the previous transition in the pipeline
	 * 
	 * @return int|NULL|bool
	 */
	public function getPreviousTransition()
	{
		// Obtenemos la transición anterior
		$result = $this->find('id', 'IdPipeProcess = %s AND IdStatusTo = %s',
		      array($this->get('IdPipeProcess'), $this->get('IdStatusFrom')), MONO);
		$resultsCount = count($result);
		
		// Si son muchas error (No previsto, creo que ni siquiera lo soporta el modelo)
		if ($resultsCount > 1) {
			Logger::fatal('No se ha podido determinar la transicion anterior a una dada');
			return false;
		}
		if ($resultsCount == 1) {
			return $result[0];
		}
		
		// Si no la encontramos en este proceso buscamos en el proceso anterior
		$id = $this->get('IdPipeProcess');
		$process = new PipeProcess($id);
		$idProcess = $process->getPreviousProcess();
		if (! $idProcess) {
			return null;
		}
		$process = new PipeProcess($idProcess);
		$transition = $process->transitions->last();
		if (is_null($transition)) {
			return null;
		}
		return $transition->get('id');
	}

	/**
	 * Transform the given content with the transition asociated callback
	 * 
	 * @param int $idVersion
	 * @param string $content
	 * @param array $args
	 * @return mixed|bool
	 */
	public function generate(int $idVersion, string $content, array $args)
	{
	    Logger::info('Transforming ' . $content . ' with the version ' . $idVersion);
		$file = $this->callback($idVersion, $content, $args, 'transform');
		return $file;
	}

	/**
	 * Reverse a transformation
	 * 
	 * @param int $idVersion
	 * @param string $content
	 * @param array $args
	 * @return mixed|bool
	 */
	public function reverse(int $idVersion, string $content, array $args)
	{
		return $this->callback($idVersion, $content, $args, 'reverse');
	}

	/**
	 * Method who contains the generate and reverse implementations, should be private
	 * 
	 * @param int $idVersion
	 * @param string $pointer
	 * @param array $args
	 * @param string $function
	 * @return bool|string
	 */
	private function callback(int $idVersion, string $pointer, array $args, string $function)
	{
		$factory = new \Ximdex\Utils\Factory(CALLBACK_FOLDER, 'View');
		$callback = $this->get('Callback');
        $callback = str_replace('_', '', ucfirst($callback) );
		$object = $factory->instantiate($callback, null, 'Ximdex\Nodeviews');
		$timer = new \Ximdex\Utils\Timer();
		$timer->start();
		if (method_exists($object, $function)) {
		    $msg = 'TRANSITION START: Calling method: ' . $function . ' to process version: ' . $idVersion;
		    if (isset($args['NODENAME'])) {
		        $msg .= ' of document: ' . $args['NODENAME'];
		    }
		    Logger::info($msg);
			$transformedPointer = $object->$function($idVersion, $pointer, $args);
			if (strpos($pointer, App::getValue('TempRoot')) and file_exists($pointer)) {
			    @unlink($pointer);
			}
			if ($transformedPointer === false or $transformedPointer === null) {
			    $timer->stop();
			    return false;
			}
		} else {
			$idTransition = $this->get('id');
			Logger::error('Method $function not found when calling to the view (IdVersion: ' . $idVersion . ', Transition: ' . $idTransition . ')');
			$transformedPointer = $pointer;
		}
		$timer->stop();
		Logger::info('PIPETRANSITION: View_' . $callback . ' time: ' . $timer->display());
		if ((isset($args['DISABLE_CACHE']) && $args['DISABLE_CACHE']) or ! $this->Cacheable) {
			Logger::info('DISABLE_CACHE active or NON_CACHEABLE transition. The cache won\'t be stored');
		} else {
 			$cache = new PipeCache();
			if (! $cache->store($idVersion, $this->get('id'), $transformedPointer, $args)) {
				Logger::error('Could not store the cache for transition with version ' . $idVersion);
				return false;
			} else {
			    Logger::info('Cache was generated/reversed successfusly for version: ' . $idVersion);
			}
		}
		$msg = ' for version: ' . $idVersion . ' and callback: ' . $callback;
		if (isset($args['CHANNEL'])) {
		    $msg .= ' with channel: ' . $args['CHANNEL'];
		}
		if (isset($args['TRANSFORMER'])) {
		    $msg .= ' with transformer: ' . $args['TRANSFORMER'];
		}
		if (isset($args['DISABLE_CACHE'])) {
		    $msg .= ' with cache disabled: ' . $args['DISABLE_CACHE'];
		}
		if (isset($args['NODEID'])) {
		    $msg .= ' with node ID: ' . $args['NODEID'];
		}
		if (isset($args['NODENAME'])) {
		    $msg .= ' and name: ' . $args['NODENAME'];
		}
		if ($transformedPointer) {
		    Logger::info('TRANSITION END: Pipeline Transition has been successfusly processed' . $msg);
		} else {
		    Logger::error('TRANSITION END: Pipeline Transition has not been processed' . $msg);
		}
		return $transformedPointer;
	}
}
