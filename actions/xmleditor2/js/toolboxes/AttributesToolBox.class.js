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

var that2;
var inputUrl2;


var AttributesToolBox = Object.xo_create(FloatingToolBox, {

	initialize: function(tool, editor) {

		var label = _('Update');

		eval("this.buttons = { "+label+": this.updateButtonHandler.bind(this) };");

		AttributesToolBox._super(this, 'initialize', tool, editor);

		this.setTitle(_('Attributes'));
		this.currentInput = null;
		this.imgObserver = null;
		this.imgDim = {w: -1, h: -1};
	},

	startStopTimer: function(ximElement) {

		var t = XimTimer.getInstance();

		var htmlDoc = this.editor.getBody().parentNode;
		var domElement = $('[uid="%s"]'.printf(ximElement.uid), htmlDoc).get(0);

		t.removeAllObservers();

		if (domElement.tagName.toUpperCase() != 'IMG') {
			t.stop();
			this.imgDim = {w: -1, h: -1};
			return;
		}

		this.imgObserver = t.addObserver(this.updateImageSize.bind(this, domElement), 500);
		t.start();
	},

	updateImageSize: function(domElement) {

		var w = $(domElement).width();
		var h = $(domElement).height();

		if (w == this.imgDim.w && h == this.imgDim.h) {
			return;
		}

		this.imgDim.w = w;
		this.imgDim.h = h;

		$('input#kupu-attributes-width', this.element).val(w);
		$('input#kupu-attributes-height', this.element).val(h);
	},

	beforeUpdateContent: function(options) {
		this._clean();
	},

	updateState: function(options) {

		if (!this.tool.selNode || (options.event && options.event.type != 'click')) return;

		this._clean();

		var input = null;
		$('<div></div>')
			.addClass('xedit-element-name')
			.html(this.tool.selNode.tagName)
			.appendTo(this.element);
		for (var attrName in this.tool.attributes) {

			var attr = this.tool.attributes[attrName];

			if (typeof attr.value == "object"){
				input = document.createElement('select');

				for (var i=0,l=attr.value.length; i<l; i++) {

					var value = attr.value[i];

					var option = document.createElement('option');
					option.setAttribute('value', value);
					text = document.createTextNode(value);
					option.appendChild(text);
					input.appendChild(option);

					if (value == attr.selectedValue) {
						option.setAttribute('selected', 'selected');
					}
				}

			} else {

				input = document.createElement('input');
				input.setAttribute('type', 'text');
			    input.value = attr.value || '';
			}

			$(input).data('attribute-name', attrName);
			input.setAttribute('class', 'wide');
			input.setAttribute('id', 'kupu-attributes-' + attrName);

			if (attrName == 'uid') {
				input.setAttribute('type', 'hidden');
			}

			if (!this.tool.PROTECTED_ATTRIBUTES.contains(attrName)) {

				if (attr.type === null) {

					this._createAttributeInput(attrName, input);
				} else {

					var method = '_createInputFor_' + attr.type;
					if (Object.isFunction(this[method])) {
						this[method](attrName, input);
					}else{
						this._createInputFor_genericSelector(attrName, input,[]);
					}
					
//					$(input).click(function (e) {this._createTreeSelector($(e.currentTarget).attr('id'));}.bind(this));
				}
			}

		}

		this.startStopTimer(this.tool.selNode);
	},

	updateButtonHandler: function(event) {

		var t = XimTimer.getInstance();
		t.removeObserver(this.imgObserver);

		var attributes = {};

		$('.kupu-attribute-value', this.element).each(function(index, elem) {
			var attrName = $(elem).data('attribute-name');
			var attrValue = $(elem).val();
			attributes[attrName] = attrValue;
		});

		this.tool.saveAttributes(attributes);

		this.editor.logMessage(_('Attributes updated!'));

		// NOTE:
		// Updating Editor Content, because actually we don't edit html attributes but xml.
		this.setActionDescription(_('Update attributes'));

		// When update button is clicked, selected element losts its focus,
		// so we clean the attributes panel to prevent errors.
		// UpdateEditor will populate panel again.
		this._clean();

		this.editor.updateEditor({caller: this});
	},

	_createInputFor_ximlink: function(label, inputUrl) {

		var ximElement = this.tool.selNode;

		var $inputUrl = $(inputUrl)
			.addClass('kupu-attribute-value')
			.val(ximElement.ximLink.url);

		var inputUrlId = $inputUrl.attr('id');

		var $inputText = document.createElement('input');
		$inputText.setAttribute('type', 'text');
		$inputText = $($inputText)
			.attr('id', inputUrlId + '-text')
			.addClass('wide kupu-attribute-ximlink-value')
			.val(ximElement.ximLink.text);

		var $inputName = document.createElement('input');
		$inputName.setAttribute('type', 'text');
		$inputName = $($inputName)
			.attr('id', inputUrlId + '-name')
			.addClass('wide kupu-attribute-ximlink-value')
			.val(ximElement.ximLink.name);

		var that = this;
		$inputName.change(function(event){that._save_attribute_ximlink(event, $inputUrl,$inputText, $inputName)});
		$inputText.change(function(event){that._save_attribute_ximlink(event, $inputUrl,$inputText, $inputName)});
		$inputUrl.change(function(event){that._save_attribute_ximlink(event, $inputUrl,$inputText, $inputName)});

		var $label = $('<div></div>').addClass('kupu-toolbox-label').html('%s:'.printf(label));
		var $fieldLabel = $('<label></label>');
		var $wrap = $('<div></div>').addClass('kupu-toolbox-attribute-value');
		var $button = $('<button></button>')
			.addClass('ximlink-search')
			.attr('type', 'button')
			.html(_('Search'))
			.click(function(event){ that._openXimlinkSelector(event, $inputUrl, $inputText, $inputName)});

		var $labelName = $fieldLabel.clone().attr('for', $inputName.attr('id')).html(_('Name:'));
		var $labelUrl = $fieldLabel.clone().attr('for', $inputUrl.attr('id')).html('Url:');
		var $labelText = $fieldLabel.clone().attr('for', $inputText.attr('id')).html(_('Text:'));


		var d = $('<div></div>')
			.addClass('xedit-element-attribute')
			.append($label)
			.append($labelName)
			.append($inputName.wrap($wrap.clone()))
			.append($labelUrl)
			.append($inputUrl.wrap($wrap.clone()));

		if (ximElement.tagName != 'image') {
			d.append($labelText).append($inputText.wrap($wrap.clone()));
		} else {
			$labelText.remove();
			$inputText.unbind().remove()
		}

		d.append($button);

		$(this.element).append(d);
	},

	_openXimlinkSelector: function(event, $inputUrl, $inputText, $inputName) {
		var drawerId = 'ximlinkdrawer';

		if($inputText) {
			var term = $inputText.val();
		}else {
			var term = "";
		}

		var dt = this.editor.getTool('ximdocdrawertool');
		if (dt.isOpen(drawerId)) return;

		var $button = $('button.ximlink-search', this.element).unbind('click');

		$.getJSON(
			X.restUrl + '?action=xmleditor2&method=getAvailableXimlinks&term='+term,
			{docid: this.editor.nodeId},
			function(data, textStatus) {

				dt.drawers[drawerId].setData(data);
				dt.drawers[drawerId].setMainTerm(term);
				dt.openDrawer(drawerId);
				$button.click(this._openXimlinkSelector.bind(this, $inputUrl, $inputText, $inputName));

			}.bind(this)
		);
	},



	_createInputFor_genericSelector : function(label, inputUrl, specificSearchOptions){

		var searchOptions = [{comparation: 'equal',
                                                content: this.editor.nodeId,
                                                field: 'nodeid',
                                                from: '',
                                                to: ''
					}];
		
		var $inputUrl = $(inputUrl).addClass('kupu-attribute-value');

		searchOptions = searchOptions.concat(specificSearchOptions);
		var getImageSelector = function($inputUrl){
			this.imageSelector = null;
			if (Object.isEmpty(this.imageSelector)){
				this.imageSelector = $("<div></div>").appendTo(this.element);
				this.imageSelector.searchpanel({
                                        url_base: X.baseUrl,
                                        use_cache: false,
                                        queryHandler: "SQLTREE",
                                        masterFilter: [{
                                                comparation: 'equal',
                                                content: '5040',
                                                field: 'nodetype',
                                                from: '',
                                                to: ''
                                        }, {
                                                comparation: 'equal',
                                                content: this.editor.nodeId,
                                                field: 'nodeid',
                                                from: '',
                                                to: ''
                                        }
                                        ],
                                        showFilters: false,
                                        view: 'treeview',
                                        showSelectButton: true
                                });

			}

			that2 = this;
                        inputUrl2 = $inputUrl;
                        $(this.imageSelector)
                                .unbind('nodesSelected')
                                .bind('nodesSelected', 
					this._imageSelector_onNodesSelected);

                        return this.imageSelector;

		}.bind(this);

		
		var $sp = getImageSelector($inputUrl);

		var $label = $('<div></div>')
			.addClass('kupu-toolbox-label')
			.html('%s:'.printf(label));		
		var $wrap = $('<div></div>')
			.addClass('kupu-toolbox-attribute-value');
		 
		var $button = $('<button></button>')
			.addClass('imageSelector-search')
			.attr('type', 'button')
			.html(_('Search'));


		var that = this;
		$button.click(function(){
			//Updating current input at open the imageSelector
			that.currentInput = $inputUrl;
			$sp.searchpanel("option","masterFilter",searchOptions);
			$sp.searchpanel("open");

			if ($inputUrl.val() && $inputUrl.val() != ""){
	                        var value = $inputUrl.val();
                                if (value.indexOf(",") != -1)
         	                       value = value.substring(0,value.indexOf(","));
				$sp.searchpanel("setSelectedElement, value");
                    	        $(".xim-treeview-container").treeview("navigate_to_idnode_from_project", value);
			}

		}.bind(this));
		
		var d = $('<div></div>')
			.addClass('xedit-element-attribute')
			.append($label)
			.append(
				$wrap.append($inputUrl).append($button)
			);

		$(this.element).append(d);

	},
	_createInputFor_imageSelector: function(label, inputUrl) {

		var searchOptions = [{
					comparation: 'equal',
                                        content: '5040',
                                        field: 'nodetype',
                                        from: '',
                                        to: ''                          
                                    }];

		this._createInputFor_genericSelector(label, inputUrl, searchOptions);
	},

	_createInputFor_ximletSelector: function(label, inputUrl){
		var searchOptions = [{
					comparation: 'equal',
                                        content: '5057',
                                        field: 'nodetype',
                                        from: '',
                                        to: ''				
              				}]; 
		this._createInputFor_genericSelector(label, inputUrl, searchOptions);
	},

	_createInputFor_ximcludeSelector: function(label, inputUrl){

		var searchOptions = [{
					comparation: 'equal',
                                        content: '5076',
                                        field: 'nodetype',
                                        from: '',
                                        to: ''
				}];
		this._createInputFor_genericSelector(label, inputUrl, searchOptions);
	},

	_imageSelector_onNodesSelected: function(event, params) {

		$(that2.imageSelector).unbind('nodesSelected');
		var image = params.selection.length > 0 ? params.selection[0] : false;
		if (!image) return;
		if (!that2.currentInput) return;
			that2.currentInput.val(image.nodeid.value);
	},

	_save_attribute_ximlink: function(event, $inputUrl, $inputText, $inputName) {

		this.tool.selNode.ximLink.name = $inputName.val();
		this.tool.selNode.ximLink.url = $inputUrl.val();
		this.tool.selNode.ximLink.text = $inputText.val();
		this.updateButtonHandler();
	}

});

