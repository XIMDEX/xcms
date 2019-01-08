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
use Ximdex\Models\ORM\PipelinesOrm;
use Ximdex\Runtime\App;

/**
 * @deprecated
 * @brief Orm extension for the pipeline table
 *
 * Extends the functionality for the Pipeline table, restricts the functionality and provides methods to load
 * some specific node status.
 */
class Pipeline extends PipelinesOrm
{
    /**
     * @deprecated
     * @var $processes \Ximdex\Pipeline\Iterators\IteratorPipeProcesses
     */
    public $processes; 
   
    /**
     * @var int
     */
    public $idNodeType;
    
    /**
     * @var bool
     */
    public $isWorkflowMaster;

    /**
     * Constructor for the Pipeline class
     *
     * @param int $id
     */
    public function __construct(int $id = null)
    {
        parent::__construct($id);
        $this->isWorkflowMaster = false;
        $pipeNodeType = new PipeNodeTypes();
        $result = $pipeNodeType->find('IdNodeType', 'IdPipeline = %s', array($this->get('id')), MONO);
        if (count($result) === 1) {
            $this->idNodeType = $result[0];
        }
        if ($this->get('id') and App::getValue('IdDefaultWorkflow') == $this->get('IdNode')) {
            $this->isWorkflowMaster = true;
        }
    }

    /**
     * Returns the pipeline who manages the given nodetype
     *
     * @param int $idNodeType
     * @return int|bool
     */
    public function loadByNodeType(int $idNodeType)
    {
        $this->__construct();
        $nodeType = new NodeType($idNodeType);
        if (! $nodeType->get('IdNodeType')) {
            Logger::error('El nodetype especificado para el pipeline no existe: ' . $idNodeType);
            $this->messages->add(_("An error has occurred while the document's transformation and the process cannot continue"), MSG_TYPE_ERROR);
            return false;
        }
        $result = $this->find('id', 'IdNodeType = %s', array($idNodeType));
        if (count($result) == 1) {
            $this->__construct($result[0]);
            return (int) $this->get('id');
        }
        $error = sprintf(_("Ha ocurrido un error inesperado al intentar transformar el nodeType %s"), $idNodeType);
        Logger::error($error);
        $this->messages->add($error, MSG_TYPE_ERROR);
        return false;
    }
    
    /**
     * Return a pipeline by idnode (used primary in the pipelines who acts as workflow)
     *
     * @param int $idNode
     * @return int|null
     */
    public function loadByIdNode(int $idNode) : ?int
    {
        $this->__construct();
        $result = $this->find('id', 'IdNode = %s', array($idNode), MONO);
        if (count($result) > 1) {
            $error = "Se ha intentado cargar el pipeline con el idnode $idNode y se han encontrado multiples resultados, abortando operación";
            Logger::warning($error);
            $this->messages->add(_($error), MSG_TYPE_WARNING);
            return null;
        }
        if (count($result) === 0) {
            $error = "Se ha intentado cargar el pipeline con el idnode $idNode y no se han encontrado resultados, abortando operación";
            Logger::warning($error);
            $this->messages->add(_($error), MSG_TYPE_WARNING);
            return null;
        }
        $this->__construct($result[0]);
        return (int) $this->get('id');
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\Data\GenericData::set()
     */
    public function set(string $key, string $value = null) : bool
    {
        if ($key == 'IdNodeType') {
            $this->idNodeType = $value;
            return true;
        }
        return parent::set($key, $value);
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\Data\GenericData::get()
     */
    public function get(string $key)
    {
        if ($key == 'IdNodeType') {
            return $this->idNodeType;
        }
        return parent::get($key);
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\Data\GenericData::add()
     */
    public function add()
    {
        $idPipeline = parent::add();
        if ($idPipeline > 0) {
            $pipeNodeType = new PipeNodeTypes();
            $pipeNodeType->set('IdPipeline', $idPipeline);
            $pipeNodeType->set('IdNodeType', $this->get('IdNodeType'));
            $pipeNodeType->add();
        }
        return $idPipeline;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\Data\GenericData::update()
     */
    public function update()
    {
        $pipelineChanged = false;
        $pipeNodeType = new PipeNodeTypes();
        if ($this->idNodeType > 0) {
            $result = $pipeNodeType->find('id, IdNodeType', 'IdPipeline = %s', array($this->get('id')));
            if (count($result) === 1) {
                $oldNodeType = $result[0]['IdNodeType'];
                $pipeNodeType = new PipeNodeTypes($result[0]['id']);
                $pipeNodeType->set('IdNodeType', $this->idNodeType);
                $pipelineChanged = $pipeNodeType->update();
            } else {
                $pipeNodeType = new PipeNodeTypes();
                $pipeNodeType->set('IdPipeline', $this->get('id'));
                $pipeNodeType->set('IdNodeType', $this->get('IdNodeType'));
                $pipelineChanged = $pipeNodeType->add();
            }
        } else {
            $result = $pipeNodeType->find('id, IdNodeType', 'IdPipeline = %s', array($this->get('id')));
            if (count($result) === 1) {
                $oldNodeType = $result[0]['IdNodeType'];
                $pipeNodeType = new PipeNodeTypes($result[0]['id']);
                $pipelineChanged = $pipeNodeType->delete();
            }
        }
        if ($pipelineChanged) {
            $oldNodeType = $result[0]['IdNodeType'];
            $node = new Node();
            $strVals = [];
            if (isset($oldNodeType)) {
                $strVals[] = $oldNodeType;
            }
            if ($this->get('IdNodeType') > 0) {
                $strVals[] = $this->get('IdNodeType');
            }
            if (! empty($strVals)) {
                $nodes = $node->find('IdNode', 'IdNodeType IN (%s)', array(implode(', ', $strVals)), MONO, false);
                if (! empty($nodes)) {
                    foreach ($nodes as $idNode) {
                        $node = new Node($idNode);
                        $idStatus = $node->getFirstStatus();
                        $node->set('IdState', $idStatus);
                        $node->update();
                    }
                }
            }
        }
        return parent::update();
    }
}
