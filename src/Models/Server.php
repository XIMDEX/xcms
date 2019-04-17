<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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
use Ximdex\Models\ORM\ServersOrm;
use Ximdex\NodeTypes\ServerNode;
use Ximdex\Utils\Date;
use Ximdex\Utils\Sitemap;
use Ximdex\Runtime\App;
use Ximdex\Runtime\Db;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\Sync\SynchroFacade;

class Server extends ServersOrm
{
    const MAX_CYCLES_TO_RETRY_PUMPING = 0;
    
    const SECONDS_TO_WAIT_FOR_RETRY_PUMPING = 60;
    
    private $serverNode;
    
    public function __construct(int $id = null)
    {
        parent::__construct($id);
        if ($id) {
            $this->setServerNode($id);
        }
    }
    
    public function setServerNode(int $id)
    {
        $this->serverNode = new ServerNode($id);
    }
    
    public function getChannels() : array
    {
        if (! $this->serverNode) {
            $this->setServerNode($this->get('IdServer'));
        }
        return $this->serverNode->getChannels($this->get('IdServer'));
    }
    
    public function resetForPumping() : bool
    {
        if (! $this->CyclesToRetryPumping) {
            return true;
        }
        $this->ActiveForPumping = 1;
        $this->DelayTimeToEnableForPumping = null;
        $this->CyclesToRetryPumping = 0;
        if ($this->update() === false) {
            return false;
        }
        $message = 'Server ' . $this->Description . ' (' . $this->IdServer . ') is now able for pumping (connection successful)';
        Logger::info($message, true);
        // User::sendNotifications('Server ' . $this->Description . ' has been reactivated', $message);
        return true;
    }
    
    public function enableForPumping() : bool
    {
        Logger::info('Enabling the server ' . $this->Description . ' for pumping');
        $this->ActiveForPumping = 1;
        $this->DelayTimeToEnableForPumping = null;
        if ($this->update() === false) {
            return false;
        }
        return true;
    }
    
    public function disableForPumping(bool $nonStopServer = false, int $delayTime = null, bool $sendNotifications = true) : bool
    {
        if (! $this->ActiveForPumping) {
            return true;
        }
        $this->ActiveForPumping = 0;
        $this->CyclesToRetryPumping = $this->CyclesToRetryPumping + 1;
        if (! $nonStopServer or (self::MAX_CYCLES_TO_RETRY_PUMPING and $this->CyclesToRetryPumping > self::MAX_CYCLES_TO_RETRY_PUMPING)) {
                
            // Disable the server permanently
            $this->DelayTimeToEnableForPumping = null;
            if ($this->update() === false) {
                return false;
            }
            $message = 'Disabling the server ' . $this->Description . ' (' . $this->IdServer . ') for pumping permanently';
            Logger::error($message);
            if ($sendNotifications) {
                User::sendNotifications('Server ' . $this->Description . ' has been disabled permanently', $message);
            }
        } else {
            
            // Disable the server temporally
            if (! $delayTime) {
                $delayTime = pow($this->CyclesToRetryPumping, 3) * self::SECONDS_TO_WAIT_FOR_RETRY_PUMPING;
            }
            if ($delayTime > 86400) {
                
                // Max time to retry 24 hours
                $delayTime = 86400;
            }
            $this->DelayTimeToEnableForPumping = time() + $delayTime;
            if ($this->update() === false) {
                return false;
            }
            $message = 'Server ' . $this->Description . ' (' . $this->IdServer . ') have been delayed temporally for pumping after cycle '
                . $this->CyclesToRetryPumping . ' (Will be restarted at ' . Date::formatTime($this->get('DelayTimeToEnableForPumping')) . ')';
            Logger::warning($message);
            if ($sendNotifications) {
                User::sendNotifications('Server ' . $this->Description . ' has been disabled temporally', $message);
            }
        }
        return true;
    }
    
    public function stats(int $portalId = null) : array
    {
        if (! $this->IdServer) {
            throw new \Exception('No server selected');
        }
        $sql = 'SELECT SUM(ServerFramesTotal) AS total';
        $sql .= ', SUM(IF (`State` != \'' . Batch::STOPPED . '\', ServerFramesPending, 0)) AS pending';
        $sql .= ', SUM(IF (`State` != \'' . Batch::STOPPED . '\', ServerFramesActive, 0)) AS active';
        $sql .= ', SUM(IF (`State` = \'' . Batch::STOPPED . '\', ServerFramesActive + ServerFramesPending, 0)) AS stopped';
        $sql .= ', SUM(ServerFramesSuccess) AS success, SUM(ServerFramesFatalError) AS fatal, SUM(ServerFramesTemporalError) AS soft';
        $sql .= ' FROM Batchs WHERE ServerId = ' . $this->IdServer;
        if ($portalId) {
            $sql .= ' AND IdPortalFrame = ' . $portalId;
        }
        $db = new Db();
        if ($db->query($sql) === false) {
            throw new \Exception('SQL error');
        }
        $stats = [];
        if (! $db->numRows) {
            throw new \Exception('There is not stats information for the server');
        }
        $stats['total'] = (int) $db->getValue('total');
        $stats['pending'] = $this->get('ActiveForPumping') ? (int) $db->getValue('pending') : 0;
        $stats['active'] = $this->get('ActiveForPumping') ? (int) $db->getValue('active') : 0;
        $stats['success'] = (int) $db->getValue('success');
        $stats['fatal'] = (int) $db->getValue('fatal');
        $stats['soft'] = (int) $db->getValue('soft');
        $stats['stopped'] = (int) $db->getValue('stopped');
        $stats['delayed'] = (! $this->get('ActiveForPumping')) ? $db->getValue('pending') + $db->getValue('active') : 0;
        return $stats;
    }
    
    /**
     * Returns a list of servers delayed by error
     * 
     * @throws \Exception
     * @return array
     */
    public static function getDelayed() : array
    {
        $sql = 'SELECT IdServer, Description FROM Servers WHERE ActiveForPumping = 0';
        $db = new Db();
        if ($db->query($sql) === false) {
            throw new \Exception('SQL error in delayed servers retrieve');
        }
        $servers = [];
        while (! $db->EOF) {
            $servers[$db->getValue('IdServer')] = $db->getValue('Description');
            $db->next();
        }
        return $servers;
    }
    
    public static function generateSitemaps() : void
    {
        $server = new static();
        $interval = (int) App::getValue('SitemapInterval', 3600);
        $servers = $server->find('IdServer, Url, IdNode'
            , "Enabled = 1 AND Indexable IS TRUE AND (LastSitemapGenerationTime + {$interval} < UNIX_TIMESTAMP() OR LastSitemapGenerationTime IS NULL)");
        if (! $servers) {
            return;
        }
        $db = new Db();
        $node = new Node();
        $sitemaps = [];
        $flagsPublication = [
            'recursive' => false,
            'childtype' => NodeTypeConstants::SITEMAP,
            'workflow' => false,
            'force' => false,
            'lastPublished' => true,
            'publicateSection' => true,
            'level' => null,
            'nodeType' => NodeTypeConstants::SITEMAP,
            'expireAll' => true,
            'structure' => true
        ];
        $syncFac = new SynchroFacade();
        foreach ($servers as $server) {
            $query = 'SELECT sf.DateUp, sf.RemotePath, sf.FileName FROM ServerFrames sf';
            $query .= ' JOIN Nodes n ON n.IdNode = sf.NodeId';
            $query .= ' JOIN NodeTypes nt ON nt.IdNodeType = n.IdNodeType AND nt.IsPublishable = 1 AND nt.HasMetadata = 1';
            $query .= ' WHERE sf.State = \'' . ServerFrame::IN . "' AND sf.IdServer = {$server['IdServer']}";
            if ($db->query($query) === false) {
                throw new \Exception($db->getDesErr());
            }
            if (! $db->numRows) {
                continue;
            }
            $fileName = "sitemap-{$server['IdServer']}.xml";
            $res = $node->find('IdNode', "Name LIKE '{$fileName}' AND IdParent = {$server['IdNode']}", null, MONO);
            if ($res) {
                $id = $res[0];
            } else {
                $id = $node->createNode($fileName, $server['IdNode'], NodeTypeConstants::SITEMAP);
                if (! $id) {
                    continue;
                }
            }
            $node = new Node($id);
            if (! $node->getID()) {
                continue;
            }
            $links = [];
            while (! $db->EOF) {
                $links[] = [
                    'loc' => trim($server['Url'], '/') . '/' . trim($db->getValue('RemotePath'), '/') . '/' . $db->getValue('FileName'),
                    'lastmod' => Date::formatTime($db->getValue('DateUp'), 'Y-m-d')
                ];
                $db->next();
            }
            $sitemap = Sitemap::generate($links);
            if ($node->setContent($sitemap) === false) {
                continue;
            }
            $sitemaps[$server['IdNode']][] = $server['IdServer'];
        }
        foreach (array_keys($sitemaps) as $id) {
            $result = $syncFac->pushDocInPublishingPool($id, time(), null, $flagsPublication);
            if (! $result) {
                continue;
            }
            foreach ($sitemaps[$id] as $serverId) {
                $server = new static($serverId);
                if (! $server->get('IdServer')) {
                    continue;
                }
                $server->set('LastSitemapGenerationTime', time());
                $server->update();
            }
        }
    }
}
