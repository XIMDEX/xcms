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

namespace Ximdex\Models;

use Ximdex\Logger;
use Ximdex\Models\ORM\SemanticTagsOrm;

class SemanticTags extends SemanticTagsOrm
{
	public function __construct($_tag = null)
	{
		parent::__construct();
		if (null != $_tag) {
			$tag = $this->getTag($_tag);
			if (!empty($tag) ) {
				$this->set('Name', $tag["Name"]);
				$this->set('IdTag', $tag["IdTag"]);
				$this->set('Total', $tag["Total"]);
			} else {
				$this->set('Name', $_tag);
			}
		}
	}

	/**
	 * Return suggested Tags
	 * Join to RelSemanticTagsDescriptions to return the "Link" field
	 * 
	 * @return boolean|array
	 */
	function getTags()
	{
        $sql = sprintf("SELECT xt.IdTag, xt.Name, xt.IdNamespace, rtd.Link, rtd.Description FROM SemanticTags xt 
                LEFT JOIN RelSemanticTagsDescriptions rtd on xt.IdTag = rtd.IdTagDescription");
        $result = $this->query($sql);
        return $result;
	}

	/**
	 * Get the max value between all Ximdex tags
	 * 
	 * @return array|boolean
	 */
	function getMaxValue()
	{
        return parent::find('max(Total)');
    }

	/**
	 * Get info about $_tag
	 * 
	 * @param $_tag
	 * @param $namespace
	 * @return boolean|string|NULL|array
	 */
	public function getTag($_tag = null, $namespace = null)
	{
		if (null == $_tag && is_numeric($this->get("IdTag")) ) {
			return array("IdTag" => $this->get("IdTag"), "Name" => $this->get("Name"),"Total" => $this->get("Total") );
		}
		if (is_numeric($_tag) ) {
			return $this->_getTagById($_tag);
		}
		if (null != $_tag and null != $namespace)  {
			return $this->_getTagByNameNamespace($_tag, $namespace);
		}
		return null;
	}

	/**
	 * Get info about $_tag
	 * 
	 * @param $_tagId
	 * @return NULL|string
	 */
	private function _getTagById($_tagId)
	{
	   $tag = parent::find(ALL, "IdTag = '$_tagId'");
       if (!empty($tag)) {
           return $tag[0];
       }
       return null;
	}

	/**
	 * Get info about $_tag
	 * 
	 * @param $_tagName
	 * @param $namespace
	 * @return NULL|string
	 */
	private function _getTagByNameNamespace($_tagName, $namespace)
	{
       $tag = parent::find(ALL, "Name = '".$_tagName."' AND IdNamespace = '".$namespace."'");
       if (!empty($tag)) {
           return $tag[0];
       }
       return null;
	}

	/**
	 * Save one tag $_tag
	 * 
	 * @param $_tag
	 * @param $namespace
	 * @return boolean|NULL|string
	 */
	function save($_tag = null, $namespace = null)
	{
		// Get Data tag
      	$tag = $this->_getTagByNameNamespace($_tag, $namespace);

  		// Check if tag exists
		if (empty($tag)) {
		    
		    // If tag dont exists
			$tag = new SemanticTags();
		    $tag->set('Name', $_tag);
		    $tag->set('IdNamespace', (int) $namespace);
  		    $tag->set('Total', 0);
		    return $tag->add();
		}
  		return $tag['IdTag'];
	}

	/**
	 * Save all tags $_tags
	 * 
	 * @param $tags
	 */
	function saveAll($tags = null)
	{
		if (!empty($tags)) {
		    foreach($tags as $_tag) {
				$this->save($_tag);
			}
		}
	}

	/**
	 * Remove one tag $_tag
	 * 
	 * @param int $_tag
	 * @return bool
	 */
	public function remove(int $_tag = null) : bool
	{
	    if (!$_tag) {
	        if (!$this->get('IdTag')) {
	            Logger::error('No tag ID has been provided in order to remove it');
	            return false;
	        }
	        $_tag = $this->get('IdTag');
	    }
	    
        // Get Tag data
        $tag = $this->getTag($_tag);

		// If tag dont exists, out
		if (empty($tag)) {
		    Logger::error('Tag with ID: ' . $_tag . ' does not exist');
		    return false;
		}
		if ($tag['Total'] <= 1) { //Remove tag
		    
			 // Remove tag descriptions
			 $relTagDesc = new RelSemanticTagsDescriptions();
			 if ($relTagDesc->removeByTag($tag['IdTag']) === false) {
			     Logger::error('Cannot remove the semantic tag description with tag ID: ' . $tag['IdTag']);
			     return false;
			 }
			 $tag = new SemanticTags($tag['IdTag']);
			 if ($tag->delete() === false) {
			     Logger::error('Cannot remove the semantic tag with tag ID: ' . $tag['IdTag']);
			     return false;
			 }
		} else {
		    
		    // Tag total - 1
		    $sql = sprintf("UPDATE SemanticTags SET Total = Total - 1 WHERE IdTag = %d", $tag['IdTag']);
	  		return $this->execute($sql);
		}
		return true;
	}
    
	/**
	 * Remove all tags $_tag
	 * 
	 * @param $_tags
	 */
	function removeAll($_tags = null)
	{
		if (!empty($tags)) {
			foreach($tags as $_tag) {
				$this->remove($_tag);
			}
		}
	}
}