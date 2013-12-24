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

ModulesManager::file('/inc/dependencies/DepsManager.class.php');
ModulesManager::file('/actions/browser3/inc/GenericDatasource.class.php');
ModulesManager::file('/inc/model/RelSectionXimlet.class.php');

/**
 * @brief This class implements the action of add a ximlet in a Node
 * Defines three methods: createrel(), deleterel()
 * @ingroup ximNEWS
 */

class Action_addximlet extends ActionAbstract {

    	// Main method: shows initial form
    	function index() {
        	$idNode = $this->request->getParam('nodeid');
        	$node = new Node($idNode);

        	$depsMngr = new DepsManager();
        	$ximlets = $depsMngr->getBySource(DepsManager::SECTION_XIMLET, $idNode);

		$rsx = new RelSectionXimlet();
		$all_ximlets=$rsx->getAllXimlets();

		if(count($ximlets)>0){
			$linkable_ximlets=array_diff($all_ximlets,$ximlets);
		}
		else{
			$linkable_ximlets=$all_ximlets;
		}

		$linked_ximlets=$this->getXimletInfo($ximlets);
		$linkable_ximlets=$this->getXimletInfo($linkable_ximlets);
	
        	$query = App::get('QueryManager');
        	$actionDelete = $query->getPage() . $query->buildWith(array('method' => 'deleterel'));
        	$actionCreate = $query->getPage() . $query->buildWith(array('method' => 'createrel'));

        	$values = array('linked_ximlets' => $linked_ximlets,
            		'id_node' => $idNode,
	    		'linkable_ximlets' => $linkable_ximlets,
            		'action_delete' => $actionDelete,
            		'action_create' => $actionCreate,
            		'name' => $node->get('Name')
        	);

        	$this->render($values, 'index', 'default-3.0.tpl');
    	}

	private function getXimletInfo($a){
		$result = array();

        	if (is_array($a) && count($a) > 0) {
            		foreach ($a as $idXimlet) {
                		$ximletNode = new Node($idXimlet);
                		if (!($ximletNode->get('IdNode') > 0)) {
                    			XMD_Log::warning(_("Ximlet with id ") . $idXimlet . _(" has been deleted."));
                    			continue;                	
				}	   
                		
				$result[$idXimlet]['path'] = str_replace('/Ximdex/Projects/', "", $ximletNode->getPath());
				$result[$idXimlet]['path'] = str_replace('/', ' / ', $result[$idXimlet]['path']);
                		$result[$idXimlet]['idximlet'] = $idXimlet;
            		}   
        	}   
		return $result;
	}

    	function createrel() {
        	$idNode = $this->request->getParam('id_node');
        	$idXimletContainers = $this->request->getParam('idximlet');
        	$recursive = $this->request->getParam('recursive');
        	$sections[] = $idNode;

		if(!$idXimletContainers || !count($idXimletContainers)) {
            		$this->messages->add(_('No ximlet has been found to be associated.'), MSG_TYPE_NOTICE);
            		$values = array(
                       		'messages' => $this->messages->messages,
                       		'goback' => true
            		);
            		$this->render($values);
            		return;
		}

        	if($recursive == 'on') {
            		$node = new Node($idNode);
            		$children = $node->getChildren($node->get('IdNodeType'));

            		if (sizeof($children) > 0) {
                		$sections = array_merge($sections, $children);
            		}
        	}
	
		foreach ($idXimletContainers as $idXimletContainer){
	        	$ximletContainer = new Node($idXimletContainer);
			$ximletName = $ximletContainer->get('Name');
	        	$ximlets = $ximletContainer->GetChildren();

        		foreach ($sections as $sectionId) {
	            		$deps = new DepsManager();
        	    		$result = $deps->set(DepsManager::SECTION_XIMLET, $sectionId, $idXimletContainer);
		    		$section = new Node($sectionId);
		    		$sectionName = $section->get('Name');

	            		if ($result) {
					$this->messages->add(_("Section ") . $sectionName . _(" has been succesfully associated to the ximlet ").$ximletName,MSG_TYPE_NOTICE);
	            		} else {
					$this->messages->add(_("Section ") . $sectionName . _(" has not been associated to the ximlet ") . $ximletName,MSG_TYPE_NOTICE);
            			}	

	            		// Inserts ximlets dependencies for all section's xml documents
        	    		$sectionNode = new Node($sectionId);
	            		$strDocs = $sectionNode->class->getXmlDocuments();

        	    		if(sizeof($strDocs) > 0) {
                			foreach ($strDocs as $docId) {
	                    			$document = new Node($docId);
			    			$docName = $document->get('Name');
                	    			$docLanguage = $document->class->getLanguage();

	                    			foreach($ximlets as $ximletId) {
        	                			$ximlet = new StructuredDocument($ximletId);
							$ximletNode = new Node($ximletId);
							$ximletName = $ximletNode->get('Name');

	                        			if($ximlet->get('IdLanguage') == $docLanguage) {
        	                    				$deps = new DepsManager();
                	            				$result = $deps->set(DepsManager::STRDOC_XIMLET, $docId, $ximletId);
                        	    				if($result) {
									$this->messages->add(_("Doc " ) . $docName . _(" has been associated to the ximlet ") . $ximletName,MSG_TYPE_NOTICE);
        	                    				}else{
									$this->messages->add(_("Doc ") . $docName . _(" has not been associated to the ximlet ") . $ximletName,MSG_TYPE_NOTICE);
                        	    				}		
	                        			}
        	            			}
               				}
	            		}
        		}

		}
        	$this->index();
    	}

    	function deleterel() {
        	$ximletContainers = $this->request->getParam('idximlet');
        	$sectionId = $this->request->getParam('id_node');
		$section = new Node($sectionId);
		$sectionName = $section->get('Name');
		$sections=array();
		$sections[]=$sectionId;
		$recursive = $this->request->getParam('recursive');

		if(!$ximletContainers) {
            		$this->messages->add(_('No ximlet has been found to be disassociated.'), MSG_TYPE_NOTICE);
            		$values = array(
                    		'messages' => $this->messages->messages,
                    		'goback' => true
            		);
            		$this->render($values);
            		return;
		}
		
		if($recursive == 'on') {
                        $node = new Node($sectionId);
                        $children = $node->getChildren($node->get('IdNodeType'));

                        if (sizeof($children) > 0) {
                                $sections = array_merge($sections, $children);
                        }   
                }
error_log(print_r($sections,true));
        	// Deletes section-ximlet dependency
        	foreach($ximletContainers as $ximletContainerId) {
	    		$ximletNode = new Node($ximletContainerId);
	    		$ximletName = $ximletNode->get('Name');

			foreach ($sections as $sectionId) {
	            		$depsMngr = new DepsManager();
        	    		$result = $depsMngr->delete(DepsManager::SECTION_XIMLET, $sectionId, $ximletContainerId);
           		 	if($result) {
					$this->messages->add(_("Section ") . $sectionName . _(" has been disassociated with the ximlet ") . $ximletName,MSG_TYPE_NOTICE);
            			}else {
					$this->messages->add(_("Section ") . $sectionName . _(" has not been disassociated with the ximlet ") . $ximletName,MSG_TYPE_NOTICE);
            			}

            			$sectionNode = new Node($sectionId);
            			$strDocs = $sectionNode->class->getXmlDocuments();

            			$ximletContainer = new Node($ximletContainerId);
            			$ximlets = $ximletContainer->GetChildren();

            			if (sizeof($strDocs) > 0) {
                			foreach($strDocs as $docId) {
                    				$document = new Node($docId);
		    				$docName = $document->get('Name');
                    				$docLanguage = $document->class->getLanguage();
                    		
						foreach($ximlets as $ximletId) {
                        				$ximlet = new StructuredDocument($ximletId);
							$ximletNode = new Node($ximletContainerId);
							$ximletName = $ximletNode->get('Name');
                        				if($ximlet->get('IdLanguage') == $docLanguage) {
                            					$result = $depsMngr->delete(DepsManager::STRDOC_XIMLET, $docId, $ximletId);
                            					if ($result) {
									$this->messages->add(_("Doc ") . $docName . _(" has been disassociated with the ximlet ") . $ximletName,MSG_TYPE_NOTICE);
                            					}else {
									$this->messages->add(_("Doc ") . $docName . _(" has not been disassociated with the ximlet ") . $ximletName,MSG_TYPE_NOTICE);
                            					}
                        				}
                    				}
                			}
            			}
			}
        	}

        	$values = array(
            		'goback' => true,
            		'nodeid' => $sectionId,
            		'messages' => $this->messages->messages
        	);

        	$this->index();
    	}

}
?>
