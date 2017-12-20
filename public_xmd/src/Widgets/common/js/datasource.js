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

var DataSource = Object.xo_create({

	_init: function(options) {
		this.running = false;
		this.options = Object.extend({
			method: 'get',
			url: null,
			cb_fetched: function(data, textStatus) {/*console.info(arguments);*/},
			type: 'xml'
		}, options);
	},
	
	method: function(method) {
		if (!Object.isEmpty(method)) {
			this.options.method = method;
			return this;
		} else {
			return this.options.method;
		}
	},
	
	url: function(url) {
		if (!Object.isEmpty(url)) {
			this.options.url = url;
			return this;
		} else {
			return this.options.url;
		}
	},
	
	type: function(type) {
		if (!Object.isEmpty(type)) {
			this.options.type = type;
			return this;
		} else {
			return this.options.type;
		}
	},

	fetch: function(params, callback) {

		var $this = this;
		this.running = true;

		callback = typeof(callback) == 'function'
			? callback
			: this.options.cb_fetched;

		var method = this.options.method;
		method = (method != 'get' && method != 'post' ? 'get' : method).toUpperCase();
		
		$.ajax({
			url: this.options.url,
			type: method,
			data: params,
			dataType: this.options.type,
			success: function(data, textStatus) {
				this.running = false;
				callback(data, textStatus);
			}.bind(this)
		});

	}
});
