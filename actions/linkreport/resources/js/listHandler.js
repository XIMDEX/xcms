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

function myhandler(options) {

	function getStringsearch() {
		return $(options.element).closest('fieldset').find('input[name="stringsearch"]').val();
	}

	var params = {
		method: 'get_links',
		stringsearch: getStringsearch()
	};

	if (options.actionid) params.actionid = options.actionid;
	if (options.nodeid) params['nodes[]'] = options.nodeid;
	if (options.all) params.all = options.all;
	if (options.rec) params.rec = options.rec;
	if (options.field) params.field = options.field;
	if (options.criteria) params.criteria = options.criteria;

	var p = [];
	for (var o in params) {
		p.push('%s=%s'.printf(o, params[o]));
	}
	var action_url = X.restUrl + '?' + p.join('&');

	var model = [
		{name: 'id', alias: 'nodeid', label: '', visible: false},
		{name: 'name', label: 'Name', visible: true},
		{name: 'has', label: '', visible: false},
		{name: 'desc', label: 'Descripcion', visible: true},
		{name: 'url', label: 'Url', visible: true},
		{name: 'found', label: 'Estado', visible: true}
	];

	var store = new DataStore({
		ds: new DataSource({
			method: 'post',
			type: 'json',
			url: action_url
		}),
		colModel: model,
		selector: function(data) {
			//No data found
			if (null == data || null == data.results[1] || 0 == data.results[1].length) {
				$(options.element).closest('fieldset').html("<p>"+_("No found broken links with your search criteria")+"</p>");
				return null;
			}

			return data.results[1];
		},
	});

	$(options.element)
	.listview({
		datastore: store,
		paginator: {
			show: false
		},
		colModel: model,
		url_base: X.baseUrl + '/',
		img_base: X.baseUrl + '/xmd/images/icons/'
	})
	.bind('itemClick', function(event, params) {
		// We can pass the list element as a parameter because it has
		// the path and the nodeid properties
		//$('#action_buttons').buttonbar('loadButtons', params.element);

			params.ui.select("NONE");
		})
	.bind('itemDblclick', function(event, params) {
		//if (params.data.children.value > 0) {
		//$('#listview').listview('loadFromSource', lds, params.data);
//		$(options.element).listview('loadByNodeId', params.data.nodeid.value);
		//}
		})
	.bind('afterSetModel', function(event, params) {

		var max_width_head = ( action_dimension.width - 110 )+"px";
		var max_width = ( action_dimension.width - 140 )+"px";
		$(".t_fixed_header_main_wrapper").width(max_width_head);
		$(".xim-listview-item").width(max_width);
		$(".xim-listview-table").width(max_width);

		var paginatorOptions = store.query(
			function(data, page) {
				if (page == null) {
					page = 1;
				}
			  	return {
					pages: parseInt(data.pages),
					selected: parseInt(page)
			  	};
			}
		);

		$('.links-paginator')
			.paginator()
			.paginator('loadOptions', paginatorOptions)
			.bind('pageClick', function(event, params) {
				var page = params.data;
				doSearch({page: page});
				//doSearch({data: store.source, page: page});
		});

	});

	$(options.element).listview('loadFromSource',
	store,
		{
			nodeid: {value: options.nodeid, visible: true},
			name: {value: 'ximdex', visible: true},
			icon: {value: 'root.png', visible: true, type: 'image'},
			children: {value: 2, visible: false},
			isdir: {value: 1},
			path: {value: '/', visible: false}
		}
	);

	$(window).bind("action_resize", function(event, params) {
		var max_width_head = ( params.dimension.width - 110 )+"px";
		var max_width = ( params.dimension.width - 140 )+"px";

		$(".t_fixed_header_main_wrapper").width(max_width_head);
		$(".xim-listview-item").width(max_width);
		$(".xim-listview-table").width(max_width);
	});

}

function doSearch(options) {
	var store = $('.xim-listview-results', this.element).listview('getDatastore');

	var data = store.query(function(data) {
		return data.results[options.page];
	});

	$('.xim-listview-results', this.element).listview('setModel', data);

	$('.links-paginator')
		.paginator('loadOptions', {pages: data.pages, selected: options.page})
		.bind('pageClick', function(event, params) {
			var page = params.data;
			doSearch({page: page});
	});
}
