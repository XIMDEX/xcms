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
 *  @version $Revision: 8778 $
 */

define('MODE_NODETYPE', 0);
define('MODE_NODEATTRIB', 1);

ModulesManager::file('/inc/io/BaseIOConstants.php');
ModulesManager::file('/inc/fsutils/FsUtils.class.php');
ModulesManager::file('/inc/model/node.php');
ModulesManager::file('/inc/model/structureddocument.php');
ModulesManager::file('/inc/workflow/Workflow.class.php');
ModulesManager::file('/inc/model/State.class.php');
ModulesManager::file('/inc/helper/Messages.class.php');
ModulesManager::file('/inc/ximNEWS_Adapter.php', 'ximNEWS');

// BaseIO API
if (!defined('XIMDEX_BASEIO_PATH'))
	define('XIMDEX_BASEIO_PATH', realpath(dirname(__FILE__)));

ModulesManager::file('/inc/model/language.php');
ModulesManager::file('/inc/auth/Auth.class.php');

class BaseIO {
	/**
	 * @var unknown_type
	 */
	var $messages = NULL;

	/**
	 * Constructor
	 * @return unknown_type
	 */
	function BaseIO() {
		$this->messages = new Messages();
	}

	/**
	 * Creates an object desribed in data array
	 *
	 * @param array $data Data of the object to create
	 * @param int $userid Optional param, if it is not specified, the identifier is obtained from the session user identifier
	 * @return identifier of the inserted node or a state specifying why it was not inserted
	 */
	function build($data, $userid = NULL) {
		global $metaTypesArray;
		if (is_null($metaTypesArray)) {
			require (XIMDEX_ROOT_PATH . '/inc/io/BaseIOConstants.php');
		}

		$data = $this->_checkVisualTemplate($data);

		if (!$userid) {
			$userid = XSession::get('userID');
		} elseif ($userid) {
			XSession::set('userID', $userid);
		}

		if (empty($data['NODETYPENAME'])) {
			XMD_Log::error(_('Empty nodetype in baseIO'));
			$this->messages->add(_('Empty nodetype'), MSG_TYPE_ERROR);
			return ERROR_INCORRECT_DATA;
		}

		if (!isset($data['CHILDRENS']))
			$data['CHILDRENS'] = array();

		if (!isset($data['ALIASNAME']))
			$data['ALIASNAME'] = '';

		if (empty($data['CLASS'])) {
			$data['CLASS'] = $this->_infereNodeTypeClass($data['NODETYPENAME']);
			if (empty($data['CLASS'])) {
				XMD_Log::error(_('Nodetype could not be infered'));
				$this->messages->add(_('Nodetype could not be infered'), MSG_TYPE_ERROR);
				return ERROR_INCORRECT_DATA;
			}
		}

		$nodeTypeClass = strtoupper($data['CLASS']);
		$nodeTypeName = strtoupper($data['NODETYPENAME']);

		$metaType = "";
		if (array_key_exists($nodeTypeClass, $metaTypesArray)) {
			$metaType = $metaTypesArray[$nodeTypeClass];
		}

		// upper all the indexes in data.		
		$data = $this->dataToUpper($data);

		if (!($this->_checkPermissions($nodeTypeName, $userid, WRITE) || $this->_checkName($data))) {
			XMD_Log::error(_('Node could not be inserted due to lack of permits'));
			$this->messages->add(_('Node could not be inserted due to lack of permits'),
					MSG_TYPE_ERROR);
			return ERROR_NO_PERMISSIONS;
		}
		if (!empty($data['PARENTID']) && !empty($data['NODETYPENAME'])) {
			$node = new Node();
			$nodeType = new NodeType();
			$nodeType->SetByName($data['NODETYPENAME']);
			if (!$node->checkAllowedContent($nodeType->GetID(), $data['PARENTID'], false)) {
				XMD_Log::error(_('Node could not be inserted due to it is not allowed in the folder'));
				$this->_dumpMessages($node->messages);
				return ERROR_NOT_ALLOWED;
			}
		}

		//general check
		if (!array_key_exists('PARENTID', $data)) {
			XMD_Log::error(_('Parentid was not specified'));
			$this->messages->add(_('Node parent was not specified'), MSG_TYPE_ERROR);
			return ERROR_INCORRECT_DATA;
		}
		if (!array_key_exists('NAME', $data)) {
			XMD_Log::error(_('Node name was not specified'));
			$this->messages->add(_('Node name was not specified'), MSG_TYPE_ERROR);
			return ERROR_INCORRECT_DATA;
		}
		return $this->createNode($data, $metaType, $nodeTypeClass, $nodeTypeName);
	}


	protected function createNode($data, $metaType, $nodeTypeClass, $nodeTypeName){
		switch ($metaType) {
			/* folder nodes */
			case 'FOLDERNODE' :
				$idNode = $this->_checkForceNew($data);
				if ($idNode > 0) {
					return $idNode;
				}
				// no extra check needed
				if (empty($data['IDTEMPLATE'])) {
					$idsVisualTemplate = $this->_getIdFromChildrenType($data['CHILDRENS'],
							'VISUALTEMPLATE');
					$data['IDTEMPLATE'] = isset($idsVisualTemplate[0]) ? $idsVisualTemplate[0] : $this->_getDefaultRNG();
				}
				switch ($nodeTypeClass) {
					case 'XIMNEWSCOLECTORNODETYPE' :
						$nodeType = new NodeType();
						$nodeType->SetByName('XimNewsColector');

						$folder = new Node();
						$idNode = $folder->CreateNode($data['NAME'], $data['PARENTID'],
								$nodeType->get('IdNodeType'), NULL, $data['FILTER'],
								$data['IDTEMPLATE'], $data['IDSECTION'], NULL,
								$data['ORDERNEWSINBULLETINS'], $data['NEWSPERBULLETIN'],
								$data['TIMETOGENERATE'], $data['NEWSTOGENERATE'], NULL,
								$data['MAILCHANNEL'], $data['PVDVERSION'], $data['INACTIVE'],
								$data['IDAREA'], $data['GLOBAL']);
						break;
					case 'XIMNEWSIMAGESFOLDER' :
						$nodeType = new NodeType();
						$nodeType->SetByName($nodeTypeName);

						$folder = new Node();
						$idNode = $folder->CreateNode($data['NAME'], $data['PARENTID'],
								$nodeType->get('IdNodeType'), NULL);

						break;

					default :
						$nodeType = new NodeType();
						$nodeType->SetByName($nodeTypeName);

						$folder = new Node();
						$idNode = $folder->CreateNode($data['NAME'], $data['PARENTID'],
								$nodeType->GetID(), NULL);
				}

				$this->_dumpMessages($folder->messages);

				if (!($idNode > 0)) {
					return ERROR_INCORRECT_DATA;
				}

				return $idNode;

			//section nodes
			case 'SECTIONNODE':
				$idNode = $this->_checkForceNew($data);
				if ($idNode > 0) {
					return $idNode;
				}

				$nodeType = new NodeType();
				$nodeType->SetByName($nodeTypeName);
				$section = new Node();
				$idNode = $section->CreateNode($data['NAME'], $data['PARENTID'],$nodeType->GetID(), NULL,$data['SUBFOLDERS']);
				if ($idNode > 0) {
					$node = new Node($idNode);
					reset($data['CHILDRENS']);
					while (list (, $attrs) = each($data['CHILDRENS'])) {
						switch ($attrs['NODETYPENAME']) {
							case 'RELGROUPSNODES' :
								$idGroup = isset($attrs['IDGROUP']) && $attrs['IDGROUP'] > 0 ? (int) $attrs['IDGROUP'] : null;
								$idRole = isset($attrs['IDROL']) && $attrs['IDROL'] > 0 ? (int) $attrs['IDROL'] : null;
								$node->AddGroupWithRole($idGroup, $idRole);
								break;
							case 'NODENAMETRANSLATION' :
								$idLanguage = isset($attrs['IDLANG']) && $attrs['IDLANG'] > 0 ? (int) $attrs['IDLANG'] : NULL;
								$description = isset(
										$attrs['DESCRIPTION']) && !empty(
										$attrs['DESCRIPTION']) ? utf8_decode(
										$attrs['DESCRIPTION']) : NULL;
								$node->SetAliasForLang($idLanguage, $description);
								break;
							default :
						}
					}
					unset($node);
				}

				$this->_dumpMessages($section->messages);

				if (!($idNode > 0)) {
					return ERROR_INCORRECT_DATA;
				}
				return $idNode;

			/* file nodes */
			case 'FILENODE' :
				$paths = $this->_getValueFromChildren($data['CHILDRENS'], 'SRC');

				if (count($paths) != 1) {
					$this->messages->add(_('A file for node creation could not be obtained'),
							MSG_TYPE_WARNING);
					return ERROR_INCORRECT_DATA;
				}
				$data['PATH'] = $paths[0];
				unset($data['CHILDRENS']);

				$nodeType = new NodeType();
				$nodeType->SetByName($data['NODETYPENAME']);
				$idNodeType = $nodeType->GetID();

				$node = new Node();
				$idNode = $node->CreateNode($data['NAME'], $data['PARENTID'], $idNodeType, null,
						$data['PATH']);

				if (!empty($data['STATE'])) {
					$node->class->promoteToWorkFlowState($data['STATE']);
				}

				$this->_dumpMessages($node->messages);

				if (!($idNode > 0)) {
					return ERROR_INCORRECT_DATA;
				}
				return $idNode;

			// link nodes
			case 'LINKNODE' :
				if (!isset($data['CHILDRENS'])) {
					$this->messages->add(_('Url was not stablished'), MSG_TYPE_ERROR);
					return ERROR_INCORRECT_DATA;
				}

				if (!($this->_searchNodeInChildrens($data['CHILDRENS'], 'URL', MODE_NODEATTRIB))) {
					$this->messages->add(_('Url was not stablished'), MSG_TYPE_ERROR);
					return ERROR_INCORRECT_DATA;
				}
				$urls = $this->_getValueFromChildren($data['CHILDRENS'], 'URL');
				if (count($urls) != 1)
					return ERROR_INCORRECT_DATA;
				$data['URL'] = $urls[0];
				if (strpos($data['URL'], '%') !== false) {
					$data['URL'] = urldecode($data['URL']);
				}

				$descriptions = $this->_getValueFromChildren($data['CHILDRENS'], 'DESCRIPTION');
				if (count($descriptions) == 1) {
					$data['DESCRIPTION'] = $descriptions[0];
				}

				$link = new Node();
				$idNode = $link->CreateNode($data['NAME'], $data['PARENTID'], '5049', null,
						$data['URL'], $data['DESCRIPTION']);

				$this->_dumpMessages($link->messages);

				if (!($idNode > 0)) {
					$this->messages->add(_('An error occurred inserting the document'),
							MSG_TYPE_ERROR);
					return ERROR_INCORRECT_DATA;
				}
				return $idNode;

			// xml container nodes
			case 'XMLCONTAINERNODE' :
				$idsVisualTemplate = array_merge(
						(array) $this->_getIdFromChildrenType($data['CHILDRENS'],
								'VISUALTEMPLATE'),
						(array) $this->_getIdFromChildrenType($data['CHILDRENS'],
								'RNGVISUALTEMPLATE'));
				$data['TEMPLATE'] = isset($idsVisualTemplate[0]) ? $idsVisualTemplate[0] : $this->_getDefaultRNG();
				if (empty($data['TEMPLATE'])) {
					$this->messages->add(_('It is being tried to insert a xmlcontainer without its corresponding schema'),MSG_TYPE_ERROR);
					return ERROR_INCORRECT_DATA;
				}
				$idNode = $this->_checkForceNew($data);
				if ($idNode > 0) {
					return $idNode;
				}
				//Obtaining the identifier of the father nodetype to know the child nodetype
				$node = new Node($data['PARENTID']); // creating a father instance
				$parentNodeTypeName = $node->nodeType->GetName();
				unset($node);

				$nodeType = new NodeType();
				if (!empty($data['NODETYPENAME'])) {
					$nodeType->SetByName($data['NODETYPENAME']);
				}

				//TODO Change it for a query crossing Nodeallowedcontents and nodetype
				if (!($nodeType->get('IdNodeType') > 0)) {
					switch ($parentNodeTypeName) {
						case 'XmlRootFolder' :
							$nodeType->SetByName('XmlContainer');
							break;
						case 'XmlFolder' :
							$nodeType->SetByName('XmlContainer');
							break;
						case 'XimletRootFolder' :
							$nodeType->SetByName('XimletContainer');
							break;
						case 'XimletFolder' :
							$nodeType->SetByName('XimletContainer');
							break;
						case 'XimNewsColector' :
							$nodeType->SetByName('XimletContainer');
							break;
						case 'XimPdfSection' :
							$nodeType->SetByName('XimPdfDocumentFolder');
							break;
					}
				}

				$idNodetype = $nodeType->get('IdNodeType');
				if (!($idNodetype > 0)) {
					XMD_Log::error('Nodetype not found');
					return false;
				}

				// Creating the node
				// TODO left to be implemented $aliasLangArray, $channelLst, $master
				$xmlcontainer = new Node();
				$idNode = $xmlcontainer->CreateNode($data['NAME'], $data["PARENTID"],
						$idNodetype, null, $data['TEMPLATE'],
                    isset($data["ALIASES"]) ? $data["ALIASES"] : null,
                    isset($data["CHANNELS"]) ? $data["CHANNELS"] : null,
                    isset($data["MASTER"]) ? $data["MASTER"] : null
                    );

				$this->_dumpMessages($xmlcontainer->messages);

				if (!($idNode > 0)) {
					return ERROR_INCORRECT_DATA;
				}
				return $idNode;

			// document nodes
			case 'XMLDOCUMENTNODE' :

				if (isset($data['FILTER']) && $data['FILTER'] == 'XIMPORTA') {
					ModulesManager::file('/actions/io/generalBaseIO.php', 'ximPORTA');
					include_once (XIMDEX_BASEIO_PATH . "/types/BaseIO_ximPORTA.class.php");
				} else {
					switch ($nodeTypeClass) {
						case 'XIMNEWSBULLETINNODETYPE' :
							ModulesManager::file('/inc/model/XimNewsBulletins.php', 'ximNEWS');
							break;
					}
				}

				$data['TEMPLATE'] = $this->_getVisualTemplateFromChildrens($data['CHILDRENS']);
				if (empty($data['TEMPLATE'])) {
					$this->messages->add(_('It was not specified a template for the node ') . $data['NAME'],
							MSG_TYPE_WARNING);
					return ERROR_INCORRECT_DATA;
				}

				$data['CHANNELS'] = $this->_getChannelFromChildrens($data['CHILDRENS']);
				if (empty($data['CHANNELS'])) {
					$this->messages->add(_('It was not specified any template for the node ') . $data['NAME'],
							MSG_TYPE_WARNING);
					return ERROR_INCORRECT_DATA;
				}

				$data['LANG'] = $this->_getLanguageFromChildrens($data['CHILDRENS']);
				if (empty($data['LANG'])) {
					$this->messages->add(_('It was not specified a language for the node ') . $data['NAME'],
							MSG_TYPE_WARNING);
					return ERROR_INCORRECT_DATA;
				}

				$paths = $this->_getValueFromChildren($data['CHILDRENS'], 'SRC');
				if (count($paths) == 1) {
					$data['CONTENT'] = FsUtils::file_get_contents($paths[0]);
				}

				$data['STATE'] = NULL;

				$language = new Language($data['LANG']);
				$sufix = sprintf('-id%s', $language->IsoName);
				// Deleting sufix repeted, over al then the source was ximIO
				$documentName = str_replace($sufix, '', $data['NAME']);
				$documentName = sprintf("%s%s",$documentName, $sufix);

				$idNode = NULL;
				if (isset($data['FILTER']) && $data['FILTER'] == 'XIMPORTA') {
					$data['PATH'] = renameFile($paths[0], $documentName, $this->messages);
					$base_io = new BaseIO_ximPORTA();
					$idNode = $base_io->build($data);
					if (isset($base_io->messages)) {
						$this->_dumpMessages($base_io->messages);
					}
				} else {

					switch ($nodeTypeClass) {
						case 'XIMNEWSNEWLANGUAGE' :
							$xmlDocument = new Node();
							$xmlDocument->CreateNode($documentName, $data['PARENTID'],
									$nodeType->get('IdNodeType'), $data['STATE'],
									$data['TEMPLATE'], $data['LANG'], $data['ALIASNAME'],
									$data['CHANNELS'], $data['DATANEWS']);
							$idNode = $xmlDocument->get('IdNode');
							break;
						case 'XIMNEWSBULLETINNODETYPE' :
							$xmlDocument = new Node();
							$xmlDocument->CreateNode($documentName, $data['PARENTID'],
									$nodeType->get('IdNodeType'), $data['STATE'],
									$data['TEMPLATE'], $data['LANG'], $data['ALIASNAME'],
									$data['CHANNELS'], $data['COLECTOR'], $data['DATE'],
									$data['SET'], $data['LOTE']);
							$idNode = $xmlDocument->get('IdNode');
							break;

						default : //XMLDOCUMENT
							$nodeType = new NodeType();
							$nodeType->SetByName($data['NODETYPENAME']);

							$xmlDocument = new Node();
							$xmlDocument->CreateNode($documentName, $data['PARENTID'],
									$nodeType->get('IdNodeType'), $data['STATE'],
									$data['TEMPLATE'], $data['LANG'], $data['ALIASNAME'],
									$data['CHANNELS']);
							//Creating a symbolic link with the master and stablishing its workflow.


							$idNode = $xmlDocument->get('IdNode');
					}
					$strDoc = new StructuredDocument($xmlDocument->get('IdNode'));

					if (array_key_exists('NEWTARGETLINK', $data)) {
						$xmlDocument->SetWorkflowMaster($data['NEWTARGETLINK']);
						$strDoc->SetSymLink($data['NEWTARGETLINK']);
					}
					if (isset($data['CONTENT']) && $data['CONTENT']) {
						$xmlDocument->SetContent($data['CONTENT']);
					}

				}
				if ($idNode > 0) {
					$node = new Node($idNode);
					$parent = new Node($node->GetParent()); // The logic names are inserted in the father
					reset($data['CHILDRENS']);
					while (list (, $attrs) = each($data['CHILDRENS'])) {
						switch ($attrs['NODETYPENAME']) {
							case 'NODENAMETRANSLATION' :
								$idLanguage = isset($attrs['IDLANG']) && $attrs['IDLANG'] > 0 ? (int) $attrs['IDLANG'] : NULL;
								$description = isset($attrs['DESCRIPTION']) && !empty(
										$attrs['DESCRIPTION']) ? utf8_decode(
										$attrs['DESCRIPTION']) : NULL;
								$parent->SetAliasForLang($idLanguage, $description);
								break;
							default :
						}
					}
					unset($node);
				}
				if (!($idNode > 0)) {
					return ERROR_INCORRECT_DATA;
				}
				return $idNode;

			case 'TRASH' :
				$node = new Node();
				$result = $node->CreateNode($data['NAME'], $data['PARENTID'], 9000);
				if (!($result > 0)) {
					reset($node->messages->messages);
					while (list (, $message) = each($node->messages->messages)) {
						XMD_Log::error($message['message']);
					}
				}
				return ($result > 0) ? $result : ERROR_INCORRECT_DATA;

			case 'IMAGENODE':
                $paths = $this->_getValueFromChildren($data['CHILDRENS'], 'SRC');
                if (count($paths) != 1) { 
                    $this->messages->add(_('A file for node creation could not be obtained'),MSG_TYPE_WARNING);
                    return ERROR_INCORRECT_DATA;
                }    
                $data['PATH'] = $paths[0];
                unset($data['CHILDRENS']);

				$node = new Node();
                $result = $node->CreateNode($data['NAME'], $data['PARENTID'], 5040,null,$data['PATH']);
                if (!($result > 0)) {
                    reset($node->messages->messages);
                    while (list (, $message) = each($node->messages->messages)) {
                        XMD_Log::error($message['message']);
                    }
                }
                return ($result > 0) ? $result : ERROR_INCORRECT_DATA;	

			case 'COMMONNODE' :
				$paths = $this->_getValueFromChildren($data['CHILDRENS'], 'SRC');

				if (count($paths) != 1) {
					$this->messages->add(_('A file for node creation could not be obtained'),
							MSG_TYPE_WARNING);
					return ERROR_INCORRECT_DATA;
				}
				$data['PATH'] = $paths[0];
				unset($data['CHILDRENS']);

				$nodeType = new NodeType();
				$nodeType->SetByName($data['NODETYPENAME']);
				$idNodeType = $nodeType->GetID();

				$node = new Node();
				$idNode = $node->CreateNode($data['NAME'], $data['PARENTID'], $idNodeType, null,$data['PATH']);

				if (!empty($data['STATE'])) {
					$node->class->promoteToWorkFlowState($data['STATE']);
				}

				$this->_dumpMessages($node->messages);

				if (!($idNode > 0)) {
					return ERROR_INCORRECT_DATA;
				}
				return $idNode;

			default :
				// TODO: trigger error.
				$this->messages->add(_('An error occurred trying to insert the node'),MSG_TYPE_ERROR);
				XMD_Log::fatal(sprintf(_("The class %s does not exist in BaseIO"),$nodeTypeName));
				return ERROR_INCORRECT_DATA;
		}
	}

	/**
	 *
	 * @param $data
	 * @param $userid
	 * @return unknown_type
	 */
	function update($data, $userid = NULL) {
		global $metaTypesArray;
		if (is_null($metaTypesArray)) {
			require (XIMDEX_ROOT_PATH . '/inc/io/BaseIOConstants.php');
		}

		if (!$userid) {
			$userid = XSession::get('userID');
		}

		// upper all the indexes in data and the nodetypename.		
		$data = $this->dataToUpper($data);

		if (empty($data['NODETYPENAME'])) {
			return ERROR_INCORRECT_DATA;
		}

		if (empty($data['CLASS'])) {
			$data['CLASS'] = $this->_infereNodeTypeClass($data['NODETYPENAME']);
			if (empty($data['CLASS'])) {
				XMD_Log::error(_('Nodetype could not be infered'));
				$this->messages->add(_('Nodetype could not be infered'), MSG_TYPE_ERROR);
				return ERROR_INCORRECT_DATA;
			}
		}

		$nodeTypeClass = strtoupper($data['CLASS']);
		$nodeTypeName = strtoupper($data['NODETYPENAME']);
		if (array_key_exists($nodeTypeClass, $metaTypesArray)) {
			$metaType = $metaTypesArray[$nodeTypeClass];
		}

		

		if (!($this->_checkPermissions($nodeTypeName, $userid, UPDATE) || $this->_checkName($data))) {
			return ERROR_NO_PERMISSIONS;
		}

		// Generic check
		if (!(array_key_exists('ID', $data))) {
			return ERROR_INCORRECT_DATA;
		}

		if (!isset($data['ID'])) {
			$this->messages->add(_('The identifier to update could not be found'),
					MSG_TYPE_ERROR);
			return ERROR_INCORRECT_DATA;
		}
		$node = new Node($data['ID']);

		switch (strtoupper($metaType)) {
			/* folder nodes */
			case 'FOLDERNODE' :
			case 'SECTIONNODE' :
				if (isset($data['NAME'])) {
					$node->set('Name', $data['NAME']);
					$result = $node->update();

					reset($node->messages->messages);
					while (list (, $message) = each($node->messages->messages)) {
						$this->messages->messages[] = $message;
					}

					if ($result) {
						return $node->get('IdNode');
					}
					return ERROR_INCORRECT_DATA;
				}
				return $node->get('IdNode');

            case 'IMAGENODE' :
                if (isset($data['CHILDRENS'])) {
                    if ($this->_searchNodeInChildrens($data['CHILDRENS'], 'PATH', MODE_NODETYPE)) {
                        $paths = $this->_getValueFromChildren($data['CHILDRENS'], 'SRC');
                        if (count($paths) != 1) { 
                            return ERROR_INCORRECT_DATA;
                        }    
                        $data['PATH'] = $paths[0];
                        if (is_file($data['PATH'])) {
                            $node->setContent(FsUtils::file_get_contents($data['PATH']));
                        }    
                        unset($data['CHILDRENS']);
                    }    
                }    

                if (!empty($data['NAME'])) {
                    $node->set('Name', $data['NAME']);
                    $result = $node->update();
                    if ($result > 0) { 
                        return $result;
                    }    
                }    

                if (!empty($data['STATE'])) {
                    $node->class->promoteToWorkFlowState($data['STATE']);
                }    

                return $data['ID'];

			/* file nodes */
			case 'FILENODE' :
				if (isset($data['CHILDRENS'])) {
					if ($this->_searchNodeInChildrens($data['CHILDRENS'], 'PATH', MODE_NODETYPE)) {
						$paths = $this->_getValueFromChildren($data['CHILDRENS'], 'SRC');
						if (count($paths) != 1) {
							return ERROR_INCORRECT_DATA;
						}
						$data['PATH'] = $paths[0];
						if (is_file($data['PATH'])) {
							$node->setContent(FsUtils::file_get_contents($data['PATH']));
						}
						unset($data['CHILDRENS']);
					}
				}

				if (!empty($data['NAME'])) {
					$node->set('Name', $data['NAME']);
					$result = $node->update();
					if ($result > 0) {
						return $result;
					}
				}

				if (!empty($data['STATE'])) {
					$node->class->promoteToWorkFlowState($data['STATE']);
				}

				return $data['ID'];

			// link nodes
			case 'LINKNODE' :
				$updateNode = false;
				$updateClass = false;

				$node = new Node($data['ID']);
				if (!($node->get('IdNode') > 0)) {
					$this->messages->add(_('It is being tried to modify a nonexistent node'),
							MSG_TYPE_ERROR);
					return ERROR_INCORRECT_DATA;
				}

				if (isset($data['CHILDRENS']) && ($this->_searchNodeInChildrens(
						$data['CHILDRENS'], 'URL', MODE_NODEATTRIB))) {
					$urls = $this->_getValueFromChildren($data['CHILDRENS'], 'URL');
					$url = $urls[0];
					if (strpos($url, '%') !== false) {
						$url = urldecode($url);
					}
					$node->class->setUrl($url, false);
					$updateClass = true;
				}

				$descriptions = $this->_getValueFromChildren($data['CHILDRENS'], 'DESCRIPTION');
				if (count($descriptions) == 1) {
					$description = $descriptions[0];
					$node->set('Description', $description);
					$updateNode = true;
				}

				if (!empty($data['NAME'])) {
					$node->set('Name', $data['NAME']);
					$updateNode = true;
				}

				if (!empty($data['PARENTID'])) {
					$node->set('IdParent', $data['PARENTID']);
					$updateNode = true;
				}

				$result = true;
				if ($updateNode) {
					$result = $result && $node->update();
				}
				if ($updateClass) {
					$result = $node->class->link->update() && $result;
				}
				$this->_dumpMessages($node->messages);

				if ($result) {
					return $data['ID'];
				}
				return ERROR_INCORRECT_DATA;
				break;

			// xml container nodes
			case 'XMLCONTAINERNODE' :
				// Just name can be modified
				$xmlContainer = new Node($data['ID']);
				$update = false;
				if (isset($data['NAME'])) {
					$xmlContainer->set('Name', $data['NAME']);
					$update = true;
				}
				if ($update) {
					$idNode = $xmlContainer->update();
				}
				$this->_dumpMessages($xmlContainer->messages);

				if (!($idNode > 0)) {
					return ERROR_INCORRECT_DATA;
				}
				return $idNode;

			// document nodes
			case 'XMLDOCUMENTNODE' :
				$xmlDocument = new Node($data['ID']);
				$result = $xmlDocument->get('IdNode');
				if (!($xmlDocument->get('IdNode') > 0)) {
					$this->messages->add(sprintf(_('Node %s could not be found'), $data['ID']),
							MSG_TYPE_ERROR);
					return ERROR_INCORRECT_DATA;
				}

				$estimatedNodeTypeClass = $xmlDocument->nodeType->get('Class');
				if (strcmp(strtoupper($estimatedNodeTypeClass), $nodeTypeClass)) {
					$this->messages->add(_('It has been specified a node of type xmldocument and the found node is of type ') .
									 $estimatedNodeTypeClass, MSG_TYPE_ERROR);
					return ERROR_INCORRECT_DATA;
				}

				$updateNode = false;
				if (isset($data['NAME'])) {
					$xmlDocument->set('Name', $data['NAME']);
					$updateNode = true;
				}
				if (isset($data['PARENTID'])) {
					$xmlDocument->set('IdParent', $data['PARENTID']);
					$updateNode = true;
				}

				$parent = new Node($xmlDocument->get('IdParent')); // Logic name are inserted in the parent
				reset($data['CHILDRENS']);
				while (list (, $attrs) = each($data['CHILDRENS'])) {
					switch ($attrs['NODETYPENAME']) {
						case 'NODENAMETRANSLATION' :
							$idLanguage = isset($attrs['IDLANG']) && $attrs['IDLANG'] > 0 ? (int) $attrs['IDLANG'] : NULL;
							$description = isset($attrs['DESCRIPTION']) && !empty(
									$attrs['DESCRIPTION']) ? utf8_decode(
									$attrs['DESCRIPTION']) : NULL;
							$parent->SetAliasForLang($idLanguage, $description);
							break;
						default :
					}
				}
				unset($node);

				$structuredDocument = new StructuredDocument($data['ID']);
				$updateStructuredDocument = false;
				$data['TEMPLATE'] = $this->_getVisualTemplateFromChildrens($data['CHILDRENS']);
				if (!empty($data['TEMPLATE'])) {
					$structuredDocument->set('IdTemplate', $data['TEMPLATE']);
					$updateStructuredDocument = true;
				}

				$data['LANG'] = $this->_getLanguageFromChildrens($data['CHILDRENS'], false);

				if (!empty($data['LANG'])) {
					$structuredDocument->set('IdLanguage', $data['LANG']);
					$updateStructuredDocument = true;
				}

				$channels = array();
				if (isset($data['CHILDRENS']) && is_array($data['CHILDRENS'])) {
					foreach ($data['CHILDRENS'] as $children) {
						if (isset($children['NODETYPENAME']) && $children['NODETYPENAME'] == 'CHANNEL') {
							$channels[] = $children;
						}
					}
				}

				$paths = $this->_getValueFromChildren($data['CHILDRENS'], 'SRC');
				if (count($paths) == 1) {
					$content = FsUtils::file_get_contents($paths[0]);
					$xmlDocument->SetContent($content, true);
				}

				if ($updateNode) {
					$result = $xmlDocument->update();
					$this->_dumpMessages($xmlDocument->messages);
				}
				if ($updateStructuredDocument && $result) {
					$result = $structuredDocument->update();
					$this->_dumpMessages(($structuredDocument->messages));
				}
				if ($result) {
					foreach ($channels as $channel) {
						$db = new DB();
						if (isset($channel['OPERATION']) && (strtoupper($channel['OPERATION']) ==
								 'REMOVE')) {
									$query = sprintf(
											"DELETE FROM RelStrDocChannels WHERE IdDoc = %s AND IdChannel = %s",
											$db->sqlEscapeString($data['ID']),
											$db->sqlEscapeString($channel['ID']));
							$db->execute($query);
							if ($db->numErr > 0) {
								$this->messages->add(_("Error deleting the document channel"),
										MSG_TYPE_ERROR);
								return ERROR_INCORRECT_DATA;
							}
							continue;
						}
						$query = sprintf("SELECT IdRel FROM RelStrDocChannels where IdDoc = %s AND IdChannel = %s",
								$db->sqlEscapeString($data['ID']),
								$db->sqlEscapeString($channel['ID']));
						$db->query($query);
						if ($db->EOF) {
							$query = sprintf("INSERT INTO RelStrDocChannels (IdDoc, IdChannel) VALUES (%s, %s)",
											$db->sqlEscapeString($data['ID']),
											$db->sqlEscapeString($channel['ID']));
							$db->execute($query);
							if ($db->numErr > 0) {
								$this->messages->add(_("Error inserting a document channel"),
										MSG_TYPE_ERROR);
								return ERROR_INCORRECT_DATA;
							}
						}
					}
				}

				switch ($nodeTypeClass) {
					case 'XIMNEWSNEWLANGUAGE' :
						$xmlDocument = new Node($data['ID']);
						$xmlDocument->class->update($data['ID'], $data['NAME'],
								$data['IDSECTION'], $data['NEWSDATA']);
						break;
					default :
				}

				return $result;
				break;

			default :
				// TODO: trigger error.
				XMD_Log::error(sprintf(_("The class %s is no existing in BaseIO update"), $nodeTypeName));
				$this->messages->add(_('A nodetype could not be determined for insertion'),
						MSG_TYPE_ERROR);
				return ERROR_INCORRECT_DATA;
		}
		return ERROR_INCORRECT_DATA;
	}

	/**
	 *
	 * @param $data
	 * @param $userid
	 * @return unknown_type
	 */
	function delete($data, $userid = NULL) {

		global $metaTypesArray;

		if (!$userid) {
			$userid = XSession::get('userID');
		}

		$node = new Node($data['ID']);

		if (!($node->get('IdNode') > 0)) {
			return ERROR_INCORRECT_DATA;
		}

		if (!($this->_checkPermissions($node->nodeType->get('Name'), $userid, DELETE) || $this->_checkName(
				$data))) {
			return ERROR_NO_PERMISSIONS;
		}

		// Generic check
		if (!(array_key_exists('ID', $data))) {
			return ERROR_INCORRECT_DATA;
		}

		$result = $node->delete();

		if ($result) {
			return 1; // == true, but keeping numeric format
		}

		return ERROR_INCORRECT_DATA;
	}
	/**
	 * This function is only used in ximIO
	 *
	 * @param array $data
	 * @param int $userId
	 * @return int
	 */
	function check($data, $userId) {

		if (isset($data['NODETYPENAME'])) {
			$nodeTypeName = $data['NODETYPENAME'];
		} else {
			return false;
		}

		if (!($this->_checkPermissions($nodeTypeName, $userId, WRITE) || $this->_checkName($data))) {
			return false;
		}

		if (!(array_key_exists('PARENTID', $data) || array_key_exists('NAME', $data) || array_key_exists(
				'NODETYPE', $data))) {
			return ERROR_INCORRECT_DATA;
		}

		//TODO commonrootfolder content missing!
		switch ($nodeTypeName) {
			/* folder nodes */
			case 'PROJECT' :
			case 'TEMPLATESROOTFOLDER' :
			case 'LINKMANAGER' :
			case 'LINKFOLDER' :
			case 'TEMPLATEVIEWFOLDER' :
			case 'CSSROOTFOLDER' :
			case 'IMAGESROOTFOLDER' :
			case 'XMLROOTFOLDER' :
			case 'COMMONROOTFOLDER' :
			case 'IMPORTROOTFOLDER' :
			case 'XIMLETROOTFOLDER' :
			case 'XIMNEWSIMAGES' :
			case 'XIMNEWSIMAGESFOLDER' :
			case 'XIMNEWSBULLETINS' :
			case 'XIMNEWSCOLECTOR' :
			case 'XIMNEWSNEWS' :
			case 'XIMNEWSNEW' :
			case 'SERVER' :
			case 'IMAGESFOLDER' :
			case 'COMMONFOLDER' :
			case 'XIMNEWSCATEGORY' :
			case 'XIMNEWSDATESECTION' :

				return 1;
				break;

			/* file nodes */
			case 'TEMPLATE' :
			case 'VISUALTEMPLATE' :
			case 'CSSFILE' :
			case 'IMAGEFILE' :
			case 'NODEHT' :
			case 'XIMNEWSIMAGEFILE' :
			case 'BINARYFILE' :
			case 'TEXTFILE':

				/*				if ( !($this->_searchNodeInChildrens($data['CHILDRENS'], 'PATH', MODE_NODETYPE) )) {
					return ERROR_INCORRECT_DATA;
					}*/
				return 1;
				break;

			// link nodes
			case 'LINK' :
				if (!isset($data['CHILDRENS'])) {
					return ERROR_INCORRECT_DATA;
				}

				if (!($this->_searchNodeInChildrens($data['CHILDRENS'], 'URL', MODE_NODEATTRIB))) {
					return ERROR_INCORRECT_DATA;
				}
				return 1;
				break;

			// document nodes
			case 'XMLDOCUMENT' :
			case 'XIMLET' :
			case 'XIMNEWSBULLETINLANGUAGE' :
			case 'XIMNEWSNEWLANGUAGE' :

				if (!(/*$this->_searchNodeInChildrens($data['CHILDRENS'], 'PATH', MODE_NODETYPE) ||*/
				$this->_searchNodeInChildrens($data['CHILDRENS'], 'CHANNEL', MODE_NODETYPE) ||
						 $this->_searchNodeInChildrens($data['CHILDRENS'], 'LANGUAGE',
								MODE_NODETYPE) || $this->_searchNodeInChildrens(
								$data['CHILDRENS'], 'VISUALTEMPLATE', MODE_NODETYPE))) {
							return ERROR_INCORRECT_DATA;
				}
				return 1;

			// template nodes
			case 'XMLCONTAINER' :
			case 'XIMLETCONTAINER' :
			case 'XIMNEWSBULLETIN:' :
				// TODO Here, it should be checked if it is containing a visualtemplate, we'll see how when debug could be made
				if (!($this->_searchNodeInChildrens($data['CHILDRENS'],
						'VISUALTEMPLATE', MODE_NODETYPE))) {
					return ERROR_INCORRECT_DATA;
				}
				return 1;

			//section nodes
			case 'XIMNEWSSECTION' :
			case 'SECTION' :
				if (!($this->_searchNodeInChildrens($data['CHILDRENS'], 'RELGROUPSNODES',
						MODE_NODETYPE))) {
					return ERROR_INCORRECT_DATA;
				}
				return 1;

			default :
				XMD_Log::warning(sprintf(_("The class %s does not exist in BaseIO"), $nodeTypeName ));
				return 1;
				break;
		}
	}

	/**
	 *
	 * @param $childrens
	 * @param $nodeKey
	 * @param $mode
	 * @return unknown_type
	 */
	function _searchNodeInChildrens($childrens, $nodeKey, $mode = MODE_NODETYPE) {
		if (!is_array($childrens))
			return false;
		if (empty($nodeKey))
			return false;

		reset($childrens);
		while (list (, $children) = each($childrens)) {
			if (!is_array($children))
				return false;
			reset($children);
			while (list ($attrKey, $attrValue) = each($children)) {

				if ($mode == MODE_NODETYPE) {
					if (!strcmp($attrValue, $nodeKey)) {
						return true;
					}
				} elseif ($mode == MODE_NODEATTRIB) {
					if (!strcmp($attrKey, $nodeKey)) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 *
	 * @param $childrens
	 * @param $nodeName
	 * @return unknown_type
	 */
	function _getValueFromChildren($childrens, $nodeName) {
		if (!is_array($childrens))
			return false;
		if (empty($nodeName))
			return false;

		reset($childrens);
		$attrValues = array();

		while (list (, $children) = each($childrens)) {
			if (!is_array($children))
				continue;
			reset($children);
			while (list ($attrKey, $attrValue) = each($children)) {
				if (!strcmp($attrKey, $nodeName))
					$attrValues[] = $attrValue;
			}
		}
		return $attrValues;
	}
	/**
	 * Funcion which obtain the idenfier of a given node children
	 *
	 * @param array $childrens
	 * @param string $nodeTypeName
	 * @return array / false
	 */
	function _getIdFromChildrenType($childrens, $nodeTypeName) {
		if (!is_array($childrens))
			return false;
		if (empty($nodeTypeName))
			return false;

		reset($childrens);
		$idValues = array();
		while (list (, $children) = each($childrens)) {
			if (!is_array($children))
				continue;
			if (!strcasecmp($children['NODETYPENAME'], $nodeTypeName))
				$idValues[] = $children['ID'];
		}
		return $idValues;
	}

	/**
	 *
	 * @param $nodeTypeName
	 * @param $userId
	 * @param $operation
	 * @return unknown_type
	 */
	function _checkPermissions($nodeTypeName, $userId, $operation) {

		// Check permissions.
		$nodeType = new NodeType();
		$nodeType->SetByName($nodeTypeName);
		$idNodeType = $nodeType->ID;
		switch ($operation) {
			case WRITE :
				if (!Auth::canWrite($userId, array('node_type' => $idNodeType))) {
					return ERROR_NO_PERMISSIONS;
				}
				break;
			case UPDATE :
				if (!Auth::canModify($userId, array('node_type' => $idNodeType))) {
					return ERROR_NO_PERMISSIONS;
				}
				break;
			case DELETE :
				if (!Auth::canDelete($userId, array('node_type' => $idNodeType))) {
					return ERROR_NO_PERMISSIONS;
				}
				break;
			default :
				if (!Auth::canWrite($userId, array('node_type' => $idNodeType))) {
					return ERROR_NO_PERMISSIONS;
				}
				break;
		}
		return true;
	}

	/**
	 *
	 * @param $data
	 * @return unknown_type
	 */
	function _checkName($data) {
		// Check del nombre del nodo
		$node = new Node();
		$nodeName = !empty($data['NAME']) ? $data['NAME'] : '';
		$nodeType = !empty($data['NODETYPE']) ? (int) $data['NODETYPE'] : 0;
		if (!$node->IsValidName($nodeName, $nodeType)) {
			return ERROR_INCORRECT_DATA;
		}
		return true;
	}

	/**
	 *
	 * @param $nodeTypeName
	 * @return unknown_type
	 */
	function _infereNodeTypeClass($nodeTypeName) {
		if (empty($nodeTypeName)) {
			return NULL;
		}
		$nodeType = new NodeType();
		$nodeType->SetByName($nodeTypeName);
		if ($nodeType->get('IdNodeType') > 0) {
			// Returned as brought from DB, because it will be turned into capitals in a loop
			return $nodeType->get('Class');
		}
		return NULL;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function _getDefaultRNG() {
		$defaultRNG = Config::getValue('defaultRNG');
		$node = new Node($defaultRNG);
		if ($node->get('IdNode') > 0) {
			return ($node->nodeType->GetName() == 'VisualTemplate') ? $defaultRNG : NULL;
		}
		return NULL;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function _getDefaultChannel() {
		$defaultChannel = Config::getValue('defaultChannel');
		$node = new Node($defaultChannel);
		if ($node->get('IdNode') > 0) {
			return ($node->nodeType->GetName() == 'Channel') ? $defaultChannel : NULL;
		}
		return NULL;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function _getDefaultLanguage() {
		$defaultLanguage = Config::getValue('DefaultLanguage');
		$language = new Language();
		$language->SetByIsoName($defaultLanguage);

		return ($language->GetID() > 0) ? $language->GetID() : NULL;
	}

	/**
	 *
	 * @param $childrens
	 * @return unknown_type
	 */
	function _getVisualTemplateFromChildrens($childrens) {
		// the children of a visual template would be the paths, and it should be just one
		$idsVisualTemplate = $this->_getIdFromChildrenType($childrens, 'VISUALTEMPLATE');
		$idsVisualTemplate = array_merge(
				(array) $this->_getIdFromChildrenType($childrens, 'VISUALTEMPLATE'),
				(array) $this->_getIdFromChildrenType($childrens, 'RNGVISUALTEMPLATE'));
		if (count($idsVisualTemplate) != 1) {
			$defaultRNG = $this->_getDefaultRNG();
			if (empty($defaultRNG)) {
				return NULL;
			}
			return $defaultRNG;
		} else {
			return $idsVisualTemplate[0];
		}
	}

	/**
	 *
	 * @param $childrens
	 * @param $withDefault
	 * @return unknown_type
	 */
	function _getChannelFromChildrens($childrens, $withDefault = true) {
		$channels = $this->_getIdFromChildrenType($childrens, 'CHANNEL');
		// Trying to get the default Channel
		if (empty($channels)) {
			if ($withDefault) {
				$defaultChannel = $this->_getDefaultChannel();
			}
			if (empty($defaultChannel)) {
				return NULL;
			}
			return array($defaultChannel);
		} else {
			return $channels;
		}
	}

	/**
	 *
	 * @param $childrens
	 * @param $withDefault
	 * @return unknown_type
	 */
	function _getLanguageFromChildrens($childrens, $withDefault = true) {
		$languages = $this->_getIdFromChildrenType($childrens, 'LANGUAGE');
		if (count($languages) != 1) {
			if ($withDefault) {
				$defaultLanguage = $this->_getDefaultLanguage();
			}
			if (empty($defaultLanguage)) {
				return NULL;
			}
			return $defaultLanguage;
		} else {
			return $languages[0];
		}
	}

	/**
	 * Dumping an array in baseio object messages
	 * The array messages is sent by reference due to efficiency
	 *
	 * @param array $messages
	 */
	function _dumpMessages(& $messages) {
		if (strtolower(get_class($messages)) != 'messages') {
			XMD_Log::error(_('Error obtaining object messages'));
			return;
		}
		foreach ($messages->messages as $message) {
			$this->messages->messages[] = $message;
		}
	}

	/**
	 *
	 * @param $data
	 * @return unknown_type
	 */
	function _checkForceNew($data) {
		if (!(isset($data['FORCENEW']) && $data['FORCENEW'] == true)) {
			$parent = new Node($data['PARENTID']);
			// It may should be done by type
			$idNode = $parent->GetChildByName($data['NAME']);
			if ($idNode > 0) {
				return $idNode;
			}
		}
		return NULL;
	}

	/**
	 * Check if the VisualTemplate is a RNGVisualTemplate
	 * @param unknown_type $data
	 */
	function _checkVisualTemplate($data) {

		if (!isset($data['NODETYPENAME']) || $data['NODETYPENAME'] != 'VisualTemplate') {
			return $data;
		}

		if (!isset($data['CHILDRENS'], $data['CHILDRENS'][0], $data['CHILDRENS'][0]['SRC'])) {
			return $data;
		}

		$content = FsUtils::file_get_contents($data['CHILDRENS'][0]['SRC']);

		$rngXMLNS = '#xmlns="http://relaxng.org/ns/structure/1.0"#';
		if (preg_match($rngXMLNS, $content) > 0) {

			// The template is a RNG template
			$data['NODETYPENAME'] = 'RngVisualTemplate';
		}

		return $data;
	}


	/**
	 * Set to upper case all the keys in $data array.
	 * @param  array $data Array with key to upper.
	 * @return array       The same array with all the keys in uppercase.
	 */
	protected function dataToUpper($data){

		$aux = array();
		foreach ($data as $idx => $item) {
			$aux[strtoupper($idx)] = $item;
		}
		return $aux;
	}
}
