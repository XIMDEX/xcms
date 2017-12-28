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



/*****************************************************************************
 *
 * Copyright (c) 2003-2005 Kupu Contributors. All rights reserved.
 *
 * This software is distributed under the terms of the Kupu
 * License. See LICENSE.txt for license text. For a list of Kupu
 * Contributors see CREDITS.txt.
 *
 *****************************************************************************/

// $Id$


window.kupu = null;

function startKupu(options) {

	options.showOverlayOnFirstEdition = options.showOverlayOnFirstEdition || false;
	options.availableViews = options.availableViews || ['tree', 'normal', 'pro'];

	$('#kupu-editor').load(function() {

		// Register the unload event.
		// If this document is a ximlet, the opener could refresh the ximlet content.
		// See ximletTool.ximletDblClick()

		var ximletid = 'unload_ximlet_' + $('.kupu-fulleditor .kupu-ximparams #kupu-nodeId').html().trim();
		if (window.opener && window.opener[ximletid]) {
			window.onunload = window.opener[ximletid];
		}

	    	// initializing the editor, initKupu groks 1 arg, a reference to the iframe
	    	var frame = getFromSelector('kupu-editor');

	    	// we create the document, hand it over the id of the iframe
	    	var doc = new KupuDocument(frame);

		// now we can create the controller
		options = $.extend(options, {
			document: doc,
			logger: null
		});

		var kupu = new XimdocEditor(options);

		kupu.initialize(startKupuCallback);

		    // $('#kupu-editor').show();

		this.kupu = kupu;

		if ((kupu.loadErrors == null || kupu.loadErrors == false)){
		    loadingImage.createLoadingImage();
		    if(options.showOverlayOnFirstEdition)
		    	loadingImage.showLoadingImage();
		}


		return kupu;


	}.bind(this));

	// This ensures that all posible scripts loaded in memory are unloaded ;)
	// $('#kupu-editor').hide();
	$('#kupu-editor')[0].src = url_root + '/actions/xmleditor2/index.html';
}

function startKupuCallback(kupu, errors) {

	// NOTE: If the beforeUnload callback returns anything (true, false, null, ...)
	// a confirm dialog will be displayed.
	// If the returned data is a string, that string will be shown in the confirm dialog.
	if (kupu){
	    if (kupu.config.confirm_on_exit == 1) {
		    window.onbeforeunload = function() {
			    return false;
		    };
	    }

	    $(window).unload(function() {
		    // None
	    }.bind(this));


	kupu.log = new XimdexLogger({
		    toolboxId: 'xedit-ximdexlogger-toolbox',
		    ctrlButtonId: 'kupu-toolbox-debuglog-button',
		    buttonActiveClass: 'kupu-toolbox-debuglog-button',
		    buttonInactiveClass: 'kupu-toolbox-debuglog-button-closed',
		    maxlength: kupu.config.logger_max_length,
		    tool: null,
		    editor: kupu,
		    visible: new Boolean(kupu.config.toolboxes.logger)
	    });

	}
	if (kupu && !errors) {
		initKupuTools(kupu);
		continueStartKupu(kupu);
		return kupu;
	} else {
		show_wMessage($('#wMessage')[0], errors);
		kupu.loadErrors = true;
		return false;
	}

	return true;
}


function show_wMessage(wMessage, errors) {

	var htmlmsg="";
	for (var i=0; i<errors.length; i++) {
		console.error(errors[i]);
		htmlmsg += '<br/>' + errors[i];
	}
	if (window.kupu){
	    window.kupu.alert(htmlmsg);
	}
	/*var htmlmsg = '<ul>';
	for (var i=0; i<errors.length; i++) {
		console.error(errors[i]);
		htmlmsg += '<li>' + errors[i] + '</li>';
	}
	htmlmsg += '</ul>';
	$('#wMessage').css({
		'margin-left': 'auto',
		'margin-right': 'auto',
		'padding-top': '4px',
		'padding-bottom': '4px',
		'padding-right': '10px',
		top: '20%',
		//width: '40%',
		height: (errors.length*4) + '%',
		'font-size': '14px'
	});
	$('#wMessage').html(htmlmsg);*/


}
