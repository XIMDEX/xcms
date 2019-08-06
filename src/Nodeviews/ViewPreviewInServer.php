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

namespace Ximdex\Nodeviews;

use Ximdex\Logger;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;
use Ximdex\Models\ServerFrame;
use Ximdex\Models\Pumper;
use Ximdex\XML\Base;
// use GuzzleHttp\Client;

class ViewPreviewInServer extends AbstractView
{
    /**
     * {@inheritDoc}
     * @see \Ximdex\Nodeviews\AbstractView::transform()
     */
    public function transform(int $idVersion = null, string $content = null, array $args = null)
    {
        if (parent::transform($idVersion, $content, $args) === false) {
            return false;
        }
        if (App::getValue('PreviewInServer') == 0) {
            Logger::error('PreviewInServer mode is disabled');
            return false;
        }
        if (! $this->serverNode->class->getPreviewServersForChannel($this->channel->getId())) {
            Logger::error('No Preview Servers for this channel');
            return false;
        }
        $publishedName = $this->node->getPublishedNodeName($this->channel->getId());
        $publishedPath = $this->node->getPublishedPath();
        
        // Pumper creation
        $pumper = new Pumper();
        if (! $pumper->create($this->server->get('IdServer'), -1)) {
            return false;
        }
        
        // Server frame creation and status update
        $sf = new ServerFrame();
        $sfFile = $sf->create($this->node->getID(), $this->server->get('IdServer'), time(), $publishedPath, $publishedName, false, null
            , $this->channel->getID(), null, null, null, null, 0, false, null, $pumper->get('PumperId'));
        if (! $sfFile) {
            return false;
        }
        $sf = new ServerFrame($sfFile);
        $sf->set('State', ServerFrame::DUE2IN);
        if ($sf->update() === false) {
            return false;
        }
        
        // Server frame file creation
        $content = Base::recodeSrc($content, $this->server->get('idEncode'));
        if (! FsUtils::file_put_contents(SERVERFRAMES_SYNC_PATH . '/' . $sfFile, $content)) {
            return false;
        }
        
        // Start pumper process
        if ($pumper->startPumper($pumper->get('PumperId'), 'php', false) === false) {
            return false;
        }
        
        /*
        // Read the content from the preview server URL
        $url = parent::getAbsolutePath($this->node, $this->server, $this->channel->getID());
        $client = new Client();
        try {
            $res = $client->request('GET', $url);
        } catch (\Exception $e) {
            Logger::error($e->getMessage());
            
            // Unpublish the remote file
            $this->remove($pumper, $sf);
            return false;
        }
        
        // Unpublish the remote file
        $this->remove($pumper, $sf);
        
        // echo $res->getStatusCode();
        return $res->getBody();
        */
        
        // Remove remote file
        $this->remove($pumper, $sf, 120);
        
        // Return the URL to the uploaded document
        return parent::getAbsolutePath($this->node, $this->server, $this->channel->getID());
    }
    
    private function remove(Pumper $pumper, ServerFrame $sf, int $delayTimeInSeconds = 0): bool
    {
        $sf->set('State', ServerFrame::DUE2OUT);
        // $sf->set('DateDown', time() + $delayTimeInSeconds);
        if ($sf->update() === false) {
            return false;
        }
        if ($pumper->startPumper($pumper->get('PumperId'), 'php', true, $delayTimeInSeconds) === false) {
            return false;
        }
        return true;
    }
}
