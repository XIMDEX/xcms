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


(function() {

	function namespace() {

		// Creates the ximdex namespace
		if (!window.com) window.com = {};
		window.com.ximdex = {};
		window.X = window.com.ximdex;

		var baseUrl = '##BASE_URL##';

		X = Object.extend(X, {
			XMLNS_XIM: 'http://ximdex.com/schema/1.0',
			baseUrl: baseUrl,
			restUrl: baseUrl + '/xmd/loadaction.php',
			iconsUrl: baseUrl + '/xmd/images/icons',
			tourInstance: null,
			getUID: function() {
				return Object.rand(10000000, 99999999);
			}
		});

		X.getTourInstance = function() {
			if (X.tourInstance === null) {
				X.tourInstance = new X.Tour();
			}
			return X.tourInstance;
		};

		X.checkSession = function(status) {
			var redirect = false;
			status = parseInt(status);
			switch (status) {
				case 401:
					location = X.baseUrl;
					redirect = true;
					break;
			}
			return redirect;
		}
	}



	if (typeof(Object.extend) != 'function') {
		Object.extend = function(subclass, superclass) {
			for (var o in superclass) {
				//if (!subclass[o]) {
					subclass[o] = superclass[o];
				//}
			}
			return subclass;
		}
	}

	Object.extend(Object, {
		isArray: function(object) {
			object = Object.isUndefined(object) ? null : object;
			return (
				object !== null &&
				typeof(object) == 'object' &&
				'splice' in object &&
				'join' in object
			);
		},
		isFunction: function(object) {
			object = Object.isUndefined(object) ? null : object;
			return typeof(object) == 'function';
		},
		isObject: function(object) {
			object = Object.isUndefined(object) ? null : object;
			return (
				object !== null &&
				typeof(object) == 'object' &&
				!Object.isArray(object)
			);
		},
		isString: function(object) {
			object = Object.isUndefined(object) ? null : object;
			return typeof(object) == 'string';
		},
		isNumber: function(object) {
			object = parseInt(Object.isUndefined(object) ? null : object);
			return !isNaN(object);
		},
		isUndefined: function(object) {
			return typeof(object) == 'undefined';
		},
		isBoolean: function(object) {
			object = Object.isUndefined(object) ? 0 : object;
			return typeof(object.valueOf()) == 'boolean';
		},
		isEmpty: function(object) {
			var empty = (
				object === null ||
				Object.isUndefined(object) ||
				(Object.isString(object) && object.length == 0)
			);
			return empty;
		},
		clone: function(object) {
			return Object.extend({}, object);
		}
	});

	if (!Object.isFunction(window.$A)) {
		window.$A = function(array) {
			return Array.prototype.slice.call(array);
		}
	}

	if (!Object.isFunction(Object.iface)) {
		Object.iface = function(/* methods */) {
			var methods = $A(arguments);
			var F = function() {
				throw new Error('This class must be implemented!');
			}
			for (var i=0, l=methods.length; i<l; i++) {
				F.prototype[methods[i]] = (function() {
					var method = methods[i];
					return function() {
						throw new Error('Method %s must be implemented!'.printf(method));
					}
				})();
			}
			return F;
		}
	}

	if (!Object.isFunction(Object.xo_create)) {
		Object.xo_create = function(/* superclass [, superclass, ...] */) {

			var args = $A(arguments);
			var superclass = args.shift() || Object.xo_create({});
			superclass = superclass.prototype || superclass;

			var fn = function() {
				if (Object.isFunction(this._init)) {
					this._init.apply(this, arguments);
				}
			};

			Object.extend(fn.prototype, superclass);
			for (var i=0; i<args.length; i++) {
				Object.extend(fn.prototype, (args[i].prototype || args[i]));
			}

			fn.prototype.constructor = fn;

			if (!fn.prototype._init) {
				fn.prototype._init = function() {};
			}

			fn.superclass = superclass;
			fn._super = function(/* scope, method, arguments */) {
				var args = $A(arguments);
				var scope = args.shift();
				var method = args.shift();
				try {
					if (Object.isFunction(this.superclass[method])) {
						return this.superclass[method].apply(scope, args);
					}
				} catch(e) {
					throw e;
				}
			}
			fn._construct = function(/* scope, arguments */) {
				var args = $A(arguments);
				var scope = args.shift();
				try {
					return this.superclass._init.apply(scope, args);
				} catch(e) {
					throw e;
				}
			}

			return fn;
		}
	}

	if (!Object.isFunction(Object.rand)) {
		Object.rand = function(lower, upper) {
			lower = lower || 0;
			upper = upper || 1;
			var n = Math.floor((upper-(lower-1)) * Math.random()) + lower;
			return n;
		}
	}

	if (!Object.isFunction(Function.prototype.bind)) {
		Function.prototype.bind = function(/* scope, arguments */) {
			var args = $A(arguments);
			var scope = args.shift();
			var method = this;
			return function() {
				var a = $A(arguments).concat(args);
				return method.apply(scope, a);
			}
		}
	}

	if (!Object.isFunction(Array.prototype.contains)) {
		Array.prototype.contains = function(value) {
			var l = this.length;
			while (--l >= 0) {
				if (this[l] === value) return true;
			}
			return false;
		}
	}

	if (!Object.isFunction(Array.prototype.clone)) {
		Array.prototype.clone = function() {
//			console.group();
//			console.log(this)
			var ret = [].concat(this);
//			console.log(ret);
//			console.groupEnd();
			return ret;
		}
	}

	if (!Object.isFunction(Array.prototype.each)) {
		Array.prototype.each = function(callback) {
			if (!Object.isFunction(callback)) return;
			for (var i=0, l=this.length; i<l; i++) {
				callback(i, this[i]);
			}
		}
	}

	if (!Object.isFunction(Array.prototype.unique)) {
		Array.prototype.unique = function() {
			var arr = [];
			for (var i=0, l=this.length; i<l; i++) {
				if (!arr.contains(this[i])) {
					arr.push(this[i]);
				}
			}
			return arr;
		}
	}

	function printf(string, values) {
		var c = 0;
		return string.replace(/%[sd]/g, function(match, position, string) {
			var value = values[c];
			switch (match) {
				case '%s':
					value = new String(value).valueOf();
					break;
				case '%d':
					value = new Number(value).valueOf();
					break;
			}
			c++;
			return value;
		});
	}
	if (!Object.isFunction(String.prototype.printf)) {
		String.prototype.printf = function() {
			return printf(this.valueOf(), $A(arguments));
		}
	}
	if (!Object.isFunction(Array.prototype.printf)) {
		Array.prototype.printf = function(string) {
			return printf(string, this);
		}
	}

	// Characters to substitute:: TAB, SPACE, LINE FEED, CARRIAGE RETURN
	var chars = ['\u0009', '\u0020', '\u000A', '\u000D'];

	if (!Object.isFunction(String.prototype.ltrim)) {
		String.prototype.ltrim = function() {
			var str = this.valueOf();
			for (var i=0, l=str.length; i<l; i++) {
				if (chars.contains(str.charAt(0))) {
					str = str.substring(1);
				} else {
					break;
				}
			}
			return str;
		}
	}
	if (!Object.isFunction(String.prototype.rtrim)) {
		String.prototype.rtrim = function() {
			var str = this.valueOf();
			for (var i=str.length-1; i>=0; i=str.length-1) {
				if (chars.contains(str.charAt(str.length-1))) {
					str = str.substring(0, str.length-1);
				} else {
					break;
				}
			}
			return str;
		}
	}
	if (!Object.isFunction(String.prototype.trim)) {
		String.prototype.trim = function() {
			return this.valueOf().ltrim().rtrim();
		}
	}

	// Loads a script
	if (!Object.isFunction(Object.loadScript)) {
		Object.loadScript = function() {

			var args = $A(arguments);
			if (args.length == 0) return;

			// Triggered when all the scripts are loaded
			var onComplete = null;
			// Triggered when one script is loaded
			var onLoad = null;

			if (Object.isArray(args[0])) args = args[0];

			if (Object.isFunction(args[0])) {
				onComplete = args.splice(0, 1)[0];
			}

			if (Object.isObject(args[0])) {
				onComplete = args[0].onComplete || null;
				onLoad = args[0].onLoad || null;
				args = args[0].js || [];
			}

			var count = args.length;
			var head = document.getElementsByTagName('head')[0];
			args.each(function(index, item) {

				Object.ajax(item, {
					onComplete: function(data, xhr) {
						var script = document.createElement('script');
						script.setAttribute('type', 'text/javascript');
						script.setAttribute('src', item);
						script.onload = (function(script, data, xhr) {
							return function() {
								// Handle memory leak in IE
								script.onload = null;
								head.removeChild(script);
								count--;
								if (Object.isFunction(onLoad)) {
									onLoad(data);
								}
								if (Object.isFunction(onComplete) && count == 0) {
									onComplete();
								}
							};

						})(script, data, xhr);
						head.appendChild(script);
					}
				});
			}.bind(this));
		}
	}

	// Loads a css stylesheet
	if (!Object.isFunction(Object.loadCss)) {
		Object.loadCss = function() {
			var args = $A(arguments);
			if (args.length == 0) return;
			if (Object.isArray(args[0])) args = args[0];
			var head = document.getElementsByTagName('head')[0];
			args.each(function(index, item) {
				var css = document.createElement('link');
				css.setAttribute('type', 'text/css');
				css.setAttribute('href', item);
				css.setAttribute('rel', 'stylesheet');
				head.appendChild(css);
//				console.log(item);
			}.bind(this));
		}
	}

	// Loads css stylesheets and scripts, css always goes first
	if (!Object.isFunction(Object.loadAssets)) {
		Object.loadAssets = function() {
			var args = $A(arguments);
			if (args.length == 0) return;

			var jsExp = /\.js$/i;
			var cssExp = /\.css$/i;
			var assets = {
				script: [],
				css: []
			};
			args.each(function(index, item) {
				// NOTE: Functions will be callbacks on Object.loadScript()
				if (jsExp.test(item) || Object.isFunction(item)) {
					assets.script.push(item);
				} else if (cssExp.test(item)) {
					assets.css.push(item);
				}
			}.bind(this));
			Object.loadCss.apply(Object, assets.css);
			Object.loadScript.apply(Object, assets.script);
		}
	}


	/**
	 *  The AJAX Request object!
	 */
	if (!Object.isFunction(Object.ajax)) {
		Object.ajax = function(url, options) {

			this.send = function(url, options) {

				// TODO: Parse and work with options.params!

				options = options || {};
				options.async = options.async || true;
				options.method = options.method || 'GET';
				options.method = options.method.toUpperCase();
				options.params = this._parseParams(options.params || {});
				options.type = options.type || 'text';
				options.type = options.type.toLowerCase();
				options.content = options.content || '';

				var req = new XMLHttpRequest();
				req.options = options;

				req.onreadystatechange = function() {

					this.req = req;
					this.options = options;

					try {

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

					var data = null;
					switch (options.type) {
						case 'text':
							data = req.responseText;
							break;
						case 'xml':
							// TODO: Create XML object?
							data = req.responseText;
							break;
						case 'json':
							try {
								data = eval('new function() {return '+req.responseText+';}');
							} catch(e) {
								data = {};
							}
							break;
					}

					if (options.onComplete) {
						options.onComplete(data, req);
					}
				};


				if (options.method == 'GET' && options.params.length > 0) {
					url = '%s?%s'.printf(url, options.params);
					options.params = null;
				}
				req.open(options.method, url, options.async);
				if (options.method == 'POST') {
					req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				}
				req.send(options.params);
			};

			this._parseParams = function(params) {
				if (Object.isString(params)) return params;
				var ret = [];
				for (var o in params) {
					ret.push('%s=%s'.printf(o, params[o]));
				}
				ret = ret.join('&');
				return ret;
			}

			this.send(url, options);

		}
	}

	if (!Object.isFunction(Object.get)) {
		Object.get = function(url, options) {
			options.method = 'get';
			Object.ajax(url, options);
		}
	}

	if (!Object.isFunction(Object.getJSON)) {
		Object.getJSON = function(url, options) {
			options.method = 'get';
			options.type = 'json';
			Object.ajax(url, options);
		}
	}

	if (!Object.isFunction(Object.post)) {
		Object.post = function(url, options) {
			options.method = 'post';
			Object.ajax(url, options);
		}
	}

	/**
	 * @param string wn Widget name
	 * @param string wi Widget ID
	 * @param string a Action name
	 * @param string m Module name
	 */
	Object.getWidgetConf = function(params) {
		/*Object.getJSON(X.restUrl, {
			params: {
				method: 'wconf',
				wn: params.wn || '',
				wi: params.wi || '',
				a: params.a || '',
				m: params.m || ''
			},
			onComplete: params.onComplete || null
		});*/
		$.get(
			X.restUrl,
			{
				method: 'wconf',
				wn: params.wn || '',
				wi: params.wi || '',
				a: params.a || '',
				m: params.m || ''
			},
			function(data, status) {
				data = eval(data);
				if (Object.isFunction(params.onComplete)) params.onComplete(data);
			}
		);
	}

	if (!Object.isFunction(Object.urldecode)) {
		Object.urldecode = function(str) {
			// Decodes URL-encoded string
			//
			// version: 905.3122
			// discuss at: http://phpjs.org/functions/urldecode
			// +   original by: Philip Peterson
			// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			// +      input by: AJ
			// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			// +   improved by: Brett Zamir (http://brett-zamir.me)
			// +      input by: travc
			// +      input by: Brett Zamir (http://brett-zamir.me)
			// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			// +   improved by: Lars Fischer
			// %          note 1: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
			// *     example 1: urldecode('Kevin+van+Zonneveld%21');
			// *     returns 1: 'Kevin van Zonneveld!'
			// *     example 2: urldecode('http%3A%2F%2Fkevin.vanzonneveld.net%2F');
			// *     returns 2: 'http://kevin.vanzonneveld.net/'
			// *     example 3: urldecode('http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a');
			// *     returns 3: 'http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a'

			var histogram = {}, ret = str.toString(), unicodeStr='', hexEscStr='';

			var replacer = function(search, replace, str) {
			    var tmp_arr = [];
			    tmp_arr = str.split(search);
			    return tmp_arr.join(replace);
			};

			// The histogram is identical to the one in urlencode.
			histogram["'"]   = '%27';
			histogram['(']   = '%28';
			histogram[')']   = '%29';
			histogram['*']   = '%2A';
			histogram['~']   = '%7E';
			histogram['!']   = '%21';
			histogram['%20'] = '+';
			histogram['\u00DC'] = '%DC';
			histogram['\u00FC'] = '%FC';
			histogram['\u00C4'] = '%D4';
			histogram['\u00E4'] = '%E4';
			histogram['\u00D6'] = '%D6';
			histogram['\u00F6'] = '%F6';
			histogram['\u00DF'] = '%DF';
			histogram['\u20AC'] = '%80';
			histogram['\u0081'] = '%81';
			histogram['\u201A'] = '%82';
			histogram['\u0192'] = '%83';
			histogram['\u201E'] = '%84';
			histogram['\u2026'] = '%85';
			histogram['\u2020'] = '%86';
			histogram['\u2021'] = '%87';
			histogram['\u02C6'] = '%88';
			histogram['\u2030'] = '%89';
			histogram['\u0160'] = '%8A';
			histogram['\u2039'] = '%8B';
			histogram['\u0152'] = '%8C';
			histogram['\u008D'] = '%8D';
			histogram['\u017D'] = '%8E';
			histogram['\u008F'] = '%8F';
			histogram['\u0090'] = '%90';
			histogram['\u2018'] = '%91';
			histogram['\u2019'] = '%92';
			histogram['\u201C'] = '%93';
			histogram['\u201D'] = '%94';
			histogram['\u2022'] = '%95';
			histogram['\u2013'] = '%96';
			histogram['\u2014'] = '%97';
			histogram['\u02DC'] = '%98';
			histogram['\u2122'] = '%99';
			histogram['\u0161'] = '%9A';
			histogram['\u203A'] = '%9B';
			histogram['\u0153'] = '%9C';
			histogram['\u009D'] = '%9D';
			histogram['\u017E'] = '%9E';
			histogram['\u0178'] = '%9F';

			for (unicodeStr in histogram) {
			    hexEscStr = histogram[unicodeStr]; // Switch order when decoding
			    ret = replacer(hexEscStr, unicodeStr, ret); // Custom replace. No regexing
			}

			// End with decodeURIComponent, which most resembles PHP's encoding functions
			ret = decodeURIComponent(ret);

			return ret;
		}
	}

	namespace();


	X.ximModules = {
		modules: null,
		getAll: function() {
				if(X.ximModules.modules == null) return 0;
				return X.ximModules.modules;
			},
			get: function(module) {
				if(X.ximModules.modules == null) return 0;
				return X.ximModules.modules[module];
			},
			isEnabled: function(module) {
				if(X.ximModules.modules == null) return 0;
				return X.ximModules.modules[module]["enable"];
			},
			init: function()  {
				if(X.ximModules.modules != null) return null;

			$.getJSON(
				X.restUrl + '?method=modules&ajax=json',
				function(data) {
					X.ximModules.modules = data;
				}
			)
		}
	}




	X.widgetsVars = {
		widgets: null,
		parseValue: function(value) {
			var ret = undefined;
			try {
				if (['true', 'false', 'null', 'undefined', ''].contains(value)) {
					ret = eval(value);
				} else if (Object.isNumber(value)) {
					ret = parseInt(value, 10);
				} else if ( Object.isFunction(eval(value)) ) {
					ret = eval(value);
				} else {
					ret = value;
				}
			} catch(e) {
				ret = value;
			}
			return ret;
		},
		triggerLoaded: function(wname, widget) {
			var _object =  X.widgetsVars[wname];
			$(X.browser.actionEventSelector).trigger("widgetLoaded",  [{object: _object,  widget: widget}]);
		},

		getWidget: function(widget) {
			return X.widgetsVars[widget] || null;
		},

		getWidgetOfType: function(type) {
			return X.widgetsVars.widgets[type];
		},

		getLastWidgetOfType: function(wtype) {
			if(null == X.widgetsVars.widgets || null == X.widgetsVars.widgets[wtype])
				return null;

			var last = X.widgetsVars.widgets[wtype].length;
			return X.widgetsVars.widgets[wtype][last];
		},

		setWidgetType: function(widget,wtype) {

			if ( null == X.widgetsVars.widgets ) {
				X.widgetsVars.widgets = {};
			}

			if(null ==  X.widgetsVars.widgets[wtype] ) {
				X.widgetsVars.widgets[wtype] = [];
			}

			X.widgetsVars.widgets[wtype].push(widget);
		},

		getValue: function(widget, param) {
			var w = X.widgetsVars.getWidget(widget);
			var ret = (w && w[param] !== undefined) ? w[param] : null;
			return ret;
		},

		getValues: function(widget) {
			return X.widgetsVars[widget] || {};
		},
		setValue: function(widget, param, value) {
			var w = X.widgetsVars.getWidget(widget);
			if (w) w[param] = X.widgetsVars.parseValue(value);
		},
		setJSONString: function(widget, jsonString) {
			var json = $.parseJSON(jsonString);

			var w = X.widgetsVars[widget] = json;
			w["wid"] = widget;


			for (var o in w) {
				w[o] = X.widgetsVars.parseValue(w[o]);
			}


			try {
				w[o] = X.widgetsVars.setWidgetType(w, w["wtype"]);
			}catch(e) {
				//console.log("Widget "+widget+" without type");
			}

		}
	};

})();
