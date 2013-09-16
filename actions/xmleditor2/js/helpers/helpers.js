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




(function() {

	/**
	 *	Iterator implementation for IE.
	 *	Can't use it with a for...in loop yet, but can use it with a while loop.
	 */
	if (!Object.isFunction(window.Iterator)) {
		window.Iterator = function(collection, keys) {

			this._collection = null;
			this._keys = false;
			this._length = null;
			this._index = null;

			this._initialize = function(collection, keys) {

				this._collection = [];
				this._keys = keys;
				this._length = 0;
				this._index = 0;

				for (var index in collection) {
					var elem = collection[index];
					if (typeof(elem) != 'function') {
						var item = [index, elem];
						this._collection.push(item);
						this._length++;
					}
				}
			};

			this.next = function() {
				var item = null;
				this._index++;
				if (this._collection[this._index]) {
					item = this._collection[this._index];
				}
				return item;
			};

			this._initialize(collection, keys);
		}
	}

	if (!Object.isFunction(Array.prototype.asArray)) {
		Array.prototype.asArray = function() {
			var l = this.length;
			var arr = [];
			for (var i=0; i<l; i++) {
				var element = this[i];
				if (!element['tagName']) {
					return this;
				}
				if (!element['PATTERN'] || element.PATTERN == 'element') {
					arr.push(element.tagName);
				} else {
					arr = arr.concat(element.childNodes.asArray());
				}
			}
			return arr;
		}
	}

	$.fn.extend({
		// Defined in inc/js/helpers.js
		contains: Array.prototype.contains
	});


	/**
	 *  The AJAX Request object!
	 */
	if (!Object.isFunction(window.AjaxRequest)) {
		window.AjaxRequest = function(url, options) {

			this.send = function(url, options) {

				// TODO: Parse and work with options.params!

				options = options || {};
				options.async = options.async || true;
				options.method = options.method || 'GET';
				options.params = options.params || {};
				options.content = options.content || '';

				var req = new XMLHttpRequest();
				req.options = options;

				req.onreadystatechange = function() {

					this.req = req;
					this.options = options;

					try {

/*  * 0 Non-initiated (the open method has not been called)
    * 1 Loading (open method is called)
    * 2 Loading (send method is called and we get the http header and status)
    * 3 Interactive (responseText property has partial data)
    * 4 Complete (responseText property has all the data asked to server)*/

						if (req.readyState != 4) return;

						/**
						 * There is a known bug in MSIE, when the server response is 204
						 * the status code in IE is 1223.
						 *
						 * http://webbugtrack.blogspot.com/2008/05/bug-122-in-ie-http-204-status-may.html
						 * http://prototype.lighthouseapp.com/projects/8886/tickets/207-ajax-request-considers-http-204-a-failure-under-ie-only
						 * http://www.mail-archive.com/jquery-en@googlegroups.com/msg13093.html
						 */
						var status = (req.status != 1223) ? req.status : 204;
						if (status >= 400) {
							if (options.onError) options.onError(req);
							return;
						}

					} catch(e) {
						return;
					}

					var json = null;
					try {
						json = eval('new function() {return '+req.responseText+';}');
					} catch(e) {
						json = {};
					}
					if (options.onComplete) {
						options.onComplete(req, json);
					}
				};


				req.open(options.method, url, options.async);
				if (options.method == 'POST') {
					req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				}
				req.send(options.content);
			};

			this._parseParams = function(params) {
				// TODO: Params in querystring, in a POST form???
				return params;
			}

			this.send(url, options);

		}
	}

})();
