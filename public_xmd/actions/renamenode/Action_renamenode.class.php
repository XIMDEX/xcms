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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

use Ximdex\Models\Language;
use Ximdex\Models\Node;
use Ximdex\MVC\ActionAbstract;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\NodeTypes\NodeTypeGroupConstants;
use Ximdex\Runtime\App;
use Ximdex\NodeTypes\XsltNode;

class Action_renamenode extends ActionAbstract
{
    /**
     * Main method: shows initial form
     */
    public function index()
    {
        $idNode = (int) $this->request->getParam('nodeid');
        $node = new Node($idNode);
        $isProject = in_array( $node->GetNodeType(),NodeTypeGroupConstants::NODE_PROJECTS );
        $isSection = $node->nodeType->get('IsSection') && $node->getNodeType() != NodeTypeConstants::SERVER;
        $allLanguages = null;
        if ($isSection) {
            $language = new Language();
            $allLanguages = $language->find('IdLanguage, Name');
            if (! empty($allLanguages)) {
                foreach ($allLanguages as $key => $languageInfo) {
                    $allLanguages[$key]['alias'] = $node->getAliasForLang($languageInfo['IdLanguage']);
                }
            }
        }
        $schemaType = $node->getProperty('SchemaType');
        if (is_array($schemaType) && count($schemaType) == 1) {
            $schemaType = $schemaType[0];
        }
        $checkUrl = App::getUrl('/?actionid=' . $this->request->getParam('actionid') . '&nodeid=' . $this->request->getParam('nodeid') 
            . '&method=checkNodeDependencies');
        $this->addJs('/actions/renamenode/resources/js/renamenode.js');
        $values = array('name' => $node->get('Name'),
            'is_section' => $isSection,
            'is_project' => $isProject,
            'all_languages' => $allLanguages,
            'schema_type' => $schemaType,
            'go_method' => 'update',
            'check_url' => $checkUrl,
            'id_node' => $idNode,
            'id_nodetype' => $node->nodeType->get('IdNodeType'),
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->getName(),
            'name' => $node->getNodeName()
        );
        $this->render($values, null, 'default-3.0.tpl');
    }

    public function update()
    {
        $idNode = (int) $this->request->getParam('id_node');
        $name = $this->request->getParam('name');
        $languages = (array) $this->request->getParam('language');
        $node = new Node($idNode);
        if (! $node->get('IdNode') > 0) {
            $this->messages->add(_('Node could not be successfully loaded'), MSG_TYPE_ERROR);
            $result = false;
        } else {
            $result = $node->renameNode($name);
            if ($result) {
                $node->deleteProperty('SchemaType');
                $schemaType = $this->request->getParam('schema_type');
                if (! empty($schemaType) && $schemaType != 'generic_schema') {
                    $node->setProperty('SchemaType', $schemaType);
                }
                $result = $node->update();
                if ($result !== false) {
                    $this->messages->add(_('Node name has been successfully updated'), MSG_TYPE_NOTICE);
                } else {
                    $this->messages->add(_('Node name could not be updated'), MSG_TYPE_ERROR);
                }
                $isSection = $node->nodeType->get('IsSection') && $node->getNodeType() != NodeTypeConstants::SERVER;
                if ($isSection) {
                    foreach ($languages as $idLanguage => $alias) {
                        if ($node->setAliasForLang($idLanguage, $alias)) {
                            $language = new Language($idLanguage);
                            $this->messages->add(sprintf(_('Alias for language %s has been successfully updated'), $language->get('Name'))
                                , MSG_TYPE_NOTICE);
                        }
                    }
                }
                
                // Update all the references in the templates includes of the old name of this node to the new one
                if ( in_array( $node->GetNodeType(),NodeTypeGroupConstants::NODE_PROJECTS ) or $node->getNodeType() == NodeTypeConstants::SERVER
                        or $node->getNodeType() == NodeTypeConstants::SECTION) {
                    $xsltNode = new XsltNode($node);
                    if (! $xsltNode->reload_templates_include(new Node($node->GetProject()))) {
                        $this->messages->mergeMessages($xsltNode->messages);
                    }
                }
            } else {
                $this->messages->mergeMessages($node->messages);
                if ($node->getError()) {
                    $this->messages->add($node->getError(), MSG_TYPE_ERROR);
                }
            }
        }
        $values = array('messages' => $this->messages->messages, 'parentID' => $node->get('IdParent'));
        $this->sendJSON($values);
    }

    public function checkNodeDependencies()
    {
        $this->render(array('messages' => $this->messages->messages), null, 'messages.tpl');
    }
}
