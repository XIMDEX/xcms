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


X.actionLoaded(function(event, fn, params)  {
    var scope = X.angularTools.initView(params.context, params.tabId);
    scope.$apply(function(){scope.viewData = params.action.data});
});

// X.actionLoaded(function(event, fn, params) {

// 	var $browser = params.browser;
// 	var $actionView = params.actionView;
// 	var results = params.actionView.action.data;

// 	var colModel = [
// 		{name: 'icon', label: '', visible: true, type: 'image', width: '24px'},
// 		{name: 'isdir', label: '', visible: false},
// 		{name: 'nodeid', label: 'Id', visible: true, type: 'number', align: 'center', width: '30px', sortable: false},
// 		{name: 'parentid', label: _('ParentId'), visible: false},
// 		{name: 'name', label: _('Name'), visible: true, sortable: false},
// 		{name: 'nodetype', label: _('Node type'), visible: true, sortable: false},
// 		{name: 'abspath', alias: 'path', label: _('Path'), visible: true, sortable: false},
// 		{name: 'creationformated', label: _('Creation'), visible: true, align: 'center', sortable: false},
// 		{name: 'versionnumber', label: _('Version'), visible: true, align: 'center', sortable: false},
// 		{name: 'versiondateformated', label:  _('Modification'), visible: true, align: 'center', sortable: false}
// 	];

// 	var ds = new DataStore({
// 		ds: results.data,
// 		colModel: colModel,
// 		//queryParams: null,
// 		selector: function(data) {
// 			return data;
// 		}
// 	});

// 	ds.load_data();

// 	var conf = {
// 		rootId: '/',
// 		renderer: 'Grid',
// 		paginator: {
// 			show: false
// 		},
// 		showBrowser: false,
// 		draggables: false,
// 		url_base: window.url_root + '/',
// 		img_base: window.url_root + '/xmd/images/icons/',
// 		colModel: colModel
// 	};

// 	fn('.xim-listview-results')
// 		.listview(conf)
// 		.bind('actionsDropdown', function(event, params) {

// 			var selector = params.event.target;
// 			var selection = params.selection;

// 			var nodes = selection;

// 			// Get the selector position before the Ajax request...
// 			var pos = $(selector).offset();
// 			pos = {x: pos.left + $(selector).width(), y: pos.top};

// 			$browser.browserwindow('getActions', {
// 				nodes: nodes,
// 				cb: $actionView.createFloatMenu.bind($actionView),
// 				data: $(params.element).data('data'),
// 				selector: selector,
// 				menuPos: pos
// 			});
// 		}.bind(this))
// 		.listview('addCreateNodeListener', function(widget, node) {

// 			$('<span></span>')
// 				.addClass('xim-listview-actions-dropdown')
// 				.addClass('ui-icon ui-icon-triangle-1-e')
// 				.mousedown(function(event) {

// 					var w = this.element;
// 					var target = event.target;
// 					var isSelected = $(target).closest('.xim-listview-selected').length > 0 ? true : false;
// 					var selection = [$(node).data('data')];

// 					if (isSelected) {
// 						// Stoping propagation here prevent the clear of selected nodes
// 						event.stopPropagation();
// 						selection = $(w).listview('getSelection').get();
// 					}

// 					w.trigger('actionsDropdown', [{
// 						ui: w,
// 						element: node,
// 						data: $(node).data('data'),
// 						event: event,
// 						selection: selection
// 					}]);
// 				}.bind(widget))
// 				.appendTo($('.xim-listview-icon', node).parent());

// 			return node;
// 		})
// 		.listview('setModel', results.data);
		
// 		var searchedTerm = results.query.filters[0].content;
		
// 		$divResults = jQuery("<div/>").addClass("results_info");
// 		var $divSearch = jQuery("<div/>").html(_("Search criteria"));
// 		var $searchTermList = jQuery("<ul/>").appendTo($divSearch);
// 		jQuery("<li/>").html(_("Name")+": "+searchedTerm).appendTo($searchTermList);
// 		if(!isNaN(parseInt(searchedTerm)))
// 			jQuery("<li/>").html(_("Node")+": "+searchedTerm).appendTo($searchTermList);
		
// 		$divSearch.appendTo($divResults);
// 		jQuery("<div/>").html(_("Number of results")+": "+results.records).appendTo($divResults);
			
// 		fn('.xim-listview-results').prepend($divResults);
// });
