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

use Ximdex\Logger;
use Ximdex\IO\BaseIO;
use Ximdex\Models\Language;
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Models\StructuredDocument;
use Ximdex\MVC\ActionAbstract;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\IO\BaseIOInferer;

class Action_createxmlcontainer extends ActionAbstract
{
    /**
     * Main method: shows initial form
     */
    public function index()
    {
        $idNode = $this->request->getParam('nodeid');
        $type = $this->request->getParam('type');
        $node = new Node($idNode);
        if (empty($node->get('IdNode'))) {
            die(_('Error with parameters'));
        }
        $nt = $node->GetNodeType();
        $nodeType = new NodeType($nt);
        if (! $nodeType->GetID()) {
            Logger::error('Cannot load the node type with ID: ' . $nt);
            die();
        }

        // If node type is HTML then obtain JSON schemas, otherwise the schemas will be the RNG ones
        if ($type == 'HTML') {
            $schemes = $node->getLayoutSchemas();
        } else if ($type == 'JSON') {
            $schemes = $node->getJsonSchemas();
        } else {
            // Gets default schema for XML documents through propInheritance
            $schemes = null;
            $section = $node->getSection();
            if ($section) {
                $section = new Node($section);
                $hasTheme = (bool) count((array) $section->getProperty('theme'));
                if ($hasTheme) {
                    $schemes = $section->getProperty('theme_visualtemplates');
                }
            }
            if ($schemes === null) {
                $schemes = $node->getSchemas();
            }
        }
        $schemaArray = array();
        if (! is_null($schemes)) {
            foreach ($schemes as $idSchema) {
                $np = new \Ximdex\Models\NodeProperty();
                $res = $np->find('IdNodeProperty', 'IdNode = %s AND Property = %s AND Value = %s', array(
                    $idSchema,
                    'SchemaType',
                    'metadata_schema'
                ));
                if (! $res) {
                    $sch = new Node($idSchema);
                    $schemaArray[] = array(
                        'idSchema' => $idSchema,
                        'Name' => $sch->get('Name')
                    );
                }
            }
        }

        // Getting languages
        $language = new Language();
        $languages = $language->getLanguagesForNode($idNode);

        // If no templates, show to user a new template with info
        if (isset($_REQUEST['reload_tree']) && $_REQUEST['reload_tree']) {
            $reloadTree = true;
        } else {
            $reloadTree = false;
        }
        $values = array(
            'idNode' => $idNode,
            'nodeName' => htmlentities($node->get('Name')),
            'schemes' => $schemaArray,
            'languages' => $languages,
            'go_method' => 'createxmlcontainer',
            'reload_tree' => $reloadTree,
            'nodeTypeName' => $nodeType->GetDescription(),
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->GetName(),
            'type' => $type
        );
        $this->render($values, null, 'default-3.0.tpl');
    }

    /**
     * Method called from View
     * Create a new HTML5 container.
     * Get the params and check them
     */
    public function createxmlcontainer()
    {
        $idNode = $this->request->getParam('nodeid');
        $aliases = $this->request->getParam('aliases');
        $name = $this->request->getParam('name');
        $idSchema = $this->request->getParam('id_schema');
        $languages = $this->request->getParam('languages');
        $type = $this->request->getParam('type');
        $master = $this->request->getParam('master');
        if (! $languages) {
            $this->messages->add(_('You must select alt least one language'), MSG_TYPE_WARNING);
            $values = array(
                'aliases' => $aliases,
                'name' => $name,
                'idSchema' => $idSchema,
                'messages' => $this->messages->messages
            );
            $this->sendJSON($values);
        }
        $node = new Node($idNode);
        $idNode = $node->get('IdNode');
        if (! ($idNode > 0)) {
            $this->messages->add(_('An error ocurred estimating parent node,') 
                . _(' operation will be aborted, contact with your administrator'), MSG_TYPE_ERROR);
            $values = array(
                'name' => 'Desconocido',
                'messages' => $this->messages->messages
            );
            $this->sendJSON($values);
        }
        if ( $type == 'HTML' ) {
            $nodeTypeID = NodeTypeConstants::HTML_CONTAINER;
        } else if ( $type == 'JSON' ) {
            $nodeTypeID = NodeTypeConstants::JSON_CONTAINER;
        } else {
            $nodeTypeID = null;
        }
        $idContainer = $this->buildXMLContainer($idNode, $aliases, $name, $idSchema, $languages, $master, $nodeTypeID);
        if (! ($idContainer > 0)) {
            $this->messages->add(_('An error ocurred creating the container node'), MSG_TYPE_ERROR);
            $values = array(
                'idNode' => $idNode,
                'nodeName' => $name,
                'messages' => $this->messages->messages
            );
            $this->sendJSON($values);
        } else {
            $this->messages->add(sprintf(_('Container %s has been successfully created'), $name), MSG_TYPE_NOTICE);
        }
        if ($master and is_array($languages)) {
            $baseIoInferer = new BaseIOInferer();
            $inferedNodeType = $baseIoInferer->infereType('FILE', $idContainer);
            $nodeType = new NodeType();
            $nodeType->SetByName($inferedNodeType['NODETYPENAME']);
            if (! $nodeType->get('IdNodeType')) {
                $this->messages->add(_('A nodetype could not be estimated to create the document,') 
                    . _(' operation will be aborted, contact with your administrator'), MSG_TYPE_ERROR);
                
                // Aborts language insertation
                $languages = array();
            }

            // Structured document inserts content document
            $setSymLinks = array();
            $idNodeMaster = null;
            foreach ($languages as $idLanguage) {
                $result = $this->_insertLanguage($idLanguage, $nodeType->get('Name'), $name, $idContainer, $idSchema, $aliases);
                if ($master > 0) {
                    if ($master != $idLanguage) {
                        $setSymLinks[] = $result;
                    } else {
                        $idNodeMaster = $result;
                    }
                }
            }
            foreach ($setSymLinks as $idNodeToLink) {
                $structuredDocument = new StructuredDocument($idNodeToLink);
                $structuredDocument->SetSymLink($idNodeMaster);
                $slaveNode = new Node($idNodeToLink);
                $slaveNode->set('SharedWorkflow', $idNodeMaster);
                $slaveNode->update();
            }
        }
        $values = array(
            'messages' => $this->messages->messages,
            'parentID' => $idNode,
            'nodeID' => $idContainer
        );
        $this->sendJSON($values);
    }

    /**
     * @param int $idNode
     * @param array $aliases
     * @param string $name
     * @param int $idSchema
     * @param array $languages
     * @param int $master
     * @param $type
     * @return boolean|number|NULL|string
     */
    private function buildXMLContainer(int $idNode, array $aliases, string $name, int $idSchema, array $languages, int $master = null
        , $nodeTypeID = null)
    {
        if ($nodeTypeID) {
            $nodeType = new NodeType($nodeTypeID);
            if (! $nodeType->GetID()) {
                $this->messages->add(_('Cannot load the node type for ' . $nodeTypeID), MSG_TYPE_ERROR);
                return false;
            }
        } else {
            
            // Creating container
            $baseIoInferer = new \Ximdex\IO\BaseIOInferer();
            $inferedNodeType = $baseIoInferer->infereType('FOLDER', $idNode);
            $nodeType = new NodeType();
            $nodeType->SetByName($inferedNodeType['NODETYPENAME']);
            if (! $nodeType->get('IdNodeType')) {
                $this->messages->add(_('A nodetype could not be estimated to create the container folder,') 
                    . _(' operation will be aborted, contact with your administrator'), MSG_TYPE_ERROR);
                return false;
            }
        }

        // Just the selected checks will be created.
        $selectedAlias = array();
        foreach ($languages as $idLang) {
            $selectedAlias[$idLang] = $aliases[$idLang];
        }
        $data = array(
            'NODETYPENAME' => $nodeType->get('Name'),
            'NAME' => $name,
            'PARENTID' => $idNode,
            'FORCENEW' => true,
            'CHILDRENS' => array(
                array(
                    'NODETYPENAME' => 'VISUALTEMPLATE',
                    'ID' => $idSchema
                )
            ),
            'LANGUAGES' => $languages,
            'ALIASES' => $selectedAlias,
            'MASTER' => $master,
            'NODETYPE' => $nodeType->getID()
        );
        $baseIO = new BaseIO();
        $res = $baseIO->build($data);
        if ($res <= 0) {
            foreach ($baseIO->messages->messages as $error) {
                Logger::warning($error['message']);
                $this->messages->add($error['message'], MSG_TYPE_WARNING);
            }
        }
        return $res;
    }

    /**
     * @param int $idNode
     * @param array $aliases
     * @param string $name
     * @param int $idSchema
     * @param array $languages
     * @param int $master
     * @return boolean|number|NULL|string
     */
    private function buildHTMLContainer(int $idNode, array $aliases, string $name, int $idSchema, array $languages, int $master = null)
    {
        // Just the selected checks will be created
        $selectedAlias = array();
        foreach ($languages as $idLang) {
            $selectedAlias[$idLang] = $aliases[$idLang];
        }
        $node = new Node($idNode);
        $data = array(
            'NODETYPENAME' => $node->nodeType->get('Name'),
            'NAME' => $name,
            'PARENTID' => $idNode,
            'FORCENEW' => true,
            'CHILDRENS' => array(
                array(
                    'NODETYPENAME' => 'VISUALTEMPLATE',
                    'ID' => $idSchema
                )
            ),
            'LANGUAGES' => $languages,
            'ALIASES' => $selectedAlias,
            'MASTER' => $master,
            'NODETYPE' => $node->nodeType->getID()
        );
        $baseIO = new BaseIO();
        $res = $baseIO->build($data);
        if ($res <= 0) {
            foreach ($baseIO->messages->messages as $error) {
                Logger::error($error);
            }
        }
        return $res;
    }
    
    private function _insertLanguage(int $idLanguage, string $nodeTypeName, string $name, int $idContainer, int $idTemplate, array $aliases
        , array $formChannels = null)
    {
        $language = new Language($idLanguage);
        if (! $language->get('IdLanguage')) {
            $this->messages->add(sprintf(_('Language %s insertion has been aborted because it was not found'),  $idLanguage), MSG_TYPE_WARNING);
            return NULL;
        }
        $data = array(
            'NODETYPENAME' => $nodeTypeName,
            'NAME' => $name,
            'PARENTID' => $idContainer,
            'ALIASNAME' => $aliases[$idLanguage],
            'CHILDRENS' => array (
                array ('NODETYPENAME' => 'VISUALTEMPLATE', 'ID' => $idTemplate),
                array ('NODETYPENAME' => 'LANGUAGE', 'ID' => $idLanguage)
            )
        );
        if(! empty($formChannels)) {
            foreach ($formChannels as $channel) {
                $data['CHILDRENS'][] = $channel;
            }
        }
        if (isset($aliases[$idLanguage])) {
            $data['CHILDRENS'][] = array(
                'NODETYPENAME' => 'NODENAMETRANSLATION',
                'IDLANG' => $idLanguage,
                'DESCRIPTION' => $aliases[$idLanguage]);
        }
        $baseIO = new baseIO();
        $result = $baseIO->build($data);
        if ($result > 0) {
            $insertedNode = new Node($result);
            $this->messages->add(sprintf(_('Document %s has been successfully inserted'), $insertedNode->get('Name')), MSG_TYPE_NOTICE);
        } else {
            $this->messages->add(sprintf(_('Insertion of document %s with language %s has failed'),
                $name, $language->get('Name')), MSG_TYPE_ERROR);
            foreach ($baseIO->messages->messages as $message) {
                $this->messages->messages[] = $message;
            }
        }
        return $result;   
    }
}
