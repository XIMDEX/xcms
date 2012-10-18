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
 *  @version $Revision: 7969 $
 */




var com; 
if (!com) com = {}; 
if (!com.ximdex) com.ximdex = {}; 
if (!com.ximdex.ximdex) com.ximdex.ximdex = {}; 
if (!com.ximdex.ximdex.editors) com.ximdex.ximdex.editors = {};

com.ximdex.ximdex.editors.LoadHandler = function() {

}

com.ximdex.ximdex.editors.SaveHandler = function(baseURL, content, callback, autoSave) {

	var url = baseURL + '&method=saveXmlFile';
	if(autoSave === true)
		url = url + '&autosave=true';


	new AjaxRequest(url, {
		method: 'POST',
		content: content,
		onComplete: function(req, json) {
			if (callback.onComplete) callback.onComplete(req, json);
		}.bind(this),
		onError: function(req) {
			if (callback.onError) callback.onError(req);
		}.bind(this)
	});
	
	//Tags saving
	try{
		$('.xim-tagsinput-container').tagsinput('save' ,baseURL, content, null, null);
	}catch(e){}
}

com.ximdex.ximdex.editors.PublicateHandler = function(baseURL, content, callback) {

	var url = baseURL + '&method=publicateFile';

	new AjaxRequest(url, {
		method: 'POST',
		content: content,
		onComplete: function(req, json) {
			if (callback.onComplete) callback.onComplete(req, json);
		}.bind(this),
		onError: function(req) {
			if (callback.onError) callback.onError(req);
		}.bind(this)
	});
}

com.ximdex.ximdex.editors.ValidateHandler = function(baseURL, content, callback) {

	var url = baseURL + '&ajax=json&method=validateSchema';

	new AjaxRequest(url, {
		method: 'POST',
		content: content,
		onComplete: function(req, json) {
			//console.log(json, this);
			if (callback.onComplete) callback.onComplete(req, json);
		}.bind(this),
		onError: function(req) {
			//console.error(req);
			if (callback.onError) callback.onError(req);
		}.bind(this)
	});
}

com.ximdex.ximdex.editors.PreviewHandler = function(loadActionURL, content, callback, bxeOptions) {

	var url = loadActionURL + '&action=prevdoc&ajax=json';

	new AjaxRequest(url, {
		method: 'POST',
		content: content,
		onComplete: function(req, json) {
			if (callback.onComplete) callback.onComplete(req, json);
		}.bind(this),
		onError: function(req) {
			if (callback.onError) callback.onError(req);
		}.bind(this)
	});
}

com.ximdex.ximdex.editors.SpellCheckingHandler = function(baseURL, content, callback, bxeOptions) {

	var url = baseURL + '&method=getSpellCheckingFile';

	new AjaxRequest(url, {
		method: 'POST',
		content: content,
		onComplete: function(req, json) {
			if (callback.onComplete) callback.onComplete(req, json);
		}.bind(this),
		onError: function(req) {
			if (callback.onError) callback.onError(req);
		}.bind(this)
	});
}

com.ximdex.ximdex.editors.noRenderizableElementsHandler = function(baseURL, content, callback, bxeOptions) {

	var url = baseURL + '&method=getNoRenderizableElements';

	new AjaxRequest(url, {
		method: 'POST',
		content: content,
		onComplete: function(req, json) {
			if (callback.onComplete) callback.onComplete(req, json);
		}.bind(this),
		onError: function(req) {
			if (callback.onError) callback.onError(req);
		}.bind(this)
	});
}

com.ximdex.ximdex.editors.AnnotationHandler = function(baseURL, content, callback, bxeOptions) {

	var url = baseURL + '&method=getAnnotationFile&ajax=json';

	new AjaxRequest(url, {
		method: 'POST',
		content: content,
		onComplete: function(req, json) {
			if (callback.onComplete) callback.onComplete(req, json);
		}.bind(this),
		onError: function(req) {
			if (callback.onError) callback.onError(req);
		}.bind(this)
	});
}

com.ximdex.ximdex.editors.PreviewInServerHandler = function(baseURL, content, callback, bxeOptions) {

	var url = baseURL + '&method=getPreviewInServerFile';

	new AjaxRequest(url, {
		method: 'POST',
		content: content,
		onComplete: function(req, json) {
			if (callback.onComplete) callback.onComplete(req, json);
		}.bind(this),
		onError: function(req) {
			if (callback.onError) callback.onError(req);
		}.bind(this)
	});
}

/*com.ximdex.ximdex.editors.GetXmlHandler = function(callback, caller) {

	var url = location + '&ajax=json&method=getXmlFile';

	/*var req = new com.ximdex.ximdex.editors.XMLHttpRequest();
	req.send(url, {
		cbFunction: callback,
		content: content,
		caller: caller
	});

	/*new AjaxRequest(url, {
		onComplete: function(req, json) {
			//console.log(json, this);
			callback(req, json);
		}.bind(this),
		onError: function(req) {
			//console.error(req);
			callback(req);
		}.bind(this)
	});
}*/

/*com.ximdex.ximdex.editors.GetSchemaHandler = function(callback, caller) {

	var url = location + '&ajax=json&method=getSchemaFile';

	/*
	var req = new XMLHttpRequest();
	req.overrideMimeType("text/xml");
	req.onload = function(e) {
		var req = e.currentTarget;
		req.caller = caller;
		var json = new Function("return " + req.responseText + ' || {}')();
		callback(req, json, e);
	};
	req.open("POST", url);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	req.send(content, true);


	/*var req = new com.ximdex.ximdex.editors.XMLHttpRequest();
	req.send(url, {
		cbFunction: callback,
		content: content,
		caller: caller
	});
}*/

/*com.ximdex.ximdex.editors.AllowedChildrensHandler = function(uid, content, callback) {

	var url = location + '&ajax=json&method=getAllowedChildrens&uid=' + uid;

	/*
	var req = new XMLHttpRequest();
	req.overrideMimeType("text/xml");
	req.onload = callback;
	req.open("POST", url);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	req.send(content, true);


	var req = new com.ximdex.ximdex.editors.XMLHttpRequest();
	req.send(url, {
		cbFunction: callback,
		content: content
	});

}*/
