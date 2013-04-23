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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */




ModulesManager::file('/inc/pipeline/PipeCacheTemplates.class.php');
ModulesManager::file('/inc/xml/XmlBase.class.php');
ModulesManager::file('/inc/xml/XML.class.php');
ModulesManager::file('/inc/helper/String.class.php');


class Action_edittext extends ActionAbstract {
	// Main method: shows initial form
    	function index() {

		$this->addCss('/actions/edittext/resources/css/style.css');
		$this->addJs('/extensions/codemirrror/lib/codemirror.js');
		$this->addCss('/extensions/codemirrror/lib/codemirror.css');
//		$this->addCSS('/extensions/codemirrror/css/docs.css');
		$this->addCss('/extensions/codemirrror/theme/default.css');

	    	$idNode = $this->request->getParam('nodeid');

		$strDoc = new StructuredDocument($idNode);
		if($strDoc->GetSymLink()) {
			$this->messages->add(_('Document cannot be opened.'), MSG_TYPE_WARNING);
			$this->messages->add(_('It is a symbolic link'), MSG_TYPE_WARNING);
			$values = array('id_node' => $idNode,
							'messages' => $this->messages->messages);
			$this->render($values, NULL, 'messages.tpl');
			return;
		}
		$node = new Node($idNode);
		$node_name = $node->GetName();

		$idNodeType = $node->get('IdNodeType');
		$nodeType = new NodeType($idNodeType);
		$nodeTypeName = $nodeType->get('Name');
		
		$isXimNewsLanguage = ($nodeTypeName == "XimNewsNewLanguage");

		$fileName = $node->get('Name');
		$infoFile = pathinfo($fileName);
		if(array_key_exists("extension", $infoFile) ) {
			$ext = $infoFile['extension'];
			if(!file_exists(XIMDEX_ROOT_PATH."/extensions/codemirrror/mode/$ext/$ext.js") ) {
				$ext = "xml";
			}
		}else {
			$ext = "xml";
		} 

                if ($ext == "php" ){ 
	                        $this->addJs("/extensions/codemirrror/mode/xml/xml.js"); 
	                        $this->addJs("/extensions/codemirrror/mode/css/css.js"); 
	                        $this->addJs("/extensions/codemirrror/mode/javascript/javascript.js"); 
	                        $this->addJs("/extensions/codemirrror/mode/clike/clike.js");

		}

		$this->addJs("/extensions/codemirrror/mode/$ext/$ext.js");
		$this->addJs("/extensions/codemirrror/mode/$ext/$ext.js"); //this double is necessary
		$this->addJs('/actions/edittext/resources/js/init.js');

		$content = $node->GetContent();
		$content = $this->formatXml($content);
		$content = htmlspecialchars($content);

		$ruta = str_replace("/", "/ ",$node->GetPath());

		$jsFiles = array(Config::getValue('UrlRoot') . '/xmd/js/ximdex_common.js');

		$values = array('id_node' => $idNode,
				'isXimNewsLanguage' => $isXimNewsLanguage,
				'ruta' => $ruta,
				'ext' => $ext,
				'content' => $content,
				'go_method' => 'edittext',
				'on_load_functions' => 'resize_caja()',
				'on_resize_functions' => 'resize_caja()',
				'node_name' => $node_name,
				'id_editor' => $idNode.uniqid()
				);

		$this->render($values, null, 'default-3.0.tpl');
	}

/*
*	If nodeType is a PTD display documents affected by change
*/
	function publishForm() {
    		$idNode = $this->request->getParam('nodeid');

		$dataFactory = new DataFactory($idNode);
		$lastVersion = $dataFactory->GetLastVersionId();
		$prevVersion = $dataFactory->GetPreviousVersion($lastVersion);

		$cacheTemplate = new PipeCacheTemplates();
		$docs = $cacheTemplate->GetDocsContainTemplate($prevVersion);

		if (is_null($docs)) {
			$this->redirectTo('index');
			return;
		}

		$numDocs = sizeof($docs);

		for ($i = 0; $i < $numDocs; $i++) {
			$docsList[] = $docs[$i]['NodeId'];
		}

		$values = array('numDocs' => $numDocs,
						'docsList' => implode('_', $docsList),
						'go_method' => 'publicateDocs',
						);

		$this->render($values);

	}

/*
*	Publicate documents from publishForm method (above)
*/
	function publicateDocs() {

		if (ModulesManager::isEnabled('ximSYNC')) {
			ModulesManager::file('/inc/manager/SyncManager.class.php', 'ximSYNC');
		} else {
			ModulesManager::file('/inc/sync/SyncManager.class.php');
		}

		$docs = explode('_', $this->request->getParam('docsList'));

		$syncMngr = new SyncManager();
		$syncMngr->setFlag('deleteOld', true);
		$syncMngr->setFlag('linked', false);

		foreach ($docs as $documentID) {
			$result = $syncMngr->pushDocInPublishingPool($documentID, mktime(), NULL, NULL);
		}

		$arrayOpciones = array('ok' => _(' have been successfully published'),
				'notok' => _(' have not been published, because of an error during process'),
				'unchanged' => _(' have not been published because they are already published on its most recent version') );

		$values = array('arrayOpciones' => $arrayOpciones,
				'arrayResult' => $result
				);

		$this->render($values, NULL, 'publicationResult.tpl');
	}

	function edittext() {

		$idNode = $this->request->getParam('nodeid');
		$content = $this->request->getParam('editor');

		//If content is empty, put a blank space in order to save a file with empty content
		$content = empty($content) ? " " : $content;

		$node = new Node($idNode);
		if ((!$node->get('IdNode') > 0)) {
			$this->messages->add(_('The document which is trying to be edited does not exist'), MSG_TYPE_ERROR);
			$this->renderMessages();
		}
		$node->SetContent(String::stripslashes($content), true);
		$node->RenderizeNode();

		$nodeType = new NodeType($node->get('IdNodeType'));
		$nodeTypeName = $nodeType->get('Name');

		if (ModulesManager::isEnabled('ximNEWS')) {
			if ($nodeTypeName == "XimNewsNewLanguage") {
				// Persistence in database
				if (method_exists($node->class, 'updateNew')) {
					$node->class->updateNew();
				} else {
					XMD_Log::error(_('It was tried to call a non-existing method for this node: $node->class->updateNew for nodeid:') . $node->get('IdNode'));
				}
			}

			if ($this->request->getParam('publicar') == 1) {
			    $_GET['publicar'] = 1;
			    $this->redirectTo('index', 'addtocolector');
			    return;
			}
		}

		if ($nodeTypeName == 'Template' || $nodeTypeName == 'XslTemplate') {
			$this->redirectTo('publishForm');
			return;
		} else {
			//$this->redirectTo('index');
			return;
		}

    }

	function formatXml($xml) {

		// add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
		$xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);

		// now indent the tags
		$token      = strtok($xml, "\n");
		$result     = ''; // holds formatted version as it is built
		$pad        = 0; // initial indent
		$matches    = array(); // returns from preg_matches()

		// scan each line and adjust indent based on opening/closing tags
		while ($token !== false) :

			// test for the various tag states

			// 1. open and closing tags on same line - no change
			if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) :
				$indent=0;
			// 2. closing tag - outdent now
			elseif (preg_match('/^<\/\w/', $token, $matches)) :
				$pad--;
			// 3. opening tag - don't pad this one, only subsequent tags
			elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
				$indent=1;
			// 4. no indentation needed
			else :
				$indent = 0;
			endif;

			// pad the line with the required number of leading spaces
			$line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
			$result .= $line . "\n"; // add to the cumulative result, with linefeed
			$token   = strtok("\n"); // get the next token
			$pad    += $indent; // update the pad size for subsequent lines
		endwhile;

		return $result;
	}
}
?>
