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


X.actionLoaded(function (event, fn, params){
	
    Object.getWidgetConf({
        wn: 'treeview',
        a: 'browser3',
        onComplete: function(data) {
    		var tree = $('.xim-treeview-selector', params.context); 
			tree.treeview( {
			    datastore : data.datastore,
			    colmodel : data.colmodel
			}).bind('itemClick', function (event, ui) {
				var id = $(event.target).attr('id');
				var regexp = /treeview-nodeid-(.*)/;
				var result = regexp.exec(id);
				if (result == null) {
					console.log('result not found');
					return;
				}
				id = result[1];
				var type = $('#contenttype', params.context).val();
				$.getJSON(X.baseUrl + '/xmd/loadaction.php', 
						{method: 'getPath', id_node: id, ajax: 'json', nodetype: type},
						function(data, textStatus) {
							if (data.node == '') {
								$('#targetfield', params.context).val('');
							} else {
								$('#targetfield', params.context).val(id);
							}
							$('#pathfield', params.context).val(data.node);
						});
			});
			
			var tds = tree.treeview('getDatastore');
			
			tds.clear();
			tds.append( {
			        name : {
			                value : 'Proyectos',
			                visible : true 
			        },
			        nodeid: {value: 10000, visible: false},
			        icon : {
			                value : 'projects.png',
			                visible : true,
			                type : 'image'
			        },
			        children : {
			                value : 1,
			                visible : false
			        }, // Not zero!
			        isdir : {
			                value : 1,
			                visible : false
			        },
			        path : {
			                value : '/',
			                visible : false
			        }
			});
			tree.treeview('setRootModel', tds.get_model(), false, true);
			tree.treeview('navigate_to_idnode', 
					$('.id_node', params.context).val());
    	}.bind(this)
    });
});