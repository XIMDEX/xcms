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

class Filters_Toldox {

	public function getFilters() {
		$filters= array(
			'field' => array(
				array('key' => 'path', 'value' => 'Name', 'comparation' => 'comparation'),
				array('key' => 'fechaalta', 'value' => 'Creation date', 'comparation' => 'date-comparation'),
				array('key' => 'fecharevision', 'value' => 'Revision date', 'comparation' => 'date-comparation'),
				array('key' => 'publicado', 'value' => 'Published', 'comparation' => 'boolean-comparation'),
				array('key' => 'usuario', 'value' => 'User', 'comparation' => 'comparation'),
				array('key' => 'tolid', 'value' => 'tolID', 'comparation' => 'comparation'),
				array('key' => 'categoria', 'value' => 'Category', 'comparation' => 'comparation')
			),
			'comparation' => array(
				array('key' => 'contains', 'value' => 'contains', 'content' => 'content'),
				array('key' => 'nocontains', 'value' => 'does not contain', 'content' => 'content'),
				array('key' => 'equal', 'value' => 'equal to', 'content' => 'content'),
				array('key' => 'nonequal', 'value' => 'not equal to', 'content' => 'content'),
				array('key' => 'startswith', 'value' => 'begins with', 'content' => 'content'),
				array('key' => 'endswith', 'value' => 'ends with', 'content' => 'content')
			),
			'nodetype-comparation' => array(
				array('key' => 'equal', 'value' => 'is', 'content' => 'nodetype-content')
			),
			'date-comparation' => array(
				array('key' => 'equal', 'value' => 'is', 'content' => 'date-content'),
				array('key' => 'previousto', 'value' => 'before than', 'content' => 'date-content'),
				array('key' => 'laterto', 'value' => 'after than', 'content' => 'date-content'),
				array('key' => 'inrange', 'value' => 'in the range', 'content' => array('date-content', 'date-to-content'))
			),
			'boolean-comparation' => array(
				array('key' => 'equal', 'value' => '=', 'content' => 'boolean-content'),
			)
		);
		
			//Including translations
		$filters['field'][0]['value']=_('Name');
		$filters['field'][1]['value']=_('Creation date');
		$filters['field'][2]['value']=_('Revision date');
		$filters['field'][3]['value']=_('Published');
		$filters['field'][4]['value']=_('User');
		$filters['field'][6]['value']=_('Category');
		
		$filters['comparation'][0]['value']=_('contains');
		$filters['comparation'][1]['value']=_('does not contain');
		$filters['comparation'][2]['value']=_('equal to');
		$filters['comparation'][3]['value']=_('not equal to');
		$filters['comparation'][4]['value']=_('begins with');
		$filters['comparation'][5]['value']=_('ends with');
		
		$filters['nodetype-comparation'][0]['vale']=_('is');
		
		$filters['date-comparation'][0]['value']=_('is');
		$filters['date-comparation'][1]['value']=_('before than');
		$filters['date-comparation'][2]['value']=_('after than');
		$filters['date-comparation'][3]['value']=_('in the range');
		
		return $filters;
	}
}

?>
