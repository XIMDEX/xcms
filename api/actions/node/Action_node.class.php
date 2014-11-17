<?php

/* * ****************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    							*
 *  Copyright (C) 2013  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 * @version $Revision: $                                                      *
 *                                                                            *
 *                                                                            *
 * **************************************************************************** */

require_once(XIMDEX_ROOT_PATH . '/inc/modules/ModulesManager.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/mvc/Request.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/model/RelNodeTypeMimeType.class.php');
require_once(XIMDEX_ROOT_PATH . '/conf/stats.conf');
ModulesManager::file('/inc/io/BaseIOInferer.class.php');
ModulesManager::file('/inc/dependencies/DepsManager.class.php');
ModulesManager::file('/inc/model/language.php');
ModulesManager::file('/inc/model/channel.php');
ModulesManager::file('/inc/i18n/I18N.class.php');
ModulesManager::file('/actions/xmleditor2/XimlinkResolver.class.php');
ModulesManager::file('/inc/xml/validator/XMLValidator_RNG.class.php');

/**
 * <p>API Node action</p>
 * <p>Handles requests to obtain and deal with nodes</p>
 */
class Action_node extends AbstractAPIAction implements SecuredAction {
    const SEPARATOR = ",";

    /**
     * <p>Default method for this action</p>
     * <p>Get the info of the node passed as parameter</p>
     * @param $request The current request
     * @param $response The response object to be sent and where to put the response of this action
     */
    public function index($request, $response) {
        $nodeid = $request->getParam('nodeid');

        if (!$this->checkParameters($request, $response)) {
            return;
        }

        $nodeInfo = $this->getNodeInfo($nodeid);
	$childInfo=array();
	foreach($nodeInfo["children"] as $chId){
		$ch=new Node($chId);
		$childInfo[] = array("nodeid"=>$ch->GetID(),"name"=>$ch->GetNodeName(),"nodetype"=>$ch->GetNodeType());
	}	
	$nodeInfo["children"]=$childInfo;

        $this->responseBuilder->ok()->content($nodeInfo)->build();
    }

    /**
     * <p>Creates a new empty node</p>
     * <p>It uses the id of the node where to insert the new one</p>
     * @param $request The current request
     * @param $response The response
     * param Response The Response object to be sent
     */
    public function create($request, $response) {
        $parentId = $request->getParam("nodeid");
        $nodeType = $request->getParam("nodetype");
        $name = $request->getParam("name");

        if ($parentId == "" || $nodeType == "" || $name == "") {
            $this->createErrorResponse('Some required parameters are missing');
            //$this->createErrorResponse($response, '400', 'Some required parameters are missing');
            return false;
        }

        if (!$this->checkParameters($request)) {
            return false;
        }

        //getting and adding file extension
        $rntmt = new RelNodeTypeMimeType();
        $ext = $rntmt->getFileExtension($nodeType);
        if (strcmp($ext, '') != 0) {
            $name_ext = $name . "." . $ext;
        } else {
            $name_ext = $name;
        }
        // creating new node
        $node = new Node();
        $idfile = $node->CreateNode($name_ext, $parentId, $nodeType, null);

        if ($idfile > 0) {
            $content = $this->getDefaultContent($nodeType, $name);
            $node->SetContent($content);
            $response->header_status('200');
            $respContent = array('error' => 0, 'data' => array('nodeid' => $idfile));
            $response->setContent($respContent);
        } else {
            $errorMsg = trim($node->messages->getRaw());
            $this->createErrorResponse("An error ocurred creating the new empty node. " . $errorMsg);
        }
    }

	/**
     * <p>Sets the content of a node of any type (css, js, text, xml ...)</p>
     * @param type $request
     * @param type $response
     */
    public function content($request, $response) {
        if (!$this->checkParameters($request, $response)) {
            return;
        }
       
        $idnode = $request->getParam('nodeid');
        $content = $request->getParam('content');

        $b64decoded = base64_decode($content, true);
        $content = $b64decoded === false ? urldecode(stripslashes($content)) : urldecode($b64decoded);
       
        if ($content == NULL || $content == false) {
            $this->createErrorResponse('Parameter content is missing or invalid');
            return;
        }
       
        $node = new Node($idnode);
       
        $node->SetContent($content);

        $this->responseBuilder->ok()->content('Content updated successfully')->build();
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

        $nodeService = new NodeService();
        
        $hasPermissionOnNode = $nodeService->hasPermissionOnNode($username, $nodeid, "View all nodes");
        if (!$hasPermissionOnNode) {
            $this->createErrorResponse('The user does not have permission on node ' . $nodeid);
            return false;
        }

        return true;
    }

    /**
     * <p>Gets the node info</p>
     * <p>It will return the following properties of the node:
     *  <ul>
     *      <li>nodeid</li>
     *      <li>nodeType</li>
     *      <li>name</li>
     *      <li>version (for nodes having version or 0 otherwise)</li>
     *      <li>creationDate (timestamp)</li>
     *      <li>modificationDate (timestamp)</li>
     *      <li>path</li>
     *      <li>parent</li>
     *      <li>children</li>
     *  </ul>
     * </p>
     *
     * @param string $nodeid the node id to get the information
     * @return array containing the node information
     */
    private function getNodeInfo($nodeid) {
        $node = new Node($nodeid);

        return array(
            'nodeid' => $node->GetID(),
            'nodeType' => $node->GetNodeType(),
            'name' => $node->GetNodeName(),
            'version' => $node->GetLastVersion() ? $node->GetLastVersion() : 0,
            'creationDate' => $node->get('CreationDate'),
            'modificationDate' => $node->get('ModificationDate'),
            'path' => $node->GetPath(),
            'parent' => $node->GetParent(),
            'children' => $node->GetChildren(),
        );
    }

    /**
     * <p>Gets the defult content depending on the nodetype</p>
     * @param int $nt the nodetype to get the default content
     * @param string $name the name of the new node
     * @return string the default content for the given nodetype
     */
    private function getDefaultContent($nt, $name) {
        switch ($nt) {
            case 5039:
                $content = "<<< DELETE \nTHIS\n CONTENT >>>";
                break;

            case 5028:
                $content = "/* CSS File: " . $name . ". Write your rules here. */\n\n * {}";
                break;

            case 5077:
                $content = "<?xml version='1.0' encoding='utf-8'?>\n<xsl:stylesheet xmlns:xsl='http://www.w3.org/1999/XSL/Transform' version='1.0'>\n<xsl:template name='" . $name . "' match='" . $name . "'>\n<!-- Insert your code here -->\n</xsl:template>\n</xsl:stylesheet>";
                break;

            case 5078:
                $content = "<?xml version='1.0' encoding='UTF-8' ?>\n<grammar xmlns='http://relaxng.org/ns/structure/1.0' xmlns:xim='http://ximdex.com/schema/1.0'>\n<!-- Create your own grammar here -->\n<!-- Need help? Visit: http://relaxng.org/tutorial-20011203.html -->\n</grammar>";
                break;

            case 5044:
                $content = "<?xml version='1.0' encoding='UTF-8'?>\n<" . $name . "><!-- Create here your own template -->\n</" . $name . ">";
                break;

            case 5045:
                $content = "<?xml version='1.0' encoding='UTF-8'?>\n<editviews xmlns:edx='msnbc-edx-edit-view'>\n<!-- Create here your views -->\n</editviews>\n##########";
                break;

            case 5076:
                $content = "<html>\n<head>\n</head>\n<body>\n</body>\n</html>";
                break;
        }
        return $content;
    }

    /**
     * <p>Creates a new XML container</p>
     * @param $request the current request
     * @param $response the response
     */
    public function createxml($request, $response) {

        if (!$this->checkParameters($request, $response)) {
            return;
        }

        $idNode = $request->getParam('nodeid');
        $node = new Node($idNode);

        /* Check whether it is possible to add a xml container as child of the supplied node */
        $nodeAllowedContent = new NodeAllowedContent();
        $allowedContents = $nodeAllowedContent->getAllowedChilds($node->GetNodeType());
        if (!in_array('5031', $allowedContents)) {
            $this->createErrorResponse("The supplied node does not allow to have structured document container as a child");
            return;
        }

        $channels = $request->getParam('channels');
        if ($channels == "" || $channels == NULL) {
            /* $this->createErrorResponse('Parameter channels is missing');
              return; */
            /*
             * Channel parameter not present. Getting all channels to be used
             */

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
        if ($name == "" || $name == NULL) {
            $this->createErrorResponse('Parameter name is missing');
            return;
        }

        $idTemplate = $request->getParam('id_schema');
        if ($idTemplate == "" || $idTemplate == NULL) {
            $this->createErrorResponse('Parameter id_schema is missing');
            return;
        }

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

        /* Creates the response using ResponseBuilder (which already contains the $response object param) */
        $this->responseBuilder->ok()->content($actionResult)->build();
    }

    /**
     * <p>Gets the possible RNG schemas for a given node which can be used to create a new XML container</p>
     * @param $request the current request
     * @param $response the response
     */
    public function schemas($request, $response) {

        $idNode = $request->getParam('nodeid');
	
	if($idNode){
        	$node = new Node($idNode);
        	if (!$this->checkParameters($request, $response)) {
            		return;
        	}
		$idproject=$node->GetProject();
		$project=new Node($idproject);
		$p_name=$project->GetNodeName();
		$schemas[$p_name]=$node->getSchemas();
	}
	else{
		//starting on the main root Ximdex node.
		$idNode=10000;
        	$node = new Node($idNode);
		$projects=$node->GetChildren(5013);

        	$schemas = array();

        	if (!is_null($projects)) {
			foreach($projects as $idproject){
				$p=new Node($idproject);
				$p_name=$p->GetNodeName();
				$schemas[$p_name]=$p->getSchemas();
			}
		}
	}

        $schemaArray = array();
        if (!is_null($schemas)) {
            foreach ($schemas as $p_name => $project) {
            	foreach ($project as $idschema) {
                	$schemaNode = new Node($idschema);
                	$schemaArray[$p_name][] = array('idschema' => $idschema, 'Name' => $schemaNode->get('Name'));
		}
            }
        }
        
        $this->responseBuilder->ok()->content($schemaArray)->build();
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
    private function _insertLanguage($isoName, $nodeTypeName, $name, $idContainer, $idTemplate, $formChannels) {
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
     * <p>Sets the content of the node</p>
     * @param type $request
     * @param type $response
     * @return type 
     */
    public function contentxml($request, $response) {
        if (!$this->checkParameters($request, $response)) {
            return;
        }

        $idnode = $request->getParam('nodeid');
        $content = $request->getParam('content');
        $validate = $request->getParam('validate');

        $b64decoded = base64_decode($content, true);
        $content = $b64decoded === false ? urldecode(stripslashes($content)) : urldecode($b64decoded);

        if ($content == NULL || $content == false) {
            $this->createErrorResponse('Parameter content is missing or invalid');
            return;
        }

        $node = new Node($idnode);
        /* Check whether the supplied node Id references to an XML document */
        if ($node->GetNodeType() != "5032") {
            $this->createErrorResponse("The supplied node id does not refer to an structured document");
            return;
        }

	if ($validate == NULL) {
		$validate=false;
	}

	if($validate){
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
            		$this->createErrorResponse('The content of the document does not match with the schema ' . $templateNode->GetNodeName());
            		return;
        	}
	}

        $node->SetContent($content);

        $this->responseBuilder->ok()->content('Content updated successfully')->build();
    }

    /**
     *
     * @param $request the current request
     * @param $response the response
     */
    public function publish($request, $response) {
        if (!$this->checkParameters($request, $response)) {
            return;
        }

        $idnode = $request->getParam('nodeid');
        $uptime = mktime();

        XSession::set('userID', $request->get(self::USER_PARAM));
        $result = SynchroFacade::pushDocInPublishingPool($idnode, $uptime);

        if (empty($result)) {
            $this->responseBuilder->ok()->content('This node does not need to be published again')->build();
        } else {
            $this->responseBuilder->ok()->content('Node ' . $idnode . " added to the publishing queue");
        }
    }

	/**
	*
     	* @param $request the current request
     	* @param $response the response
     	*/
    	public function getcontent($request, $response) {
		if (!$this->checkParameters($request, $response)) {
            		return;
        	}

        	$idnode = $request->getParam('nodeid');	
        	$clean = $request->getParam('clean');	

		$node = new Node($idnode);
		$content = $node->GetContent();
		if($clean){
			$content=preg_replace('/(\v|\s)+/', ' ', $content);
//			$content=preg_replace("/<\\//", '</', $content);
//			$content=preg_replace("/\\\"/", '"', $content);
		}
		if (empty($content)) {
			$this->createErrorResponse("The content of the given node couldn't be successfully retrieved.");
            		return;
        	} else {
            		$this->responseBuilder->ok()->content($content);
        	}
	}

	/**
	*
     	* @param $request the current request
     	* @param $response the response
     	*/
	public function delete($request, $response){
		if (!$this->checkParameters($request, $response)) {
                        return;
                }
		$idnode = $request->getParam('nodeid');
		$node = new Node($idnode);
                $result = $node->delete();

		if (!$result) {
            		$this->responseBuilder->ok()->content("This node couldn't be deleted.")->build();
        	} else {
            		$this->responseBuilder->ok()->content('Node ' . $idnode . " successfully deleted.");
        	}


	}

}