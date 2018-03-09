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




var com; 
if (!com) com = {}; 
if (!com.ximdex) com.ximdex = {}; 
if (!com.ximdex.ximdex) com.ximdex.ximdex = {}; 
if (!com.ximdex.ximdex.editors) com.ximdex.ximdex.editors = {};

com.ximdex.ximdex.editors.LoadHandler = function() {

}


com.ximdex.ximdex.editors.AjaxRequest = function(url, content, callback){
	$.post(url, content,function(data){
		if (callback.onComplete) callback.onComplete(arguments,data);
	}).fail(function(){
		if (callback.onError) callback.onError();
	});
}

com.ximdex.ximdex.editors.SaveHandler = function(baseURL, content, callback, autoSave) {

	var url = baseURL + '&method=saveXmlFile';
	if(autoSave === true)
		url = url + '&autosave=true';

	com.ximdex.ximdex.editors.AjaxRequest(url, content, callback);
	
	$(document).trigger('saveTags');
}

com.ximdex.ximdex.editors.PublicateHandler = function(baseURL, content, callback) {

	var url = baseURL + '&method=publicateFile';
	com.ximdex.ximdex.editors.AjaxRequest(url, content, callback);
}

com.ximdex.ximdex.editors.ValidateHandler = function(baseURL, content, callback) {

	var url = baseURL + '&ajax=json&method=validateSchema';
	com.ximdex.ximdex.editors.AjaxRequest(url, content, callback);	
}

com.ximdex.ximdex.editors.PreviewHandler = function(loadActionURL, content, callback, bxeOptions) {

	var url = loadActionURL + '&action=rendernode&ajax=json';
	com.ximdex.ximdex.editors.AjaxRequest(url, content, callback);
}

com.ximdex.ximdex.editors.SpellCheckingHandler = function(baseURL, content, callback, bxeOptions) {

	var url = baseURL + '&method=getSpellCheckingFile';
	com.ximdex.ximdex.editors.AjaxRequest(url, content, callback);
}

com.ximdex.ximdex.editors.noRenderizableElementsHandler = function(baseURL, content, callback, bxeOptions) {

	var url = baseURL + '&method=getNoRenderizableElements';
	com.ximdex.ximdex.editors.AjaxRequest(url, content, callback);
}

com.ximdex.ximdex.editors.AnnotationHandler = function(baseURL, content, callback, bxeOptions) {

	var url = baseURL + '&method=getAnnotationFile&ajax=json';
	com.ximdex.ximdex.editors.AjaxRequest(url, content, callback);
}

com.ximdex.ximdex.editors.PreviewInServerHandler = function(baseURL, content, callback, bxeOptions) {

	var url = baseURL + '&method=getPreviewInServerFile';
	com.ximdex.ximdex.editors.AjaxRequest(url, content, callback);
}