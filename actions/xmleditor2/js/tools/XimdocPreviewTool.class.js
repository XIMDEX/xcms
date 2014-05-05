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
 * Previews document.
 */

function XimdocPreviewTool() {

	this.initialize = function (editor) {
	        this.editor = editor;
	};

	this.preview = function () {

    		var imagestool = this.editor.getTool('imagestool');
	    	imagestool.updateImagesSize(this.editor.getInnerDocument());

		// It's necessary to update editor content before previewing
		this.setActionDescription(_('Show preview'));
		this.editor.updateEditor({caller: this});

		var content = this.editor._ximdoc.saveXML({
			asString: true,
			hideXimlets: true
	 	});

		//Get selected channel
		var channelSelector = this.editor.tools.ximdoctool.toolboxes.channelstoolbox.select;
		var channelValue = channelSelector.options[channelSelector.selectedIndex].value;

		// Calling PreviewHandler
		var encodedContent = "&nodeid=" + this.editor.nodeId +
				 "&channelid="+ channelValue +
				 "&content=" + encodeURIComponent(content);

		var encondedObject = {
			"nodeid": this.editor.nodeId,
			"channelid": channelValue,
			"content": content
		};

		com.ximdex.ximdex.editors.PreviewHandler(kupu.getLoadActionURL(), encondedObject, {
			onComplete: function(req, json) {
//				this.showPreviewByContent(req.responseText);
				this.showPreviewByUrl(json.prevUrl);
			}.bind(this),
			onError: function(req) {
				this.editor.alert(_('Error obtaining preview.'));
			}.bind(this)
		});
	};

	this.showPreviewByContent = function(content) {

        	var win = window.open();
			win.document.write(content);
			win.document.close();
			delete win;
		};

	
	this.showPreviewByUrl = function(url) {

        	var win = window.open(url);
			delete win;
	};
}

XimdocPreviewTool.prototype = new XimdocTool();
