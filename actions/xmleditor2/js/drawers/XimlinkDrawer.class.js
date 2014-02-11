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




var XimlinkDrawer = Object.xo_create(new Drawer(), {

	_init: function(elementid, tool) {

//		this.setTitle(_('Attributes'));

		this.element = getFromSelector(elementid);
	    this.tool = tool;
	    this.data = null;
		this.term_main = null;
		this.term = null;
		this.ximElement = null;
		this.$input = null;

		$('div.descriptions-list-options', this.element).empty();
	},

	setXimElement: function(ximElement) {
		this.ximElement = ximElement;
	},
	setData: function(data) {
		this.data = Object.isArray(data) ? data : [];
	},

	setInput: function($input) {
		this.$input = $input;
	},

	setTerm: function(term) {
		this.term = term;
		$('div.js_search_link_panel input.ximlink-search', this.element).val(term);
	},

	setMainTerm: function(term) {
			this.term_main = term;			
	},

	deleteContent: function(){
		$('select.ximlink-list', this.element).empty();
	},

	addContent : function(){
		this.data.each(function(index, item) {
    	$('select.ximlink-list', this.element).append(this._createOption(item));
    	}.bind(this));

	},

    createContent: function() {
		$('div.descriptions-list-options', this.element).empty();
		this._addDescription(this.term_main, 1);


    	this.data.each(function(index, item) {
    		$('select.ximlink-list', this.element).append(this._createOption(item));
    	}.bind(this));

    	$('div.js_search_link_panel input.ximlink-search', this.element).keyup(function(event) {
    		this.filterXimlinks($(event.currentTarget).val());
    	}.bind(this));

    	$('button.save-button', this.element).click(this.save.bind(this));
    	$('button.close-button', this.element).click(this.close.bind(this));
    	$('button.create-button', this.element).click(this.create.bind(this));
    	$('a.js_add_link', this.element).click(function(){
			$('div.js_add_link_panel', this.element).show();
			$('div.js_add_link_panel', this.element).next("div.buttons").show(); ;
			$('div.js_search_link_panel', this.element).hide() ; 
			$('div.js_search_link_panel', this.element).next("div.buttons").hide() ; 
			$('.new_link').hide();
			return false;}.bind(this)
		);

		$('button.cancel-button', this.element).click(function(){

    		$('div.js_add_link_panel', this.element).hide(); ;
    		$('div.js_add_link_panel', this.element).next("div.buttons").hide(); ;
			$('div.js_search_link_panel', this.element).show() ; 
			$('.new_link').show();
			$('div.js_search_link_panel', this.element).next("div.buttons").show() ; 
			return false;
		}.bind(this));

		function getClosestInput(element, name) {
			return $(element).closest('div.js_add_link_panel').find("input[name='"+name+"']");
		}

		var $ts = $("div.xim-treeview-selector",this.element);

			var tcm = [
		// Node name
		{name: 'text', alias: 'name', label: 'Name', visible: true},
		// Node ID
		{name: 'nodeid', label: 'NodeId', visible: false, type: 'number', align: 'center'},
		// Node is Folder
		{name: 'isdir', label: 'IsFolder', visible: false, type: 'number', align: 'center'},
		// Node Path
		{name: 'path', label: 'Path', visible: false},
		// Parent ID
		{name: 'padre', alias: 'parentid', label: 'ParentId', visible: false, type: 'number', align: 'center'},
		// NPI
		{name: 'contenidotipo', alias: 'contenttype', label: 'ContentType', visible: false},
		// GET Request when node is expanded
		{name: 'action', label: 'Action', visible: false},
		// Icon
		{name: 'icon', label: '', visible: true, type: 'image', width: '24px'},
		// Open icon
		{name: 'openIcon', label: '', visible: true, type: 'image', width: '24px'},
		// NPI
		{name: 'state', label: 'State', visible: false},
		// Number of children
		{name: 'children', visible: false, type: 'number'},
		// Node must be shown expanded
		{name: 'open', visible: false},
		// Node must be selected by default
		{name: 'selected', visible: false},
		// NodeType NAME
		{name: 'tipoFiltro', alias: 'filter', visible: false},
		// Node ID for being copied
		{name: 'targetid', visible: false},
		// Pagination
		{name: 'startIndex', label: 'Inicio', visible: false},
		{name: 'endIndex', label: 'Fin', visible: false}
	];

	var tds = {
		ds: new DataSource({
			method: 'get',
			type: 'xml',
			url:  window.url_root + '/inc/widgets/treeview/helpers/treeselectordata.php'
		}),
		colModel: tcm,
		queryParams: function(params, options) {
			console.debug(params);
			var p = {
				// Node to be expanded
				nodeid: params.nodeid.value,
				// NPI
				contenttype: params.contenttype ? params.contenttype.value : '',
				// Node to be copied
				targetid: params.targetid ? params.targetid.value : window.nodeId,
				// NodeType NAME
				filtertype: "LinkFolder",
				// NodeType ID
				nodetype: 5048,
				// Elements per page
				nelementos: options.paginator.defaultValue
			};
			return p;
		},
		selector: 'tree tree'
	};

 

	$ts
		.treeview({
			datastore: tds,
			paginator: {
				show: false
			},
			colModel: tcm,
			url_base: window.url_root + '/',
			img_base: window.url_root + '/xmd/images/icons/'
		})
		.bind('select', function(event, params) {
			getClosestInput(params.element, 'link_id_parent').val(params.data.nodeid.value);			
			//checkNodeName(params.data.nodeid.value);
		});

	tds = $ts.treeview('getDatastore');
	tds.clear();

	$.getJSON(
			X.restUrl + '?action=xmleditor2&method=getLinkFolder',
			{nodeid:this.tool.editor.nodeId},
			function(data, textStatus) {
				if (data.success){
				tds.append({
					name: {value: 'links', visible: true},
					nodeid: {value: data.idLinkFolder, visible: false},
					isdir: {value: 1, visible: false},
					icon: {value: 'folder_links.png', visible: true, type: 'image'},
					children: {value: 2, visible: false},
					path: {value: '/', visible: false},
					targetid: {value:getClosestInput($ts, 'id_parent').val()},
					nodetype: {value: 5048},
					filtertype: {value: "LinkFolder"},
					contenttype: {value: ''},
					nelementos: {value: 50}
				});
				$ts.treeview('setModel', tds.get_model(), null, true);
				}
			}.bind(this)
		);

    	$(this.element).show();
        this.focusElement();
    },

    _createOption: function(data) {
		this._addDescription(this.term_main, 1);

    	var opt = $('<option></option>')
    		.val(data.idnode)
    		.html('%s - %s'.printf(data.name, data.url))
    		.data('data', data)
    		.click(this._showXimlinkDescriptions.bind(this, data));

    	data.option = opt;

    	return opt;
    },

    _showXimlinkDescriptions: function(data,event) {
		this._addDescription(this.term_main,1);
    	data.text.each(function(index, item) {
			this._addDescription(item);
    	}.bind(this));
    },

	_addDescription: function(text, init) {
			if(init) {
				$('div.descriptions-list-options', this.element).empty();
				this._addDescription(this.term_main);
				$('div.descriptions-list-options input:radio', this.element).eq(0).attr("checked", true );
				return ;
			}

			var description_exists = $('div.descriptions-list-options input[value="'+text+'"]', this.element).length;


			if(description_exists) return ;

			var opt = $('<input type="radio" />')
				.attr({name: "descriptions-list"})
	    		.val(text);

			var label =  $('<label></label>')
				.append(opt)
				.append(text);

    		$('div.descriptions-list-options', this.element).append(label);
	},

	_getXimLinkData: function(term){
		$.getJSON(
			X.restUrl + '?action=xmleditor2&method=getAvailableXimlinks&term='+term,
			{docid: this.tool.editor.nodeId},
			function(data, textStatus) {
				this.setData(data);
				this.deleteContent();
				this.addContent();
			}.bind(this)
		);
	},

    filterXimlinks: function(term) {
    	term = term || '';
    	this._getXimLinkData(term);
    },

    save: function() {

    	var item = $('select.ximlink-list option:selected', this.element);
    	var text = $('div.descriptions-list-options input:radio:checked', this.element);
		if( item.length >= 1){
			this.$input.val(item.val());
			if (text.length && text.val() != ""){
				$(this.ximElement._htmlElements).filter(":visible").text(text.val());
			}
		}
   	   var toolbox = this.tool.toolboxes['attributestoolbox'];
       toolbox.updateButtonHandler();
		this.close();
    },

    create: function(){

    	var url = $("input[name='link_url']", this.element).val();
    	var name = $("input[name='link_name']", this.element).val();
    	var description = $("input[name='link_description']", this.element).val();
    	var idParent = $("input[name='link_id_parent']", this.element).val();
    	if (this.checkParams()){
    		$.getJSON(
			X.restUrl + '?action=xmleditor2&method=saveXimLink',
			{
				url: url,
				name: name,
				description: description,
				idParent: idParent
			},
			function(data, textStatus) {
				if (data.success && data.idLink>0){
					this.$input.val(data.idLink);
					var toolbox = this.tool.toolboxes['attributestoolbox'];
				    toolbox.updateButtonHandler();
					this.close();
				}
			}.bind(this)
		);
    	}
    },

    checkParams: function(){
    	var result = true;
    	
    	result = this.checkParam("link_name", "Name");
    	result = this.checkParam("link_url", "Url") && result; 
    	result = this.checkParam("link_id_parent", "IdParent") && result;

    	$inputName = $("div.js_add_link_panel input[name='link_name']");
    	$inputUrl = $("div.js_add_link_panel input[name='link_url']");
    	$inputName.unbind("keyup").keyup(this.checkName($inputName));
    	$inputUrl.unbind("keyup").keyup(this.checkUrl($inputUrl));

    	return result;
    },

    checkParam: function(name, check){

		var result = true;
    	var $input = $("div.js_add_link_panel input[name='"+name+"']");
    	var checkMethod = "check"+check;
    	var result = this[checkMethod]($input);
    	if (result)
    		$input.removeClass('error');
    	else
    		$input.addClass('error');
    	return result;
    	
    },

    checkName: function($input){
    	
    	if (!$input || $input.val() == "")
    		return false;
		if (!/^\w+$/.test($input.val())){
    		return false
    	}
    	return true;
    },

    checkUrl: function($input){

		if (!$input || $input.val() == "")
			return false;    	
    	if (!/^(mailto:)?[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i.test($input.val())
    		&&
    		!/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test($input.val())){
    		
    		return false;
    	}
    	return true;
    },

    checkIdParent: function($input){
    	if (!$input || $input.val() == "")
			return false;    	
		return true;
    },

    close: function() {
    	this.data = null;
    	$('div.js_add_link_panel', this.element).hide(); ;
		$('div.js_add_link_panel', this.element).next("div.buttons").hide(); ;
		$('div.js_search_link_panel', this.element).show() ; 
		$('div.js_search_link_panel', this.element).next("div.buttons").show() ;
		$("input", this.element).val("");
    	$('select.ximlink-list', this.element).unbind().empty();
    	$('input.ximlink-search', this.element).unbind();
    	$('button.save-button', this.element).unbind();
    	$('button.close-button', this.element).unbind();
    	var dt = this.tool.editor.getTool('ximdocdrawertool');
    	$("div.xim-treeview-selector",this.element).treeview("destroy");
    	$("div.xim-treeview-selector",this.element).empty();
    	dt.closeDrawer();
    }

});
