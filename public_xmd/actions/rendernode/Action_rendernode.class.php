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

use Ximdex\Logger;
use Ximdex\Models\Node;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Parsers\ParsingPathTo;

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
        if ($this->request->getParam("nodeid")) {
            
            // Receives request node param
            $idNode = $this->request->getParam("nodeid");
            
            // Checks node existence
            $node = new Node($idNode);
            if (! ($node->get('IdNode') > 0)) {
                $this->messages->add(_('It is not possible to show preview.') . _(' The node you are trying to preview does not exist.')
                    , MSG_TYPE_NOTICE);
                $this->render(array('messages' => $this->messages->messages), null, 'messages.tpl');
                return false;
            }
            Logger::info('Call to rendernode from given ID: ' . $idNode);
        }
        elseif ($this->request->getParam('expresion')) {
            
            // Receives an expression param containing a nodeID or a path
            $expression = $this->request->getParam("expresion");
            Logger::info('Call to rendernode from expresion: ' . $expression);
            
            // Generate the node ID using pathTo parser
            $parserPathTo = new ParsingPathTo();
            if (!$parserPathTo->parsePathTo($expression)) {
                $this->messages->mergeMessages($parserPathTo->messages());
            }
            if ($parserPathTo->getNode() === null) {
                
                // Change the logs output to default one
                Logger::setActiveLog();
                return true;
            }
            $node = $parserPathTo->getNode();
        }
        if ($node->nodeType->GetIsStructuredDocument()) {
            
            // Receives request params for structured documents
            $idChannel = $this->request->getParam("channelid");
            if (empty($idChannel)) {
                $idChannel = $this->request->getParam("channel");
            }
            $showprev = $this->request->getParam("showprev");
            $content = stripslashes($this->request->getParam("content"));
            $version = $this->request->getParam('version');
            $subversion = $this->request->getParam('sub_version');
            $mode = $this->request->getParam('mode');
        }
        else {
            $idChannel = null;
            $showprev = null;
            $content = null;
            $version = null;
            $subversion = null;
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
