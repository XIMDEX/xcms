<?php
/******************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    							*
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 * @version $Revision: $                                                      *
 *                                                                            *
 *                                                                            *
 ******************************************************************************/

ModulesManager::file('/inc/model/orm/RelTagsNodes_ORM.class.php', 'ximTAGS');
ModulesManager::file('/inc/Tags.inc', 'ximTAGS');
ModulesManager::file('/inc/RelTagsDescriptions.inc', 'ximTAGS');
ModulesManager::file('/inc/metadata/ImageFile.class.php', 'ximTAGS');
class RelTagsNodes extends RelTagsNodes_ORM {

	/*
		Get Node Tags
	*/
	function getTags($_id_node) {
	    $this->ClearError();
		 $dbObj = new DB();

		$sql = sprintf("SELECT Tag, Name, n.idNamespace as Type, Link, Description, IdTagDescription FROM XimTAGSTags tag inner join Namespaces n on n.idNamespace=tag.idNamespace , RelTagsNodes rel, RelTagsDescriptions relD WHERE tag.IdTag = relD.Tag AND relD.IdTagDescription = rel.TagDesc AND Node = '%s'", $_id_node); 

		$dbObj->Query($sql);
		$out = array();

		if(!$dbObj->numErr) {
			while(!$dbObj->EOF) {
				$out[] = array(
					"Name" => $dbObj->GetValue("Name"),
					"IdNamespace" => $dbObj->GetValue("Type"),
					"link" => $dbObj->GetValue("Link"),
					"description" => $dbObj->GetValue("Description"),
					"iddesc" => $dbObj->GetValue("IdTagDescription"),
					"idtag" => $dbObj->GetValue("Tag")
				);
				$dbObj->Next();
			}
			return $out;
		} else {
			$this->SetError(5);
			return null;
		}

	}


	/**
	 *	get rel between tag and node
	*/
	function getRel($_idNode, $_tag = -1) {
		$idtag='';
		if(-1 != $_tag) {
			$tag = new Tag($_tag);

			$_id_tag = (int) $tag->get('IdTag');
			$idtag= sprintf(" AND Tag='%s' ", $_id_tag);
		}

	    $rel = $this->find(ALL, "Node = '{$_idNode}' $idtag");
       	if(!empty($rel)) {
       		return $rel;
       	}else {
       		return null;
       	}
	}

	/**
	 *	save $_tags in bbdd
	*/
	function saveAll($_tags = null, $_id_node = 0, $_previous_tags=null) {
		//save tags
		//Tags to remove 
        if(!empty($_previous_tags) ) { 
            foreach($_previous_tags as $_tag) { 
                if(!empty($_tag) && !empty($_id_node) ) { 
					//remove rel betweeen tag and node 
					$this->removeRel($_tag["iddesc"], $_id_node); 
					//remove tag 
					$tag = new Tag($_tag["idtag"]); 
					$tag->remove(); 
                } 
            } 
        } 
		$alltags = '';

		if(!empty($_tags) ) {
			 $i= 0;
			 foreach($_tags as $_tag) {
			 	$rel = new RelTagsDescriptions();
			 	$relinfo = $rel->getId($_tag->Name, $_tag->IdNamespace, '#');
				$id = $relinfo["IdTagDescription"];
			 	//if not rel exits between description and tag, try create it
			 	if(empty($id) ) {
					//save tag
			 		$id = $rel->save($_tag->Name, $_tag->IdNamespace, '#', '');
			 	} else {
					//if already rel exits between description and node, try create tags and rel
			 		if(!empty($_previous_tags) && isset($_previous_tags[$id])){
			 			//Quitamos el tags de lats_tags
			 			unset($_previous_tags[$id]);
				 	}			
				}
				$this->createRel($id, $_id_node);
			 	if($i != 0) $alltags .= ",";

			 	//add tag to alltags
			 	$alltags .= $_tag->Name;
			 	$i++;
			 }
		}


		//save in exif if nodetype is image
		$node = new Node($_id_node);
		$nodetype = new NodeType($node->GetNodeType() );
		if("ImageFile" == $nodetype->GetName() ) {
			$image = new ImageFile($_id_node);
			$image->saveTags($alltags);
		}
	}


	/**
	 *	create rel between tag and node
	*/
	function createRel($_id_tag, $_id_node) {

		$rel = new RelTagsNodes();
		$rel->set('TagDesc', $_id_tag);
		$rel->set('Node', $_id_node);

	   return $rel->add();
	}


	/**
	 *	Remove rel between tag and node
	*/
	function removeRel($_id_tag, $_id_node) {
	    $sql = sprintf("DELETE FROM RelTagsNodes WHERE TagDesc='%d' AND Node='%d'",$_id_tag, $_id_node );
	  	$this->execute($sql);
	}

    /**
     * Remove relation for the current node. If neither node has that tags, 
     * the tag must be removed
     * @param int $idnode
     */
    public function deleteTags($idnode){
        //Get the tags to delete
        $tagsToDelete = $this->find("TagDesc", "node=%s", array($idnode), MONO);
        
        //Delete the rows for this idnode
        $sql = sprintf("DELETE FROM RelTagsNodes WHERE Node='%d'",$idnode);
	$this->execute($sql);
        
        //Check if every tag is linked to other node. If it isn't 
        //must to delete the node. 
        if ($tagsToDelete){
            foreach ($tagsToDelete as $idTag) {
                $currentExistingRelations = $this->count("TagDesc=%s", array($idTag));
                if (count($currentExistingRelations) &&
                    intval($currentExistingRelations[0]) === 0){
                        //Deleting tag.
                        $tag = new Tag($idTag);
                        if ($tag->get("IdTag")){
                            $tag->delete();
                        }
                }
            }        
        }
        
        
    }
}
