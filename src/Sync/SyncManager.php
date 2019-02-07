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

namespace Ximdex\Sync;

use Ximdex\Logger;
use Ximdex\Models\Node;
use Ximdex\Models\NodesToPublish;
use Ximdex\Models\User;
use Ximdex\Models\FastTraverse;
use Ximdex\NodeTypes\NodeTypeConstants;

include_once XIMDEX_ROOT_PATH . '/src/Sync/conf/synchro_conf.php';

/**
 * @brief Manages the phases previous and later of the publication process.
 */
class SyncManager
{
    public $workflow;
    public $deleteOld;
    public $markEnd;
    public $linked;
    public $type;
    public $mail;
    private $lastPublished;
    private $publicateSection;
    private $level;
    private $nodeType;
    private $docsToPublishByLevel = [];
    private $computedDocsToPublish = [];
    private $pendingDocsToPublish = [];
    private $generatorNodes = [];
    private $useCache;

    function __construct()
    {
        // Default values for state flags
        $this->setFlag('workflow', true);
        $this->setFlag('deleteOld', false);
        $this->setFlag('markEnd', false);
        $this->setFlag('linked', false);
        $this->setFlag('recurrence', false);
        $this->setFlag('type', 'core');
        $this->setFlag('mail', false);
        $this->setFlag('deeplevel', DEEP_LEVEL < 0 ? 1 : DEEP_LEVEL);
        $this->setFlag('globalForcePublication', FORCE_PUBLICATION);
        $this->setFlag('usecache', true);
    }

    /**
     * Sets the value of any variable
     *  
     * @param string key
     * @param unknown value
     */
    function setFlag($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * Gets the value of any variable
     * 
     * @param string key
     */
    function getFlag($key)
    {
        if (isset($this->$key)) {
            return $this->$key;
        }
        return null;
    }
    
    public function setFlags(array $flags) : void
    {
        foreach ($flags as $key => $value) {
            $this->setFlag($key, $value);
        }
    }

    private function buildPublishingDependencies(Node $node, array $params) : array
    {
        if (! $node->GetID()) {
            throw new \Exception('Cannot build publishing dependencies in a node with a void ID');
        }
        $this->generatorNodes = [$node->GetID()];
        $this->pendingDocsToPublish = [$node->GetID()];
        $this->docsToPublishByLevel = [(string) $node->GetID() => 0];
        $workFlowSlaves = $node->GetWorkFlowSlaves();
        if ($workFlowSlaves === false) {
            throw new \Exception('Cannot get the workflow slave documents');
        }
        foreach ($workFlowSlaves as $slave) {
            $this->generatorNodes[] = $slave;
            $this->pendingDocsToPublish[] = $slave;
            $this->docsToPublishByLevel[(string) $slave] = 0;
        }
        $this->computedDocsToPublish = [];
        while (!empty($this->pendingDocsToPublish)) {
            $nodeId = array_shift($this->pendingDocsToPublish);
            if ($this->hasDependences($nodeId, $params)) {
                continue;
            }
            $this->computedDocsToPublish[] = $nodeId;
        }
        return $this->computedDocsToPublish;
    }
    
    /**
     * Get the nodes over a section, only the publishable and no folder type ones
     * 
     * @param Node $node
     * @param array $nodes
     * @param int $level
     * @param int $nodeTypeID
     * @return bool
     */
    private function buildPublishingSection(Node $node, array & $nodes, int $level = null, int $nodeTypeID = null) : bool
    {
        $nodeTypeFlags = ['IsPublishable' => true, 'IsFolder' => false];
        if ($nodeTypeID) {
            $filters = ['include' => ['node.IdNodeType' => [$nodeTypeID]]];
        }
        else {
            $filters = null;
        }
        if ($level !== null) {
            
            // Publication of the section until a given level
            if ($node->GetNodeType() == NodeTypeConstants::SERVER or $node->GetNodeType() == NodeTypeConstants::SECTION) {
                
                // Server and sections nodes must increment one level more
                $level++;
            }
        }
        
        // Avoid structure assets (images, stylesheets, javascript files, etc) if structure flag is false
        if ($this->getFlag('structure')) {
            
            // First search for structural assets nodes
            $nodeTypeFlagsAux = $nodeTypeFlags + ['IsStructuredDocument' => false];
            $assetsChildren = FastTraverse::getChildren($node->GetID(), null, $level, $filters, $nodeTypeFlagsAux);
            if ($assetsChildren === false) {
                return false;
            }
            foreach ($assetsChildren as $nodeLevel => $levelNodes) {
                $nodeLevel += 100;
                foreach ($levelNodes as $id) {
                    $nodes[] = $id;
                    $this->docsToPublishByLevel[$id] = $nodeLevel;
                }
            }
        }
        
        // Then search the structured documents with one plus level to include at the end of the pool
        $nodeTypeFlagsAux = $nodeTypeFlags + ['IsStructuredDocument' => true];
        if ($level) {
            $level++;
        }
        $documentsChildren = FastTraverse::getChildren($node->GetID(), null, $level, $filters, $nodeTypeFlagsAux);
        if ($documentsChildren === false) {
            return false;
        }
        foreach ($documentsChildren as $nodeLevel => $levelNodes) {
            foreach ($levelNodes as $id) {
                $nodes[] = $id;
                $this->docsToPublishByLevel[$id] = $nodeLevel;
            }
        }
        return true;
    }
    
    public function getPublishableDocs(Node $node, int $timeUp, int $timeDown = null) : array
    {
        if ($this->getFlag('publicateSection') or $this->getFlag('expireSection')) {
            
            // Obtain all the whole children above the given section
            $docsToPublish = [];
            if (! $this->buildPublishingSection($node, $docsToPublish, $this->getFlag('level'), $this->getFlag('nodeType'))) {
                return null;
            }
        }
        else {
            
            // Flags for dependencies
            $params = [];
            $params['withstructure'] = ($this->getFlag('structure') === false) ? false : true;
            $params['deeplevel'] = $this->getFlag('deeplevel');
            $docsToPublish = $this->buildPublishingDependencies($node, $params);
        }
        if ($node->nodeType->get('IsPublishable') == '1') {
            if (sizeof($docsToPublish) > 0) {
                $docsToPublish = array_unique(array_merge(array($node->GetID()), $docsToPublish));
            }
            else {
                $docsToPublish = array($node->GetID());
                $this->docsToPublishByLevel = array($node->GetID());
            }
        }
        elseif ($node->GetNodeType() != NodeTypeConstants::XML_ROOT_FOLDER and $node->GetNodeType() != NodeTypeConstants::XML_CONTAINER 
            and $node->GetNodeType() != NodeTypeConstants::HTML_CONTAINER) {
            return array();
        }
        return $docsToPublish;
    }

    /**
     * Gets the Nodes that must be published with the current Node and calls the methods for build the Batchs.
     * 
     * @param int $nodeId
     * @param int up
     * @param int down
     * @return array|null
     */
    function pushDocInPublishingPool(int $idNode, int $up, int $down = null)
    {
        if (! $idNode) {
            Logger::error('Pushdocinpool - Empty IdNode');
            return NULL;
        }
        $node = new Node($idNode);
        if (! $node->GetID()) {
            Logger::error(sprintf('Node %s does not exist', $idNode));
            return NULL;
        }
        $docsToPublish = $this->getPublishableDocs($node, $up, $down);
        $userID = \Ximdex\Runtime\Session::get('userID');
        $force = $this->getFlag('globalForcePublication') ? true : $this->getFlag('force');
        $nodesToPublish = new NodesToPublish();
        foreach ($docsToPublish as $idDoc) {
            if (! array_key_exists($idDoc, $this->docsToPublishByLevel)) {
                continue;
            }
            // if ($this->getFlag('publicateSection') or $idNode == $idDoc) {
            if (in_array($idDoc, $this->generatorNodes)) {
                $expire = $down;
                $idGeneratorNode = $idDoc;
            } else {
                
                // Dependencies won't be expired
                $expire = null;
                $idGeneratorNode = $idNode;
            }
            $nodesToPublish->create($idDoc, $idGeneratorNode, $up, $expire, $userID, $force, $this->getFlag('lastPublished')
                , $this->docsToPublishByLevel[$idDoc], $this->getFlag('useCache'));
        }
        if ($this->getFlag('mail')) {
            $this->sendMail($idNode, null, $up, $down);
        }

        // Exec batchManagerDaemon in background
        $cmd = 'php ' . XIMDEX_ROOT_PATH . '/bootstrap.php  src/Sync/scripts/batch/batchManagerDaemon.php';
        // shell_exec(sprintf('%s > /dev/null & echo $!', $cmd));
        shell_exec(sprintf('%s > ' . sys_get_temp_dir() . '/batch_manager_daemon.err &', $cmd));
        return $docsToPublish;
    }

    /**
     * @param $nodeId
     * @param $params
     * @return mixed
     */
    public function hasDependences($nodeId, $params)
    {
        $deepLevel = $params['deeplevel'];
        if (! isset($this->docsToPublishByLevel["$nodeId"])) {
            return false;
        }
        $currentDeepLevel = $this->docsToPublishByLevel["$nodeId"] + 1;
        if ($deepLevel != -1 && $deepLevel < $currentDeepLevel) {
            return false;
        }
        $node = new Node($nodeId);
        $nodeDependences = $node->class->getPublishabledDeps($params);
        if (! isset($nodeDependences) || empty($nodeDependences)) {
            return false;
        }
        $pending = array_values(array_diff($nodeDependences, $this->pendingDocsToPublish, $this->computedDocsToPublish));
        if (empty($pending)) {
            return false;
        }
        else {
            $idDoc = $pending[0];
            while ($idDoc == $nodeId) {
                array_shift($pending);
                if (! empty($pending)) {
                    $idDoc = $pending[0];
                } else {
                    return false;
                }
            }
            $this->docsToPublishByLevel["$idDoc"] = $currentDeepLevel;
            $this->pendingDocsToPublish = array_merge([$idDoc, $nodeId], $this->pendingDocsToPublish);
            return true;
        }
    }

    private function sendMail($nodeID, $type, $up, $down)
    {
        $node = new node($nodeID);
        $name = $node->Get('Name');
        $msg = sprintf(_('Node %s is going to be published'), $name);
        if (! $down) {
            $downString = _('Undetermined');
        }
        else {
            $downString = date('d-m-Y H:i:s', $down);
        }
        $msg .= "\n" . _('Publication date') . ': ' . date('d-m-Y H:i:s', $up);
        $msg .= "\n" . _('Expiration date') . ':' . " $downString";
        $user = new User(301);
        $email = $user->Get('Email');
        $mail = new \Ximdex\Utils\Mail();
        $mail->addAddress($email);
        $mail->Subject = _('Publication of') . " $name";
        $mail->Body = $msg;
        $mail->Send();
    }
}
