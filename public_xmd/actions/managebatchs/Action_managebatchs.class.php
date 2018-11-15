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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

use Ximdex\Models\User;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;
use Ximdex\Utils\Date;
use Ximdex\Runtime\Session;
use Ximdex\Models\Batch;
use Ximdex\Models\Node;
use Ximdex\Models\Server;
use Ximdex\Models\PortalFrames;

class Action_managebatchs extends ActionAbstract
{   
    /**
     * Main method: shows initial form
     */
    public function index()
    {
        $acceso = true;
        $idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);
        
        // Initializing variables
        $userID = Session::get('userID');
        $user = new User();
        $user->SetID($userID);
        if (!$user->HasPermission('view_publication_resume')) {
            $acceso = false;
            $errorMsg = 'You have not access to this report. Consult an administrator.';
        } else {
            $errorMsg = '';
        }
        $jsFiles = array(
            App::getUrl('/actions/managebatchs/resources/js/index.js'),
            App::getUrl('/assets/js/ximtimer.js')
        );
        $this->addJs('/actions/managebatchs/resources/js/managebatchs.js');
        $cssFiles = array(
            App::getUrl('/actions/managebatchs/resources/css/index.css')
        );
        $arrValores = array(
            'acceso' => $acceso,
            'errorBox' => $errorMsg,
            'js_files' => $jsFiles,
            'node_Type' => $node->nodeType->GetName(),
            'css_files' => $cssFiles
        );
        $this->render($arrValores, null, 'default-3.0.tpl');
    }

    /**
     * Return a JSON code with a list of portal frames with its servers
     */
    public function getFrameList()
    {
        $report = [];
        try {
            $order = 1;
            $portals = PortalFrames::getByState(PortalFrames::STATUS_ACTIVE);
            foreach ($portals as $portal) {
                $report[] = self::portalInfo($portal, $order);
            }
            $portals = PortalFrames::getByState(PortalFrames::STATUS_CREATED);
            foreach ($portals as $portal) {
                $report[] = self::portalInfo($portal, $order);
            }
            $portals = PortalFrames::getByState(PortalFrames::STATUS_ENDED, 3600);
            foreach ($portals as $portal) {
                $report[] = self::portalInfo($portal, $order);
            }
        } catch (Exception $e) {
            $this->sendJSON(['error' => $e->getMessage()]);
        }
        $this->sendJSON($report);
    }
    
    public function pausePortal()
    {
        $id = $this->request->getParam('id');
        if (! $id) {
            $this->sendJSON(['success' => false, 'error' => 'No portal frame ID given']);
        }
        $portal = new PortalFrames($id);
        if (!$portal->get('id')) {
            $this->sendJSON(['success' => false, 'error' => 'Portal frame with ID ' . $id . ' does not exist']);
        }
        if ($portal->get('Playing')) {
            $portal->set('Playing', 0);
            if ($portal->update() === false) {
                $this->sendJSON(['success' => false, 'error' => 'Cannot pause the portal frame']);
            }
        }
        $this->sendJSON(['success' => true]);
    }
    
    public function playPortal()
    {
        $id = $this->request->getParam('id');
        if (! $id) {
            $this->sendJSON(['success' => false, 'error' => 'No portal frame ID given']);
        }
        $portal = new PortalFrames($id);
        if (!$portal->get('id')) {
            $this->sendJSON(['success' => false, 'error' => 'Portal frame with ID ' . $id . ' does not exist']);
        }
        if (! $portal->get('Playing')) {
            $portal->set('Playing', 1);
            if ($portal->update() === false) {
                $this->sendJSON(['success' => false, 'error' => 'Cannot play the portal frame']);
            }
        }
        $this->sendJSON(['success' => true]);
    }
    
    public function restartBatchs()
    {
        $id = $this->request->getParam('id');
        if (! $id) {
            $this->sendJSON(['success' => false, 'error' => 'No portal frame ID given']);
        }
        $portal = new PortalFrames($id);
        if (!$portal->get('id')) {
            $this->sendJSON(['success' => false, 'error' => 'Portal frame with ID ' . $id . ' does not exist']);
        }
        if (Batch::restart($id) === false) {
            $this->sendJSON(['success' => false, 'error' => 'Error restarting portal batchs']);
        }
        $this->sendJSON(['success' => true]);
    }
    
    public function restartServer()
    {
        $id = $this->request->getParam('id');
        if (! $id) {
            $this->sendJSON(['success' => false, 'error' => 'No server ID given']);
        }
        $server = new Server($id);
        if (!$server->get('IdServer')) {
            $this->sendJSON(['success' => false, 'error' => 'Server with ID ' . $id . ' does not exist']);
        }
        if ($server->enableForPumping() === false) {
            $this->sendJSON(['success' => false, 'error' => 'Error restarting the server ' . $server->get('Description')]);
        }
        $this->sendJSON(['success' => true]);
    }
    
    public function boostPortal()
    {
        $id = $this->request->getParam('id');
        $value = $this->request->getParam('value');
        if (!$id or !$value) {
            $this->sendJSON(['success' => false, 'error' => 'No portal frame ID or boost value given']);
        }
        $portal = new PortalFrames($id);
        if (!$portal->get('id')) {
            $this->sendJSON(['success' => false, 'error' => 'Portal frame with ID ' . $id . ' does not exist']);
        }
        $portal->set('Boost', $value);
        if ($portal->update() === false) {
            $this->sendJSON(['success' => false, 'error' => 'Error estabishing boost to portal frame']);
        }
        try {
            if (App::getValue('SchedulerPriority') == 'portal') {
                
                // Priority by portal boost cycles
                PortalFrames::resetBoostCycles();
            } else {
                
                // Priority by batchs boost
                $batchs = $portal->getBatchs();
                foreach ($batchs as $id => $state) {
                    if ($state != Batch::ENDED) {
                        $batch = new Batch($id);
                        $batch->calcPriority();
                        $batch->update();
                    }
                }
            }
        } catch (Exception $e) {
            $this->sendJSON(['success' => false, 'error' => $e->getMessage()]);
        }
        $this->sendJSON(['success' => true]);
    }
    
    /**
     * Return a list of portal information including a list of servers affected
     * 
     * @param PortalFrames $portal
     * @param int $order
     * @return array
     */
    private static function portalInfo(PortalFrames $portal, int & $order) : array
    {
        $node = new Node($portal->get('IdNodeGenerator'));
        
        // User name
        if (!$portal->get('CreatedBy')) {
            $userName = 'Unknown';
        } else {
            $user = new User($portal->get('CreatedBy'));
            $userName = $user->getRealName() . ' (' . $user->getLogin() . ')';
        }
        
        // Servers stats
        $servers = [];
        $total = $pending = $active = $delayed = $success = $fatal = $soft = $stopped = 0;
        foreach ($portal->getServers() as $id => $name) {
            $server = new Server($id);
            $stats = $server->stats($portal->get('id'));
            $servers[] = [
                'id' => $id, 
                'name' => $name,
                'total' => $stats['total'],
                'active' => $stats['active'],
                'delayed' => $stats['delayed'],
                'pending' => $stats['pending'],
                'success' => $stats['success'],
                'fatal' => $stats['fatal'],
                'soft' => $stats['soft'],
                'stopped' => $stats['stopped'],
                'activeForPumping' => (int) $server->get('ActiveForPumping'),
                'delayedTime' => Date::formatTime($server->get('DelayTimeToEnableForPumping')),
                'enabled' => (int) $server->get('Enabled')
            ];
            $total += $stats['total'];
            $pending += $stats['pending'];
            $active += $stats['active'];
            $delayed += $stats['delayed'];
            $success += $stats['success'];
            $fatal += $stats['fatal'];
            $soft += $stats['soft'];
            $stopped += $stats['stopped'];
        }
        
        // Portal frames information
        $report = [
            'idPortal' => (int) $portal->get('id'),
            'idNodeGenerator' => (int) $node->GetID(),
            'nodeName' => $node->GetNodeName(),
            'userName' => $userName,
            'version' => (int) $portal->get('Version'),
            'creationTime' => Date::formatTime($portal->get('CreationTime')),
            'type' => $portal->get('PublishingType'),
            'scheduledTime' => Date::formatTime($portal->get('ScheduledTime')),
            'statusTime' => Date::formatTime($portal->get('StatusTime')),
            'startTime' => Date::formatTime($portal->get('StartTime')),
            'endTime' => Date::formatTime($portal->get('EndTime')),
            'total' => $total,
            'active' => $active,
            'delayed' => $delayed,
            'pending' => $pending,
            'success' => $success,
            'fatal' => $fatal,
            'soft' => $soft,
            'stopped' => $stopped,
            'playing' => (int) $portal->get('Playing'),
            'successRate' => round($portal->get('SuccessRate'), 2),
            'cycles' => (int) $portal->get('CyclesTotal'),
            'boost' => (int) $portal->get('Boost'),
            'order' => (int) $order++
        ];
        $report['servers'] = $servers;
        
        // Batchs list
        $report['batchs'] = $portal->getBatchs();
        $report['totalBatchs'] = count($report['batchs']);
        return $report;
    }
}
