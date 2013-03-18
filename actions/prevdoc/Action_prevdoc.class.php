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




ModulesManager::file('/inc/utils.inc');
ModulesManager::file('/inc/helper/String.class.php');
ModulesManager::file('/inc/filters/Filter.class.php');
ModulesManager::file('/inc/pipeline/PipelineManager.class.php');
ModulesManager::file('/inc/repository/nodeviews/View_NodeToRenderizedContent.class.php');
ModulesManager::file('/inc/repository/nodeviews/View_PrefilterMacros.class.php');
ModulesManager::file('/inc/repository/nodeviews/View_Dext.class.php');
ModulesManager::file('/inc/repository/nodeviews/View_Xslt.class.php');
ModulesManager::file('/inc/repository/nodeviews/View_FilterMacrosPreview.class.php');


class Action_prevdoc extends ActionAbstract
{

    function index () {

    	// Initializes variables:
		$args = array();

		// Receives request params:
		$idNode = $this->request->getParam("nodeid");
		$idChannel = $this->request->getParam("channelid");

		if(empty($idChannel) )
				$idChannel = $this->request->getParam("channel");

		$showprev = $this->request->getParam("showprev");
		$json = $this->request->getParam("ajax");
		$content = stripslashes($this->request->getParam("content"));

		// Checks node existence:
		// TODO: If node does not exist, receives rest of params by request
		$node = new Node($idNode);
		if (!($node->get('IdNode') > 0)) {
			$this->messages->add(_('It is not possible to show preview.')
				. _(' The node you are trying to preview does not exist.'),
				MSG_TYPE_NOTICE);
			$values = array('messages' => $this->messages->messages);
			$this->render($values, NULL, 'messages.tpl');
		}

		// Checks if node is a structured document
		$structuredDocument = new StructuredDocument($idNode);
		if (!($structuredDocument->get('IdDoc') > 0)) {
			$this->messages->add(_('It is not possible to show preview.')
				. _(' Provided node is not a structured document.'),
				MSG_TYPE_NOTICE);
			$values = array('messages' => $this->messages->messages);
			$this->render($values, NULL, 'messages.tpl');
		}

		// Checks content existence
		if (!$content) {
			$content = $structuredDocument->GetContent(
				$this->request->getParam('version'),
				$this->request->getParam('sub_version')
			);
		} else {
			$content = $this->_normalizeXmlDocument($content);
		}

		// Validates channel
		if (!is_numeric($idChannel)) {
			$channels = $node->getChannels();
			$firstChannel = null;
			$idChannel = NULL;
			if (!empty($channels)) {
				foreach ($channels as $c) {
					$c = new Channel($c);
					$cName = $c->getName();
					$ic = $c->get('IdChannel');
					if ($firstChannel === null) $firstChannel = $ic;
					if (strToUpper($cName) == 'HTML') $idChannel = $ic;
					unset($c);
				}
			}
			if ($idChannel === null) $idChannel = $firstChannel;

			if ($idChannel === null) {
				$this->messages->add(_('It is not possible to show preview. There is not any defined channel.'), MSG_TYPE_NOTICE);
				$values = array('messages' => $this->messages->messages);
				$this->render($values, NULL, 'messages.tpl');
			}
		}

		// Populates variables and view/pipelines args
		// TODO: if node does not exist receive rest of params by request
		$idSection = $node->GetSection();
		$idProject = $node->GetProject();
		$idServerNode = $node->getServer();
		$documentType = $structuredDocument->getDocumentType();
		$idLanguage = $structuredDocument->getLanguage();
		$docXapHeader = null;
		if(method_exists($node->class, "_getDocXapHeader" ) ) {
			$docXapHeader = $node->class->_getDocXapHeader($idChannel, $idLanguage, $documentType);
		}
		$nodeName = $node->get('Name');
		$depth = $node->GetPublishedDepth();


		$args['MODE'] = $this->request->getParam('mode') == 'dinamic' ? 'dinamic' : 'static';
		$args['CHANNEL'] = $idChannel;
		$args['SECTION'] = $idSection;
		$args['PROJECT'] = $idProject;
		$args['SERVERNODE'] = $idServerNode;
		$args['LANGUAGE'] = $idLanguage;
		$args['DOCXAPHEADER'] = $docXapHeader;
		$args['NODENAME'] = $nodeName;
		$args['DEPTH'] = $depth;
		$args['DISABLE_CACHE'] = true;
		$args['CONTENT'] = $content;
		$args['NODETYPENAME'] = $node->nodeType->get('Name');
		$idNode = $idNode > 10000 ? $idNode : 10000;
		$node = new Node($idNode);
		$transformer = $node->getProperty('Transformer');
		$args['TRANSFORMER'] = $transformer[0];
		// Process Structured Document -> dexT/XSLT:
		$pipelineManager = new PipelineManager();

		$content = $pipelineManager->getCacheFromProcess(NULL, 'StrDocToDexT', $args);

		// Specific FilterMacros View for previsuals:
		$viewFilterMacrosPreview = new View_FilterMacrosPreview();
		$file = $viewFilterMacrosPreview->transform(NULL, $content, $args);
		$hash = basename($file);

		if (!empty($showprev)) {
			$this->request->setParam('hash', $hash);
			$this->prevdoc();
			return;
		}

		$queryManager = new QueryManager();
		$prevUrl = $queryManager->getPage() . $queryManager->buildWith(array('method' => 'prevdoc', 'hash' => $hash));

//    	$this->addCss('/actions/prevdoc/resources/css/prevdoc.css');

    	if ($json == 'json') {
    		$this->sendJSON(array('prevUrl' => $prevUrl));
    		return;
    	}

		$this->render(array('prevUrl' => $prevUrl), 'index', 'only_template.tpl');
    }

    public function prevdoc() {

		$this->response->set('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
		$this->response->set('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT");
		$this->response->set('Cache-Control', array('no-store, no-cache, must-revalidate', 'post-check=0, pre-check=0'));
		$this->response->set('Pragma', 'no-cache');
		$this->response->set('Content-type', 'text/html');

    	$hash = $this->request->getParam('hash');
    	$file = sprintf('%s/data/tmp/%s', Config::getValue('AppRoot'), $hash);
    	$content = '';

    	if (file_exists($file)) {
    		$content = FsUtils::file_get_contents($file);
    	}

		//Show preview as web
		$content = str_replace("&ajax=json", "&showprev=1", $content);

    	echo $content;
    	die();
    }

	/**
	 * Deletes docxap tag
	 */
	private function _normalizeXmlDocument($xmldoc) {

		/*$xmldoc = '<?xml version="1.0" encoding="UTF-8"?>' . String::stripslashes($xmldoc);*/
		$xmldoc = String::stripslashes($xmldoc);

		$doc = new DOMDocument();
		$doc->loadXML($xmldoc);
		$doc->encoding = 'UTF-8';
		$docxap = $doc->getElementsByTagName('docxap');
		if(!$docxap) return $xmldoc;
		$docxap = $docxap->item(0);

		$childrens = $docxap->childNodes;
		$l = $childrens->length;

		$xmldoc = '';
		for ($i=0; $i<$l; $i++) {
			$child = $childrens->item($i);
			if ($child->nodeType == 1) {
				$xmldoc .= $doc->saveXML($child) . "";
			}
		}

		return $xmldoc;
	}
}
?>
