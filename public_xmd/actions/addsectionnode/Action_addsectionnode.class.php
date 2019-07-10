<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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
use Ximdex\Models\NodeDefaultContents;
use Ximdex\MVC\ActionAbstract;
use Ximdex\Properties\InheritedPropertiesManager;
use Ximdex\IO\BaseIO;
use Ximdex\NodeTypes\XsltNode;

class Action_addsectionnode extends ActionAbstract
{
    /**
     * Main method: shows initial form
     */
    public function index()
    {
        $nodeID = $this->request->getParam('nodeid');
        $node = new Node($nodeID);
        $values = array(
            'name' => $node->GetNodeName(),
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->GetName()
        );
        $this->addCss('/actions/addsectionnode/resources/css/style.css');
        $this->addJs('/actions/addsectionnode/resources/js/init.js');
        $this->addJs('/actions/addsectionnode/resources/js/addSectionCtrl.js');
        $this->render($values, 'index', 'default-3.0.tpl');
    }

    public function getSectionInfo()
    {
        $nodeID = $this->request->getParam('nodeid');
        $sectionType = new SectionType();
        $sectionTypes = $sectionType->find(ALL);
        $ndc = new NodeDefaultContents();
        $sectionTypeOptions = [];
        foreach ($sectionTypes as $sectionTypeInfo) {
            if (empty($sectionTypeInfo['module']) || \Ximdex\Modules\Manager::isEnabled($sectionTypeInfo['module'])) {
                $subfolders = array();
                $subfoldersAll = $ndc->getDefaultChilds($sectionTypeInfo['idNodeType']);
                foreach ($subfoldersAll as $sub) {
                    $newFolder = array(
                        'NodeType' => $sub['NodeType'],
                        'Name' => $sub['Name'],
                        'description' => $this->_getDescription($sub['NodeType'])
                    );
                    $subfolders[] = $newFolder;
                }
                $sectionTypeOptions[] = array(
                    'value' => $sectionTypeInfo['idSectionType'],
                    'label' => $sectionTypeInfo['sectionType'],
                    'subfolders' => $subfolders
                );
            }
        }
        
        // Getting languages
        $languageOptions = $this->_getLanguages($nodeID);
        $values = array(
            'sectionTypeOptions' => $sectionTypeOptions,
            'languageOptions' => $languageOptions
        );
        $this->sendJSON($values);
    }

    public function addsectionnode()
    {
        $nodeid = $this->request->getParam('nodeid');
        $name = $this->request->getParam('name');
        $langidlst = $this->request->getParam('langidlst');
        if (! $langidlst) {
            $this->messages->add(_('At least one language is required'), MSG_TYPE_WARNING);
            $this->sendJSON(['messages' => $this->messages->messages]);
        }
        $namelst = $this->request->getParam('namelst');
        $folderlst = $this->request->getParam('folderlst');
        if (! $folderlst) {
            $this->messages->add(_('At least one section subfolder is required'), MSG_TYPE_WARNING);
            $this->sendJSON(['messages' => $this->messages->messages]);
        }
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
            'FORCENEW' => true,
            'SECTIONTYPE' => $nodetype
        );
        $baseio = new BaseIO();
        $id = $baseio->build($data);
        if ($id > 0) {
            $section = new Node($id);
            
            // Language block
            if (! $langidlst) {
                $langidlst = array();
            }
            $aliasLangArray = array();
            foreach ($langidlst as $key) {
                $aliasLangArray[$key] = $namelst[$key];
            }
            foreach ($aliasLangArray as $langID => $longName) {
                $section->SetAliasForLang($langID, $longName);
            }
            
            // Get the project node for the current section
            $project = new Node($section->getProject());
            
            // Reload the templates include files for this new project
            $xsltNode = new XsltNode($section);
            if ($xsltNode->reload_templates_include($project) === false) {
                $this->messages->mergeMessages($xsltNode->messages);
            }
            
            // Reload the document folders and template folders relations
            if (! $xsltNode->rel_include_templates_to_documents_folders($project)) {
                $this->messages->mergeMessages($xsltNode->messages);
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

    private function _getLanguages($nodeID)
    {
        $properties = InheritedPropertiesManager::getValues($nodeID, true);
        $propertiesLang = array();
        if (isset($properties['Language'])) {
            foreach ($properties['Language'] as $prop) {
                $newLang = array();
                $newLang['Id'] = $prop['Id'];
                $newLang['Name'] = _($prop['Name']);
                $propertiesLang[] = $newLang;
            }
        }
        return $propertiesLang;
    }

    private function _getDescription($nodetypeId)
    {
        $nt = new NodeType($nodetypeId);
        if (! $nt) {
            return '';
        }
        return $nt->GetDescription();
    }
}
