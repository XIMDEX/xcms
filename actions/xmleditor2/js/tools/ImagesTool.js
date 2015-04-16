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
    this.scroll ();
    this.resize ();
	},

	updateState: function (options) {

    this.closePopover ();

		if (this.editor.ximElement.schemaNode.type == 'image') {

			this.updateImagesSize (this.editor.getInnerDocument());
			if (this.editor.getView () == 'form') this.popover ();

			//show the drawer
			/*var drawerId 	= 'ximimagedrawer';
			var dt 			= this.editor.getTool ('ximdocdrawertool');

			if (dt.isOpen (drawerId)) return;

			dt.drawers[drawerId].setXimElement (this.editor.ximElement);
			dt.openDrawer (drawerId);*/
		}
	},

	popover: function () {

    var that    = this;
		var element = this.editor.ximElement;
		var img  	  = $('[uid="' + element.uid + '"]', this.editor.getBody());

		$('img', this.editor.getBody()).not (img).popover ('destroy');
		img.popover({
      'animation':  false,
      'placement':  'right',
      'html': 			true,
      'container': 	'.iwrapper',
      'template':   '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
      title: function () {

        return element.tagName;
    	}
    })
		.on ('shown.bs.popover', function () {

      var div = $('<div />');

      for (var attrName in element.schemaNode.attributes) {

        if (attrName == 'uid') continue;

        var attribute = element.schemaNode.attributes [attrName];
        var label     = $('<label />').attr ({for: 'ximimage-' + attrName}).addClass ('title').html (attrName);
        var input     = attribute.values.length ? $('<select />') : $('<input />');

        input.attr ({id: 'ximimage-' + attrName}).addClass ('ximage-input');

        if (attribute.values.length) {

          var j = 0;

          while (j < attribute.values.length) {

            var option  = $('<option />');
            var valor   = attribute.values [j];

            if (valor == element.attributes [attrName]) option.attr('selected', 'selected');
            input.append (option.attr ({value: valor}).html (valor));
            j++;
          }
        }else input.attr ({type: 'text', value: element.attributes [attrName]});

        input.data ('attribute-name', attrName);
        div.append (label, input);
      }

      var buttons = $('<div />').addClass ('buttons clearfix');
      var bsave   = $('<button />').addClass ('btn btn-sm btn-save').html ('ACCEPT');
      var bcancel = $('<button />').addClass ('btn btn-sm btn-cancel').html ('CANCEL');

      buttons.append (bsave, bcancel);
      div.append (buttons);

      $('.popover-content').append (div);

			var img        = $(this);
			var scroll 		 = img.parents ('body').scrollTop ();
			var p 			   = img.position ();
  		var popover 	 = $('.popover');
  		var tolerance  = 50;

  		popover.css ({
  			position: 	'absolute',
  			top: 		    p.top - scroll - (img.outerHeight () / 2),
  			left: 		  p.right - tolerance,
  			zIndex: 	  999,
        opacity:    1
  		})
      .show ();

      $('.btn-save', popover).click (function (event) {

        event.preventDefault ();
        that.save ();
      });

      $('.btn-cancel', popover).click (function (event) {

        event.preventDefault ();
        that.closePopover ();
      });
		})
    .popover ('show');
  },

  scroll: function () {

    var that = this;

    $('iframe').contents ().scroll (function () {

      that.closePopover ();
    });
  },

  resize: function () {

    var that = this;

    $(window).resize (function () {

      that.closePopover ();
    });
  },

  closePopover: function () {

    $('img', $('iframe').contents ()).popover ('destroy');
  },

  beforeUpdateContent: function (options) {

    this.closePopover ();
  },

	afterUpdateContent: function (options) {

		this.updateImagesSize (options.xslResult);
	},

	beforeSave: function() {

		this.updateImagesSize (this.editor.getInnerDocument());
	},

	updateImageSize: function (image) {

		if (! $(image).length) return;

        var width = 0, height = 0;
        if($(image).attr('width') == "auto"){
            width = null;
        }else if($(image).attr('width')){
            width = $(image).width();
        }
        if($(image).attr('height') == "auto" ){
            height = null;
        }else if($(image).attr('height')){
            height = $(image).height();
        }

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
	},

	save: function () {

    var attributes  = {};
    var tool        = this.editor.tools.attributestool;
    var toolbox     = tool.toolboxes.attributestoolbox;

    //no conflict with toolbox
    $('.ximage-input', $('.popover')).each (function (index, elem) {

      var attrName    = $(elem).data ('attribute-name');
      var attrValue   = $(elem).val ();

      attributes[attrName] = attrValue;
    });

    tool.saveAttributes (attributes);
    this.editor.logMessage(_('Attributes updated!'));
    toolbox.setActionDescription(_('Update attributes'));
    toolbox._clean();
    this.editor.updateEditor({caller: this});
    this.closePopover ();
  }
});