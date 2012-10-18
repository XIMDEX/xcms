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

  $.widget('ui.menubar', {
    _init: function() {
      var $this = this.element;
      $this.addClass('cmDiv');
      this.loadFromSource(this.options.datastore);
    },
    _createMenuItems: function(data, parent) {

    	var menuText = data.text.value;
    	if (data.accel.visible && data.accel.value != '') {
    		// TODO: mejorar este codigo
    		menuText += '&nbsp;&nbsp;&nbsp;('+data.accel.value+')';
    	}

		var item = $('<li></li>')
			.addClass('ui-widget ui-corner-all ui-widget-content main')
			.data('data', data);

		if (data.icon.visible && data.icon.value != '') {
			$('<img></img>')
				.attr('src', this.options.img_base + data.icon.value)
				.attr('width', 18)
				.attr('height', 18)
				.appendTo(item);
		}

		$('<span></span>').html(menuText).appendTo(item);

		var children = this.options.datastore.query('menuitem[id="'+data.id.value+'"] > menu > menuitem');
		if (children.length > 0) {
			var menu = $('<ul></ul>');
			for (var i=0; i<children.length; i++) {
				this._createMenuItems(children[i], menu);
			}
			item.append(menu);
		}

		$(parent).append(item);
    },
	loadFromSource: function(dstore) {
		dstore.load_data({},
			function(store) {
				this.setModel(store.get_model());
			}.bind(this)
		);
	},
	getDatastore: function() {
		return this.options.datastore;
	},
	setDatastore: function(datastore) {
		this.options.datastore = datastore;
	},
    getModel: function() {
      //return this.options.model;
    },
    setModel: function(model) {
      $this = this.element;
	  $this.empty();
      model = model || [];
      for (var i=0; i<model.length; i++) {
      	var data = model[i];
      	//console.log(data);
      	this._createMenuItems(data, $this);
      }
      $($this).clickMenu({
		arrowSrc: this.options.url_root + this.options.arrowSrc,
		onClick: function(event) {
			//console.info(arguments, $(this).data('data'));
			$this.trigger('itemClick', [{ui: $this, element: $(this), data: $(this).data('data')}]);
		}
	   });
    },
    
	  options: {
	  	datastore: null,
	  	url_root: '',
	  	img_base: '',
	  	arrowSrc: '/inc/widgets/img/arrow_right.gif'
	  },
	  
	  getter: ['getModel', 'getDatastore']
  });

})(jQuery);
