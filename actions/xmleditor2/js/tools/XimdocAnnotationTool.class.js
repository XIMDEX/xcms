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
 *  @version $Revision: 8535 $
 */




/**
 * Annotate document.
 */

function XimdocAnnotationTool() {

	this._highlightStartTag = null;
	this._highlightEndTag = null;
	this._annotationDoc = null;

    this.initialize = function (editor) {

        this.editor = editor;
        this._highlightStartTag = '<font owner="annotator" style="border: 2px solid yellow; background-color: red; color: white;">';
        this._highlightEndTag = '</font>';
        this.editor.logMessage(_('Annotation tool initialized'));
    };

//	this.beforeUpdateContent = function(options) {
//	    //this.editor.removeAnnotationTags(this.editor.getBody());
//		return;
//	}
//
//	this.afterUpdateContent = function(options) {
//		//this.showAnnotation();
//		return;
//	};

    this.doAnnotate = function () {
		 loadingImage.showLoadingImage();
		this._setAnnotationDoc(this);
    }

    this._setAnnotationDoc = function (XimdocAnnotationTool) {

		//Update content before send the info to stanbol and zemanta
		this.editor.updateEditorContent();
		// Calling AnnotationHandler
		//var content = encodeURIComponent(this.editor._ximdoc._xmldoc.documentElement.textContent);
		var content= this.editor._ximdoc.saveXML({
                        asString: true,
                        hideXimlets: false,
                        resolveXimlinks: false,
                        onCreateNode: null
		});
		content= encodeURIComponent(content);
		var lang = "es";
		var encodedContent = "&nodeid=" + this.editor.nodeId +
							 "&content=" + content +
							 "&lang=" + encodeURIComponent(lang);

		com.ximdex.ximdex.editors.AnnotationHandler(kupu.getBaseURL(), encodedContent, {
			onComplete: function(req, json) {
				if(req.responseText != '' && req.responseText !== null) {
//					var responseObj = eval('(' + req.responseText + ')');
					/*console.debug(req);
					console.debug(json)*/
					if(json.status == 'ok') {
						var options = {selNode: this.editor.getSelectedNode(), caller: this};
						this.editor.beforeUpdateContent(options);
						this._annotationDoc = json;

						//This method shows all the references marked on the text.
						//this.showAnnotation();

						this.editor.afterUpdateContent(options);
						loadingImage.hideLoadingImage();
					} else {
						loadingImage.hideLoadingImage();
						//this.editor.alertvideo(_('References could not be loaded.') + "<br/><br/>\n" + _('Reason: ') + json.status + json.videourl);
						this.editor.alertvideo(json.status + json.videourl);
					}
				} else {
					loadingImage.hideLoadingImage();
					this.editor.alert(_('Error while obtaining annotations. ') + _('No server answer.'));
				}
				if (this.toolboxes['annotationstoolbox']) {
					this.toolboxes['annotationstoolbox'].refreshInfo(this._annotationDoc);
				}
				}.bind(this),
			onError: function(req) {
				loadingImage.hideLoadingImage();
				//console.info(req);
				this.editor.alert('Error while obtaining annotation file.');
			}.bind(this)
		});
    }

    this.showAnnotation = function () {

		var doc = this.editor.getInnerDocument();
		var bodyText = $('body', doc).html();
		var searchedTerms = "";

		var searchLength = this._annotationDoc.zemanta.markup.links.length;
		if (searchLength == 0) return;
		for (var k = 0; k < searchLength; k ++) {
			searchedTerm = this._annotationDoc.zemanta.markup.links[k].anchor;
			searchedTerm = searchedTerm.replace("(", "\\\(");
			searchedTerm = searchedTerm.replace(")", "\\\)");
			searchedTerm = searchedTerm.replace("-", "\\\-");
			searchedTerms += searchedTerm;
			if(k < (searchLength - 1))
				searchedTerms += "|";
		}

		var rgx = new RegExp("([^a-zA-Z0-9_>]{1})(" + searchedTerms + ")([^a-zA-Z0-9_<]{1})", "g");
		bodyText = bodyText.replace(rgx, "$1" + this._highlightStartTag + "$2" + this._highlightEndTag + "$3");
		$('body', doc).html(bodyText);
		this.editor.extendElements();
    }

    this.annotateWord = function (word) {
    	//this.editor.updateEditor();
    	return null;
    }
}

XimdocAnnotationTool.prototype = new XimdocTool();
