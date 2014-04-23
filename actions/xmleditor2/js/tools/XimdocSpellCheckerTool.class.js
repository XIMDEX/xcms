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
 * Checks document spelling.
 */

function XimdocSpellCheckerTool() {

	this._highlightStartTag = null;
	this._highlightEndTag = null;
	this._spellCheckWordsArray = [];
	this._spellCheckDoc = null;
	this.active = false;
	this.searchedTerms = '';

    	this.initialize = function (editor) {

        	this.editor = editor;
        	this._highlightStartTag = '<font owner="spellchecker" class="spellchecker">';
        	this._highlightEndTag = '</font>';
        	this.editor.logMessage(_('Spell checking tool initialized'));
    	};

    	this.beforeUpdateContent = function(options) {
		this.editor.removeCheckSpellingTags(this.editor.getBody());
		return;
	}

    	this.afterUpdateContent = function(options) {
		if(this.isActive()) {
			this._setSpellCheckDoc(this);
		}
		return;
    	};

    	this.doCheck = function () {
		this.toggle();
        	this.editor.updateEditor({updateContent: true});
		//calling loadingImage after updateEditor to show some feedback to the user.
		if(this.isActive())
			loadingImage.showLoadingImage();
    	}

    	this._setSpellCheckWordsArray = function () {
		var nodes = this._spellCheckDoc.evaluate("//name//text()", this._spellCheckDoc, null, XPathResult.ANY_TYPE,null);
		var result = nodes.iterateNext();
		this._spellCheckWordsArray = [];
		while (result) {
			this._spellCheckWordsArray.push(result);
			result = nodes.iterateNext();
		}
		this._setSearchedTerms();
    	}

    	this._setSearchedTerms = function () {
		var searchedTerm = "";
		var searchLength = this._spellCheckWordsArray.length;
		this.searchedTerms = "";
		if (searchLength == 0) return;
		for (var k = 0; k < searchLength; k ++) {
			searchedTerm = this._spellCheckWordsArray[k].textContent;
			searchedTerm = searchedTerm.replace("(", "\\\(");
			searchedTerm = searchedTerm.replace(")", "\\\)");
			searchedTerm = searchedTerm.replace("-", "\\\-");
			this.searchedTerms += searchedTerm;
			if(k < (searchLength - 1))
				this.searchedTerms += "|";
		}
    	}

    	this._setSpellCheckDoc = function (XimdocSpellCheckerTool) {
		// Calling SpellCheckingHandler
		var content = $('body', this.editor.getInnerDocument()).text();
		var lang = "es";
		var encodedContent = "&nodeid=" + this.editor.nodeId +
							 "&content=" + encodeURIComponent(content) +
							 "&lang=" + encodeURIComponent(lang);

		var encodedObject = {
			"nodeid": this.editor.nodeId,
			"content": content,
			"lang": lang
		}

		com.ximdex.ximdex.editors.SpellCheckingHandler(kupu.getBaseURL(), encodedObject, {
			onComplete: function(req, json) {
		        	this._spellCheckDoc = this.editor.createDomDocument(req.responseText);
		        	this._setSpellCheckWordsArray();
				this.applyCheckSpelling();
				loadingImage.hideLoadingImage();
			}.bind(this),
			onError: function(req) {
				loadingImage.hideLoadingImage();
				this.editor.alert(_('Error obtaining spell checking file.')+'<br/><br/>'+_('Maybe your text is too long and/or there are so much words to correct in your document.')+'<br/><br/>'+_('Be sure that your content and the base language of your document are the same.'));
				this.toggle();
				$(this.editor.tools.spellchecker.button).toggleClass('kupu-spellchecker-pressed').toggleClass('kupu-spellchecker');
			}.bind(this)
		});
    	}

	this.applyCheckSpelling = function (node) {

		var doc = this.editor.getInnerDocument();
		var searchedTerms = this.searchedTerms.split('|').unique().join('|');
//		var searchText = "([\\s\\.,\\n\\r\\t\\:])(" + this.searchedTerms + ")([\\s\\.,\\n\\r\\t\\:])";
		var searchText = "\\b(" + searchedTerms + ")\\b";
		var searchNode = node ? node : doc.getElementsByTagName('body')[0];
//		var replacement = "$1" + this._highlightStartTag + "$2" + this._highlightEndTag + "$3";
		var replacement = this._highlightStartTag + "$1" + this._highlightEndTag;

	    	var regex = typeof searchText === 'string'? new RegExp(searchText, 'g'): searchText;
		var childNodes = searchNode.childNodes;
	    	var cnLength = childNodes.length;
	    	var excludes = ['html', 'head', 'style', 'title', 'link', 'meta', 'script', 'object', 'iframe'];

	    while (cnLength--) {

	        var currentNode = childNodes[cnLength];

	        // ELEMENT_NODE == 1
	        // TEXT_NODE == 3

	        if (currentNode.nodeType === currentNode.ELEMENT_NODE && !excludes.contains(currentNode.nodeName.toLowerCase())) {
	            this.applyCheckSpelling(currentNode);
	            continue;
	        }

	        if (currentNode.nodeType !== currentNode.TEXT_NODE || !regex.test(currentNode.data)) {
	            continue;
	        }


	        var parent = currentNode.parentNode;
            var frag = (function() {

//            	console.log(currentNode.data.match(regex));

            	var html = currentNode.data.replace(regex, replacement);
                var wrap = document.createElement('div');
                var frag = document.createDocumentFragment();

                wrap.innerHTML = html;
                while (wrap.firstChild) {
                    frag.appendChild(wrap.firstChild);
                }

                return frag;
            })();

	        parent.insertBefore(frag, currentNode);
	        parent.removeChild(currentNode);
	    }

	}.bind(this);

    	this.replaceWord = function(word, suggestion, clickedNodeParentText, clickedNodeParent, mode) {
		return function () {
			if(mode == 'leave')
				clickedNodeParentText = clickedNodeParentText.replace(this._highlightStartTag + word + this._highlightEndTag, suggestion);
			else
				clickedNodeParentText = clickedNodeParentText.replace(this._highlightStartTag + word + this._highlightEndTag, suggestion.textContent);
			clickedNodeParent.innerHTML = clickedNodeParentText;
		}.bind(this);
    	}

    	this.isActive = function () {
    		return this.active;
    	}

    	this.enable = function () {
    		this.active = true;
    	}

    	this.disable = function () {
    		this.active = false;
    	}

    	this.toggle = function () {
    		if(this.isActive()){
    			this.disable();
		}
    		else{
    			this.enable();
		}
    	}
}

XimdocSpellCheckerTool.prototype = new XimdocTool();
