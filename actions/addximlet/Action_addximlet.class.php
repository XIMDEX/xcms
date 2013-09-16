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


/**
  @brief This class implements the action of add a ximlet in a Node
 *
 * Defines three methods: createrel(), deleterel(), readtree()
 *
 * @ingroup ximNEWS
 */

class Action_addximlet extends ActionAbstract {

    // Main method: shows initial form
    function index() {
        $idNode = $this->request->getParam('nodeid');
        $node = new Node($idNode);

        $depsMngr = new DepsManager();
        $ximlets = $depsMngr->getBySource(DepsManager::SECTION_XIMLET, $idNode);
        $relXimlets = array();

        if (is_array($ximlets) && count($ximlets) > 0) {
            foreach ($ximlets as $idXimlet) {
                $ximletNode = new Node($idXimlet);
                if (!($ximletNode->get('IdNode') > 0)) {
                    XMD_Log::warning(_("Ximlet with identity ") . $idXimlet . _("has been deleted"));
                    continue;
                }
                $relXimlets[$idXimlet]['path'] = str_replace('/', ' / ', $ximletNode->GetPath());
                $relXimlets[$idXimlet]['idximlet'] = $idXimlet;
            }
        }

        $query = App::get('QueryManager');
        $actionDelete = $query->getPage() . $query->buildWith(array('method' => 'deleterel'));
        $actionCreate = $query->getPage() . $query->buildWith(array('method' => 'createrel'));

        $this->addJs('/actions/addximlet/javascript/addximlet.js');

        $values = array('ximlet_list' => $relXimlets,
            'id_node' => $idNode,
            'action_delete' => $actionDelete,
            'action_create' => $actionCreate,
            'name' => $node->get('Name')
        );

        $this->render($values, null, 'default-3.0.tpl');
    }

    function createrel() {
        $idNode = $this->request->getParam('id_node');
        $idXimletContainer = $this->request->getParam('targetfield');
        $recursive = $this->request->getParam('recursive');
        $sections[] = $idNode;

	if(!$idXimletContainer) {
            $this->messages->add(_('No ximlet has been found to be associated.'), MSG_TYPE_NOTICE);
            $values = array(
                       'messages' => $this->messages->messages,
                       'goback' => true
            );
            $this->render($values);
            return;
	}
	
	$this->createRelXimletSection($idNode, $idXimletContainer, $recursive);

        $values = array(
            'id_node' => $idNode,
            'nodeid' => $idNode,
            'goback' => true,
            'messages' => $this->messages->messages
        );

        $this->render($values);
    }

    public function createRelXimletSection($idNode, $idXimletContainer, $recursive){

        $sections[] = $idNode;

        if ($recursive == 1) {
            $node = new Node($idNode);
            $children = $node->getChildren($node->get('IdNodeType'));

            if (sizeof($children) > 0) {
                $sections = array_merge($sections, $children);
            }
        }

        $ximletContainer = new Node($idXimletContainer);
        $ximletName = $ximletContainer->get('Name');
        $ximlets = $ximletContainer->GetChildren();

        foreach ($sections as $sectionId) {

            $deps = new DepsManager();
            $result = $deps->set(DepsManager::SECTION_XIMLET, $sectionId, $idXimletContainer);
            $section = new Node($sectionId);
            $sectionName = $section->get('Name');

            if ($result) {
        $this->messages->add(_("Section ") . $sectionName ." ". _("has been associated to ximlet ").$ximletName,MSG_TYPE_NOTICE);
            } else {
        $this->messages->add(_("Section ") . $sectionName ." ". _("has not been associated to ximlet ") . $ximletName,MSG_TYPE_NOTICE);
            }

            // Inserts ximlets dependencies for all section's strdDoc

            $sectionNode = new Node($sectionId);
            $strDocs = $sectionNode->class->getXmlDocuments();

            if (sizeof($strDocs) > 0) {

                foreach ($strDocs as $docId) {

                    $document = new Node($docId);
                    $docName = $document->get('Name');
                    $docLanguage = $document->class->getLanguage();

                    foreach ($ximlets as $ximletId) {

                        $ximlet = new StructuredDocument($ximletId);
                        $ximletNode = new Node($ximletId);
                        $ximletName = $ximletNode->get('Name');

                        if ($ximlet->get('IdLanguage') == $docLanguage) {
                            $deps = new DepsManager();

                            $result = $deps->set(DepsManager::STRDOC_XIMLET, $docId, $ximletId);
                            if ($result) {
                                $this->messages->add(_("Doc " ) . $docName . _("has been associated to ximlet ") . $ximletName,MSG_TYPE_NOTICE);
                            } else {
                                $this->messages->add(_("Doc ") . $docName . _("has not been associated to ximlet ") . $ximletName,MSG_TYPE_NOTICE);
                            }
                        }
                    }
                }
            }
        }

    }


    function deleterel() {

        $ximletContainers = $this->request->getParam('idximlet');
        $sectionId = $this->request->getParam('id_node');
	$section = new Node($sectionId);
	$sectionName = $section->get('Name');


	if(!$ximletContainers) {
            $this->messages->add(_('No ximlet has been found to be disassociated.'), MSG_TYPE_NOTICE);
            $values = array(
                    'messages' => $this->messages->messages,
                    'goback' => true
            );
            $this->render($values);
            return;
	}

        // Deletes section-ximlet dependency
        foreach ($ximletContainers as $ximletContainerId) {

	    $ximletNode = new Node($ximletContainerId);
	    $ximletName = $ximletNode->get('Name');

            $depsMngr = new DepsManager();

            $result = $depsMngr->delete(DepsManager::SECTION_XIMLET, $sectionId, $ximletContainerId);
            if ($result) {
		$this->messages->add(_("Section ") . $sectionName ." ". _("has been disassociated with ximlet ") . $ximletName,MSG_TYPE_NOTICE);
            } else {
		$this->messages->add(_("Section ") . $sectionName ." ". _("has not been disassociated with ximlet ") . $ximletName,MSG_TYPE_NOTICE);
            }


            $sectionNode = new Node($sectionId);
            $strDocs = $sectionNode->class->getXmlDocuments();

            $ximletContainer = new Node($ximletContainerId);
            $ximlets = $ximletContainer->GetChildren();


            if (sizeof($strDocs) > 0) {

                foreach ($strDocs as $docId) {

                    $document = new Node($docId);
		    $docName = $document->get('Name');

                    $docLanguage = $document->class->getLanguage();

                    foreach ($ximlets as $ximletId) {

                        $ximlet = new StructuredDocument($ximletId);
			$ximletNode = new Node($ximletContainerId);
			$ximletName = $ximletNode->get('Name');


                        if ($ximlet->get('IdLanguage') == $docLanguage) {

                            $result = $depsMngr->delete(DepsManager::STRDOC_XIMLET, $docId, $ximletId);
                            if ($result) {
				$this->messages->add(_("Doc ") . $docName . _("has been disassociated") . $ximletName,MSG_TYPE_NOTICE);
                            } else {
				$this->messages->add(_("Doc ") . $docName . _("has not been disassociated with ximlet ") . $ximletName,MSG_TYPE_NOTICE);
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

        $this->render($values);
    }

    /**
     * Filtering the tree data, we need just to show the allowed project.
     */
    public function readtree() {

        $ret = GenericDatasource::read($this->request);
        $node = new Node($this->request->getParam('nodeforximlet'));

        // removing every node in collection which aren't in same project as nodeforximlet
        $aux = array();
        foreach ($ret["collection"] as $childNode) {

            $node_object = new Node($childNode["nodeid"]);
            $id_parent_node = $node_object->GetParent();

            if (!($id_parent_node == "10000" && !$node->IsOnNode($node_object->GetID()))) {
				$aux[] = $childNode;
            }
        }

        $ret["collection"] = $aux;
        $this->sendJSON($ret);
    }

}

?>
