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




 

function PublicateXimlet($sectionID,$dateUp,$recurrence) {

        $node = new Node($sectionID);
        $childList = $node->GetChildren();

        $nodeList= array();
        foreach ($childList as $child) {
                $node->SetID($child);
                if ($recurrence==1 || ($node->nodeType->GetName() != "Section" && $recurrence==0)) {
                        $nodeList = array_merge($nodeList, $node->TraverseTree(6));
                }
        }
		$dateUp = time();
        foreach ($nodeList as $nodeID) {
            $sync = new Synchronizer();
            $node->SetID($nodeID);
            $sync->SetID($nodeID);
            $sync->DeleteFramesFromNow($nodeID,0,$dateUp);
            $sync->CreateFrame($dateUp, null, null, 1,1);
        }
}
?>
