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



var ImagesTool = Object.xo_create(XimdocTool, {
	
	initialize: function(editor) {
		
		ImagesTool._super(this, 'initialize', editor);
	},

	updateState: function(options) {
		this.updateImagesSize(this.editor.getInnerDocument());
	},

	afterUpdateContent: function(options) {
		this.updateImagesSize(options.xslResult);
	},
	
	beforeSave: function() {
		this.updateImagesSize(this.editor.getInnerDocument());
	},

	// Doesn't works fine...
//	mouseUp: function(options) {
//		
//		var images = [options.selNode];
//		
//		if (options.selNode.tagName.toUpperCase() != 'IMG') {
//			images = $('img[uid]', options.selNode);
//		}
//		
//		for (var i=0,l=images.length; i<l; i++) {
//			this.updateImageSize(images[i]);
//		}
//	},
	
	updateImageSize: function(image) {
		if($(image).length <1) return ;
		
		var width = 0, height = 0;
		if($(image).attr('width')   )
			width = $(image).width();

		if($(image).attr('height') )
			height = $(image).height();
		

		var dim = {
			'w': parseInt(width),
			'h': parseInt(height)
		};
		

		if (isNaN(dim.w) || isNaN(dim.h)) return;
		
		var ximElement = this.editor.getXimDocument().getElement(image.getAttribute('uid'));
		if(ximElement) {
			ximElement.attributes.width = dim.w;
			ximElement.attributes.height = dim.h;
		}
	},
	
	updateImagesSize: function(doc) {
		$('img[uid]', doc).each(function(index, item) {
			this.updateImageSize(item);
		}.bind(this));
	}

});
