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

use Ximdex\Models\Node;
use Ximdex\Models\StructuredDocument;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;
use Ximdex\Utils\Strings;

/**
 * Class Action_edittext
 */
class Action_edittext extends ActionAbstract
{
    /**
     * Main method: shows initial form
     * @return bool
     */
    public function index()
    {
        $this->addCss('/actions/edittext/resources/css/style.css');
        $this->addCss('/vendors/codemirror/Codemirror/lib/codemirror.css');
        $this->addCss('/vendors/codemirror/Codemirror/addon/fold/foldgutter.css');
        $idNode = $this->request->getParam('nodeid');
        $strDoc = new StructuredDocument($idNode);
        if ($strDoc->GetSymLink()) {
            $masterNode = new Node($strDoc->GetSymLink());
            $values = array(
                'path_master' => $masterNode->GetPath()
            );
            $this->render($values, 'linked_document', 'default-3.0.tpl');
            return;
        }
        $node = new Node($idNode);
        $node_name = $node->GetNodeName();
        $idNodeType = $node->get('IdNodeType');
        $fileName = $node->get('Name');
        $infoFile = pathinfo($fileName);
        if (array_key_exists('extension', $infoFile)) {
            $ext = $infoFile['extension'];
        }
        elseif ($idNodeType == \Ximdex\NodeTypes\NodeTypeConstants::XML_DOCUMENT) {
            
            // For the documents
            $ext = 'xml';
        }
        elseif ($idNodeType == \Ximdex\NodeTypes\NodeTypeConstants::HTML_DOCUMENT) {
            
            // For the documents
            $ext = 'html';
        } else {
            $ext = 'txt';
        }
        $content = $node->GetContent();
        $content = htmlspecialchars($content);
        switch ($ext) {
            case 'java':
                $this->addJs('/vendors/codemirror/Codemirror/addon/edit/closebrackets.js');
                $this->addJs('/vendors/codemirror/Codemirror/addon/fold/brace-fold.js');
                break;
            case 'yml':
                $this->addJs('/vendors/codemirror/Codemirror/addon/fold/indent-fold.js');
                $this->addJs('/vendors/codemirror/Codemirror/addon/fold/brace-fold.js');
                break;
            case 'html':
                $this->addJs('/vendors/codemirror/Codemirror/addon/edit/closetag.js');
                $this->addJs('/vendors/codemirror/Codemirror/addon/fold/xml-fold.js');
                $this->addJs('/vendors/codemirror/Codemirror/addon/edit/closebrackets.js');
                break;
            case 'md':
                $this->addJs('/vendors/codemirror/Codemirror/addon/fold/markdown-fold.js');
                break;
        }
        $this->addJs('/vendors/codemirror/Codemirror/addon/fold/foldcode.js');
        $this->addJs('/vendors/codemirror/Codemirror/addon/fold/foldgutter.js');
        $this->addJs('/vendors/codemirror/Codemirror/addon/fold/comment-fold.js');
        $this->addJs('/vendors/codemirror/Codemirror/addon/selection/active-line.js');
        $this->addJs('/vendors/codemirror/Codemirror/addon/mode/loadmode.js');
        $this->addJs('/vendors/codemirror/Codemirror/mode/meta.js');
        $this->addJs('/actions/edittext/resources/js/init.js');
        $values = array('id_node' => $idNode,
            'codemirror_url' => App::getUrl('/vendors/codemirror/Codemirror'),
            'ext' => $ext,
            'content' => $content,
            'go_method' => 'edittext',
            'on_load_functions' => 'resize_caja()',
            'on_resize_functions' => 'resize_caja()',
            'node_name' => $node_name,
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->GetName(),
            'id_editor' => $idNode . uniqid()
        );
        $this->render($values, null, 'default-3.0.tpl');
    }
    
    public function edittext()
    {
        $idNode = $this->request->getParam('nodeid');
        $content = $this->request->getParam('editor');

        // Symbolic link cannot be edited
        $strDoc = new StructuredDocument($idNode);
        if ($strDoc->GetSymLink()) {
            $this->messages->add(_('The document which is trying to be edited is a symbolic link'), MSG_TYPE_ERROR);
            $this->renderMessages();
        }
        
        // If content is empty, put a blank space in order to save a file with empty content
        $content = empty($content) ? ' ' : $content;
        $node = new Node($idNode);
        if (! $node->get('IdNode')) {
            $this->messages->add(_('The document which is trying to be edited does not exist'), MSG_TYPE_ERROR);
            $this->renderMessages();
        }
        if ($node->setContent(Strings::stripslashes($content), true) === false) {
            $this->messages->mergeMessages($node->messages);
            $this->sendJSON(array('messages' => $this->messages->messages, 'type' => MSG_TYPE_WARNING));
        }
        if (! $this->messages->count() and $node->messages->count()) {
            $this->messages->messages[0] = $node->messages->messages[0];
        }
        if ($node->renderizeNode() === false) {
            $this->messages->mergeMessages($node->messages);
            $this->sendJSON(array('messages' => $this->messages->messages, 'type' => MSG_TYPE_WARNING));
        }
        $this->messages->add('The document has been saved', MSG_TYPE_NOTICE);
        $this->sendJSON(array('messages' => $this->messages->messages));
    }
}
