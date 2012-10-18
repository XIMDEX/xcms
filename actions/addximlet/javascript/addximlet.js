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

    var node_id = fn('input[name="id_node"]').val();    
    Object.getWidgetConf({
        wn: 'treeview',
        a: 'addximlet',
        onComplete: function(data) {        	
    		var tree = fn('.xim-treeview-selector');
                data.datastore.ds = new DataSource(
                {
                    method: 'get',
                    type: 'json',
                    url:  X.restUrl + '?action=addximlet&method=readtree&nodeforximlet='+node_id
                });
			tree.treeview( {
			    datastore : data.datastore,
			    colmodel : data.colmodel,
				paginator: {
					show: false
				}
			}).bind('itemClick', function (event, ui) {
				var id = $(event.target).attr('id');
				var regexp = /treeview-nodeid-(.*)/;
				var result = regexp.exec(id);
				if (result == null) {
					return;
				}
				id = result[1];
				var type = fn('#contenttype').val();
				$.getJSON(X.restUrl, 
						{method: 'getPath', id_node: id, ajax: 'json', nodetype: type},
						function(data, textStatus) {
							if (data.node == '') {
								fn('#targetfield').val('');
							} else {
								fn('#targetfield').val(id);
							}
							fn('#pathfield').val(data.node);
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
/*			tree.treeview('navigate_to_idnode', 
					fn('#id_node').val());*/
    	}.bind(this)
    });
});