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
 *  @version $Revision: 8529 $
 */



/**
 * This array has to be maintained manually.
 * Keeps track of widgets dependencies and the inclusion order of files.
 */
return array(
	'common' => array(
		'deps' => array(),
		'js' => array(
			'datasource.js',
			'datastore.js',
			'datastores.js',
			'queryProcessor.js',
			'canvas.js',
			'formsManager.js',
			'widgetsManager.js',
//			'actionsManager.js',
			'statusbar_panels.js',
			'ximmenuHooks.js'
		)
	),
	'browserwindow' => array(
		'deps' => array('hbox', 'listview', 'treeview', 'contextmenu', 'spotlight'),
		'js' => array(
			'browserwindow.js',
			'abstractView.js',
			'dataView.js',
			'panels.js',
			'controlcenter.js',
			'projects.js',
			'reports.js',
			'sets.js',
			'modules.js',
			'actions.js',
			'dialogs.js',
			'history.js',
			'usersList.js'
		)
	),
	'buttonbar' => array(
		'deps' => array(),
		'js' => array('buttonbar.js')
	),
	'calendar' => array(
		'deps' => array(),
		'js' => array(
			'calendar.js',
			'ValidityCalendar.js'
		)
	),
	'tagsinput' => array(
		'deps' => array(),
		'js' => array('tagsinput.js',
			      'writingtag.js',
			      'taglist.js',
			      'tag.js'
		)
	),

	'uploader' => array(
		'deps' => array(),
		'js' => array('uploader.js',
			      'ximfile.js'
		)
	),
	'contextmenu' => array(
		'deps' => array(),
		'js' => array(
				'contextmenu.js', 
				'jquery.contextMenu.js', 
				'jquery.ui.position.js'
		)
	),
	'hbox' => array(
		'deps' => array(),
		'js' => array('hbox.js'),
	),
	'itemsSelector' => array(
		'deps' => array(),
		'js' => array('itemsSelector.js')
	),
	'listview' => array(
		'deps' => array('itemsSelector'),
		'js' => array(
			'fix.jquery.events.js',
			'listview.js',
			'listviewRenderer_Columns.js',
			'listviewRenderer_Details.js',
			'listviewRenderer_Grid.js',
			'listviewRenderer_Icon.js',
			'listviewRenderer_List.js',
			'selections.js',
			'jquery.fixheadertable.js'
		)
	),
	'menubar' => array(
		'deps' => array(),
		'js' => array(
			'jquery.clickmenu.js',
			'menubar.js'
		)
	),
	'paginator' => array(
		'deps' => array(),
		'js' => array('paginator.js')
	),
	'qpsearch' => array(
		'deps' => array(),
		'js' => array(
			'datastore.js',
			'qpsearch.js'
		)
	),
	'searchpanel' => array(
		'deps' => array('paginator'),
		'js' => array(
			'searchpanel.js',
			'AdvancedSearch.js',
			'LastSearches.js',
			'SavedSearches.js',
			'SearchFilters.js',
			'ResultsView.js'
		)
	),
	'statusbar' => array(
		'deps' => array(),
		'js' => array('statusbar.js')
	),
	'tabs' => array(
		'deps' => array(),
		'js' => array(
			'tabs.js',
		)
	),
	'toolbox' => array(
		'deps' => array(),
		'js' => array('toolbox.js')
	),
	'treeview' => array(
		'deps' => array('itemsSelector'),
		'js' => array(
			'treeview.js'
		)
	),
	'spotlight' => array(
		'deps' => array(),
		'js' => array(
			'spotlight.js'
		)
	)
);

?>
