<?php

/**
 *  \details &copy; 2013  Open Ximdex Evolution SL [http://www.ximdex.org]
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

ModulesManager::file('/inc/utils.php');
ModulesManager::file('/inc/filters/Filter.class.php');
ModulesManager::file('/inc/pipeline/PipelineManager.class.php');
ModulesManager::file('/inc/repository/nodeviews/View_NodeToRenderizedContent.class.php');
ModulesManager::file('/inc/repository/nodeviews/View_PrefilterMacros.class.php');
ModulesManager::file('/inc/repository/nodeviews/View_Dext.class.php');
ModulesManager::file('/inc/repository/nodeviews/View_Xslt.class.php');
ModulesManager::file('/inc/repository/nodeviews/View_FilterMacrosPreview.class.php');


 /* <p>API language action</p>
 * <p>Handles requests to obtain the languages</p>
 */
class Action_preview extends AbstractAPIAction implements SecuredAction {

	/**
     	* <p>Default method for this action</p>
     	* <p>Renders a XML file into the browser</p>
     	* @param Request The current request
     	* @param Response The Response object to be sent and where to put the response of this action
     	*/
    	public function index($request, $response) {
		// Initializes variables
                $args = array();

                // Receives request params
                $idnode = $request->getParam("nodeid");
                $idchannel = $request->getParam("channelid");
                $json = $request->getParam("json");

		if($json == "" || $json == null){$json=0;}

                if(empty($idchannel)){$idchannel = $request->getParam("channel");}

		if (!$this->checkParameters($request, $response)) {
            		return;
        	}
		
                $node = new Node($idnode);

                // Checks if node is a structured document
                $structuredDocument = new StructuredDocument($idnode);
                if (!($structuredDocument->get('IdDoc') > 0)) {
			$this->createErrorResponse("It is not possible to show preview. Provided node is not a structured document.");
            		return;
                }

                // Checks content existence
		$content=$node->GetContent();
                if (!$content) {
                        $content = $structuredDocument->GetContent($request->getParam('version'),$request->getParam('sub_version'));
                } else {
                        //$content = $this->_normalizeXmlDocument($content);
                }

                // Validates channel
                if (!is_numeric($idchannel)) {
                        $channels = $node->getChannels();
			$firstChannel = null;
                        $idchannel = NULL;
                        if (!empty($channels)) {
                                foreach ($channels as $c) {
                                        $c = new Channel($c);
                                        $cName = $c->getName();
                                        $ic = $c->get('IdChannel');
                                        if ($firstChannel === null) $firstChannel = $ic;
                                        if (strToUpper($cName) == 'HTML') $idchannel = $ic;
                                        unset($c);
                                }
                        }
                        if ($idchannel === null) $idchannel = $firstChannel;

                        if ($idchannel === null) {
				$this->createErrorResponse("It is not possible to show preview. There isn't any defined channel.");
	                        return;
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
                        $docXapHeader = $node->class->_getDocXapHeader($idchannel, $idLanguage, $documentType);
                }
                $nodeName = $node->get('Name');
                $depth = $node->GetPublishedDepth();

                $args['MODE'] = $request->getParam('mode') == 'dinamic' ? 'dinamic' : 'static';
                $args['CHANNEL'] = $idchannel;
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

                $idnode = $idnode > 10000 ? $idnode : 10000;
                $node = new Node($idnode);

                $transformer = $node->getProperty('Transformer');
                $args['TRANSFORMER'] = $transformer[0];
                // Process Structured Document -> dexT/XSLT:
                $pipelineManager = new PipelineManager();

                $content = $pipelineManager->getCacheFromProcess(NULL, 'StrDocToDexT', $args);
                // Specific FilterMacros View for previsuals:
                $viewFilterMacrosPreview = new View_FilterMacrosPreview();
                $file = $viewFilterMacrosPreview->transform(NULL, $content, $args);
                $hash = basename($file);

		if($json==true){
			$content = FsUtils::file_get_contents($file);
        		$this->responseBuilder->ok()->content($content)->build();
		}
		else{
			$response->set('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
                	$response->set('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT");
                	$response->set('Cache-Control', array('no-store, no-cache, must-revalidate', 'post-check=0, pre-check=0'));
                	$response->set('Pragma', 'no-cache');
                	$response->set('Content-type', 'text/html');
			$content = FsUtils::file_get_contents($file);
			echo $content;
		}	
    	}

	/**
         * Deletes docxap tag
         */
        private function _normalizeXmlDocument($xmldoc) {
                $xmldoc = \Ximdex\Utils\String::stripslashes( $xmldoc);

                $doc = new DOMDocument();
                $doc->loadXML($xmldoc);
                $doc->encoding = 'UTF-8';
                $docxap = $doc->getElementsByTagName('docxap');

                if(!$docxap){
			return $xmldoc;
		}

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

	/**
     	* <p>Checks whether the required parameters are present in the request
     	* and modifies the response accordingly</p>
     	* 
     	* @param $request the request
     	* @param $response the response
     	* @return true if all required parameters are present and valid and false otherwise
     	*/
    	private function checkParameters($request, $response) {
        	$nodeid = $request->getParam('nodeid');
        	$username = $request->getParam(self::USER_PARAM);

        	// Is a valid user !
        	$user = new User();
        	$user->setByLogin($username);
        	$user_id = $user->GetID();
        	if ($user_id == null) {
        	    	$this->createErrorResponse('Unknown user');
        		return false;
        	}

        	$node = new Node($nodeid);

        	if ($nodeid == null) {
            		$this->createErrorResponse('The nodeid parameter is missing');
            		return false;
        	}
        	if ($node->GetID() == null) {
            		$this->createErrorResponse('The node ' . $nodeid . ' does not exist');
            		return false;
        	}

        	$nodeService = new \Ximdex\Services\Node();

        	$hasPermissionOnNode = $nodeService->hasPermissionOnNode($username, $nodeid, "View all nodes");
        	if (!$hasPermissionOnNode) {
            		$this->createErrorResponse('The user does not have permission on node ' . $nodeid);
        	    	return false;
        	}

        	return true;
    	}

}
