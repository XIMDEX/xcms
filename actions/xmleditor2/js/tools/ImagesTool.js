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

var ImagesTool = Object.xo_create(XimdocTool, {

	initialize: function (editor) {

		ImagesTool._super(this, 'initialize', editor);
	},

	updateState: function (options) {

		if (this.editor.ximElement.schemaNode.type == 'image') {

			this.updateImagesSize (this.editor.getInnerDocument());

			//show the drawer
			var drawerId 	= 'ximimagedrawer';
			var dt 			= this.editor.getTool ('ximdocdrawertool');

			if (dt.isOpen (drawerId)) return;

			dt.drawers[drawerId].setXimElement (this.editor.ximElement);
			dt.openDrawer (drawerId);
		}
	},

	afterUpdateContent: function (options) {

		this.updateImagesSize (options.xslResult);
	},

	beforeSave: function() {

		this.updateImagesSize (this.editor.getInnerDocument());
	},

	updateImageSize: function (image) {

		if (! $(image).length) return;

		var width 	= $(image).attr ('width') 	== 'auto' 	? $(image).width () 	: null;
		var height 	= $(image).attr ('height') 	== 'auto' 	? $(image).height () 	: null;

		var dim = {
			'w': parseInt (width),
			'h': parseInt (height)
		};

		if (isNaN (dim.w) || isNaN (dim.h)) return;

		var ximElement = this.editor.getXimDocument().getElement (image.getAttribute('uid'));

		if (ximElement) {
			ximElement.attributes.width 	= dim.w;
			ximElement.attributes.height 	= dim.h;
		}
	},

	updateImagesSize: function (doc) {
		$('img[uid]', doc).each(function(index, item) {

			this.updateImageSize(item);
		}.bind(this));
	}
});