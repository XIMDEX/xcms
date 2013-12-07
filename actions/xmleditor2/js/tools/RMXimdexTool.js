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



var RMXimdexTool = Object.xo_create(XimdocTool, {
	
	initialize: function(editor) {
		
		RMXimdexTool._super(this, 'initialize', editor);
	},

	updateState: function(options) {
		
	},
	
	beforeUpdateContent: function(options) {
		this.resolveMacros(options.xslResult);
	},
	
	resolveMacros: function(xslResult) {

		// dotdot macro
		$("[src*='@@@RMximdex.dotdot']", $('body', xslResult)[0]).each(function(index, elem) {
			var path = unescape($(elem).attr('src'));
			path = this.editor.getDotDotPath() + path.replace(/@@@RMximdex.dotdot\((.*)\)@@@/ig, "$1");
			$(elem).attr('src', path);
		}.bind(this));

		$("link[href*='@@@RMximdex.dotdot']", $('html', xslResult)[0]).each(function(index, elem) {
			var path = unescape($(elem).attr('href'));
			path = this.editor.getDotDotPath() + path.replace(/@@@RMximdex.dotdot\((.*)\)@@@/ig, "$1");
			$(elem).attr('href', path);
		}.bind(this));

		// pathto macro
		$("img[src*='@@@RMximdex.pathto']", $('body', xslResult)[0]).each(function(index, elem) {
			var targetid = unescape($(elem).attr('src'));
			targetid = targetid.replace(/@@@RMximdex.pathto\((.*)\)@@@/ig, "$1");
			var path = '%s?expresion=%s&action=filemapper&method=nodeFromExpresion'.printf(X.restUrl, targetid);
			$(elem).attr('src', path);
		});
	}

});
