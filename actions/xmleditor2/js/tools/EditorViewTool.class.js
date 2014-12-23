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




function EditorViewTool() {

   	this.VIEW_TREE = 'tree';
   	this.VIEW_DESIGN = 'normal';
        this.VIEW_FORM = 'form';
   	this.VIEW_REMOTE = 'pro'; //Why is not called as "remote"?


	this.editor = null;
	this._body = null;

	this.treeButton = null;
	this.designButton = null;
        this.formButton = null;
	this.remoteButton = null;

    this.initialize = function(editor) {

        this.toolboxes = {};
        this.editor = editor;
        this._body = editor.getBody();

        this.treeButton = new KupuButton('kupu-treeview-button', this._getSetViewWrapper(this.VIEW_TREE));
		editor.registerTool('treeview', this.treeButton);
        this.designButton = new KupuButton('kupu-designview-button', this._getSetViewWrapper(this.VIEW_DESIGN));
		editor.registerTool('designview', this.designButton);
        /*this.formButton = new KupuButton('kupu-formview-button', this._getSetViewWrapper(this.VIEW_FORM));
		editor.registerTool('formview', this.formButton);*/
        this.activateButtons();
    };

    this._getSetViewWrapper = function(view) {
    	return function() {
    		this.setView(view);
    	}.bind(this);
    };

    this.setView = function(view) {

		 loadingImage.showLoadingImage();
		this.editor.setView(view);

		$(this.treeButton.button).removeClass('kupu-treeview-pressed').addClass('kupu-treeview');
		$(this.designButton.button).removeClass('kupu-designview-pressed').addClass('kupu-designview');
                //$(this.formButton.button).removeClass('kupu-formview-pressed').addClass('kupu-formview');
		//$(this.remoteButton.button).removeClass('kupu-remoteview-pressed').addClass('kupu-remoteview');

		switch (view) {
			case this.VIEW_TREE:
				$(this.treeButton.button).addClass('kupu-treeview-pressed').removeClass('kupu-treeview');
				break;
			case this.VIEW_DESIGN:
				$(this.designButton.button).addClass('kupu-designview-pressed').removeClass('kupu-designview'); 
				break;
                        case this.VIEW_FORM:
				//$(this.formButton.button).addClass('kupu-formview-pressed').removeClass('kupu-formview');
				break;
			case this.VIEW_REMOTE:
				//$(this.remoteButton.button).addClass('kupu-remoteview-pressed').removeClass('kupu-remoteview');
				break;
		}

		// XSLT request
		var xslIncludesOnServer = $('.kupu-fulleditor .kupu-ximparams #kupu-xslIncludesOnServer').html().trim();
		var includesInServer="";
		//Avoid includes tag in xsl when by configuration or when browser is in safari or chrome
		if (xslIncludesOnServer == 1 || IS_SAFARI || IS_CHROME)
		    includesInServer = "&includesInServer=1";
		var xslUrl = this.editor.getBaseURL() + '&ajax=json&method=getXslFile'+includesInServer+'&view=' + this.editor.getView();
		this.editor.fileRequest('_xslDom', xslUrl, function() {
			this.editor.logMessage(_('View changed to') + ' ' + _(view));
		}.bind(this), true);
    };

    this.activateButtons = function() {
    	var views = this.editor.options.availableViews;
        if (!views.contains(this.VIEW_TREE)) this.treeButton.disable();
		if (!views.contains(this.VIEW_DESIGN)) this.designButton.disable();
		//if (!views.contains(this.VIEW_REMOTE)) this.remoteButton.disable();
    };
};

EditorViewTool.prototype = new XimdocTool();
