
/**
 * ximdex v.3 --- A Semantic CMS
 * Copyright (C) 2010, Open Ximdex Evolution SL <dev@ximdex.org>
 *
 * This program is commercial software.
 * Check version 2 of ximdex for the open source version.
 *
 * @author XIMDEX Team <dev@ximdex.org>
 *
 * @version $Revision: 7620 $
 *
 *
 * @copyright (C) Open Ximdex Evolution SL, {@link http://www.ximdex.org}
 * @license Commercial (check ximdex version 2 for the open source software)
 *
 * $Id: datasource.js 7620 2011-06-09 11:55:08Z fjcarretero $
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
