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
 *  @version $Revision: 8362 $
 */


window.com.ximdex = Object.extend(window.com.ximdex, {
	treeview: {
		colmodel: null,
		datastores: {}
	}
});

(function(X) {

	$.widget('ui.treeview', {

		id: null,
		selected: null,
		selection: null,
		container: null,
		root_model: null,
		createNodeListeners: null,
		time_refresh: null,

		_init: function() {

			var $this = this.element;

			this.selection = new X.Collection({unique: true});
			this.createNodeListeners = [];

			var gv = X.widgetsVars.getWidget(this.getId());
			if (gv) {
				for (var o in gv) {
					switch (o) {
						case 'paginator':
							break;
						case 'rowModel':
							break;
						case 'paginatorShow':
							this.options.paginator.show = gv[o];
							break;
						case 'paginatorName':
							this.options.paginator.name = gv[o];
							break;
						case 'paginatorClassName':
							this.options.paginator.className = gv[o];
							break;
						case 'paginatorDefaultValue':
							this.options.paginator.defaultValue = gv[o];
							break;
						case 'paginatorView':
							this.options.paginator.view = gv[o];
							break;
						default:
							this.options[o] = gv[o];
							break;
					}
				}
			}


			/** TAB INFO OPEN WITH CTRL+ALT+i */
			$(document).keydown(function(event) {
				//ctrl+alt+i

				//ctrl+alt+i
				if(event.ctrlKey && event.altKey && 73 == event.which && null != this.selected) {
					var data = $(this.selected).data("data");
					console.log(data);

					var selected = data["nodeid"].value;
					//console.log(selected, event, this, this.selected);

					parent.$('#bw1').browserwindow('openAction', {
						label: _("Node Info"),
						name: _("Node Info"),
						command: 'infonode',
						params: 'method=index&nodeid='+selected,
						nodes: selected,
						url: X.restUrl + '?action=infonode&nodes[]='+selected+'&nodeid='+selected,
						bulk: '0'
					},selected);


					event.originalEvent.stopPropagation();
					event.originalEvent.preventDefault();
				}

				if(event.ctrlKey && event.altKey && 69 == event.which && null != this.selected) {
					var data = $(this.selected).data("data");

					var selected = data["nodeid"].value;
					var nodetype= data["nodetypeid"].value;

					if(5032 == nodetype) {
						window.open (X.restUrl + "?action=xmleditor2&method=load&nodes[]="+selected+"&nodeid="+selected, "Editor");
						event.originalEvent.stopPropagation();
						event.originalEvent.preventDefault();
					}

				}

			}.bind(this));

			this.options.datastore = new DataStore(this.options.datastore);

			$this.addClass('xim-treeview-container');
/*
			$this.resizable({
				//autoHide: true,
				containment: 'parent',
				handles: 'e',
				maxWidth: 500,
				minWidth: 40
			});
*/

			var btnReload = $('<div></div>')
				.html( _("Reload node"))
				.addClass('xim-treeview-btnreload ui-corner-all ui-state-default')
				.click(function(event, params) {
						this.refresh(this.selected);
						this.element.focus();
				}.bind(this));
			$this.append(btnReload);

			this.container = $('<div></div>').addClass('xim-treeview-branch-container').appendTo($this);

			if (this.options.paginator && this.options.paginator.show && $.ui['itemsSelector']) {

				var paginator = $('<div></div>').itemsSelector(this.options.paginator)
				.bind('itemClick', function(event, params) {
					this.options.paginator.defaultValue = params.data.value;
					this.refresh(this.selected);
					this.focus();
				}.bind(this));
				btnReload.after(paginator);
			}


		},


		getId: function() {

			var a_id = $(this.element).attr('id');
			var b_id = X.session.get('projectsview.treeview.id');

			var id = a_id
				|| b_id
				|| 'treeview_' + X.getUID();

			if (Object.isEmpty(a_id)) {
				$(this.element).attr('id', id);
			}

			if (Object.isEmpty(b_id)) {
				X.session.set('projectsview.treeview.id', id);
			}

			this.id = id;
			this.getId = function() {
				// Lazy function definition
				return this.id;
			}.bind(this);

			return this.id;
		},

		_showLoading: function(parent) {
			var container = $('<ul></ul>')
				.addClass('xim-treeview-loading')
				.attr('id', 'treeloading-'+$(parent).data('nodeid'));
			var loading = $('<img></img>')
				.attr('src', this.options.url_base + this.options.loading_icon);
			$(parent).append(container.append('<li></li>').append(loading));
		},
		_hideLoading: function(parent) {
			// WARNING: Use the attribute format: "[id=xxx]"
			// Using the ID format ("#xxx") will not work with XVFS paths
			$('[id=treeloading-'+$(parent).data('nodeid')+']', parent).remove();
		},
		_setDraggable: function(node) {
			var data = $(node).data("data");
			var origin = 0;
			$(node).draggable({
				helper: function(event) {
					var target = event.currentTarget;
					var icon = $('.xim-treeview-icon:first', target).clone();
					var label = $('.xim-treeview-label:first', target).clone();

					return $('<div/>')
						.addClass('xim-treeview-node')
						.append(icon)
						.append(label)
						.data('selection', [$(target).data('data')]);
				}.bind(this),

				start: function(event) {
				  event.originalEvent.stopPropagation();
				  event.originalEvent.preventDefault();
				  try {
				   var data = $(node).data("data");
				   origin = data.nodeid.value;
				  }catch(e) {
					 return null;
				  }

				}.bind(this),

				stop: function(event) {
					 event.originalEvent.stopPropagation();
					 event.originalEvent.preventDefault();

					 try {
					 	var element = event.srcElement ? event.srcElement : event.originalTarget;
					    var dest = $(element).closest('li');
						var data = $(dest).data("data");
						if ( null == data || data.isdir.value != 1) { return null }
						var nodeid = data.nodeid.value;
					 }catch(e) {
						return null;
					 }

					 parent.$('#bw1').browserwindow('openAction', {
						   label: _("Move node"),
							name: _("Move node"),
							command: 'movenode',
							params: 'method=confirm_move&draggable=1&targetid='+nodeid+'&nodeid='+origin,
							nodes: origin,
							url: X.restUrl + '?action=movenode&nodes[]='+origin+'&nodeid='+origin+'&targetid='+nodeid+'&draggable=1',
							bulk: '0'
							},origin);
				}.bind(this),

				revert: false,
				cursor: 'pointer',
				appendTo: 'body',
				containment: '#body',
				zIndex: 3000
			});

			/* *************** DRAG & DROP FILES ******* */
			//only folder node
	 	 	if ( data.isdir.value == 1 ) {
				$(node).bind('dragover', function(event) {

					  event.originalEvent.stopPropagation();
					  event.originalEvent.preventDefault();
						$('div:first', node).addClass('xim-treeview-container-selected');
						var dragstart = $(node).data("dragstart");
						//Only one dragover
						if(1 == dragstart)
							return node;
						else
							$(node).data("dragstart",1)

						if (!$(node).hasClass('xim-treeview-expanded')) {
						  if(null != this.time_refresh) {
							 clearTimeout(this.time_refresh);
						  }
						  this.time_refresh=setTimeout(function() { this.expand(node); }.bind(this), 700);
						}

			 	 	}.bind(this));

			 	 	$( node).bind('dragleave', function(event) {
							$(node).data("dragstart",0)
							$('div:first', node).removeClass('xim-treeview-container-selected');
		 	 	 		  event.originalEvent.stopPropagation();
						  event.originalEvent.preventDefault();
			 	 	}.bind(this));


			 	 	$(node).bind('dragenter', function(event) {
						$(node).data("dragstart",0)
						$('div:first', node).removeClass('xim-treeview-container-selected');
		 	 	 		  event.originalEvent.stopPropagation();
						  event.originalEvent.preventDefault();
			 	 	}.bind(this));



			 	 	$(node).bind('drop', function(event) {

		 	  		  event.originalEvent.stopPropagation();
					  event.originalEvent.preventDefault();

			 	 		try {
							var data = $(node).data("data");
							if (  null == data.canUploadFiles || 0 == data.canUploadFiles.value  ) { return null; }
							var nodetype = data.nodetypeid.value;
							var nodeid = data.nodeid.value;
							var uploadFiles =  data.canUploadFiles.value;
							var files = event.originalEvent.dataTransfer.files;
						}catch(e) {
							return null;
						}



	 	 			if( uploadFiles) {
					  parent.$('#bw1').browserwindow('openAction', {
						   label: _("Add files"),
							name: _("Add files"),
							command: 'fileupload_common_multiple',
							params: '',
							nodes: nodeid,
							url: X.restUrl + '?action=fileupload_common_multiple&method=index&nodes[]='+nodeid,
							bulk: '0'
							},nodeid
						);

						$(X.browser.actionEventSelector).one("widgetLoaded", function(event, options) {
							var uploader = options.widget;
							uploader.addFiles(this.originalEvent.dataTransfer.files);
						}.bind(event));
						//console.log(X.widgetsVars.last, $(X.widgetsVars.last));
				  }

					$(node).click();

		 	 	}.bind(this));
			}



			return node;
		},

		addCreateNodeListener: function(cb) {
			if (!Object.isFunction(cb)) return;
			this.createNodeListeners.push(cb);
		},

		_createNode: function(data) {
			var id;
			if (data.backend && !Object.isEmpty(data.backend.value)) {
				id = data.backend.value + ':treeview-nodeid-'+data.nodeid.value;
			} else {
				id = 'treeview-nodeid-'+data.nodeid.value;
			}
			var node = $('<li></li>')
				.addClass('xim-treeview-node xim-treeview-collapsed')
				.append(this._createLabel(data))
				.attr('id', id);

			node.data('widget', this);
			node.data('data', data);


			if (data.isdir.value == 1) {
				$('div:first > span.ui-icon', parent)
					.addClass('ui-icon ' + this.options.collapsed_icon);
			} else {
				node.addClass("isLeaf");
			}


			node
				.click(function(event) {

					event.stopPropagation();

					if (!event.ctrlKey) {
						this.selection.clear();
					}
					this.selection.add(node.get(0));

					node.focus();
					this.highlightSelection();

					// Fire event if this is not part of a dblClick and current node isn't already selected
					// En IE7 event.detail devuelve null
					//if ($(this.selected)[0] != node) {
						this.select(node);
					//}

					node.trigger('itemClick', [{ui: $(this).data('widget'), element: node, data: data}]);

				}.bind(this))
		/*				.mousedown(function(event) {
		console.log('mouseDown');
					event.stopPropagation();
					/ *if (!event.ctrlKey) {
						this.selection.clear();
					}
					this.selection.add(node.get(0));* /
				}.bind(this))*/
				.dblclick(function(event) {
					event.stopPropagation();
					var data = $(this).data('data');
					if (data.isdir.value == 0) return;
					$(this).data('widget').toggle(this);
					node.focus();
				})
				.bind('contextmenu', function(event) {
					event.stopPropagation();

					if (!event.ctrlKey) {
						this.selection.clear();
					}
					this.selection.add(node.get(0));

					node.focus();
					//this.highlightSelection();

					// Fire event if this is not part of a dblClick and current node isn't already selected
					// En IE7 event.detail devuelve null
					//if ($(this.selected)[0] != node) {
						this.select(node);
					//}
					node.trigger('itemContextmenu', [{ui: $(this).data('widget'), element: node, data: data, event:event, selection: this.selection}]);
					return false;
				}.bind(this));

			node = this._setDraggable(node);
			for (var i=0,l=this.createNodeListeners.length; i<l; i++) {
				var cb = this.createNodeListeners[i];
				node = cb(this, node);
			}



			return node;
		},
		_createImage: function(data) {
			var cadena = data.icon.value.substring(0, data.icon.value.length - 4 );
			var image = $('<span></span>')
				.addClass('xim-treeview-icon')
				.addClass('icon-' + cadena);
				//.attr('src', this.options.img_base + data.icon.value);

			return image;
		},
		_createLabel: function(data) {
			var lbl = $('<div></div>');
			var lblIcon = $('<span></span>');

			if (data.isdir.value != 1 /*|| data.children.value == '0'*/) {
				lblIcon.addClass('ui-no-icon');
			} else {
				var parent = $(this).parents('.xim-treeview-node:first');
				lblIcon
					.addClass('ui-icon ' + this.options.collapsed_icon)
					.click(function(event) {
						event.stopPropagation();
						var parent = $(this).parents('.xim-treeview-node:first');
						parent.data('widget').toggle(parent);
						parent.focus();
					})
					.dblclick(function(event) {
						event.stopPropagation();
						parent.focus();
					});
			}

			lbl.append(lblIcon);

			/* behavior for breadcrumbs */
			if (this.options.behavior == 'selectable') {
				if (data.nodetypeid && (data.nodetypeid.value == 5032 || data.nodetypeid.value == 5039)) {
					var lblCheck = $('<input />').attr('type', 'checkbox');
					if (data.is_section_index && data.is_section_index.value == '1') {
						lblCheck.attr('checked', 'checked');
					}
					url_base = this.options.url_base;

					// Se podr�a hacer m�s simple marcando los
					// nodos con la secci�n a la que pertenecen
					lblCheck.click(function(event) {
						checked = $(this).attr('checked') ? '1' : '0';
						$.post(
							url_base + '/xmd/loadaction.php?method=set_index_for_section&mod=ximTHEMES',
							{
								action : 'composer',
								mod : 'ximTHEMES',
								method : 'set_index_for_section',
								id_node : data.nodeid.value,
								checked : checked
							},
							function(args) {
								if (args['UNCHECK']) {
									$('#treeview-nodeid-' + args['UNCHECK'])
										.find('input')
										.attr('checked', '');
								}
								if (args['CHECK']) {
									$('#treeview-nodeid-' + args['CHECK'])
										.find('input')
										.attr('checked', 'checked');
								}
							}, 'json');
						});
					lbl.append(lblCheck);
				}
			}
			/* end of behavior */

			if (data.icon.visible) lbl.append(this._createImage(data));
			lbl.append($('<span></span>').addClass('xim-treeview-label').html(data.name.value));
			lbl.addClass(data.icon.value.substring(0, data.icon.value.length - 4 ));
			return lbl;
		},
		loadByNodeId: function(nodeid) {
			var node = null;
			if (typeof(nodeid) != 'object') {
				node = this.getNodeById(nodeid);
			} else {
				node = nodeid;
			}
			var data = $(node).data('data');
			this.loadFromSource(this.options.datastore, data /*{nodeid: {value: nodeid}}*/);
		},
		loadFromSource: function(dstore, data, parent, expand, selected_node, list) {

//			var object = this;
//		    if(list instanceof Array) {
//		    	if (dstore.options.ds.running){
//		    		setTimeout(function(){
//		    			object.loadFromSource(dstore, data, parent, expand, selected_node, list);
//		    			clearTimeout();
//		    		}, 250);
//		    		return;
//		    	}
//		    }

		    if (dstore.options.ds.running) {
		    	return;
		    }

			if (!parent) parent = this.container;

/*
			// ???

			if ($(parent).hasClass('xim-treeview-expanded')) {
				var shifted_node;
				if (list && list.length > 0) {
			        shifted_node = list.shift();
			        this.expand(shifted_node, list);
				}
				return;
			}

			// ???
*/

			$('ul', parent).unbind().remove();
			this._showLoading(parent);
			dstore.load_data({
					params: data,
					options: this.options
				},
				function(store) {

					this.setModel(store.get_model(), parent, expand, selected_node, list);
//					console.log(list);
//					if (list instanceof Array) {
//			    		this.loadFromSource(dstore, data, parent, expand, selected_node, list);
//			    	}
				}.bind(this)
			);
		},
		getModel: function() {
			//return this.model;
		},
		setRootModel: function (model, parent, expand, selected_node) {
			this.root_model = model;
			this.setModel(model, parent, expand, selected_node);
		},
		setModel: function(model, parent, expand, selected_node, list) {
			if (!parent) parent = this.container;
			$('ul', parent).unbind().remove();


			if (!model) model = [];
			var branch = $('<ul></ul>').addClass('xim-treeview-branch');

			for (var i=0; i < model.length; i++) {
				var data = model[i];
				$(branch).append(this._createNode(data));
				$(parent).append(branch);
			}

			$(parent).data('model', model);

			if (expand) {
				this.expand($('li.xim-treeview-node:first', branch));
			}

			$(branch).show();
			$(parent)
				.removeClass('xim-treeview-collapsed')
				.addClass('xim-treeview-expanded');
			$('.ui-icon:first', parent)
				.removeClass(this.options.collapsed_icon)
				.addClass(this.options.expanded_icon);
			$(parent).trigger('expand', [{ui: this, element: parent, data: $(parent).data('data')}]);
			if (selected_node != undefined) {
				this.select($('#' + selected_node));
			}
			if (typeof(list) == 'object') {
			    if (list.length > 0) {
			        var id_node = list[0];
			        list.shift();
					this.expand(id_node, list);
			    }
			}
		},
		refresh: function(node, list) {
			if (typeof(node) != 'object') node = this.getNodeById(node);
			if (node == undefined) return;
			var selected_node = $('.xim-treeview-selected', this.element).closest('li').attr('id');
			this.loadFromSource(this.options.datastore, $(node).data('data'), node, false, selected_node, list);
		},
		toggle: function(node) {
			var widget = $(node).data('widget');
			if ($(node).hasClass('xim-treeview-expanded')) {
				widget.collapse(node);
			} else {
				widget.expand($(node));
			}
		},
		expand: function(node, list) {

			var nodeString;
			var is_dom_object;
			var nodeId = node.nodeid;

			// code to expand on demand a node
			// call come from navigate_to_nodeid
			if (Object.isObject(node) && node.backend != undefined) {
				is_dom_object = false;
		    // an object is received, so we already have an object
		    } else {
		    	if (node.nodeid > 0) {
		    		is_dom_object = false;
		    	} else {
		    		is_dom_object = Object.isObject(node);
		    	}
		    }

			var tmpString;

		    if (!is_dom_object) {
		    	tmpString = this.escape('#' + node.backend + ':treeview-nodeid-' + node.bpath);
		    	nodeString = $(tmpString, this.getRoot());
		    	if (!(nodeString.length > 0)) {
		    		tmpString = this.escape('#' + node.backend + ':treeview-nodeid-' + node.nodeid);
		    		nodeString = $(tmpString, this.getRoot());
		    	}
		    	if (!(nodeString.length > 0)) {
		    		tmpString = this.escape('#treeview-nodeid-' + node.nodeid);
		    		nodeString = $(tmpString, this.getRoot());
		    	}
		    	// for root node
		    	if (!(nodeString.length > 0)) {
		    		tmpString = (this.escape("#treeview-nodeid-" + node.bpath));
		    		nodeString = $(tmpString, this.getRoot());
		    	}
		    	node = nodeString;
		    }
		    /* end of functionality*/
		    if (node.length > 0) {
				var widget = node.data('widget');
				var data = node.data('data');

				if (widget) {
					widget.refresh(node, list);
				}
		    } else {
		    }

			if (this.selectedId && (nodeId == this.selectedId)){

				var selNode = $('#treeview-nodeid-'+nodeId, this.element).get(0);
				this.selection.clear().add(selNode);
				this.select(selNode);
				$("div.xim-search-panel div.results").scrollTop($('#treeview-nodeid-'+nodeId, this.element).position().top);
			}

			// NOTE: Expand algorithm on setModel()
			/*$('ul.xim-treeview-branch:first', node).show();
			$(node)
				.removeClass('xim-treeview-collapsed')
				.addClass('xim-treeview-expanded');
			$('.ui-icon:first', node)
				.removeClass(widget.options.collapsed_icon)
				.addClass(widget.options.expanded_icon);
			$(node).trigger('expand', [{ui: widget, element: node, data: data}]);*/
		},
		collapse: function(node) {
			var widget = $(node).data('widget');
			$('ul.xim-treeview-branch:first', node).hide();
			$(node)
				.removeClass('xim-treeview-expanded')
				.addClass('xim-treeview-collapsed');
			$('.ui-icon:first', node)
				.removeClass(widget.options.expanded_icon)
				.addClass(widget.options.collapsed_icon);
			var data = $(node).data('data');
			$(node).trigger('collapse', [{ui: widget, element: node, data: data}]);
		},
		select: function(node) {
			var widget = $(node).data('widget');
			if (!widget) {
				return;
			}
			this.highlightSelection();
			widget.selected = node;
			var data = $(node).data('data');
			$(node).trigger('select', [{ui: widget, element: node, data: data}]);
		},
		highlightSelection: function() {
			$('.xim-treeview-selected', this.element).removeClass('xim-treeview-selected');
			$('.xim-treeview-container-selected', this.element).removeClass('xim-treeview-container-selected');

			this.selection.get().each(function(id, item) {
				$('.xim-treeview-label:first', item).addClass('xim-treeview-selected');
				$('.xim-treeview-label:first', item).parent().addClass('xim-treeview-container-selected');
			}.bind(this));
		},
		getSelected: function() {
			return this.selected;
		},
		getSelection: function() {
			return this.selection;
		},
		getNodeById: function(nodeid) {
			return $('[id=treeview-nodeid-'+nodeid+']', this.element);
		},
		getRoot: function() {
			return this.element;
		},
		getDatastore: function(datastore) {
			return this.options.datastore;
		},
		setDatastore: function(datastore) {
			this.options.datastore = datastore;
		},
		navigate_to_idnode: function(path) {
			var node_list;
			var shifted_node;
			$.getJSON(
				X.restUrl + '?method=getTraverseForPath&ajax=json&nodeid=' + path,
				function(data) {
					var root_path = this.root_model[0].nodeid.value;					
					node_list = data['nodes'];
					while(node_list.length > 0) {
						shifted_node = node_list.shift();
				        if (shifted_node.bpath == root_path || shifted_node.nodeid == root_path) {
					        this.expand(shifted_node, node_list);

							// Highlight the selected node
					        var nodeId = node_list[node_list.length-1];
					        if (nodeId) {
					        	nodeId = nodeId.nodeid;
						        var selNode = $('#treeview-nodeid-'+nodeId, this.element).get(0);
						        this.selection.clear().add(selNode);
						        this.select(selNode);

					       }

					       break;
				        }
					}
				}.bind(this)
			);
		},
		navigate_to_idnode_from_project: function(path) {
			this.selectedId = path;
			var node_list;
			var shifted_node;
			$.getJSON(
				X.restUrl + '?method=getTraverseForPath&ajax=json&nodeid=' + path,
				function(data) {
					node_list = data['nodes'];
					var root_path = node_list[1]["nodeid"];
					while(node_list.length > 0) {
						shifted_node = node_list.shift();
				        	if (shifted_node.bpath == root_path || shifted_node.nodeid == root_path) {
					        	this.expand(shifted_node, node_list);
					        
							// Highlight the selected node
						        var nodeId = node_list[node_list.length-1];
						        if (nodeId) {
						        	nodeId = nodeId.nodeid;
							        var selNode = $('#treeview-nodeid-'+nodeId, this.element).get(0);
							        this.selection.clear().add(selNode);
							        this.select(selNode);
							}
							break;
						}

					}
				}.bind(this)
			);
		},

		setSelectedId: function(selectedId){
			this.selectedId = selectedId;
		},
		navigate_to_idnode_list: function(list) {
			var item;
			while(list.length > 0) {
				item = list.shift();
				this.navigate_to_idnode(item);
			}
		},
		escape: function (string) {
            string = string.replace(/\//g, '\\/');
            string = string.replace(/:/g, '\\:');
            string = string.replace(/\./g, '\\.');
            string = string.replace(/ /g, '\\ ');
            return string;
		},

		options: {
			datastore: null,
			useXVFS: false,
			colModel: null,
			behavior: null,
			no_icon: 'ui-no-icon',
			collapsed_icon: 'ui-icon-triangle-1-e',
			expanded_icon: 'ui-icon-triangle-1-se',
			loading_icon: '/actions/browser3/resources/images/loading.gif',
			url_base: X.baseUrl,
			img_base: '',
			paginator: {
				show: false,
				name: 'treeview',
				className: 'xim-treeview-itemsSelector',
				view: 'radio',
				defaultValue: 50
			}
			/*collapsed_icon: 'ui-icon-carat-1-e',#xvfs_backend_xnodes\\:treeview-nodeid-10000
			expanded_icon: 'ui-icon-carat-1-se'*/
		},

		getter: [
			'getId',
			'getSelected',
			'getSelection',
			'getModel',
			'getNodeById',
			'getOptions',
			'getRoot',
			'getDatastore',
			'escape'
		]
	});

})(com.ximdex);
