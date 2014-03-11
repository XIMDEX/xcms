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
 * @version $Revision: $			                                             *  
 *                                                                            *
 *                                                                            *
 ******************************************************************************/

ModulesManager::file('/inc/model/orm/RelTagsDescriptions.class.php', 'ximTAGS');
ModulesManager::file('/inc/Tags.inc', 'ximTAGS');

class RelTagsDescriptions extends RelTagsDescriptions_ORM {


	function getId($_tag, $_type, $_link) {
	
		$tag = new Tag();
		$tag = $tag->getTag($_tag, $_type);
		if(null ==  $tag) return null;
	
		$rel = parent::find(ALL, "Tag = '".$tag["IdTag"]."'");

		

       if(!empty($rel)) {
       	return $rel[0];
       }else {
       	return null;
       }
	}
	

	function save($_name, $_type, $_link, $_description) {
			 
 		 $tag = new Tag();
		 $_tag = $tag->save($_name, $_type);	
	
		 $rel = $this->getId($_name, $_type, $_link, $_description);

		 if(!empty($rel) ) {
		 	return $rel["IdTagDescription"];
		 }else {
			$rel = new RelTagsDescriptions();
			$rel->set('Type', strtoupper($_type));
			$rel->set('Tag', strtoupper($_tag));
			$rel->set('Link', $_link);
			$rel->set('Description', $_description);
			 
			$id =  $rel->add();
	 	    return $id;
		}
	}

	function removeByTag($_tag) {
		$tag = new Tag();
		$tag = $tag->getTag($_tag);
		
	   $sql = sprintf("DELETE FROM RelTagsDescriptions where Tag='%d'",$tag["IdTag"] );

	

  		return $this->execute($sql);
	}
}
