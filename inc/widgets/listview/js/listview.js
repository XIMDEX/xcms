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

window.com.ximdex = Object.extend(window.com.ximdex, {
	listview: {
		colmodel: null,
		datastores: {}
	}
});

/**
 * Extend window.com.ximdex object with preferences from server
 */
$.get( window.com.ximdex.baseUrl+"/xmd/loadaction.php?action=browser3&method=getPreferences", function( data ) {
    window.com.ximdex = Object.extend(window.com.ximdex, {
        preferences:data.preferences
    });

    (function($) {

        $.widget('ui.listview', {

            list: null,
            renderer: null,
            selected: null,
            parent: null,
            browser: null,
            model: null,
            selection: null,
            createNodeListeners: null,
            sorts: null,

            _init: function() {

    //			console.info('Listview: ', this);

                var widget = this;
                var $this = this.element;
                this.sorts = {};
                this.createNodeListeners = [];
                this.selection = new X.listview.ItemSelections({
                    unique: true,
                    // Overwrite contains() method because there are problems with object
                    // references in the comparation when the renderer changes.
                    contains: function(item) {
                        for (var i=0, l=this.collection.length; i<l; i++) {
                            if (this.collection[i].nodeid.value == item.nodeid.value) {
                                return true;
                            }
                        }
                        return false;
                    }
                });

                this.options.datastore = new DataStore(this.options.datastore);

                var gv = X.widgetsVars.getWidget($this.attr('id'));
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

                this.setRenderer(this.options.renderer || 'Grid');

                if (this.options.paginator.show && $.ui['itemsSelector']) {

                    var paginator = $('<div></div>')
                        .itemsSelector(this.options.paginator)
                        .bind('itemClick', function(event, params) {

    //						this.options.paginator.defaultValue = params.data.value;

                            $(this.element).trigger('paginatorChange', [{ui: widget, element: this.element, data: params.data}]);
                            // NOTE: This event is captured by the listview object (browser.js)
                            // but it's fired by diferent objects ???
                            // Call stopPropagation method explicity
                            event.stopPropagation();
                        }.bind(this));
                    $this.append(paginator);
                }

                $this.addClass('xim-listview-container');

    //			this._registerKeyEvents();

                $(this.element).droppable({
                    tolerance: 'pointer',
                    drop: function(event, ui) {
                        $(this.element).trigger('itemDrop', [{data: $(ui.draggable).data('data')}]);
                    }.bind(this),
                    accept: '.xim-treeview-node'
                });

                this._showSelectionHandlers();
            },
            clearSorts: function() {
                this.sorts = {};
            },
            _showSelectionHandlers: function() {

                if (!this.options.showSelectionHandlers) return;

                var handler = function(event) {
                    this.select($(event.target).val().toUpperCase());
                }.bind(this);

                var $div = $('<div></div>').addClass('xim-listview-selectionhandler').appendTo(this.element);

                $('<button/>').addClass('select-all').html(_('All')).val('ALL').click(handler).appendTo($div);
                $('<button/>').addClass('select-none').html(_('None')).val('NONE').click(handler).appendTo($div);
                $('<button/>').addClass('select-invert').html(_('Invert')).val('INVERT').click(handler).appendTo($div);
            },
            _registerKeyEvents: function() {

                // FIXME: The callback is called twice! �?
                $(document)
                    .keypress(function(event) {
                        if (!event.ctrlKey) return;
                        var char = String.fromCharCode(event.charCode);
    //				console.log(event.keyCode, event.charCode, char);
                        var renderer = null;
                        if (char == '1') {
                            renderer = 'Icon';
                        } else if (char == '2') {
                            renderer = 'Grid';
                        } else if (char == '3') {
                            renderer = 'List';
                        } else if (char == '4') {
                            renderer = 'Columns';
                        } else {
                            return;
                        }
                        this.setRenderer(renderer);
                    }.bind(this));
            },
            _showLoading: function() {

                if ($('.xim-listview-loading', this.element).length > 0) return;

                var container = $('<div></div>')
                    .addClass('xim-listview-loading');
                var loading = $('<img></img>')
                    .attr('src', this.options.url_base + this.options.loading_icon);
                $(this.element).append(container.append(loading));
            },
            _hideLoading: function() {
                if ($('.xim-listview-loading').length == 0) return;
                $('.xim-listview-loading', this.element).remove();
            },
            clearList: function(showLoading) {

                showLoading = showLoading || false;

                var $this = this.element;
                if (this.list) $(this.list).unbind().remove();

                var listContainer = $('div.xim-listview-table-container', $this);
                if (listContainer.length == 0) {
                    listContainer = $('<div></div>').addClass('xim-listview-table-container');
                    $this.append(listContainer);
                }

                //this.list = this.renderer.clear(listContainer);

                if (showLoading) this._showLoading();
                return listContainer;
            },
            addCreateNodeListener: function(cb) {
                if (!Object.isFunction(cb)) return;
                this.createNodeListeners.push(cb);
            },
            loadByNodeId: function(nodeid) {

                if (nodeid === null) return;
                var data = null;

                if (!Object.isObject(nodeid)) {

                    data = $(this.getNodeById(nodeid)).data('data');
                    if (data === undefined) {
                        data = {
                            nodeid: {value: nodeid},
                            isdir: {value: 1}
                        };
                    }
                } else if (Object.isObject(nodeid.ownerDocument)) {

                    data = $(nodeid).data('data');
                } else {

                    data = nodeid;
                }

                this.parent = data;
    //			console.log(data, nodeid);
                this.loadFromSource(this.options.datastore, data /*{nodeid: {value: nodeid}}*/);
            },
            loadFromSource: function(dstore, data) {

                if (data === null) return;

                if (!data.isdir && Object.isObject(data)) {
    //				console.error(data, $(data).data('data'));
                    data = $(data).data('data');
                }

                if (data.isdir.value == 0) return;
                if (dstore.options.ds.running) return;
                this.clearList(true);

                dstore.load_data(
                    {
                        params: data,
                        options: this.options
                    },
                    function(store) {
                        this.parent = data;
                        this.setModel(store.get_model());
                    }.bind(this)
                );
            },
            getDatastore: function() {
                return this.options.datastore;
            },
            setRenderer: function(renderer) {

                if (typeof(renderer) == 'function' && !(renderer['clear'] && renderer['createView'])) {

                    console.error(_('Renderer not valid'), renderer);
                    return;

                } else if (typeof(renderer) == 'string') {

                    renderer = eval('new X.listview.ListviewRenderer_'+renderer+'(this);');

                } else {

                    console.error(_('Renderer not valid'), renderer);
                    return;
                }

                if ((this.renderer ? this.renderer.RENDER_TYPE : null) === renderer.RENDER_TYPE) return;

                this.renderer = renderer;
                var selection = this.selection.get();

                try {

                    // IMPORTANT: Handle jQuery bug
                    // http://forum.jquery.com/topic/javascript-error-when-unbinding-a-custom-event-using-jquery-1-4-2
                    // Fixed in http://dev.jquery.com/ticket/6184
                    // Waiting for jquery-1.4.3 .....

                    $(this)
                        .unbind('lv_columnSort')
                        .unbind('lv_itemMousedown')
                        .unbind('lv_itemMouseup')
                        .unbind('lv_itemClick')
                        .unbind('lv_itemDblclick')
                        .unbind('lv_itemContextmenu');
                } catch (e) {
                    //console.info('[jQuery bug]', e);
                }

                this.refresh(null);

                $(this)
                    .bind('lv_itemMousedown', function(event, ui) {

                        if (ui.originalEvent.button == 0) {
                            // Handle item selection
                            this.selection.add(event, ui.data, ui.originalEvent.ctrlKey);
                            this.highlightSelection();
                        }
                        $(this.element).trigger('itemMousedown', [ui]);

                    }.bind(this))
                    .bind('lv_itemMouseup', function(event, ui) {
                        $(this.element).trigger('itemMouseup', [ui]);
                    }.bind(this))
                    .bind('lv_itemClick', function(event, ui) {
                        $(this.element).trigger('itemClick', [ui]);
                    }.bind(this))
                    .bind('lv_itemDblclick', function(event, ui) {
                        $(this.element).trigger('itemDblclick', [ui]);
                    })
                    .bind('lv_itemContextmenu', function(event, ui) {
                        $(this.element).trigger('itemContextmenu', [ui]);
                    })
                    .bind('lv_columnSort', function(event, params) {
                        $(this.element).trigger('columnSort', [params]);
                    });


                this.selection.setArray(selection);
                this.highlightSelection();
            },
            getModel: function() {
                return this.model;
            },
            setModel: function(model) {

                $(this.element).trigger('beforeSetModel', [{ui: widget, element: this.element, data: {}, model: model}]);

                if ($('.xim-listview-loading', this.element).length == 0) {
                    this._showLoading();
                }

                var widget = this;
                var listContainer = this.clearList(true);
                this.selection.clear();

                if (!model) model = [];
                this.model = model;
                this._generateBrowser(model[0]);

                if (this.options.draggables) {
                    $('.xim-listview-table-container .xim-listview-item-icon').draggable('destroy');
                }

            //	$('.xim-listview-table-container', widget.element).css('overflow', 'auto');
                widget.list = this.renderer.createView(model, this.createNodeListeners);
                listContainer.append(widget.list);

                //Make the results table resizable
                $('.xim-listview-table', this.element).fixheadertable({
                     colratio    : [24, 55, 116, 80, 150, 70, 50, 80],
                     //height      : 200,
                     width       : 625,
                     zebra       : false,
                     resizeCol   : true,
                     minColWidth : 24,
                     wrapper     : false
                });

                if (this.options.draggables) {
                    $('.xim-listview-table-container .xim-listview-item').draggable({
                        helper: function(event) {

                            var iconSelector = '.xim-listview-item-icon';
                            var labelSelector = '.xim-listview-item-label';
                            if (this.renderer.RENDER_TYPE == 'GRID_RENDERER') {
                                iconSelector = '.xim-listview-grid-icon';
                                labelSelector = '.noidea';
                            }

                            var icon = $(iconSelector, event.currentTarget).clone();
                            var label = $(labelSelector, event.currentTarget).clone();

                            return $('<div/>')
                                .addClass('xim-listview-item')
                                .append(icon)
                                .append(label)
                                .data('selection', this.getSelection().get());
                        }.bind(this),
                        revert: false,
                        cursor: 'pointer',
                        appendTo: 'body',
                        containment: '#body',
                        zIndex: 3000
                    });
                }

                this._hideLoading();

                $(this.element).data('setid', false);
                $(this.element).trigger('afterSetModel', [{ui: widget, element: this.element, data: {}, model: model}]);
            },
            clearBrowser: function() {
                var $this = this.element;
                if (this.browser) $(this.browser).unbind().remove();
                if (!this.options.showBrowser) return;
                this.browser = $('<div></div>').addClass('xim-listview-browser').attr('id', 'xim-listview-browser');
                $this.prepend(this.browser);
            },
            _generateBrowser: function(data) {

                if (!this.options.showBrowser || !data || this.browserSemaphore) {
                    if (!data) {
                        if (this.parent) {
                            // Substitute the last (omega) link with a working one
                            var omega = $('a.omega', this.browser);
                            var zindex = $(omega).css('z-index');
                            var link = this._createBrowserLink(
                                $(omega).data('data'),
                                $(omega).html(),
                                'xim-listview-link',
                                zindex
                            );
                            $(omega).replaceWith(link);
                            // Appends the new (omega) level
                            $(this.browser).append(this._createSeparator());
                            $(this.browser).append(this._createBrowserLink([], this.parent.name.value, 'xim-listview-link omega', ++zindex));
                        }
                    }
                    return;
                }
                this.clearBrowser();

                var path = data.path.value;

                if (path == '/') {
                    $(this.browser).append(this._createSeparator());
                    return;
                }

                this.browserSemaphore = true;
                $.get(
                    X.restUrl + '?action=browser3&method=parents',
                    {nodeid: data.parentid.value},
                    function(data) {

                        var parents = [data.node].concat(data.node.parents);
                        var length = $(parents).length;

                        $(parents).each(function(id, dataItem) {
                            var name = $(dataItem).attr('name');
                            var nodeid = $(dataItem).attr('nodeid');
                            var data = {
                                nodeid: {value: nodeid},
                                isdir: {value: 1}
                            };
                            if (id == 0) {
                                $(this.browser).prepend(this._createBrowserLink(data, name, 'xim-listview-link omega', id+1));
                            } else if (id == length-1) {
                                // Do nothing (root XIMDEX node is not needed)
                            } else if (id == length-2) {
                                $(this.browser).prepend(this._createBrowserLink(data, name, 'xim-listview-link alpha', id+1));
                            } else {
                                $(this.browser).prepend(this._createBrowserLink(data, name, 'xim-listview-link', id+1));
                            }
                            $(this.browser).prepend(this._createSeparator());

                        }.bind(this));

                        this.browserSemaphore = false;
                    }.bind(this)
                );

            },
            _createBrowserLink: function(data, path, c, id) {

                var link = $('<a href="#"></a>').html(path).addClass(c);

                $(link).css('z-index', id);
                $(link).data('data', data);

                var isOmega = $(link).hasClass('omega');
                $(link).click(function(event) {
                    if (!isOmega) this.loadFromSource(this.options.datastore, data);
                    return false;
                }.bind(this));

                return link;
            },
            _createSeparator: function() {
                return $('<span class="path-separator">&nbsp;/&nbsp;</span>');
            },
            refresh: function(node) {
                if (typeof(node) != 'object') node = this.getNodeById(node);
                if (node) {
                    this.loadFromSource(this.getDatastore(), $(node).data('data'));
                } else if (this.model) {
                    this.setModel(this.model);
                }
            },
            select: function(node) {

                if (Object.isObject(node)) {

                    var widget = $(node).data('widget');
                    if (widget.selected) {
                        $('.xim-listview-label:first', widget.selected).removeClass('xim-listview-selected');
                    }
                    widget.selected = node;
                    $('.xim-listview-label:first', widget.selected).addClass('xim-listview-selected');
                    $(node).trigger('select', [{ui: widget, element: node, data: $(node).data('data')}]);

                } else if (Object.isString(node)) {

                    switch (node.toUpperCase()) {
                        case 'ALL':
                            this.selection.setArray(this.getModel());
                            this.highlightSelection();
                            break;

                        case 'NONE':
                            this.selection.clear();
                            this.highlightSelection();
                            break;

                        case 'INVERT':

                            var newSelection = [];
                            var model = this.getModel();

                            $.each(model, function(index, item) {
                                if (!this.selection.contains(item)) {
                                    newSelection.push(item);
                                }
                            }.bind(this));

                            this.selection.setArray(newSelection);
                            this.highlightSelection();
                            break;
                    }

                }
            },
            getSelected: function() {
                return this.selected;
            },
            getSelection: function() {
                return this.selection;
            },
            getParent: function() {
                return this.parent;
            },
            getNodeById: function(nodeid) {
                return $('[id=listview-nodeid-'+nodeid+']', this.element);
            },
            getOptions: function() {
                return this.options;
            },
            highlightSelection: function() {
                $('.xim-listview-selected', this.element).removeClass('xim-listview-selected');
                $.each(this.selection.get(), function(index, item) {
                    var id = '#listview-nodeid-'+item.nodeid.value;
                    id = id.replace(/\//g, '\\/');
                    id = id.replace(/\./g, '\\.');
                    id = id.replace(/ /g, '\\ ');
                    $(id, this.element).addClass('xim-listview-selected');
                }.bind(this));
            },
            options: {
                rootId: 10000,
        //		datastore: null,
                renderer: 'Grid',
                url_base: X.baseUrl,
                img_base: '',
                loading_icon: '/actions/browser3/resources/images/loading.gif',
        //		colModel: null,
                showBrowser: false,
                showSelectionHandlers: false,
                draggables: false,
                collapsed_icon: 'ui-icon-triangle-1-e',
                expanded_icon: 'ui-icon-triangle-1-se',
                /*collapsed_icon: 'ui-icon-carat-1-e',
                expanded_icon: 'ui-icon-carat-1-se'*/
                paginator: {
                    show: false,
                    name: 'listview',
                    className: 'xim-listview-itemsSelector',
                    view: 'select',
                    defaultValue: window.com.ximdex.preferences.MaxItemsPerGroup
                }
            },
            getter: [
                'getSelected', 'getSelection', 'getParent',
                'getModel', 'getNodeById', 'getOptions',
                'getDatastore', 'rootId'
            ]
        });

    })(jQuery);
},"json");

    X.listview.ListviewRenderer = Object.xo_create({
        RENDER_TYPE: 'GENERIC_RENDERER',
        _init: function(widget) {
            this.widget = widget;
        },
        createView: function(model, listeners) {
        },
        _createCell: function(data) {
        },
        _callCreateNodeListeners: function(node, listeners) {
            listeners = listeners || [];
            for (var i=0,l=listeners.length; i<l; i++) {
                var cb = listeners[i];
                node = cb(this.widget, node);
            }
            return node;
        }
    });

