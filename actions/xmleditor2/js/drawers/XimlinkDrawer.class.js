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

		$('div.descriptions-list-options', this.element).empty();
	},

	setData: function(data) {
		this.data = Object.isArray(data) ? data : [];
	},

	setTerm: function(term) {
		this.term = term;
		$('input.ximlink-search', this.element).val(term);
	},

	setMainTerm: function(term) {
			this.term_main = term;
			this.setTerm(term);
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

    	$('input.ximlink-search', this.element).keyup(function(event) {
    		this.filterXimlinks($(event.currentTarget).val());
    	}.bind(this));

    	$('button.save-button', this.element).click(this.save.bind(this));
    	$('button.close-button', this.element).click(this.close.bind(this));

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
    	if (term.length <= 2) {
    		return;
    	}

    	this._getXimLinkData(term);
    },

    save: function() {

    	var item = $('select.ximlink-list option:selected', this.element);
    	var text = $('div.descriptions-list-options input:radio:checked', this.element);
		if( item.length >= 1) {
			var data = $(item[0]).data('data');
			this.tool.selNode.ximLink.name = data.name;
			this.tool.selNode.ximLink.url = data.url;

			if (item.length == 0 || text.length == 0) {
				this.tool.selNode.ximLink.text = this.tool.term_main;
			}else {
				this.tool.selNode.ximLink.text = $(text).val();
			}
		}else {
				this.tool.selNode.ximLink.text = $(text).val();
		}

		var toolbox = this.tool.toolboxes['attributestoolbox'];
    	toolbox.updateButtonHandler();

    	this.close();
    },

    close: function() {
    	this.data = null;

    	$('select.ximlink-list', this.element).unbind().empty();
    	$('input.ximlink-search', this.element).unbind();
    	$('button.save-button', this.element).unbind();
    	$('button.close-button', this.element).unbind();
    	var dt = this.tool.editor.getTool('ximdocdrawertool');
    	dt.closeDrawer();
    }

});
