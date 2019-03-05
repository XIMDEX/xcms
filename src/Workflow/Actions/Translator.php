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

namespace Ximdex\Workflow\Actions;

use Ximdex\Models\Node;
use Ximdex\Models\StructuredDocument;
use Ximdex\Models\Language;
use Ximdex\Properties\InheritedPropertiesManager;
use Ximdex\Workflow\WorkflowAction;
use Ximdex\NodeTypes\XmlContainerNode;
use Plata\Plata;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\Nodeviews\ViewFilterMacros;

class Translator extends WorkflowAction
{
    const DEFAULT_ISO_LANG = 'es';
    
    public function sendTranslation() : bool
    {
        if (! $this->node->getID()) {
            $this->error = 'There is not a node ID for the document to translate';
            return false;
        }
        
        // Check the original document language to do the job only for the default origin language
        $structuredDocument = new StructuredDocument($this->node->getID());
        if (! $structuredDocument->get('IdLanguage')) {
            $this->error = 'Language has not been specified for document: ' . $this->node->getNodeName();
            return false;
        }
        $language = new Language($structuredDocument->get('IdLanguage'));
        if (! $language->GetID()) {
            $this->error = 'Language not found for ID: ' . $structuredDocument->get('IdLanguage');
            return false;
        }
        if ($language->GetIsoName() != self::DEFAULT_ISO_LANG) {
            return true;
        }
        
        // We need to load the document container folder
        if (! $this->node->getParent()) {
            $this->error = 'There is not a parent document folder for the document';
            return false;
        }
        $docFolder = new Node($this->node->GetParent());
        if (! $docFolder->GetId()) {
            $this->error = 'Cannot load the document folder with ID: ' . $this->node->GetParent();
            return false;
        }
        
        // Obtain the inherited language properties
        $properties = InheritedPropertiesManager::getValues($docFolder->GetID(), true);
        if (! isset($properties['Language']) or ! $properties['Language']) {
            $this->error = 'Cannot load the language properties for the folder with node ID: ' . $docFolder->GetID();
            return false;
        }
        if (count($properties['Language']) == 1) {
            return true;
        }
        
        // Obtain the document language versions already created
        $xmlContainerNode = new XmlContainerNode($docFolder);
        $langs = $xmlContainerNode->getLanguages();
        if ($langs === false) {
            $this->error = 'Cannot load the document language versions for the folder with node ID: ' . $docFolder->GetID();
            return false;
        }
        
        // Generate a translations array with all the documents that will be updated / created
        elseif (in_array($docFolder->GetNodeType(), [NodeTypeConstants::HTML_CONTAINER, NodeTypeConstants::XML_CONTAINER])) {
            $type = Plata::TYPE_HTML;
        } else {
            $type = Plata::TYPE_TXT;
        }
        $documents = array();
        $plata = new Plata($this->node->getContent(), '', $language->GetIsoName(), $type);
        foreach ($properties['Language'] as $languageProp) {
            if ($languageProp['IsoName'] == self::DEFAULT_ISO_LANG) {
                continue;
            }
            if (isset($langs[$languageProp['Id']])) {
                
                // The document has been created for the current language
                $documents[$languageProp['Id']] = ['id' => $langs[$languageProp['Id']]['nodeID']];
            } else {
                
                // The document does not exist in the current language
                $documents[$languageProp['Id']] = ['id' => null];
            }
            
            // Translate the original content
            $plata->setTo($languageProp['IsoName']);
            $res = $plata->translate();
            if ($res['status'] == 'fail') {
                $this->error = $res['message'];
                return false;
            }
            $documents[$languageProp['Id']]['translation'] = $this->fixMacros($res['message']);
        }
        unset($plata);
        
        // Update the document content with the translation
        if ($documents) {
            foreach ($documents as $idLang => $docInfo) {
                if (! $docInfo['id']) {
                    
                    // The document does not exist in the current language
                    $id = $xmlContainerNode->addLanguageVersion($idLang);
                    if (! $id) {
                        $this->error = 'Cannot create the document language version for ID: ' . $idLang;
                        return false;
                    }
                } else {
                    $id = $docInfo['id'];
                }
                $node = new Node($id);
                if (! $node->getID()) {
                    $this->error = 'Cannot load the document with node ID: ' . $id;
                    return false;
                }
                if (! $node->setContent($docInfo['translation'], true)) {
                    foreach ($node->messages->messages as $error) {
                        $this->error = $error;
                    }
                    return false;
                }
            }
        }
        return true;
    }
    
    private function fixMacros(string $html) : string
    {
        $html = str_replace(') @@@', ')@@@', $html);
        $html = str_replace(') GMximdex@@@', ')GMximdex@@@', $html);
        $macros = ViewFilterMacros::get_class_constants();
        foreach ($macros as $name => $macro) {
            if (strpos($name, 'MACRO_') === false) {
                continue;
            }
            $macro = str_replace(['@@@', '\\', '/'], '', $macro);
            $macro = explode('(', $macro)[0];
            $html = str_replace("{$macro} ", $macro, $html);
        }
        return $html;
    }
}
