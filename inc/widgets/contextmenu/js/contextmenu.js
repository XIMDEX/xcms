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


(function($) {

	$.widget('ui.contextmenu', {

 	    id: null,

		/** Widget Initialize from composer->includeDinamicjs */
		_init: function() {
			//Save widget id
  			this.id = $(this.element).attr('id');
			//Retrieve widget options 
			this._initOptions();

			//Initialize menu entries
			var items = null
			//is there a html5 menu?
			if (null == this.options.items) {
				//import items from html5 menu
				items = $.contextMenu.fromMenu($(this.options.from) );
			}else {
				//items
				eval("items='"+this.options.items+"'");
			}

			//if selector isn't class(.myclass), it's a id(#miclass): miclass -> #miclass
			if(this.options.selector && "." != this.options.selector.charAt(0) ) {
					this.options.selector = "#"+this.options.selector;
			}	

			//add items
			this.options.items = items;


			//Run jquery contextmenu plugin 
			$.contextMenu(this.options); 

			//Add hand to launcher/selector
			$(this.options.selector).css('cursor','pointer');
			
		},

		//Initialize widget options 
		_initOptions: function() {
			this.options.from = "#"+this.id;
			if(!this.options.callback) 
				this.options.callback = function(key, opt) { this.openAction(key, opt); }.bind(this);

			//Widget default options
			var options =  X.widgetsVars.getValues(this.id);

			//Mix Javascript options and Widget default options
		    $.extend(this.options, options);

		},

		//Default Callback
		openAction: function(key, opt) {			
			//nodeid for action
			var nodeid = opt.choosed.node.dataset.nodeid || 10000;	
			//command action. Eg: uploader, welcome, ...
			var action = opt.choosed.node.dataset.action;
			//Action method
			var method = opt.choosed.node.dataset.method || "index";
			//¿?
			var bulk =  opt.choosed.node.dataset.bulk || '0';	

			//Open action through browserwindow
			$('#bw1').browserwindow('openAction', {
				label: opt.choosed.name,
				name:  opt.choosed.name,
				command: action,
				params: 'method='+method+'&nodeid='+nodeid+"&"+opt.choosed.node.dataset.params,
				nodes: nodeid,
				url: X.restUrl + '?action='+action+'&nodes[]='+nodeid+'&nodeid='+nodeid,
				bulk: bulk
			},nodeid);

		},

		getOptions: function() {
			return this.options;
		},
		
		options: {
			selector: undefined,
			trigger: 'right',
			callback: undefined,
			from: undefined,
			items: undefined,
			build: undefined,
			from: undefined
		},


		getter: ['getOptions']
	});

})(jQuery);
