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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\NodeTypes;

use Ximdex\Models\Language;
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Models\StructuredDocument;

class XmlContainerNode extends FolderNode
{
    /**
     * Get the schema for the current document.
     * Old method. It stay here because backward compatibility.
     * 
     * @return int Schema Id.
     */
    function getVisualTemplate()
    {
        return $this->getIdSchema();
    }

    /**
     * Get the schema for the current document.
     * 
     * @return int Schema Id.
     */
    public function getIdSchema()
    {
        $relTemplateContainer = new \Ximdex\Models\RelTemplateContainer();
        $result = $relTemplateContainer->find("IdTemplate", "IdContainer = %s", array(
            $this->nodeID
        ), MONO);
        if (! $result || ! is_array($result) || ! count($result)) {
            return false;
        }
        return $result[0];
    }

    /**
     * Build a new document and all its language versions
     * 
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\FolderNode::createNode()
     */
    public function createNode(string $name = null, int $parentID = null, int $nodeTypeID = null, int $stateID = null, int $idSchema = null
        , array $aliasLangList = null, array $channelList = null, int $idNodeMaster = null, $dataChildren = null)
    {
        $result = false;
        $reltemplate = new \Ximdex\Models\RelTemplateContainer();
        $reltemplate->createRel($idSchema, $this->nodeID);
        if (is_array($aliasLangList)) {
            foreach ($aliasLangList as $idLanguage => $alias) {
                $result = $this->addLanguageVersion($idLanguage, $alias, $channelList, $dataChildren);
            }
        }
        if (! $result) {
            return false;
        }
        if ($idNodeMaster) {
            $this->setNodeMaster($idNodeMaster);
        }
        if ($nodeTypeID == \Ximdex\NodeTypes\NodeTypeConstants::XML_CONTAINER 
            or $nodeTypeID == \Ximdex\NodeTypes\NodeTypeConstants::HTML_CONTAINER) {
            $this->UpdatePath();
        }
        return true;
    }

    /**
     * Add a new language version for the current document
     * 
     * @param int $idLang
     * @param string $alias Description name for the language version. Useful in breadcrum.
     * @param array $channelList Array within idchannels
     * @param array $data Required data to create the language version.
     */
    public function addLanguageVersion($idLang, $alias = '', $channelList = null, $data = null)
    {
        $xmldoc = new Node();
        $childrenNodeType = new NodeType();
        switch ($this->nodeType->GetName()) {
            case 'XmlContainer':
                $childrenNodeType->SetByName('XmlDocument');
                break;
            case 'XimletContainer':
                $childrenNodeType->SetByName('Ximlet');
                break;
            case 'XimPdfDocumentFolder':
                $childrenNodeType->SetByName('XimPdfDocumentLang');
                break;
            case 'MetaDataContainer':
                $childrenNodeType->SetByName('MetaDataDoc');
                break;
            case 'HTMLContainer':
                $childrenNodeType->SetByName('HTMLDocument');
                break;
            default:
                return false;
        }
        if ($childrenNodeType->HasError()) {
            $this->parent->SetError(1);
            return false;
        }
        $lang = new Language($idLang);
        if ($lang->HasError()) {
            $this->parent->SetError(1);
            return false;
        }
        $idSchema = $this->getIdSchema();
        $nameDoc = $this->parent->getNodeName() . "-id" . $lang->GetIsoName();
        $idDoc = $xmldoc->CreateNode($nameDoc, $this->nodeID, $childrenNodeType->GetID(), null, $idSchema, $idLang, $alias
            , $channelList, $data);
        if ($xmldoc->HasError()) {
            $this->parent->SetError(1);
            return false;
        }
        return $idDoc;
    }

    /**
     * Set the current idNodeMaster
     * 
     * @param int $idNodeMaster
     */
    public function setNodeMaster($idNodeMaster)
    {
        $children = $this->parent->GetChildren();
        foreach ($children as $idChild) {
            $child = new Node($idChild);
            $childLanguage = $child->class->GetLanguage();
            if ($childLanguage != $idNodeMaster) {
                $child = new Node($idChild);
                $child->SetWorkflowMaster($idNodeMaster);
                $strDoc = new StructuredDocument($idChild);
                $strDoc->SetSymLink($idNodeMaster);
            }
        }
    }

    public function deleteNode() : bool
    {
        $templatecontainer = new \Ximdex\Models\RelTemplateContainer();
        $templatecontainer->deleteRel($this->nodeID);
        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\FolderNode::renameNode()
     */
    public function renameNode(string $name) : bool
    {
        if (! $name) {
            return false;
        }
        $listaDocs = $this->parent->GetChildren();
        if (sizeof($listaDocs) > 0) {
            foreach ($listaDocs as $docID) {
                $node = new Node($docID);
                $strdoc = new StructuredDocument($docID);
                $langId = $strdoc->GetLanguage();
                $lang = new Language($langId);
                $nameDoc = $name . "-id" . $lang->GetIsoName();
                $node->RenameNode($nameDoc);
            }
        }
        $this->updatePath();
        return true;
    }
    
    public function GetLanguages()
    {
        $node = new Node($this->nodeID);
        $docList = $node->GetChildren();
        if ($node->HasError()) {
            $this->parent->SetError(1);
            return false;
        }
        $langList = [];
        foreach ($docList as $docID) {
            $strDoc = new StructuredDocument($docID);
            if (!$strDoc->GetID()) {
                return false;
            }
            $language = new Language($strDoc->get("IdLanguage"));
            if (!$language->GetID()) {
                return false;
            }
            $langList[$language->GetID()] = ['iso' => $language->GetIsoName(), 'nodeID' => $docID];
        }
        return $langList;
    }
    
    public function GetChildByLang($langID)
    {
        $node = new Node($this->nodeID);
        $docList = $node->GetChildren();
        if ($node->HasError()) {
            $this->parent->SetError(1);
            return false;
        }
        foreach ($docList as $docID) {
            $strDoc = new StructuredDocument($docID);
            $docLang = $strDoc->GetLanguage();
            if ($docLang == $langID) {
                return $docID;
            }
        }
        return null;
    }

    public function toXml(int $depth, array & $files, bool $recurrence = false)
    {
        $xml = '';
        $query = sprintf("SELECT IdTemplate FROM `RelTemplateContainer` WHERE IdContainer = %d", $this->parent->nodeID);
        $this->dbObj->Query($query);
        while (! $this->dbObj->EOF) {
            $idTemplate = $this->dbObj->GetValue('IdTemplate');
            if (! $idTemplate) {
                continue;
            }
            $template = new Node($idTemplate);
            $xml .= $template->ToXml($depth, $files, $recurrence);
            $this->dbObj->Next();
        }
        return $xml;
    }
}
