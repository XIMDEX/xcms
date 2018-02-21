<?php

/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
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
use Ximdex\Models\NodeType;
use Ximdex\Models\PipeCacheTemplates;
use Ximdex\Models\StructuredDocument;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Runtime\App;
use Ximdex\Runtime\Constants;
use Ximdex\Runtime\DataFactory;
use Ximdex\Utils\Strings;
use Ximdex\Sync\SyncManager;

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
        if ($strDoc->GetSymLink())
        {
            $masterNode = new Node($strDoc->GetSymLink());
            $values = array(
                'path_master' => $masterNode->GetPath()
            );
            $this->render($values, 'linked_document', 'default-3.0.tpl');
            return false;
        }
        $node = new Node($idNode);
        $node_name = $node->GetNodeName();

        $idNodeType = $node->get('IdNodeType');
        $nodeType = new NodeType($idNodeType);
        $nodeTypeName = $nodeType->get('Name');

        $fileName = $node->get('Name');
        $infoFile = pathinfo($fileName);
        if (array_key_exists("extension", $infoFile))
        {
            $ext = $infoFile['extension'];
        }
        elseif ($idNodeType == \Ximdex\NodeTypes\NodeTypeConstants::XML_DOCUMENT)
        {
            //for the documents
            $ext = "xml";
        }
        else
        {
            $ext = "txt";
        }

        $content = $node->GetContent();
        $content = htmlspecialchars($content);

        switch ($ext)
        {
            case "java":
                $this->addJs('/vendors/codemirror/Codemirror/addon/edit/closebrackets.js');
                $this->addJs('/vendors/codemirror/Codemirror/addon/fold/brace-fold.js');
                break;
            case "yml":
                $this->addJs('/vendors/codemirror/Codemirror/addon/fold/indent-fold.js');
                $this->addJs('/vendors/codemirror/Codemirror/addon/fold/brace-fold.js');
                break;
            case "html":
                $this->addJs('/vendors/codemirror/Codemirror/addon/edit/closetag.js');
                $this->addJs('/vendors/codemirror/Codemirror/addon/fold/xml-fold.js');
                $this->addJs('/vendors/codemirror/Codemirror/addon/edit/closebrackets.js');
                break;
            case "md":
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

        //if is not node state equals to edition, send a message.
        $allowed = $node->GetState();

        if ($nodeType->get('IsStructuredDocument') > 0 && $allowed != Constants::EDITION_STATUS_ID)
        {
            $this->messages->add(_('You can not edit the document.'), MSG_TYPE_WARNING);
            $values = array(
                'messages' => $this->messages->messages
            );
            $this->renderMessages();
            return false;
        }

        $values = array('id_node' => $idNode,
            'codemirror_url' => App::getUrl('/vendors/codemirror/Codemirror'),
            'ext' => $ext,
            'content' => $content,
            'go_method' => 'edittext',
            'on_load_functions' => 'resize_caja()',
            'on_resize_functions' => 'resize_caja()',
            'node_name' => $node_name,
            'id_editor' => $idNode . uniqid()
        );

        $this->render($values, null, 'default-3.0.tpl');
        return true;
    }
    
    /**
     * If nodeType is a template display documents affected by change
     */
    public function publishForm()
    {
        $idNode = $this->request->getParam('nodeid');
        
        $dataFactory = new DataFactory($idNode);
        $lastVersion = $dataFactory->GetLastVersionId();
        $prevVersion = $dataFactory->GetPreviousVersion($lastVersion);

        $cacheTemplate = new PipeCacheTemplates();
        $docs = $cacheTemplate->GetDocsContainTemplate($prevVersion);
        if (is_null($docs))
        {
            $this->redirectTo('index');
            return;
        }
        $numDocs = count($docs);
        for ($i = 0; $i < $numDocs; $i++)
        {
            $docsList[] = $docs[$i]['NodeId'];
        }

        $values = array('numDocs' => $numDocs,
            'docsList' => implode('_', $docsList),
            'go_method' => 'publicateDocs',
        );
        $this->render($values);
    }
    
    /**
     * Publicate documents from publishForm method (above)
     */
    public function publicateDocs()
    {
        if (\Ximdex\Modules\Manager::isEnabled('ximSYNC'))
        {
            \Ximdex\Modules\Manager::file('/inc/manager/SyncManager.class.php', 'ximSYNC');
        }

        $docs = explode('_', $this->request->getParam('docsList'));

        $syncMngr = new SyncManager();
        $syncMngr->setFlag('deleteOld', true);
        $syncMngr->setFlag('linked', false);

        foreach ($docs as $documentID)
        {
            $result = $syncMngr->pushDocInPublishingPool($documentID, time(), NULL, NULL);
        }

        $arrayOpciones = array('ok' => _(' have been successfully published'),
            'notok' => _(' have not been published, because of an error during process'),
            'unchanged' => _(' have not been published because they are already published on its most recent version'));

        $values = array('arrayOpciones' => $arrayOpciones,
            'arrayResult' => $result
        );

        $this->render($values, NULL, 'publicationResult.tpl');
    }
    
    public function edittext()
    {
        $idNode = $this->request->getParam('nodeid');
        $content = $this->request->getParam('editor');

        //If content is empty, put a blank space in order to save a file with empty content
        $content = empty($content) ? " " : $content;

        $node = new Node($idNode);
        if ((!$node->get('IdNode') > 0))
        {
            $this->messages->add(_('The document which is trying to be edited does not exist'), MSG_TYPE_ERROR);
            $this->renderMessages();
        }
        if ($node->SetContent(Strings::stripslashes($content), true, $node) === false)
        {
            $this->messages->mergeMessages($node->messages);
            $this->sendJSON(array('messages' => $this->messages->messages, 'type' => MSG_TYPE_WARNING));
            return false;
        }
        if (!$this->messages->count() and $node->messages->count())
            $this->messages->messages[0] = $node->messages->messages[0];
        if ($node->RenderizeNode() === false)
        {
            $this->messages->mergeMessages($node->messages);
            $this->sendJSON(array('messages' => $this->messages->messages, 'type' => MSG_TYPE_WARNING));
            return false;
        }

        $this->messages->add('The document has been saved', MSG_TYPE_NOTICE);
        $this->sendJSON(
            array(
                'messages' => $this->messages->messages
            )
        );
        return true;
    }
}