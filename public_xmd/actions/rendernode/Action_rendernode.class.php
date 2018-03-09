<?php

use Ximdex\Logger;
use Ximdex\Models\Node;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Parsers\ParsingPathTo;
use Ximdex\NodeTypes\XmlDocumentNode;
use Ximdex\NodeTypes\CommonNode;

class Action_rendernode extends ActionAbstract
{
    /**
     * Render a node in the previews output
     * 
     * @return boolean
     */
    public function index()
    {
        // Change the logs output to preview file
        Logger::setActiveLog('preview');
        
        if ($this->request->getParam("nodeid")) {
            
            // Receives request node param
            $idNode = $this->request->getParam("nodeid");
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
            if (!$parserPathTo->getIdNode()) {
                
                // The node can not be found
                $this->messages->add(_('It is not possible to show preview.') . _(' The node you are trying to preview does not exist:') 
                    . $expression, MSG_TYPE_NOTICE);
                $this->render(array('messages' => $this->messages->messages), null, 'messages.tpl');
            }
            $idNode = $parserPathTo->getIdNode();
        }
        
        // Checks node existence
        $node = new Node($idNode);
        if (! ($node->get('IdNode') > 0)) {
            $this->messages->add(_('It is not possible to show preview.') . _(' The node you are trying to preview does not exist.'), MSG_TYPE_NOTICE);
            $this->render(array('messages' => $this->messages->messages), null, 'messages.tpl');
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
        
        // Render the node
        $xmlDocNode = new XmlDocumentNode();
        $xmlDocNode->filemapper($idNode, $idChannel, $showprev, $content, $version, $subversion, $mode);
        if ($content === false) {
            $this->messages->mergeMessages($xmlDocNode->messages);
            $this->render(array('messages' => $this->messages->messages), null, 'messages.tpl');
        }
        $commonNode = new CommonNode();
        if ($node->nodeType->GetIsStructuredDocument()) {
            
            // Get mime type for structured documents
            $mimeType = $node->getMimeType($content);
        }
        else {
            
            // Response headers for non structured documents
            $mimeType = $node->getMimeType();
            $this->response->set('Content-Disposition', "attachment; filename=" . $node->GetNodeName());
            $this->response->set('Content-Length', strlen(strval($content)));
        }
        
        // Common response headers
        $this->response->set('Content-type', $mimeType);
        $this->response->set('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
        $this->response->set('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT");
        $this->response->set('Cache-Control', array('no-store, no-cache, must-revalidate','post-check=0, pre-check=0'));
        $this->response->set('Pragma', 'no-cache');
        $this->response->sendHeaders();
        echo $content;
        
        // Change the logs output to default one
        Logger::setActiveLog();
    }
}