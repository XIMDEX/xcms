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

/**
 * Controls the load of an action inside a specific container.
 * Includes the necessary JS and CSS.
 *
 * Widgets registered with this class MUST implement a method called 'callAction'
 * and MUST trigger an event called 'load' when the action is loaded.
 */

X.WidgetsManager = Object.xo_create({


	content: null,
	type: null,
	element: null,
	widget: null,
	widget_id: null,
	container: null,
	params: {},
	params_init: {},

	/**
	 * type: type widget to load(on demand)
	 * params: paramters widget
	 * callback: when widge is loaded then callback is called
	 * */
	_init: function(type, container) {
		this.type = type;
		this.container = container;

	},

	load: function(params, params_init) {
		this.params = params;
		this.params_init = params_init;

		var params = "";
		if(null != this.params) {
			for ( var index in this.params ) {
				params += "&params["+index+"]="+this.params[index];
			}

		}


		/*
		 * X.restUrl+"?action=widgets&method=get_widget&widget=calendar&params[type]=to&params[timestamp_value]=500000
		 * */
		var url = X.restUrl+"?action=widgets&method=get_widget&widget="+this.type+""+params;

		$.ajax({
			url: url,
			type: "GET",
			data: null,
			success: function(data, textStatus) {
				this.content = data;
				this._loadAssets();
			}.bind(this)
		});
	},

		/** Load widget css & js  */
		_loadAssets: function() {

			var css = $('ul.css_to_include li', this.content).map(function(index, item) {
				return $(item).html();
			});
			css = $.makeArray(css);

			var js = $('ul.js_to_include li', this.content).map(function(index, item) {
				var url = $(item).html();
				url = Object.urldecode(url.replace(/&amp;/g, '&', url));
				return url;
			}.bind(this));

			js = {
				onComplete: this._onAssetsCompleted.bind(this),
					//onLoad: this._onScriptLoaded.bind(this),
					js: js
			};

			Object.loadCss(css);

			if (js.js.length > 0) {
				Object.loadScript(js);
			} else {
				this._onAssetsCompleted();
			}
		},


	/** Loaded all css & js */
	_onAssetsCompleted: function() {
			$(this.container).append(this.content);

			this.widget_id = $(this.content).filter('[id^="'+this.type+'"]').attr("id");

			this.element = $("#"+this.widget_id, this.container);
			if(null != this.element) {
				eval("this.widget = $(this.element)."+this.type+"(this.params_init);");

				$(this.container).trigger("widgetLoaded", [{widget: this.widget, id: this.widget_id, type: this.type, params: this.params, element: this.element}]);
			}
	}

});

