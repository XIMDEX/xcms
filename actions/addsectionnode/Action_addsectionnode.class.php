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
use Ximdex\Services\NodeType as NodetypeService;

ModulesManager::file('/inc/model/NodeDefaultContents.class.php');
ModulesManager::file('/inc/model/SectionType.class.php');
ModulesManager::file('/inc/model/language.php');
ModulesManager::file('/actions/manageproperties/inc/InheritedPropertiesManager.class.php');
ModulesManager::file('/inc/nodetypes/xlyreopendatasection.php', 'xlyre');
ModulesManager::file('/inc/nodetypes/xlyreopendataset.php', 'xlyre');
ModulesManager::file('/inc/io/XlyreBaseIO.class.php', 'xlyre');

class Action_addsectionnode extends ActionAbstract {

    // Main method: shows initial form
    function index() {
        $this->loadResources();
        $this->render($this->loadValues(), null, 'default-3.0.tpl');
    }

    function addsectionnode() {
        $nodeID = $this->request->getParam('nodeid');
        $name = $this->request->getParam('name');
        $nodeType = $this->request->getParam('nodetype');
        $langidlst = $this->request->getParam('langidlst');
        $namelst = $this->request->getParam('namelst');
        $type = $this->request->getParam('nodetype');
        $sectionOTF = $this->request->getParam('sectionOTF');
        $selectedTheme = $this->request->getParam('selectedTheme');


        $aliasLangArray = array();
        if ($langidlst) {
            foreach ($langidlst as $key) {
                $aliasLangArray[$key] = $namelst[$key];
            }
        }

        $sectionType = new SectionType($type);
        if ($sectionType->get('idSectionType') > 0) {
            $idNodeType = $sectionType->get('idNodeType');
        } else {
            XMD_Log::warning(_('Error obtaining section type'));
            $idNodeType = 5015;
        }
        if ($type == 3) {
            $id = $this->addcatalog();
        } else {
            $nodeType = new NodeType($idNodeType);
            $nodeTypeName = $nodeType->get('Name');

            $data = array(
                'NODETYPENAME' => $nodeTypeName,
                'NAME' => $name,
                'PARENTID' => $nodeID,
                'FORCENEW' => true
            );

            $baseio = new baseIO();
            $id = $baseio->build($data);

            $themeMessages = array();

            if ($id > 0) {

                $section = new Node($id);

                if ($aliasLangArray) {
                    foreach ($aliasLangArray as $langID => $longName) {
                        $section->SetAliasForLang($langID, $longName);
                    }
                }

                $themeMessages = $this->_setSectionTheme($section, $selectedTheme);
                $this->reloadNode($nodeID);
            }

            if (!($id > 0)) {
                $this->messages->mergeMessages($baseio->messages);
                $this->messages->add(_('Operation could not be successfully completed'), MSG_TYPE_ERROR);
            } else {
                $this->messages->add(sprintf(_('%s has been successfully created'), $name), MSG_TYPE_NOTICE);
                //set the OTF property
                if ($sectionOTF) {
                    $node = new Node($id);
                    $node->setProperty('otf', "true");
                }
            }
        }
        $values = array(
            'action_with_no_return' => $id > 0,
            'messages' => $this->messages->messages
        );

        $this->render($values, NULL, 'messages.tpl');
    }

    private function _setSectionTheme(&$section, $theme) {

        $messages = array();

        if ($theme == '0') {
            return $messages;
        }

        $rngPath = sprintf('%s' . ModulesManager::path('ximTHEMES') . '/themes/%s/rng', XIMDEX_ROOT_PATH, $theme);
        $ptdPath = sprintf('%s' . ModulesManager::path('ximTHEMES') . 's/%s/ptd', XIMDEX_ROOT_PATH, $theme);
        $baseio = new baseIO();

        $arrRNG = FsUtils::readFolder($rngPath, false);
        if (!is_array($arrRNG))
            $arrRNG = array();
        $project = new Node($section->getProject());
        $rngFolder = new Node($project->GetChildByName(\App::GetValue('VisualTemplateDir')));

        $arrIdRNG = array();

        foreach ($arrRNG as $rng) {
            if (preg_match('/.ini/', $rng) > 0) {
                continue;
            }

            $idRNG = $rngFolder->GetChildByName($rng);
            if ($idRNG === false) {

                $data = array(
                    'NODETYPENAME' => 'RNGVISUALTEMPLATE',
                    'NAME' => $rng,
                    'PARENTID' => $rngFolder->get('IdNode'),
                    'CHILDRENS' => array(
                        array('NODETYPENAME' => 'PATH', 'SRC' => sprintf('%s/%s', $rngPath, $rng))
                    )
                );

                $idRNG = $baseio->build($data);
            }

            if (!($idRNG > 0)) {
//				$messages[] = sprintf(_('RNG %s could not be successfully inserted'), $rng);
            } else {
                $arrIdRNG[] = $idRNG;
            }
        }

        $arrPTD = FsUtils::readFolder($ptdPath, false);
        if (!is_array($arrPTD))
            $arrPTD = array();
        $ptdFolder = new Node($section->GetChildByName(\App::GetValue('GeneratorTemplateDir')));

        foreach ($arrPTD as $ptd) {

            $idPTD = $ptdFolder->GetChildByName($ptd);
            if ($idPTD === false) {

                $data = array(
                    'NODETYPENAME' => 'TEMPLATE',
                    'NAME' => $ptd,
                    'PARENTID' => $ptdFolder->get('IdNode'),
                    'CHILDRENS' => array(
                        array('NODETYPENAME' => 'PATH', 'SRC' => sprintf('%s/%s', $ptdPath, $ptd))
                    )
                );

                $idPTD = $baseio->build($data);
            }

            if (!($idPTD > 0)) {
//				$messages[] = sprintf(_('PTD %s could not be successfully inserted'), $ptd);
            }
        }

        $section->setProperty('theme', array($theme));
        $section->setProperty('theme_visualtemplates', $arrIdRNG);

//		return $messages;
        return $baseio->messages->messages;
    }

    private function _getLanguages($nodeID) {
        $properties = InheritedPropertiesManager::getValues($nodeID);

        return $properties["Language"];
    }

    private function _getAvailableThemes() {

        $themes = FsUtils::readFolder(XIMDEX_ROOT_PATH . ModulesManager::path('ximTHEMES') . '/themes', false);

        if ($themes === null)
            $themes = array();
        $values = array_merge(array(_('--- Ninguno ---')), $themes);
        $keys = array_merge(array('0'), $themes);
        $themes = array_combine($keys, $values);

        return $themes;
    }

    protected function loadResources() {
        $this->addCss('/actions/addsectionnode/resources/css/style.css');
    }

    protected function loadValues() {
        $nodeID = $this->request->getParam("nodeid");
        $action = $this->request->getParam("action");
        $type_sec = $this->request->getParam("type_sec");

        $nt = 5015;
        if (empty($type_sec)) {
            $type_sec = 1;
        }

        $sectionType = new SectionType();
        $sectionTypes = $sectionType->find(ALL);
        reset($sectionTypes);
        while (list(, $sectionTypeInfo) = each($sectionTypes)) {
            if (empty($sectionTypeInfo['module']) || ModulesManager::isEnabled($sectionTypeInfo['module'])) {
                $sectionTypeOptions[] = array('id' => $sectionTypeInfo['idSectionType'], 'name' => $sectionTypeInfo['sectionType']);
                if ($type_sec == $sectionTypeInfo['idSectionType']) {
                    $nt = $sectionTypeInfo['idNodeType'];
                }
            }
        }
        $sectionTypeCount = count($sectionTypeOptions);

        // Getting languages
        $languageOptions = $this->_getLanguages($nodeID);
        $languageCount = sizeof($languageOptions);

        $subfolders = $this->_getAvailableSubfolders($nt);

        $values = array('nodeID' => $nodeID,
            'nodeURL' => \App::getValue('UrlRoot') . '/xmd/loadaction.php?action=' . $action . '&nodeid=' . $nodeID,
            'sectionTypeOptions' => $sectionTypeOptions,
            'sectionTypeCount' => $sectionTypeCount,
            'selectedsectionType' => $type_sec,
            'languageOptions' => $languageOptions,
            'languageCount' => $languageCount,
            'subfolders' => $subfolders,
            'go_method' => 'addsectionnode',
        );

        return $values;
    }

    private function _getAvailableSubfolders($nodetype_sec) {
        $res = array();
        $ndc = new NodeDefaultContents();
        $subfolders = $ndc->getDefaultChilds($nodetype_sec);
        if (count($subfolders) > 0) {
            foreach ($subfolders as $subfolder) {
                $nt = $subfolder["NodeType"];
                $res[$nt][0] = $subfolder["Name"];
                $res[$nt][1] = $this->_getDescription($nt);
            }
        }
        asort($res);
        return $res;
    }

    protected function _getDescription($nodetype) {
        switch ($nodetype) {
            case "5018": return "This is the main repository for all your XML contents. It's the most important folder in a section.";
            case "5016": return "Inside this folder you can store all the image files you need in several formats (gif, png,jpg, tiff,...)";
            case "5020": return "Into this folder you could store several HTML snippets that you can add directly into your XML documents";
            case "5022": return "Use this folder if you need to store JavaScript scripts or text files like PDFs, MS Office documents, etc.";
            case "5026": return "Create here your own XSL Templates to redefine some particular appareance in your XML documents.";
            case "5054": return "Create XML snippets that you can import into your XML documents. Typical uses are menus, shared headers, shared footers between all your XML documents.";
            case "5301": return "ximNEWS module manages and organizes all the existing news into bulletins. This is a required folder.";
            case "5304": return "Into this folder you could create XML based news in several languages. This is a required folder.";
            case "5306": return "All the images used in your defined news are stored here.";
            case "5083": return "Create metadata structured documents to describe other resources stored in Ximdex CMS.";
            default: "...";
        }
    }

    function addcatalog() {
        error_log("DEBUG bb");
        $nodeID = $this->request->getParam('nodeid');
        $name = $this->request->getParam('name');
        $langidlst = $this->request->getParam('langidlst');

        $catalog = new Node();

        $data = array(
            'NODETYPENAME' => 'OpenDataSection',
            'NAME' => $name,
//                    'SUBFOLDERS' => $lst,
            'PARENTID' => $nodeID,
            'FORCENEW' => true
        );

        $baseio = new XlyreBaseIO();
        $id = $baseio->build($data);
        error_log("DEBUG aaaa");
        if ($id > 0) {
            $nt = new NodeType(XlyreOpenDataSet::IDNODETYPE);
            foreach ($datasets as $datasetName) {

                // Creating Licenses subfolder in links folder
                $catalognode = new Node($id);
                $projectnode = new Node($catalognode->getProject());
                $folder = $projectnode->getChildren(NodetypeService::LINK_MANAGER);
                $this->_createLicenseLinksFolder($folder[0]);


                $this->reloadNode($nodeID);
            }
            return id;
        }
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
        $result = $bio->build($data);
    }

}
