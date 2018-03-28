<?php
    
namespace Ximdex\Workflow\Actions;

use Ximdex\Logger;
use Ximdex\Models\Node;
use Ximdex\Models\StructuredDocument;
use Ximdex\Models\Language;
use Ximdex\Properties\InheritedPropertiesManager;
use Ximdex\NodeTypes\XmlContainerNode;
use Plata\Plata;
use Ximdex\NodeTypes\NodeTypeConstants;

class Translator extends WorkflowAction
{
    const DEFAULT_ISO_LANG = 'es';
    
    public function sendTranslation() : bool
    {
        if (!$this->node->GetID()) {
            Logger::error('There is not a node ID for the document to translate');
            return false;
        }
        
        // Check the original document language to do the job only for the default origin language
        $structuredDocument = new StructuredDocument($this->node->GetID());
        if (!$structuredDocument->get('IdLanguage')) {
            Logger::error('Language has not been specified for document: ' . $this->node->GetNodeName());
            return false;
        }
        $language = new Language($structuredDocument->get('IdLanguage'));
        if (!$language->GetID()) {
            Logger::error('Language not found for ID: ' . $structuredDocument->get('IdLanguage'));
            return false;
        }
        if ($language->GetIsoName() != self::DEFAULT_ISO_LANG) {
            return true;
        }
        
        // We need to load the document container folder
        if (!$this->node->GetParent()) {
            Logger::error('There is not a parent document folder for the document');
            return false;
        }
        $docFolder = new Node($this->node->GetParent());
        if (!$docFolder->GetId()) {
            Logger::error('Cannot load the document folder with ID: ' . $this->node->GetParent());
            return false;
        }
        
        // Obtain the inherited language properties
        $properties = InheritedPropertiesManager::getValues($docFolder->GetID());
        if (!isset($properties['Language']) or !$properties['Language']) {
            Logger::error('Cannot load the language properties for the folder with node ID: ' . $docFolder->GetID());
            return false;
        }
        if (count($properties['Language']) == 1) {
            return true;
        }
        
        // Obtain the document language versions already created
        $xmlContainerNode = new XmlContainerNode($docFolder);
        $langs = $xmlContainerNode->GetLanguages();
        if ($langs === false) {
            Logger::error('Cannot load the document language versions for the folder with node ID: ' . $docFolder->GetID());
            return false;
        }
        
        // Generate an translations array with all the documents that will be updated / created
        if ($docFolder->GetNodeType() == NodeTypeConstants::XML_CONTAINER) {
            $type = Plata::TYPE_XML;
        }
        elseif ($docFolder->GetNodeType() == NodeTypeConstants::HTML_CONTAINER) {
            $type = Plata::TYPE_HTML;
        }
        else {
            $type = Plata::TYPE_TXT;
        }
        $documents = array();
        $plata = new Plata($this->node->GetContent(), '', $language->GetIsoName(), $type);
        foreach ($properties['Language'] as $languageProp) {
            if ($languageProp['IsoName'] == self::DEFAULT_ISO_LANG) {
                continue;
            }
            if (isset($langs[$languageProp['Id']])) {
                
                // The document has been created for the current language
                $documents[$languageProp['Id']] = ['id' => $langs[$languageProp['Id']]['nodeID']];
            }
            else {
                
                // The document does not exist in the current language
                $documents[$languageProp['Id']] = ['id' => null];
            }
            
            // Translate the original content
            $plata->setTo($languageProp['IsoName']);
            $res = $plata->translate();
            if ($res['status'] == 'fail') {
                Logger::error($res['message']);
                return false;
            }
            $documents[$languageProp['Id']]['translation'] = $res['message'];
        }
        
        // Update the document content with the translation
        if ($documents) {
            foreach ($documents as $idLang => $docInfo) {
                if (!$docInfo['id']) {
                    
                    // The document does not exist in the current language
                    $id = $xmlContainerNode->addLanguageVersion($idLang);
                    if (!$id) {
                        Logger::error('Cannot create the document language version for ID: ' . $idLang);
                        return false;
                    }
                }
                else {
                    $id = $docInfo['id'];
                }
                $node = new Node($id);
                if (!$node->GetID()) {
                    Logger::error('Cannot load the document with node ID: ' . $id);
                    return false;
                }
                if (!$node->SetContent($docInfo['translation'], true)) {
                    foreach ($node->messages->messages as $error) {
                        Logger::error($error);
                    }
                    return false;
                }
            }
        }
        return true;
    }
}