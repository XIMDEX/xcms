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




 

ModulesManager::file('/actions/movenode/baseIO.php');

	
function baseIO_MoveNode($nodeID, $targetParentNodeID) {

	XMD_Log::info("IO-movenode -> nodeID=$nodeID, nodeID_destino=$targetParentNodeID");
	$messages = new Messages();
	
	$node = new Node($nodeID);
	if (!($node->get('IdNode') > 0)) {
		return _("Source node does not exist") . $node->msgErr; // Operation error
	}
	
	$target = new Node($targetParentNodeID);
 	if (!($target->get('IdNode') > 0)) {
		return _("Source node does not exist") . $node->msgErr; // Operation error
	}
		  
	$parent = $node->GetParent();
  	if ($parent == $targetParentNodeID) {
		return _("This node is already associated with that parent ") . $node->msgErr; // Operation error
	}
  
	$node->MoveNode($target->get('IdNode'));
	if($node->numErr) return _("The operation has failed") . $node->msgErr; // Operation error
	
	return null;
}

function baseIO_MoverNodos($nodeID, $targetParentNodeID) {

	XMD_Log::info("IO-movenode -> nodeID=$nodeID, nodeID_destino=$targetParentNodeID");
	
	$node = new Node($nodeID);
	$target = new Node($targetParentNodeID);
  
	if($node->GetID()==NULL){
		return "0";
	} 
  
	$parent = $node->GetParent();
	if ($parent == $targetParentNodeID) return (1);
  
	if(!$target->get('IdNode') > 0) return;
  
	$node->MoveNode($target->get('IdNode'));
	if($node->numErr) return;
	
	return;
}

  
?>