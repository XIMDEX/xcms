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

namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Models\ORM\RelSemanticTagsNodesOrm;

class RelSemanticTagsNodes extends RelSemanticTagsNodesOrm
{
    /**
     * Get Node Tags
     * 
     * @param int $_id_node
     * @param bool $keysWithID
     * @return array|NULL
     */
	public function getTags(int $_id_node, bool $keysWithID = false) : ?array
	{
		$this->clearError();
		$dbObj = new \Ximdex\Runtime\Db();
		$sql = sprintf("SELECT Tag, Name, n.idNamespace as Type, Link, Description, IdTagDescription 
                FROM SemanticTags tag inner join SemanticNamespaces n on n.idNamespace = tag.idNamespace
                , RelSemanticTagsNodes rel, RelSemanticTagsDescriptions relD 
                WHERE tag.IdTag = relD.Tag AND relD.IdTagDescription = rel.TagDesc AND Node = '%s'", $_id_node);
		$dbObj->query($sql);
		$out = array();
		if (! $dbObj->numErr) {
			while (! $dbObj->EOF) {
			    $tag = array(
					'Name' => $dbObj->getValue('Name'),
					'IdNamespace' => $dbObj->getValue('Type'),
					'Link' => $dbObj->getValue('Link'),
					'Description' => $dbObj->getValue('Description'),
					'iddesc' => $dbObj->getValue('IdTagDescription'),
					'idtag' => $dbObj->getValue('Tag')
				);
			    if ($keysWithID) {
			        $out[$dbObj->getValue('Tag')] = $tag;
			    } else {
			        $out[] = $tag;
			    }
				$dbObj->next();
			}
			return $out;
		}
        return null;
	}

	/**
	 * Get rel between tag and node
	 * 
	 * @param int $_idNode
	 * @param int $_tag
	 * @return array|NULL
	 */
	public function getRel(int $_idNode, int $_tag = -1) : ?array
	{
		if (-1 != $_tag) {
			$tag = new SemanticTags($_tag);
			$_id_tag = (int) $tag->get('IdTag');
			$idtag = sprintf(" AND Tag = '%s'", $_id_tag);
		} else {
		    $idtag = '';
		}
		$rel = $this->find(ALL, "Node = '{$_idNode}' $idtag");
		if (! empty($rel)) {
			return $rel;
		}
        return null;
	}

	/**
	 * Save tags in data base
	 * 
	 * @param array $tags
	 * @param array $previousTags
	 * @return bool
	 */
	public function saveAll(array $tags = [], array $previousTags = []) : bool
	{
	    if (! $this->get('Node')) {
	        Logger::error('Cannot save the semantic tags without a node ID');
	        return false;
	    }
	    
	    // Create new tags relations
	    $relTagsDesc = new RelSemanticTagsDescriptions();
	    foreach ($tags as $tag) {
	        
	        // Look for a tag description with the same given data
	        $relinfo = $relTagsDesc->getId($tag->Name, $tag->IdNamespace, '#');
	        if (! $relinfo['IdTagDescription']) {
	            
	            // Save tag description
	            $tagID = $relTagsDesc->save($tag->Name, $tag->IdNamespace, ! isset($tag->Link) || empty($tag->Link) ? '#' : $tag->Link
	                   , isset($tag->Description) ? $tag->Description : '');
	        } else {
	            
	            // Obtain the tag ID
	            $tagID = $relinfo['IdTagDescription'];
	        }
	        if (isset($previousTags[$tagID])) {
	            
	            // The tag is already saved, remove the element from previous tags
	            unset($previousTags[$tagID]);
	            continue;
	        }
	        if (! $this->createRel($tagID)) {
	            return false;
	        }
	    }
	    
	    // Remove non used previous tags relations
        foreach ($previousTags as $tag) {
            
            // Remove relation between tag and node
            if (! $this->removeRel($tag['iddesc'])) {
                return false;
            }
        }
	    return true;
	}

	/**
	 * Create rel between tag and node
	 * 
	 * @param int $_id_tag
	 * @return bool
	 */
	private function createRel(int $_id_tag) : bool
	{
	    $rel = new RelSemanticTagsNodes();
		$rel->set('TagDesc', $_id_tag);
		$rel->set('Node', $this->get('Node'));
		$rel->add();
		
		// Increment the count of semantic tags usage 
		$sql = 'UPDATE SemanticTags SET Total = Total + 1 WHERE IdTag = ' . $_id_tag;
		if (! $this->execute($sql)) {
		    return false;
		}
		return true;
	}

	/**
	 * Remove rel between tag and node
	 * 
	 * @param int $_id_tag
	 * @return bool
	 */
	private function removeRel(int $_id_tag) : bool
	{
	    $sql = sprintf("DELETE FROM RelSemanticTagsNodes WHERE TagDesc = '%d' AND Node = '%d'", $_id_tag, $this->get('Node'));
	    if (! $this->execute($sql)) {
	        return false;
	    }
		
		// Remove tag or update the count field
	    $semanticTag = new SemanticTags($_id_tag);
		if (! $semanticTag->remove()) {
		    Logger::error('Cannot remove the semantic tag with ID: ' . $_id_tag);
		    return false;
		}
		return true;
	}

	/**
	 * Remove relation for the current node. If neither node has that tags,
	 * the tag must be removed
	 * 
	 * @param int $idnode
	 */
	public function deleteTags(int $idnode)
	{
		// Get the tags to delete
		$tagsToDelete = $this->find('TagDesc', 'node = %s', array($idnode), MONO);

		// Delete the rows for this idnode
		$sql = sprintf("DELETE FROM RelSemanticTagsNodes WHERE Node = '%d'", $idnode);
		$this->execute($sql);

		// Check if every tag is linked to other node. If it isn't must to delete the node.
		if ($tagsToDelete) {
			foreach ($tagsToDelete as $idTag) {
				$currentExistingRelations = $this->count('TagDesc = %s', array($idTag));
				if ($currentExistingRelations === 0) {
				    
					// Deleting tag
				    $tag = new SemanticTags($idTag);
					if ($tag->get('IdTag')) {
						$tag->delete();
					}
				}
			}
		}
	}
}
