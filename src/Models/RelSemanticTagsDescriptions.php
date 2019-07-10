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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

namespace Ximdex\Models;

use Ximdex\Models\ORM\RelSemanticTagsDescriptionsOrm;

class RelSemanticTagsDescriptions extends RelSemanticTagsDescriptionsOrm
{
	public function getId(string $_tag, int $_type, string $_link)
	{
		$tag = new SemanticTags();
		$tag = $tag->getTag($_tag, $_type);
		if (null == $tag) {
		    return null;
		}
		$rel = parent::find(ALL, 'Tag = \'' . $tag["IdTag"] . '\'');
        if (! empty($rel)) {
            return $rel[0];
        }
        return null;
	}

	public function save(string $_name, int $_type, string $_link, string $_description)
	{
	    $tag = new SemanticTags();
		$_tag = $tag->save($_name, $_type);
		$rel = $this->getId($_name, $_type, $_link);
		if (! empty($rel)) {
			return $rel['IdTagDescription'];
		}
		$rel = new RelSemanticTagsDescriptions();
		$rel->set('Tag', $_tag);
		$rel->set('Link', $_link);
		$rel->set('Description', $_description);
		return $rel->add();
	}
	
	public function removeByTag(int $_tag) : bool
	{
	    $tag = new SemanticTags();
		$tag = $tag->getTag($_tag);
		$sql = sprintf('DELETE FROM RelSemanticTagsDescriptions where Tag = \'%d\'', $tag['IdTag']);
  		return $this->execute($sql);
	}
}
