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

namespace Ximdex\NodeTypes;

use Ximdex\Deps\DepsManager;
use Ximdex\Logger;

class XimletNode extends AbstractStructuredDocument
{
    public function getRefererDocs()
    {
        $query = sprintf("SELECT Distinct(idNodeDependent) FROM Dependencies WHERE DepType ='XIMLET' AND idNodeMaster = %d", $this->nodeID);
        $this->dbObj->Query($query);
        $docsToPublish = array();
        while (! $this->dbObj->EOF) {
            $docsToPublish[] = $this->dbObj->GetValue("idNodeDependent");
            $this->dbObj->Next();
        }
        return $docsToPublish;
    }

    /**
     * Gets the ximlet dependencies
     * 
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\AbstractStructuredDocument::GetDependencies()
     */
    public function getDependencies() : array
    {
        $depsMngr = new DepsManager();
        $deps = array();
        if ($sections = $depsMngr->getByTarget(DepsManager::SECTION_XIMLET, $this->parent->get('IdNode'))) {
            $deps = array_merge($deps, $sections);
        }
        if ($strDocs = $depsMngr->getByTarget(DepsManager::STRDOC_XIMLET, $this->parent->get('IdNode'))) {
            $deps = array_merge($deps, $strDocs);
        }
        return $deps;
    }

    /**
     * Deletes dependencies in rel tables
     * 
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\AbstractStructuredDocument::DeleteNode()
     */
    public function deleteNode() : bool
    {
        $depsMngr = new DepsManager();
        if ($depsMngr->deleteByTarget(DepsManager::SECTION_XIMLET, $this->parent->get('IdNode')) === false) {
            return false;
        }
        if ($depsMngr->deleteByTarget(DepsManager::STRDOC_XIMLET, $this->parent->get('IdNode')) === false) {
            return false;
        }
        if ($depsMngr->deleteBySource(DepsManager::STRDOC_TEMPLATE, $this->parent->get('IdNode')) === false) {
            return false;
        }
        Logger::info('Ximlet dependencies deleted');
        return true;
    }
    /**
     * Get the documents that must be publicated when the ximlet is published
     * 
     * {@inheritDoc}
     * @see \Ximdex\NodeTypes\AbstractStructuredDocument::getPublishabledDeps()
     */
    public function getPublishabledDeps(array $params = []) : ?array
    {
        $depsMngr = new DepsManager();
        return $depsMngr->getByTarget(DepsManager::STRDOC_XIMLET, $this->parent->get('IdNode'));
    }

    /**
     * The intended use for this method is just generate a colector, is not related with xmldocument
     * 
     * @return boolean
     */
    public function generator()
    {
        Logger::fatal('Se ha estimado un tipo de nodo incorrecto');
        return false; // xmd::fatal must kill the process anyway, so dont wait any further trace
    }
}
