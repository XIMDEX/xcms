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
use Ximdex\Models\Node;
use Ximdex\Models\Version;
use Ximdex\Runtime\App;
use Ximdex\Utils\Curl;
use Ximdex\Utils\FsUtils;

class ViewPreviewInServer extends AbstractView
{
    private $_node;
    private $_serverNode;
    private $_idChannel;

    /**
     * {@inheritDoc}
     * @see \Ximdex\Nodeviews\AbstractView::transform()
     */
    public function transform(int $idVersion = null, string $content = null, array $args = null)
    {
        if (! $this->_setNode($idVersion)) {
            return false;
        }
        if (! $this->_setIdChannel($args)) {
            return false;
        }
        if (! $this->_setServerNode($args)) {
            return false;
        }
        if (App::getValue('PreviewInServer') == 0) {
            Logger::error('PreviewInServer mode is disabled');
            return false;
        }
        $content = htmlspecialchars_decode(\Ximdex\Utils\Strings::stripslashes($content));
        $previewServer = $this->_serverNode->class->GetPreviewServersForChannel($this->_idChannel);
        if (! $previewServer) {
            Logger::error('No Preview Servers for this channel');
            return false;
        }
        $commandParams = array();
        $commandParams['publishedName'] = $this->_node->getPublishedNodeName($this->_idChannel);
        $commandParams['publishedPath'] = $this->_node->GetPublishedPath();
        $commandParams['publishedBaseURL'] = $this->_serverNode->class->GetURL($previewServer);
        $commandParams['publishedURL'] = $commandParams['publishedBaseURL'] . $commandParams['publishedPath']
            . "/" . $commandParams['publishedName'];
        $commandParams['tmpPath'] = XIMDEX_ROOT_PATH . App::getValue("TempRoot");
        $commandParams['tmpfile'] = tempnam($commandParams['tmpPath'], null);
        $commandParams['tmpfileName'] = basename($commandParams['tmpfile']);
        if (! FsUtils::file_put_contents($commandParams['tmpfile'], $content)) {
            return false;
        }
        $command = XIMDEX_ROOT_PATH . App::getValue("SynchronizerCommand") .
            " --verbose 10 --direct --hostid " . $previewServer . " " .
            " --localbasepath " . $commandParams['tmpPath'] . " --dcommand up --dlfile " .
            $commandParams['tmpfileName'] . " --drfile " . $commandParams['publishedName'] . " " .
            " --drpath " . $commandParams['publishedPath'] . "/";
        $returnValue = null;
        $outPut = array();
        exec($command, $outPut, $returnValue);
        switch ($returnValue) {
            
            // TODO: manage fetching errors
            case 0:
                $curl = new Curl();
                $response = $curl->get($commandParams['publishedURL']);
                Logger::info('Success');
                $content = $response['data'];
                break;
            case 10:
                Logger::error('Error accessing remote server');
                $content = '';
                break;
            case 200:
                Logger::error('Error accessing to the remote server (please, check IPs and login credentials)');
                $content = '';
                break;
            default:
                Logger::error('Error de invocación, comando mal formado, etc. (error desconocido)');
                $content = '';
                break;
        }
        return $content;
    }

    private function _setNode(int $idVersion = null) : bool
    {
        if (! is_null($idVersion)) {
            $version = new Version($idVersion);
            if (! $version->get('IdVersion')) {
                Logger::error('VIEW FILTERMACROSPREVIEW: Se ha cargado una versión incorrecta (' . $idVersion . ')');
                return false;
            }
            $this->_node = new Node($version->get('IdNode'));
            if (! $this->_node->get('IdNode')) {
                Logger::error('VIEW FILTERMACROSPREVIEW: El nodo que se está intentando convertir no existe: ' . $version->get('IdNode'));
                return false;
            }
        }
        return true;
    }

    private function _setIdChannel(array $args = array()) : bool
    {
        if (array_key_exists('CHANNEL', $args)) {
            $this->_idChannel = $args['CHANNEL'];
        }

        // Check Params
        if (! $this->_idChannel > 0) {
            Logger::error('VIEW FILTERMACROSPREVIEW: Channel not specified for node ' . $args['SERVERNODE']);
            return false;
        }
        return true;
    }

    private function _setServerNode(array $args = array()) : bool
    {
        if ($this->_node) {
            $this->_serverNode = new Node($this->_node->getServer());
        } elseif (array_key_exists('SERVERNODE', $args)) {
            $this->_serverNode = new Node($args['SERVERNODE']);
        }

        // Check Params
        if (! $this->_serverNode || ! is_object($this->_serverNode)) {
            Logger::error('VIEW FILTERMACROSPREVIEW: There is no server linked to the node ' . $args['NODENAME']);
            return false;
        }
        return true;
    }
}
