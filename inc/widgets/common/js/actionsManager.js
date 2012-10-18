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

X.ActionsManager = Object.xo_create({

	_init: function(options) {
		
		this.options = Object.extend({
			prefix: '',
			container: null
		}, options);
		
		if (!this.options.container || this.options.prefix.length == 0) return;
		
		this.method = this.options.prefix;
		this.loadEvent = this.method + 'load';
		this._loadAssetsWrapper = this._loadAssets.bind(this);
	},
	
	callAction: function(params) {
		if (!this.options.container) return;
		
		var action = '';
		var nodes = [];
		var actionParams = '';
		
		if (params.data['actionid']) {
		
			action = 'actionid=' + params.data.actionid.value;
		} else if (params.data['action']) {
		
			action = 'action=' + params.data.action.value;
		} else {
			
			// No defined action
			action = 'action=_NONE_';
		}
		
		if (params.data['nodes']) {
			params.data.nodes.each(function(index, item) {
				nodes.push('nodes[]=' + item);
			});
		} else if (params.data['nodeid']) {
			nodes.push('nodeid=' + params.data.nodeid.value);
		}
		
		if (params.data.params.value.length > 0) {
			actionParams = params.data.params.value;
		}
		
		var qs = [
			nodes.join('&'),
			action,
			'mod='+params.data.mod.value,
			actionParams,
			'noCacheVar='+(new Date().getTime())
		].join('&');
		
		var url = '%s/xmd/loadaction.php?%s'.printf(window.url_root, qs);

		if (!Object.isFunction($(this.options.container)[this.method])) return;
		
		$(this.options.container)
			.unbind(this.loadEvent, this._loadAssetsWrapper)
			.bind(this.loadEvent, this._loadAssetsWrapper);
	
		$(this.options.container)[this.method]('callAction', url, params.data.name.value);		
	},
	
	_loadAssets: function(event, ui) {
		var panel = ui.panel;
		this._loadAssetsForPanel(panel);

	},

	_loadAssetsForPanel: function(panel) {
		var css = $('ul.css_to_include li', panel).map(function(index, item) {
			return $(item).html();
		});
		css = $.makeArray(css);

		var js = $('ul.js_to_include li', panel).map(function(index, item) {
			
			var url = $(item).html();
			url = this._urldecode(url.replace(/&amp;/g, '&', url));
			return url;
		}.bind(this));
		
		js = {
			onComplete: this._onScriptCompleted.bind(this),
			onLoad: function(content) { return this._onScriptLoaded(content, panel); }.bind(this),
			js: js
		};
		Object.loadCss(css);
		Object.loadScript(js);
	},
	_urldecode: function( str ) {
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
	},
	
	
	/**
	 * Specific stuff for classes and tabs
	 */
	_onScriptLoaded: function(content, panel) {
	
		if (this.method != 'tabs') return;
		
		var matches = content.match(/\$\.widget\(\'ui\.canvas_i\'/);
		if (matches != null && matches.length > 0) {
			var panel_url = $('.anchor', $('.' + panel.attr('id'))).attr('href');
			$(this.options.container).canvas('add', panel, panel_url);
		}
	},
	
	_onScriptCompleted: function() {
		// css modifiers
		$('.cabeceratabla').removeClass('cabeceratabla').addClass('ui-widget ui-widget-header');
		$('.filaoscura').removeClass('filaoscura').addClass('ui-state ui-state-active');
		$('.filaclara').removeClass('filaclara').addClass('ui-state');
	}

});
