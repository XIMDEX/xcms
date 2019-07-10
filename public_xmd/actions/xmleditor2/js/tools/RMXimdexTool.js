/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

	updateState: function(options) {},
	
	beforeUpdateContent: function(options) {
		this.resolveMacros(options.xslResult);
	},
	
	resolveMacros: function(xslResult) {

		// SRC dotdot macro
		$("[src*='@@@RMximdex.dotdot']", $('body', xslResult)[0]).each(function(index, elem) {
			var path = unescape($(elem).attr('src'));
			path = this.editor.getDotDotPath() + path.replace(/@@@RMximdex.dotdot\((.*)\)@@@/ig, "$1");
			path += '&token=' + Math.random();
			$(elem).attr('src', path);
		}.bind(this));

		// CSS dotdot macro
		$("link[href*='@@@RMximdex.dotdot']", $('html', xslResult)[0]).each(function(index, elem) {
			var path = unescape($(elem).attr('href'));
			path = this.editor.getDotDotPath() + path.replace(/@@@RMximdex.dotdot\((.*)\)@@@/ig, "$1");
			path += '&token=' + Math.random();
			$(elem).attr('href', path);
		}.bind(this));

		// IMG pathto macro
		$("img[src*='@@@RMximdex.pathto']", $('body', xslResult)[0]).each(function(index, elem) {
			var targetid = unescape($(elem).attr('src'));
			targetid = targetid.replace(/@@@RMximdex.pathto\((.*)\)@@@/ig, "$1");
			var path = '%s?expresion=%s&action=rendernode' . printf(X.restUrl, targetid);
			if (this.editor.nodeId) {
				path += '&id=' + this.editor.nodeId;
			}
			path += '&token=' + Math.random();
			$(elem).attr('src', path);
		});

        // STYLE pathto and dotdot macro
        $("[style*='@@@RMximdex']", $('body', xslResult)[0]).each(function(index, elem) {
            var targetid = unescape($(elem).attr('style'));
            targetid = targetid.replace(/@@@RMximdex\.pathto\((.*)\)@@@/ig, X.restUrl + "?expresion=$1&action=rendernode");
            targetid = targetid.replace(/@@@RMximdex\.dotdot\((.*)\)@@@/ig, this.editor.getDotDotPath() + "$1");
            if (this.editor.nodeId) {
            	targetid += '&id=' + this.editor.nodeId;
        	}
            targetid += '?token=' + Math.random();
            $(elem).attr('style', targetid);
        }.bind(this));
        
        // CSS pathto macro
		$("link[href*='@@@RMximdex.pathto']", $('html', xslResult)[0]).each(function(index, elem) {
			var path = unescape($(elem).attr('href'));
			path = path.replace(/@@@RMximdex.pathto\((.*)\)@@@/ig, X.restUrl + "?expresion=$1&action=rendernode");
			if (this.editor.nodeId) {
				path += '&id=' + this.editor.nodeId;
			}
			path += '&token=' + Math.random();
			$(elem).attr('href', path);
		}.bind(this));
	}
});
