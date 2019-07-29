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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

use Ximdex\Logger;
use Ximdex\Models\Node;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Parsers\ParsingPathTo;
use Ximdex\Nodeviews\ViewPreviewInServer;
use Ximdex\NodeTypes\ServerNode;
use Ximdex\Runtime\DataFactory;
use Ximdex\Models\StructuredDocument;

class Action_rendernode extends ActionAbstract
{
    /**
     * Render a node in the previews output
     * 
     * @return bool
     */
    public function index() : bool
    {
        // Change the logs output to preview file
        Logger::setActiveLog('preview');
        if ($this->request->getParam('nodeid')) {
            
            // Receives request node param
            $idNode = (int) $this->request->getParam('nodeid');
            
            // Checks node existence
            $node = new Node($idNode);
            if (! $node->get('IdNode')) {
                $this->messages->add(_('It is not possible to show preview.') . _(' The node you are trying to preview does not exist.')
                    , MSG_TYPE_NOTICE);
                $this->render(array('messages' => $this->messages->messages), null, 'messages.tpl');
                return false;
            }
            Logger::info('Call to rendernode from given ID: ' . $idNode);
        } elseif ($this->request->getParam('expresion')) {
            
            // Receives an expression param containing a nodeID or a path
            $expression = $this->request->getParam('expresion');
            Logger::info('Call to rendernode from expresion: ' . $expression);
            
            // Some times a id from parent node will be necesary
            $id = (int) $this->request->getParam('id');
            
            // Generate the node ID using pathTo parser
            $parserPathTo = new ParsingPathTo();
            if (! $parserPathTo->parsePathTo($expression, $id)) {
                $this->messages->mergeMessages($parserPathTo->messages());
            }
            if ($parserPathTo->getNode() === null) {
                
                // Change the logs output to default one
                Logger::warning('cannot resolve the pathTo macro with expression: ' . $expression);
                Logger::setActiveLog();
                return true;
            }
            $node = $parserPathTo->getNode();
        }
        $version = $this->request->getParam('version');
        $subversion = $this->request->getParam('subversion');
        if ($node->nodeType->getIsStructuredDocument()) {
            
            // Receives request params for structured documents
            $idChannel = (int) ($this->request->getParam('channelId')) ?? $this->request->getParam('channel');
            if (! $idChannel) {
                $this->messages->add('No channel given', MSG_TYPE_ERROR);
                $this->render(array('messages' => $this->messages->messages), null, 'messages.tpl');
                return false;
            }
            if ($this->request->getParam('content')) {
                $content = stripslashes($this->request->getParam('content'));
            } else {
                $content = null;
            }
            
            // If channel uses a preview server, send the content to that server and redirect to published URL
            $serverNode = new ServerNode($node->getServer());
            if ($previewServer = $serverNode->getPreviewServersForChannel($idChannel)) {
                $args = [
                    'NODEID' => $idNode,
                    'CHANNEL' => $idChannel,
                    'SERVER' => $previewServer
                ];
                $dataFactory = new DataFactory($idNode);
                if ($version !== null) {
                    $versionId = $dataFactory->getVersionId($version, $subversion);
                } else {
                    $versionId = $dataFactory->getLastVersionId();
                }
                if ($content === null) {
                    $structuredDocument = new StructuredDocument($node->getID());
                    $content = $structuredDocument->getContent($version, $subversion);
                }
                $viewPreviewInServer = new ViewPreviewInServer();
                $content = $viewPreviewInServer->transform($versionId, $content, $args);
                if ($content === false) {
                    $this->messages->add('Cannot publish the document in the preview server', MSG_TYPE_ERROR);
                    $this->render(array('messages' => $this->messages->messages), null, 'messages.tpl');
                    return false;
                }
            }
            $showprev = (bool) $this->request->getParam('showprev');
            $mode = $this->request->getParam('mode');
        } else {
            $idChannel = null;
            $showprev = false;
            $content = null;
            $mode = null;
        }
        
        // Obtain the content and data to render the node
        $data = $node->filemapper($idChannel, $showprev, $content, $version, $subversion, $mode);
        if ($data === false) {
            $this->messages->mergeMessages($node->messages);
            $this->render(array('messages' => $this->messages->messages), null, 'messages.tpl');
            return false;
        }
        
        // Response headers
        foreach ($data['headers'] as $header => $info) {
            $this->response->set($header, $info);
        }
        $this->response->sendHeaders();
        echo $data['content'];
        
        // Change the logs output to default one
        Logger::setActiveLog();
        return true;
    }
}
