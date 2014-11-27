<?php

ModulesManager::file('/inc/io/BaseIOInferer.class.php');

class Action_common implements APIRestAction, SecuredAction{
    	const SEPARATOR = ",";

    	/**
     	* <p>Read file information</p>
     	* @param type $request
     	* @param type $response
     	*/
    	public function get($request, $response){
        	$id = $request->getParam('id');
        	$username = $request->getParam(self::USER_PARAM);

        	if (!empty($id)) {
            		$nodeService = new \Ximdex\Services\Node();
            		if ($nodeService->existsNode($id) && $nodeService->hasPermissionOnNode($username, $id) && $nodeService->isOfNodeType($id, \Ximdex\Services\NodeType::BINARY_FILE)) {
                		$nodeInfo = $nodeService->getNodeInfo($id);
                		unset($nodeInfo['nodeType']);
                		unset($nodeInfo['children']);
                		$response->header_status(200);
                		$response->setContent(array("error" => 0, "data" => $nodeInfo));
            		}else{
                		$response->setContent(array("error" => 1, "msg" => "The requested file with id " . $id . " does not exist or you don't have permission to manage it"));
                		$response->header_status(404);
            		}
        	} else {
            		/* get all files for the user */
            		/* TODO: How to do it, query DB directly or do it through NodeService */
        	}
    	}

    /**
     * <p>Create new file</p>
     * @param type $request
     * @param type $response
     * 
     * parentid:
     * name:
     * 
     * languages: optional, default all configured languages
     * channels: optional, default all configured channels
     * 
     * 
     */
	public function post($request, $response){
        	$id = $request->getParam('parentid');
        	$name = $request->getParam('name');
        	$nodeService = new \Ximdex\Services\Node();

        	if (empty($name)) {
            		$this->createErrorResponse($response, "The name for the file is missing");
            		return;
        	}

        	if (empty($id) || $nodeService->getNode($id)== null) {
            		$this->createErrorResponse($response, "The parent id parameter where to create the new file is missing or it does not exist");
            		return;
        	}

        	if (empty($id)) {
            		$this->createErrorResponse($response, "The id of the schema to be used for the new file is missing");
            		return;
        	}

        	$this->createiNode($request, $response);
    	}

    /**
     * <p>Update file information</p>
     * 
     * content:
     * validate:
     * name: optional
     * 
     * @param type $request
     * @param type $response 
     */
    public function put($request, $response)
    {

        $id = $request->getParam('id');
        $username = $request->getParam(self::USER_PARAM);
        $content = $request->getParam('content');
        $validate = $request->getParam('validate');
        
        $nodeService = new \Ximdex\Services\Node();

        if (empty($id)) {
            $this->createErrorResponse($response, "The id of the file is missing");
            return;
        }
        
        if (!$nodeService->existsNode($id) || !$nodeService->hasPermissionOnNode($username, $id) || !$nodeService->isOfNodeType($id, \Ximdex\Services\NodeType::XML_DOCUMENT)) {
            $this->createErrorResponse($response, "The id for the file is missing or you don't have permission to manage it");
            return;
        }

        $node = $nodeService->getNode($id);

        if ($node == null) {
            $this->createErrorResponse($response, "The document does not exist", 404);
        }


        $b64decoded = base64_decode($content, true);
        $content = $b64decoded === false ? urldecode(stripslashes($content)) : urldecode($b64decoded);

        if ($content == NULL || $content == false) {
            $data[] = "The content of the file has not been updated because is empty, missing or not readable";
        } else {

            /* Check whether the supplied node Id references to an XML document */
            if (!$nodeService->isOfNodeType($node, \Ximdex\Services\NodeType::XML_DOCUMENT)) {
                $this->createErrorResponse("The supplied node id does not refer to an structured document");
                return;
            }

            if ($validate == NULL) {
                $validate = false;
            }

            if ($validate) {
                /* Check whether the document is compliant with the schema */
                $idcontainer = $node->getParent();
                $reltemplate = new RelTemplateContainer();
                $idTemplate = $reltemplate->getTemplate($idcontainer);

                $templateNode = new Node($idTemplate);
                $templateContent = $templateNode->GetContent();

                $contentToValidate = "<docxap>" . $content . "</docxap>";

                $validator = new XMLValidator_RNG();
                $result = $validator->validate($templateContent, $contentToValidate);

                if (!$result) {
                    $this->createErrorResponse($response, 'The content of the document does not meet the schema ' . $templateNode->GetNodeName());
                    return;
                }
            }

            $node->SetContent($content);
            $data[] = "The content of the document has been successfully updated.";

            $response->setContent(array('error' => 0, 'data' => implode('\n', $data)));
            $response->header_status(200);
        }
    }

    /**
     * <p>Delete file information</p>
     * @param type $request
     * @param type $response 
     */
	public function delete($request, $response){
        	$id = $request->getParam('id');
        	$username = $request->getParam(self::USER_PARAM);
        	$nodeService = new \Ximdex\Services\Node();

        	if (!empty($id)) {
            		if (!$nodeService->existsNode($id)) {
                		$response->setContent(array("error" => 1, "msg" => "The requested file with id " . $id . " does not exist and it can not be deleted"));
                		$response->header_status(404);
                		return;
            		}

            		if ($nodeService->hasPermissionOnNode($username, $id) && $nodeService->isOfNodeType($id, \Ximdex\Services\NodeType::BINARY_FILE)) {
                		$removed = $nodeService->deleteNode($id);
                
                		if ($removed) {
                    			$response->header_status(200);
                    			$response->setContent(array("error" => 0, "data" => "The file has been successfully deleted."));
                		}else{
                    			$response->setContent(array("error" => 1, "msg" => "The requested file with id " . $id . " does not exist and it can not be deleted"));
                    			$response->header_status(404);
                		}
            		}else{
                		$response->header_status(400);
                		$response->setContent(array("error" => 1, "msg" => "You don't have permission to delete this file."));
            		}
        	} else {
            	/* delete all documents (all XML Containers) for the user */
            	/* TODO: How to do it, query DB directly or do it through NodeService */
        	}
    	}

    /**
     * <p>Creates a new XML Container</p>
     * @param Request $request The request
     * @return type 
     */
    private function createXmlContainer($request, $response)
    {

        $idNode = $request->getParam('parentid');
        $nodeService = new \Ximdex\Services\Node();
        $node = $nodeService->getNode($idNode);

        /* Check whether it is possible to add a xml container as child of the supplied node */
        $nodeAllowedContent = new NodeAllowedContent();
        $allowedContents = $nodeAllowedContent->getAllowedChilds($node->GetNodeType());
        if (!in_array(\Ximdex\Services\NodeType::XML_CONTAINER, $allowedContents)) {
            $this->createErrorResponse($response, "The supplied node does not allow to have structured document container as a child");
            return;
        }

        $channels = $request->getParam('channels');
        if ($channels == "" || $channels == NULL) {
            // Getting channels for the node (in node properties or all registered channels)
            $channel = new Channel();
            $channelsNode = $channel->getChannelsForNode($idNode);
            $channels = array();
            foreach ($channelsNode as $channelNode) {
                array_push($channels, $channelNode['IdChannel']);
            }
        }
        /* Split the supplied channels using the SEPARATOR */ else {

            $channels = explode(self::SEPARATOR, $channels);
        }

        $name = $request->getParam('name');


        $idTemplate = $request->getParam('id_schema');


        /* The result of the action to be put in the response */
        $actionResult = array();

        // Creating container
        $baseIoInferer = new BaseIOInferer();
        $inferedNodeType = $baseIoInferer->infereType('FOLDER', $idNode);
        $nodeType = new NodeType();
        $nodeType->SetByName($inferedNodeType['NODETYPENAME']);
        if (!($nodeType->get('IdNodeType') > 0)) {
            $this->createErrorResponse('A nodetype could not be estimated to create the container folder, operation will be aborted, contact with your administrator');
            return;
        }

        $data = array(
            'NODETYPENAME' => $nodeType->get('Name'),
            'NAME' => $name,
            'PARENTID' => $idNode,
            'FORCENEW' => true,
            'CHILDRENS' => array(
                array('NODETYPENAME' => 'VISUALTEMPLATE', 'ID' => $idTemplate)
            )
        );
        $username = $request->getParam(self::USER_PARAM);
        $user = new User();
        $user->setByLogin($username);
        $user_id = $user->GetID();

        $baseIO = new baseIO();
        $idContainer = $result = $baseIO->build($data, $user_id);

        if (!($result > 0)) {
            $errorMessage = 'An error ocurred creating the container node.';
            foreach ($baseIO->messages->messages as $message) {
                $errorMessage .= " " . $message . ".";
            }

            $this->createErrorResponse($errorMessage);
            return;
        }

        $actionResult['container_nodeid'] = $idContainer;

        $languages = $request->getParam('languages');
        if ($languages == "" || $languages == NULL) {
            $language = new Language();
            $languagesNodes = $language->getLanguagesForNode($idNode);
            $languages = array();
            foreach ($languagesNodes as $languageNode) {
                $lang = new Language($languageNode['IdLanguage']);
                array_push($languages, $lang->GetIsoName());
            }
        } else {
            $languages = explode(self::SEPARATOR, $languages);
        }


        if ($result && is_array($languages)) {
            $baseIoInferer = new BaseIOInferer();
            $inferedNodeType = $baseIoInferer->infereType('FILE', $idContainer);
            $nodeType = new NodeType();
            $nodeType->SetByName($inferedNodeType['NODETYPENAME']);
            if (!($nodeType->get('IdNodeType') > 0)) {
                $this->createErrorResponse('A nodetype could not be estimated to create the document, operation will be aborted, contact with your administrator');
                return;
            }

            foreach ($channels as $idChannel) {
                $formChannels[] = array('NODETYPENAME' => 'CHANNEL', 'ID' => $idChannel);
            }

            // structureddocument inserts content document
            $setSymLinks = array();
            $master = $request->getParam('master');

            foreach ($languages as $isoLanguage) {
                $result = $this->_insertLanguage($isoLanguage, $nodeType->get('Name'), $name, $idContainer, $idTemplate, $formChannels);

                if ($result == NULL) {
                    /* If any error occurred when inserting the languages, stop the process and removes the container created previously */
                    $containerNode = new Node($idContainer);
                    $containerNode->DeleteNode();
                    $this->createErrorResponse(sprintf('Insertion of document %s with language %s has failed', $name, $isoLanguage));
                    return;
                }
                if ($master != "") {
                    if ($master != $isoLanguage) {
                        $setSymLinks[] = $result;
                    } else {
                        $idNodeMaster = $result;
                    }
                }

                $insertedNode = new Node($result);
                $actionResult['container_langs'][$isoLanguage] = array('nodeid' => $result, 'nodename' => $insertedNode->get('Name'));
            }

            foreach ($setSymLinks as $idNodeToLink) {
                $structuredDocument = new StructuredDocument($idNodeToLink);
                $structuredDocument->SetSymLink($idNodeMaster);

                $slaveNode = new Node($idNodeToLink);
                $slaveNode->set('SharedWorkflow', $idNodeMaster);
                $slaveNode->update();
            }
        }

        /* Creates the response content (for the time being) */
        $response->setContent(array('error' => 0, 'data' => $actionResult));
        $response->header_status(200);
    }

    /**
     * <p>Inserts given language as a child of the container</p>
     * @param type $isoName the language iso name
     * @param type $nodeTypeName the name of the node type
     * @param type $name
     * @param type $idContainer
     * @param type $idTemplate
     * @param type $formChannels
     * @return type 
     */
    private function _insertLanguage($isoName, $nodeTypeName, $name, $idContainer, $idTemplate, $formChannels)
    {
        $language = new Language();
        $language->SetByIsoName($isoName);

        if (!($language->get('IdLanguage') > 0)) {
            return NULL;
        }

        $idLanguage = $language->get('IdLanguage');

        $data = array(
            'NODETYPENAME' => $nodeTypeName,
            'NAME' => $name,
            'PARENTID' => $idContainer,
            "CHILDRENS" => array(
                array("NODETYPENAME" => "VISUALTEMPLATE", "ID" => $idTemplate),
                array("NODETYPENAME" => "LANGUAGE", "ID" => $idLanguage)
            )
        );

        foreach ($formChannels as $channel) {
            $data['CHILDRENS'][] = $channel;
        }

        $baseIO = new baseIO();
        $result = $baseIO->build($data);
        if ($result > 0) {
            return $result;
        } else {
            return NULL;
        }
    }

    /**
     * <p>Creates an error response, finishing the execution of the script</p>
     * @param Response $response The response object
     * @param string $message The message of the response
     * @param int $code The code of the response
     * 
     */
    private function createErrorResponse($response, $message, $code = 400)
    {
        $response->setContent(array("error" => 1, "msg" => $message));
        $response->header_status($code);
    }

}

?>