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





//Methods for browser
function baseIO_listadoDependencias($idnode) {

	//Returns a list of all associations of a section

		$dependencia = new node($idnode);
		if($dependencia->GetID()==NULL){
			return "0";
		}
		$nombre = $dependencia->GetNodeName();
		$res = $dependencia->GetDependencies();
		$arr_dependencias = array();
		$limite = count($res);

		if ($limite==0){
			$arr_dependencias[0]['Id'] = null;
			$arr_dependencias[0]['Name'] = null;
			$arr_dependencias[0]['Padre'] = $nombre;
		}
		else{

			for($i=0;$i<$limite;$i++) {
				$node = new node($res[$i]);
				$arr_dependencias[$i]['Id'] = $res[$i];
				$arr_dependencias[$i]['Name'] = $node->GetPath().$node->GetNodeName();
				$arr_dependencias[$i]['Padre'] = $nombre;
			}
		}
	return $arr_dependencias;
}
//End of methods added for browser
function baseIO_borradoNodos($nodeID,$userID) {
	// Checking if user has permits of general gropu to perform cascade deletion 

	$user = new User($userID);
	$canDeleteOnCascade = $user->HasPermission("delete on cascade");
	//echo "@@".$canDeleteOnCascade."@@";
	/// If user has permit to cascade deletion and node has not children and has not dependencies
	if($canDeleteOnCascade){
		/// Checking all we want to delete, can be deleted down node
		$node = new Node($nodeID);
		if(!$node->GetID()){
			return "0";
		}
		$depList = $node->GetDependencies();
		$undeletableChildren = $node->TraverseTree(5);
		// Deletes all dependencies of  container child.
		$node_type=new NodeType($node->GetNodeType());
		$depList=array();
		if ($node_type->GetName()=="XmlContainer") {
			$children=$node->GetChildren();
			foreach($children as $child) {
				$node_child=new Node($child);
				$depList=array_merge($depList, $node_child->GetDependencies());
			}
		}
		else {
		
		if(is_array($depList)) foreach($depList as $depID)
			{
			$node->SetID($depID);
			$undeletableChildren = array_unique(array_merge($undeletableChildren, $node->TraverseTree(5)));
			}
		}
			/// Deleting node recursively 
			$node = new Node($nodeID);
			$node->DeleteNode();
			/// echo "3 ##".$node->numErr."@@".$node->nodeID."@@<br>";
			if($node->numErr)
				{

				$err = _("An error occurred while deleting:");
				$err .= $node->GetID(). " " .$node->GetPath()._("Error message: ").$node->msgErr."<br><br>";
				}
			else{
				$err = "0";
			}
			/// Y todas las dependencias
			if(is_array($depList)) foreach($depList as $depID)
				{
				$node->SetID($depID);
				$node->DeleteNode();
				if($node->numErr)
					{
					if(!strlen($err))
						$err .= $node->GetID(). " " .$node->GetPath()._("Error message: ").$node->msgErr."<br><br>";
					}
				}
				else{
					$err = "0";

				}

		}
	else
		{
			$node		= new Node($nodeID);
			$parentID	= $node->GetParent();
			$fsEntity	= $node->nodeType->GetHasFSEntity();
			$deps		= $node->GetDependencies();
			$children	= $node->GetChildren();
		/// Error: If it has not permit to cascade deletion and node has children and has dependencies
		if(sizeof($children) && sizeof($deps))
			$err = _("Node is not empty and it has external dependencies.");

		/// Error: If it has not permit to cascade deletion and node has children but dependencies
		if(sizeof($children) && !sizeof($deps))
			$err = _("Node is not empty.");

		/// Error: If it has not permit to cascade deletion and node has not children and has dependencies
		if(!sizeof($children) && sizeof($deps))
			$err = _("It has external dependencies.");


		/// If it has not permit to cascade deletion and node has not children and has not dependencies
		///Here it is allowed atomic deletion
		if(!sizeof($children) && !sizeof($deps)){
			$node->DeleteNode();
			$err = "0";
			}
		}
return $err;
}