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



ModulesManager::file("/actions/searchpanel/inc/Searchpanel_Filters.class.php");


class Filters_Tags {

	public function getFilters() {
		$filtersTags= array(
			'field' => array(
				array('key' => 'Descripcion', 'value' => 'Text', 'comparation' => 'comparation'),
				array('key' => 'materiaid', 'value' => 'Theme', 'comparation' => 'comparation'),
				array('key' => 'catid', 'value' => 'CatID', 'comparation' => 'comparation'),
			),
			'comparation' => array(
				array('key' => 'contains', 'value' => 'contains', 'content' => 'content'),
				array('key' => 'nocontains', 'value' => 'does not contain', 'content' => 'content'),
				array('key' => 'equal', 'value' => 'equal to', 'content' => 'content'),
				array('key' => 'nonequal', 'value' => 'not equal to', 'content' => 'content'),
				array('key' => 'startswith', 'value' => 'begins with', 'content' => 'content'),
				array('key' => 'endswith', 'value' => 'ends with', 'content' => 'content')
			)
		);
		//Including translations
		$filtersTags['field'][0]['value']=_('Text');
		$filtersTags['field'][1]['value']=_('Theme');
		$filtersTags['field'][1]['value']=_('CatID');
		
		$filtersTags['comparation'][0]['value']=_('contains');
		$filtersTags['comparation'][1]['value']=_('does not contain');
		$filtersTags['comparation'][2]['value']=_('equal to');
		$filtersTags['comparation'][3]['value']=_('not equal to');
		$filtersTags['comparation'][4]['value']=_('begins with');
		$filtersTags['comparation'][5]['value']=_('ends with');
		
		return $filtersTags;
	}
}

?>
