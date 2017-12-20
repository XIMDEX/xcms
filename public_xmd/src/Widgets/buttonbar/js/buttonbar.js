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

(function($) {

	$.widget('ui.buttonbar', {

		nodeid: null,

		_init: function() {
			var $this = this.element;
			$this.addClass('xim-buttonbar-container');
			this.am = new X.ActionsManager({
				prefix: 'tabs',
				container: '#tabs'
			});
		},
		clearButtons: function() {
			var $this = this.element;
			$this.empty();
		},
		_createButton: function(data) {

			// Don't create duplicates
			if ($('#xim-button-' + data.actionid.value, this.element).length > 0) {
				return null;
			}

			var button = $('<div></div>')
				.attr('id', 'xim-button-' + data.actionid.value)
				.addClass('xim-buttonbar-button');
			var img = $('<img></img>')
				.addClass('xim-buttonbar-img')
				.attr({
					src: this.options.images_url + data.icon.value,
					alt: data.description.value,
					title: data.description.value
				})
				.hover(function(event){
					button.trigger('itemHover', [{ui: this, element: button, data: data}])
				}.bind(this))
				.mouseout(function(event){
					button.trigger('itemMouseOut', [{ui: this, element: button, data: data}])
				}.bind(this))
				.click(function(event) {
					button.trigger('itemClick', [{ui: this, element: button, data: data}]);
				}.bind(this));

			button.append(img);
			button.data('data', data);
			return button;
		},
		loadButtons: function(node) {
			var data = null;
			if (typeof(node) == 'object' && node['nodeid']) {
				data = node;
			} else if (typeof(node) == 'object' && !node['nodeid']) {
				data = $(node).data('data');
			}
			this.loadFromSource(this.options.datastore, data);
		},
		loadFromSource: function(dstore, data) {

			if (data.nodeid.value == this.nodeid) return;
			this.nodeid = data.nodeid.value;

			if (dstore.options.ds.running) return;
			dstore.load_data({
					params: data,
					options: this.options
				},
				function(store) {
					this.setModel(store.get_model(), data.nodeid.value);
				}.bind(this)
			);
		},
		getDatastore: function(datastore) {
			return this.options.datastore;
		},
		setDatastore: function(datastore) {
			this.options.datastore = datastore;
		},
		getModel: function() {
			//return this.options.model;
		},
		setModel: function(model, nodeid) {
			var $this = this.element;
			this.clearButtons();

			if (!model) model = [];

			for (var i=0; i<model.length; i++) {
				var data = model[i];
				data.nodeid = {value: nodeid};
				var button = this._createButton(data);
				if (button) $this.append(button);
			}
		},
		/**
		 * Funcion que es llamada cuando se hace una llamada a una funcion
		 * @param params
		 * @return NULL
		 */
		call_action: function(params) {
			this.am.callAction(params);
		},
		
		options: {
			datastore: null,
			colModel: null,
			images_url: null
		},
		
		getter ['getModel', 'getDatastore']
	});

})(jQuery);
