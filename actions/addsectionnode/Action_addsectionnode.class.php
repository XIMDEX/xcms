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
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Models\SectionType;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Services\NodeType as NodetypeService;

ModulesManager::file('/inc/model/NodeDefaultContents.class.php');
ModulesManager::file('/actions/manageproperties/inc/InheritedPropertiesManager.class.php');
ModulesManager::file('/inc/io/XlyreBaseIO.class.php', 'xlyre');

class Action_addsectionnode extends ActionAbstract {

    // Main method: shows initial form
    public function index() {
        $nodeID = $this->request->getParam("nodeid");
        $node   = new Node($nodeID);

        $values = array(
            'name' => $node->GetNodeName()
        );

        $this->loadResources();
        $this->render($values, "index", 'default-3.0.tpl');
    }

    public function getSectionInfo() {
        $this->sendJSON($this->loadValues());
    }

    public function addsectionnode() {
        $nodeid = $this->request->getParam('nodeid');
        $name = $this->request->getParam('name');
        $langidlst = $this->request->getParam('langidlst');
        $namelst = $this->request->getParam('namelst');
        $folderlst = $this->request->getParam('folderlst');
        $nodetype = $this->request->getParam('nodetype');

        $sectionType = new SectionType($nodetype);
        if ($sectionType->get('idSectionType') > 0) {
            $idNodeType = $sectionType->get('idNodeType');
        }

        $nodeTypeObj = new NodeType($idNodeType);
        $nodeTypeName = $nodeTypeObj->get('Name');
        $data = array(
            'NODETYPENAME' => $nodeTypeName,
            'NAME' => $name,
            'SUBFOLDERS' => $folderlst,
            'PARENTID' => $nodeid,
            'FORCENEW' => true
        );

        if ($nodetype == 3) {
            $id = $this->addcatalog($data);
        } else {
            $baseio = new baseIO();
            $id = $baseio->build($data);
        }

        if ($id > 0) {
            $section = new Node($id);

            // language block
            if (!$langidlst) {
                $langidlst = array();
            }
            $aliasLangArray = array();
            foreach ($langidlst as $key) {
                $aliasLangArray[$key] = $namelst[$key];
            }

            foreach ($aliasLangArray as $langID => $longName) {
                $section->SetAliasForLang($langID, $longName);
            }

            $this->messages->add(sprintf(_('%s has been successfully created'), $name), MSG_TYPE_NOTICE);
        } else {
            $this->messages->mergeMessages($baseio->messages);
            $this->messages->add(_('Operation could not be successfully completed'), MSG_TYPE_ERROR);
        }

        $values = array(
            'parentID' => $nodeid,
            'messages' => $this->messages->messages
        );

        $this->sendJSON($values);
    }

    private function _getLanguages($nodeID) {
        $properties = InheritedPropertiesManager::getValues($nodeID);
        $propertiesLang = array();
        foreach ($properties["Language"] as $prop) {
            $newLang = array();
            $newLang["IdLanguage"] = $prop["IdLanguage"];
            $newLang["Name"] = _($prop["Name"]);
            $propertiesLang[] = $newLang;
        }
        return $propertiesLang;
    }

    private function loadResources() {
        $this->addCss('/actions/addsectionnode/resources/css/style.css');
        $this->addJs('/actions/addsectionnode/resources/js/init.js');
        $this->addJs('/actions/addsectionnode/resources/js/addSectionCtrl.js');
    }

    private function loadValues() {
        $nodeID = $this->request->getParam("nodeid");
        $nodetype_sec = $this->request->getParam("type_sec");

        $nt = array();
        if (empty($nodetype_sec)) {
            $nodetype_sec = 1;
        }

        $sectionType = new SectionType();
        $sectionTypes = $sectionType->find(ALL);
        while (list(, $sectionTypeInfo) = each($sectionTypes)) {
            if (empty($sectionTypeInfo['module']) || ModulesManager::isEnabled($sectionTypeInfo['module'])) {
                $sectionTypeOptions[] = array(
                    'value' => $sectionTypeInfo['idSectionType'],
                    'label' => $sectionTypeInfo['sectionType'],
                    'subfolders' => $this->_getAvailableSubfolders($sectionTypeInfo['idNodeType'])
                );
                $nt[] = $sectionTypeInfo['idNodeType'];
            }
        }

        // Getting languages
        $languageOptions = $this->_getLanguages($nodeID);
        $values = array(
            'sectionTypeOptions' => $sectionTypeOptions,
            'languageOptions' => $languageOptions,
        );

        return $values;
    }

    private function _getAvailableSubfolders($nodetype_sec) {
        $ndc = new NodeDefaultContents();

        $subfolders = array();
        $subfoldersAll = $ndc->getDefaultChilds($nodetype_sec);
        foreach ($subfoldersAll as $sub) {
            $newFolder = array(
                'NodeType' => $sub['NodeType'],
                'Name' => $sub['Name'],
                'description' => $this->_getDescription($sub['NodeType'])
            );
            $subfolders[] = $newFolder;
        }

        return $subfolders;
    }

    private function _getDescription($nodetypeId) {
        $nt = new NodeType($nodetypeId);
        if (!$nt) {
            return "";
        }
        return $nt->GetDescription();
    }

    private function addcatalog($data) {
        $baseio = new XlyreBaseIO();
        $id = $baseio->build($data);
        if ($id > 0) {
            // Creating Licenses subfolder in links folder
            $catalognode = new Node($id);
            $projectnode = new Node($catalognode->getProject());
            $folder = $projectnode->getChildren(NodetypeService::LINK_MANAGER);
            $this->_createLicenseLinksFolder($folder[0]);
        }
        return $id;
    }

    private function _createLicenseLinksFolder($links_id) {
        $nodeaux = new Node();
        $linkfolder = $nodeaux->find('IdNode', "idnodetype = %s AND Name = 'Licenses'", array(NodetypeService::LINK_FOLDER), MONO);
        if (!$linkfolder) {
            $nodeType = new NodeType();
            $nodeType->SetByName('LinkFolder');
            $folder = new Node();
            $idFolder = $folder->CreateNode('Licenses', $links_id, $nodeType->GetID(), null);
            $this->_createLicenseLinks("ODbL", "http://opendatacommons.org/licenses/odbl/", "Open Data Commons Open Database License (ODbL)", $idFolder);
        }
    }

    private function _createLicenseLinks($link_name, $link_url, $link_description, $idFolder) {
        $data = array(
            'NODETYPENAME' => 'LINK',
            'NAME' => $link_name,
            'PARENTID' => $idFolder,
            'IDSTATE' => 0,
            'CHILDRENS' => array(
                array('URL' => $link_url),
                array('DESCRIPTION' => $link_description)
            )
        );
        $bio = new baseIO();
        $bio->build($data);
    }

}
