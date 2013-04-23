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




var IS_MOZ		= (document.implementation && document.implementation['createDocument'] && document.implementation['hasFeature']) || false;
var IS_SAFARI	= navigator.userAgent.toLowerCase().indexOf("applewebkit") != -1 || false;
var IS_IE		= document.all && window.ActiveXObject && navigator.userAgent.toLowerCase().indexOf("msie") > -1  && navigator.userAgent.toLowerCase().indexOf("opera") == -1 || false;
var IS_CHROME		= navigator.userAgent.toLowerCase().indexOf('chrome/') != -1 || false;


var toString = function(node) {
	return new XMLSerializer().serializeToString(node);
}

/**
 * Extends KupuEditor class
 * @constructor
 */
function XimdocEditor(options) {

	this.options = options;

    // attrs
    this.document = options.document; // the model
    this.config = null; //config; // an object that holds the config values
    this.log = options.logger; // simple logger object
    this.channels = options.channels;
    this.tools = {}; // mapping id->tool
    this.maintoolboxes = [];
    this.filters = new Array(); // contentfilters
    this.view = options.edition_view; // edition view: 'tree', 'normal', 'pro'
    this.rngEditorMode = options.rngEditorMode; // Editor Rng: true | false
    this.dotdotPath = options.dotdotPath; // macro dotdotPath base calculated on ximdex

    this._designModeSetAttempts = 0;
    this._initialized = false;

    // some properties to save the selection, required for IE to remember where in the iframe the selection was
    this._previous_range = null;

    // this property is true if the content is changed, false if no changes are made yet
    this.content_changed = false;

	this.actionId = null;
	this.nodeId = null;
    // Base URL for Ajax requests
    this._baseURL = null;

    // Base URL for external action requests
    this._loadActionURL = null;


    // -------------------------------------------------------

	/**
	 *	Browser constants (From Sarissa library)
	 */
	this.IS_MOZ		= IS_MOZ;
	this.IS_SAFARI	= IS_SAFARI;
	this.IS_IE		= IS_IE;

	this._ximdoc = null;
	this._rngdoc = null;

	this._xmlDom = null;
	this._hasTmp = null;
	this._rngDom = null;
	this._xslDom = null;
	this._i18n = null;
	this._noRenderizableElements = null;

	this.selNode = null;
	this.lastSelNode = null;
	this.selectedText = null;
	this.selectedTextLength = null;
	this.ximElement = null;
	this.ximParent = null;
	this.elements = null;	// Collection of elements with UID attribute (XML elements)
	this.loadErrors = null;


	this.getTools = function() {
		return this.tools;
	};

	this.getXmlDocument = function() {
		return this._xmlDom;
	};

	this.getXslDocument = function() {
		return this._xslDom;
	};

	this.getRngDocument = function() {
		return this._rngdoc;
	};

	this.getXimDocument = function() {
		return this._ximdoc;
	};

	this.getEditorConfig = function() {
		return this.config;
	};

	this.getBaseURL = function() {
		return this._baseURL;
	};

	this.getLoadActionURL = function() {
		return this._loadActionURL;
	};

	this.getBody = function() {
		return $('body', this.getInnerDocument())[0];
	};

	this.getNoRenderizableElements = function () {
		var noRenderizableElementsArr = [];
		$('element', this._noRenderizableElements).each( function(index, elem) {
			if($(elem).text() != 'docxap')
				noRenderizableElementsArr.push($(elem).text());
		});

		return noRenderizableElementsArr;
	}

	this.isRngEditionMode = function() {
		if(this.rngEditorMode == "")
			return false;
		else
			return true;
	};

	this.getDotDotPath = function() {
		return this.dotdotPath;
	};

	this.getView = function() {
		if(['tree', 'normal', 'pro'].contains(this.view))
			return this.view;
		else
			return 'normal';
	};

	this.setView = function(edition_view) {
		return this.view = edition_view;
	};

	this.toggleSchemaValidator = function() {
		this.getXimDocument().toggleSchemaValidator();
	};

	this.schemaValidatorIsActive = function() {
		return this.getXimDocument().schemaValidatorIsActive();
	};

	this.setSchemaValidator = function(validate) {
		this.getXimDocument().setSchemaValidator(validate);
	};

	this.initialize = function(callback) {

		// kupu initialize stuff

        	/* Should be called on iframe.onload, will initialize the editor */
        	//DOM2Event.initRegistration();

		this._rngDom = null;
		this._xmlDom = null;
		this._xslDom = null;
		this.config = null;
		this._i18n = null;

        	this._initializeEventHandlers();

		if (this.getBrowserName() == "IE") {
            		// provide an 'afterInit' method on KupuEditor.prototype
            		// for additional bootstrapping (after editor init)
            		this._initialized = true;
            		if (this.afterInit) {
                		this.afterInit();
            		};
            		this._saveSelection();
        	} else {
            		this._setDesignModeWhenReady();
        	};

        	this.elements = $([]);

		this.actionId = $('.kupu-fulleditor .kupu-ximparams #kupu-actionId').html().trim();
		this.nodeId = $('.kupu-fulleditor .kupu-ximparams #kupu-nodeId').html().trim();
		var xslIncludesOnServer = $('.kupu-fulleditor .kupu-ximparams #kupu-xslIncludesOnServer').html().trim();

		// ximdex initialize stuff
		//this._baseURL = url_root + '/xmd/loadaction.php?actionid=' + this.actionId + '&nodeid=' + this.nodeId;
		this._baseURL = url_root + '/xmd/loadaction.php?action=xmleditor2&nodeid=' + this.nodeId;
		this._baseActionURL = url_root + '/actions/xmleditor2/';
		this._loadActionURL = url_root + '/xmd/loadaction.php?nodeid=' + this.nodeId;
		var xmlI18N = url_root + '/extensions/kupu/i18n/kupu-'+locale+'.pox';
		var xmlUrl = this._baseURL + '&ajax=json&method=getXmlFile';
		var verifyTmpUrl = this._baseURL + '&ajax=json&method=verifyTmpFile';
		var rngUrl = this._baseURL + '&ajax=json&method=getSchemaFile';
		var includesInServer="";
		//Avoid includes tag in xsl when by configuration or when browser is in safari or chrome
		if (xslIncludesOnServer == 1 || IS_SAFARI || IS_CHROME)
		    includesInServer = "&includesInServer=1";
		var xslUrl = this._baseURL + '&ajax=json&method=getXslFile'+ includesInServer +'&view=' + this.getView();
		var editorConf = this._baseURL + '&ajax=json&method=getConfig';
		var noRenderizableElementsUrl = this._baseURL + '&ajax=json&method=getNoRenderizableElements';

		// i18n request
		this.fileRequest('_i18n', xmlI18N, callback);

		// Verify Tmp file request
		this.fileRequest('_hasTmp', verifyTmpUrl, callback);

		// XML request

		this.fileRequest('_xmlDom', xmlUrl, callback);

		// RNG request


		this.fileRequest('_rngDom', rngUrl, callback);

		// XSLT request
		this.fileRequest('_xslDom', xslUrl, callback);

		// No renderizable Elements file request
		this.fileRequest('_noRenderizableElements', noRenderizableElementsUrl, callback);

		// Editor config file request
		this.fileRequest('config', editorConf, callback);				
                
	};

    	this.getActionDescription = function() {
    		return this._actionDescription || '';
    	};

    	this.setActionDescription = function(description) {
    		this._actionDescription = description || '';
    	};

	this.fileRequest = function(propertyName, url, callback, updateEditor, hideLoadingImage, method, content) {
		var method = (method) ? method : 'GET';
		var content = (content) ? content : null;
		new AjaxRequest(url, {
			method: method,
			content: content,
			onComplete: function(req, json) {

				if (json['error']) {
					callback(null, json.error);
					return;
				}

				if (json["result"] !== false){
					var data = json.data || req.responseText;
					
					if(json.method && json.method == 'verifyTmpFile') {
						this[propertyName] = json.result;
					}
					else if (url.indexOf("loadaction") == -1){
					    this[propertyName] = this.createDomDocument(data,0,true);
					}
					else {
						this[propertyName] = this.createDomDocument(data);
					}
	
					this._afterInitialize(callback);
				}
			}.bind(this),
			onError: function(req) {
				//console.error(req);
				loadingImage.hideLoadingImage();
			}.bind(this)
		});
	}

	/**
	 * Called from XimdocEditor.process()
	 */
	this._afterInitialize = function(callback) {

		if (
			this._rngDom === null || this._xmlDom === null ||
			this._xslDom === null || this.config === null ||
			this._i18n === null || this._noRenderizableElements === null
			) return;


		window.i18n_message_catalog.initialize(this._i18n);

		this.config = this._parseConfig(this.config);

		this._rngdoc = new RngDocument();
		this._rngdoc.loadXML(this._rngDom);

		this._ximdoc = new XimDocument(this.config);
		this._ximdoc.editor = this;
		this._ximdoc.loadXML(this._xmlDom, this._rngdoc);
		this._xslDom = this.BrowserCompatibility.parseXSLDocument(this._xslDom, this.getView(), this.getNoRenderizableElements());

		// 'callback' is an user defined function passed as a parameter to XimdocEditor.process()
		if (callback) callback(this);

		this.setActionDescription(_('Initialize editor'));
		this.updateEditor({caller: this});
        	this.logMessage(_('Editor initialized'));

        if (this._hasTmp && this.config.autosave_time > 0) {
        	var confirmCallback = {
        		'yes': function () {
        			// Recover doc with temp file
					new AjaxRequest(this._baseURL + '&ajax=json&method=recoverTmpFile&view=' + this.getView(), {
						method: 'POST',
						content: null,
						onComplete: function(req, json) {
							if(json.result === true) {
								this.fileRequest('_xmlDom', this._baseURL + '&ajax=json&method=getXmlFile', function () {this.tools.editorviewtool.setView(this.getView());this._hasTmp = false;}.bind(this));
								this.logMessage(_('Temporal file recovered and removed'));
							} else {
								this.logMessage(_('Error recovering temporal file'));
							}
						}.bind(this),
						onError: function(req) {
							this.logMessage(_('Error recovering temporal file'));
						}.bind(this)
					});
        		}.bind(this),
        		'no': function () {
        			// Delete tmp file
					new AjaxRequest(this._baseURL + '&ajax=json&method=removeTmpFile', {
						method: 'POST',
						content: null,
						onComplete: function(req, json) {
							if(json.result === true) {
								this._hasTmp = false;
								this.logMessage(_('Temporal file removed'));
							} else {
								this.logMessage(_('Error removing temporal file'));
							}
						}.bind(this),
						onError: function(req) {
							this.logMessage(_('Error removing temporal file'));
						}.bind(this)
					});
        		}.bind(this)
        	}
        	this.confirm(_('It is available a temporal version of the document with saving date more recent than the last one you saved. Do you want to recover it?'), confirmCallback);
        }

	};

	this._parseConfig = function(confNode) {
		//console.log(confNode);
		// Config XML already parsed
		if (confNode.nodeType != 9) return confNode;

	    var root = confNode.getElementsByTagName('kupuconfig');
		//console.log(root);
	    root = root[0] || null;
	    if (!root) {
	        this.log.log(_('No element found in the configuration'));
	        throw(_('No element found in the configuration'));
	    };

	    // _load_dict_helper() defined in kupuhelpers
	    root = _load_dict_helper(root);
	    return root;
	};

	/**
	 * Creates a XML Document and, optionally, loads it with a XML string
	 */
	this.createDomDocument = function(xml, ctype, forceNotParse) {

		ctype = ctype == 1 ? 'application/xhtml+xml' : 'text/xml';

		/** Using Sarissa library **/
		var xmldoc = Sarissa.getDomDocument();

		if (xml) {
			xml = this.BrowserCompatibility.parseXMLString(xml);
//console.log(xml);
			xmldoc = new DOMParser().parseFromString(xml, ctype);
			if (!forceNotParse){
			    xmldoc = this.BrowserCompatibility.parseXMLDocument(xmldoc);
			}
		}

		// IE doesn't allow to extend XML DOM documents.
		/*xmldoc.toString = function(node) {
			node = node || this;
			var str = new XMLSerializer().serializeToString(node);
			return str;
		}*/

		return xmldoc;
	};

	this.preSanitizeHTML = function(xsltResult) {

		// Trying to remove the <transformiix:result /> node
		var t = toString($('html', xsltResult).get(0));
		xsltResult = this.createDomDocument(t);

		// Removing <script/> tags
		$('script', xsltResult).each(function(index, elem) {
			elem.parentNode.removeChild(elem);
		});

		if (!IS_IE) {

			// Trying to fix tags defined into CDATA blocks

			// Replacing entities with < > simbols
			var htmlString = toString(xsltResult);
			var pattern = /&lt;([^<]+?)&gt;/g;

			if (pattern.test(htmlString)) {

				htmlString = htmlString.replace(pattern, '<$1>');

				// Transforming all tags to lower case
				pattern = /<([^>\s]+)/g;
				var res = htmlString.match(pattern);
				var processed = [];
				for (var i=0; i<res.length; i++) {
					if (!processed[res[i]]) {
						var regex = new RegExp(res[i], 'g');
						htmlString = htmlString.replace(regex, res[i].toLowerCase());
						processed[res[i]] = true;
					}
				}

				xsltResult = this.createDomDocument(htmlString);
			}
		}

		// Throwing a warning message if any element has no UID attribute
		$('[uid="{@uid}"]', xsltResult).each(
			function(index, elem) {
				elem.attributes.removeNamedItem('uid');
				var msg = 'Element <' + elem.tagName + '/> has no UID attribute!';
				//this.logMessage(_(msg), 2);
				console.warn(_(msg));
			}.bind(this)
		);

		// This correction is needed because the fix of attributes on CDATA sections
		/*$('[uid]', xsltResult).each(
			function(index, elem) {
				elem.setAttribute('uid', elem.getAttribute('uid').trim());
			}
		);*/

		var createLink = function(href) {
			var link = xsltResult.createElement('link');
			link.setAttribute('rel', 'stylesheet');
			link.setAttribute('type', 'text/css');
			link.setAttribute('href', href);
			return link
		}

		var head = xsltResult.getElementsByTagName('head')[0];
		var css = this._baseActionURL + 'views/common/css/kupustyles.css';
		head.appendChild(createLink(css));

		css = this._baseActionURL + 'views/common/css/innerframe.css';
		head.appendChild(createLink(css));

		return xsltResult;
	};

	this.postSanitizeHTML = function(xslResult, htmlDoc) {

		// Fixing the UID attribute for <body/> element
		// <body/> element maybe is not present in docxap template
		// A better way to resolve this problem is to assign the first possible uid directly
		htmlDoc.setAttribute('uid', this.nodeId + ".1");

		$('img[uid]', htmlDoc).each(
			function(i, e) {
				var uid = e.getAttribute('uid');
				var elem = $('[uid="'+uid+'"]', xslResult)[0];
				// IE sets default width and height of an image to 1px,
				// the only way to get the real default size is to remove both attributes.
				if (e.getAttribute('width') != elem.getAttribute('width')) {
					e.attributes.removeNamedItem('width');
				}
				if (e.getAttribute('height') != elem.getAttribute('height')) {
					e.attributes.removeNamedItem('height');
				}
			}.bind(this)
		);

		// We don't want working links
		$('a', htmlDoc).each(function(index, elem) {
			if (elem.getAttribute('href')) elem.attributes.removeNamedItem('href');
			if (elem.getAttribute('target')) elem.attributes.removeNamedItem('target');
		});

		return htmlDoc;
	};

	this.updateEditor = function(options) {

		options = options || {
			updateContent: true,
			caller: null,
			hideXimlets: false
		};

		options.updateContent = (options['updateContent'] == undefined)
			? true
			: (options.updateContent ? true : false);
		/*options.caller = options['caller']
			? options.caller
			: this;*/

		if (options.updateContent) this.updateEditorContent();

		var oldBody = this.getBody();
		var xmldoc = this._ximdoc.saveXML({
			asString: false,
			hideXimlets: options.hideXimlets ? options.hideXimlets : false
    	});

		if (!xmldoc) return false;

		this._xmlDom = xmldoc;

		if(!this._xslDom || this._xslDom.documentElement == null) {
			this.alert(_('No docxap template detected. Changing back to tree view.'));
			selectView('tree');
			return false;
		}

		var xslResult = xslt.xsltTransform(this._xmlDom, this._xslDom,this);
		if (!xslResult) return false;

		xslResult = this.preSanitizeHTML(xslResult);

		options.xslResult = xslResult;
		this.beforeUpdateContent(options);

		$('head', this.getInnerDocument()).html(toString($('head', xslResult)[0]));
		$('body', this.getInnerDocument()).html(toString($('body', xslResult)[0]));

		this.postSanitizeHTML(xslResult, this.getBody());


		// If extended view is choosen, previewInServer is active and there are preview servers:
		// get preview
		if(this.getView() == 'pro') {
			// TODO: Are there preview servers? Choose one.

			var newBodyString = $('html', this.getInnerDocument()).html();

			var content = encodeURIComponent(newBodyString);

			this._ximdoc.editor.tools.ximdoctool.toolboxes.channelstoolbox.getChannelId();

			var encodedContent = "&nodeid=" + this.nodeId +
								 "&content=" + content +
								 "&channelid=" + this._ximdoc._channelId;

			com.ximdex.ximdex.editors.PreviewInServerHandler(this.getBaseURL(), encodedContent, {
				onComplete: function(req, json) {
					switch(req.responseText) {
						case '1':
							this.alert(_('Cannot show Remote View') + '. ' + _('PreviewInServer mode is disabled'));
							selectView('normal');
							break;
						case '2':
							this.alert(_('Cannot show Remote View') + '. ' + _('No Preview Servers for this channel'));
							selectView('normal');
							break;
						case '3':
							this.alert(_('Cannot show Remote View') + '. ' + _('Error accessing remote server. Please, verify permissions and synchro base path'));
							selectView('normal');
							break;
						case '4':
							this.alert(_('Cannot show Remote View') + '. ' + _('Error connecting remote server. Please, verify synchro data (IP, access keys)'));
							selectView('normal');
							break;
						case '5':
							this.alert(_('Cannot show Remote View') + '. ' + _('Unknown error'));
							selectView('normal');
							break;
						default:
							var rgx = new RegExp("<head[^>]*>(.*)</head>", "g");
							var head = req.responseText.match(rgx);
							var rgx = new RegExp("<body(.*[^>])*>(.*)</body>", "g");
							var body = req.responseText.match(rgx);
							$('head', this.getInnerDocument()).html(head[0]);
							$('body', this.getInnerDocument()).html(body[0]);

							this.afterUpdateContent(options);
							loadingImage.hideLoadingImage();
					}
				}.bind(this),
				onError: function(req) {
					this.alert(_('Error obtaining preview file in server.'));
				}.bind(this)
			});
		}

		this.afterUpdateContent(options);
		loadingImage.hideLoadingImage();

		this.selNode = (options.selNode) ? options.selNode : this.getSelectedNode();
        this.updateState(options.event);

        if(options.callback) {
        	var callback = options.callback;
        	callback();
        }

		return true;
	};

	/**
	 * NOTE: It's not needed to traverse DOM tree hierarchic.
	 * We only need a list of nodes with 'uid' attribute setted.
	 * First solution: using getElementsByTagName.
	 * TODO: Explore XPATH use from browser.
	 * a) http://code.google.com/p/ajaxslt/
	 * b) sarissa
	 */
	this.updateEditorContent = function() {

		var domElement = this.getBody();

		this.removeCheckSpellingTags(domElement);
		this.removeAnnotationTags(domElement);

		// TODO: There will be problems with the content if XSL template renderize more than one element per UID
		var items = $('[uid]', domElement);
		var i = items.length;
		var item = null;
		while (item = items[i-1]) {
			var uid = item.getAttribute('uid');
			if (item.isEditable) {
				var ximElement = this._ximdoc.importHtmlElement(item);
				this._ximdoc.updateElement(uid, ximElement);
			}
			i--;
		}
	};


	this.alert = function (msg) {
		var dialog = $('<div id="kupu-jdialog">' + window.i18n_message_catalog.acents ( msg)  + '</div>',
			$('.kupu-editorframe'));
		$('.kupu-editorframe').append(dialog);

		var check = $("#kupu-jdialog").data("dialog");

		if(check) {
			$("#kupu-jdialog").dialog("destroy");
		}


		$("#kupu-jdialog").dialog({
			modal: true,
			maxHeight: 400,
			height: 400,
			buttons: {
				Ok: function() {
					$(this).dialog('close');
				}
			}
		});
	}


	this.alertvideo = function (msg) {


		var dialog = $('<div class="video" id="kupu-jdialog">' + window.i18n_message_catalog.acents( msg ) + '</div>',
                        $('.kupu-editorframe'));
                $('.kupu-editorframe').append(dialog);


			var check = $("#kupu-jdialog").data("dialog");

			if(check) {
				$("#kupu-jdialog").dialog("destroy");
			}


                $("#kupu-jdialog").dialog({
                        modal: true,
                        maxHeight: 450,
                        height: 450,
			width: 500,
                        buttons: {
                                Ok: function() {
                                        $(this).dialog('close');
                                }
                        }
                });
        }

	this.confirm = function (msg, callback) {
		var dialog = $('<div id="kupu-jdialog">' + window.i18n_message_catalog.acents ( msg)   + '</div>',
			$('.kupu-editorframe'));
		$('.kupu-editorframe').append(dialog);


		var check = $("#kupu-jdialog").data("dialog");

		if(check) {
			$("#kupu-jdialog").dialog("destroy");
		}



		$("#kupu-jdialog").dialog({
			resizable: false,
			height:300,
			width: 400,
			modal: true,
			buttons: {
				_('Yes'): function() {
					callback.yes();
					$(this).dialog('close');
				},
				_('No'): function() {
					callback.no();
					$(this).dialog('close');
				}
			}
		});
	}

	this.setDefaultImages = function() {
		$('img[uid]', this.getBody()).each(
			function(index, elem) {
				// Image source defaults to iframe URL
				var img_url = elem.src.replace(this._baseActionURL, '');

				if (!img_url || "index.html" == img_url || !elem.src ) {
					elem.src = url_root + '/xmd/images/insert_img.png';
				}
			}.bind(this)
		);
	};

	this.removeCheckSpellingTags = function(domItem) {
		$('font[owner="spellchecker"]', domItem).each(function(index, elem) {
        	$(elem).after(elem.textContent);
        	$(elem).remove();
		}.bind(this));
	};

	this.removeAnnotationTags = function(domItem) {
		$('font[owner="annotator"]', domItem).each(function(index, elem) {
        	$(elem).after(elem.textContent);
        	$(elem).remove();
		}.bind(this));
	};

	this.setDinamicContentImages = function() {
		var bodyText = $(this.getBody()).html();
		var rgx = new RegExp("&lt;php?([^<>]*)?&gt;", "g");
		bodyText = bodyText.replace(rgx, "<div style='background-color: gray; padding-left: 25px; border: 1px solid black; color: white; font-weight: bold; text-decoration: italic; height: 16px; background-repeat: no-repeat; background-image: url(" + url_root + "/actions/xmleditor2/gfx/page_white_php.png);'>Non XHTML Code</div>");
		$(this.getBody()).html(bodyText);
	};

    // -------------------------------------------------------


    this.prepareForm = function(form, id) {
        /* adding a field to the form and placing the contents in it

            it can be used for simple POST support where Kupu is part of a form
        */

        // making sure people can't edit or save during saving
        if (!this._initialized) {
            return;
        }
        this._initialized = false;

        // setting the window, status so people can see we're actually saving
        window.status= _("Please wait while saving document...");

        // call (optional) beforeSave() method on all tools
        for (var tid in this.tools) {
            var tool = this.tools[tid];
            if (tool.beforeSave) {
                try {
                    tool.beforeSave();
                } catch(e) {
                    this.alert(e);
                    this._initialized = true;
                    return;
                };
            };
        };

        // setting a default id
        if (!id) {
            id = 'kupu';
        };

        // passing the content through the filters
        this.logMessage(_("Starting HTML cleanup"));
        var transform = this._filterContent(this.getInnerDocument().documentElement);

        // XXX need to fix this.  Sometimes a spurious "\n\n" text
        // node appears in the transform, which breaks the Moz
        // serializer on .xml
        var contents =  this._serializeOutputToString(transform);

        this.logMessage(_("Cleanup done, sending document to server"));


        // now create the form input, since IE 5.5 doesn't support the
        // ownerDocument property we use window.document as a fallback (which
        // will almost by definition be correct).
        var document = form.ownerDocument ? form.ownerDocument : window.document;

		var kupuForm = document.getElementsByName(id);

		// Don't create same element twice!
		if (kupuForm.length == 0) {
	        var ta = document.createElement('textarea');
	        ta.style.visibility = 'hidden';
	        var text = document.createTextNode(contents);
	        ta.appendChild(text);
	        ta.setAttribute('name', id);
	        // and add it to the form
	        form.appendChild(ta);
        } else {
        	kupuForm[0].value = contents;
        }

        // letting the calling code know we have added the textarea
		this._initialized = true;
        return true;
    };

    this.getClickedNode = function() {
        /* returns the selected node (read: parent) or none */
        return this.getSelection().parentElement();
    };

    this.getNearestParentWithAttribute = function(node, attrName, tagName) {
        while (node) {
    		var it = new DOMAttrIterator(node);
    		while (it.hasNext()) {
    			var attr = it.next();
    			if (attr.nodeName.toLowerCase() == attrName.toLowerCase()) return node;
    		}
            var node = node.parentNode;
        }

        return false;
    };

    this._restoreSelection = function() {
        /* re-selects the previous selection in IE. We only restore if the current selection is not in the document.*/
        /*if (this._previous_range && !this._isDocumentSelected()) {
            try {
                this._previous_range.select();
            } catch (e) {
                //this.alert("Error placing back selection");
                this.logMessage(_('Error placing back selection'));
            };
        };*/
    };

    this.updateState = function(event) {
        /* let each tool changes state if required */
        // first seeing if the event is interesting enough to trigger the whole updateState machinery

        if(event && event.type == 'mouseup') {
        	this.fireMouseupEvent(event);
        	return;
        }

		// Cursors Keys events
		if (event && event.type == 'keyup' && event.keyCode >= 37 && event.keyCode <= 40) {
			var positionNode = this.getSelection().selection.anchorNode.parentNode;
			if(this.selNode && positionNode && this.selNode.getAttribute('uid') != positionNode.getAttribute('uid')) {
				this.selNode = positionNode;
			}
		}

		// If user presses the enter key we create a new element of the same type
		if (event && event.type == 'keyup' && event.keyCode == 13) {
			var elems = $('[uid="'+this.selNode.getAttribute('uid')+'"]', this.getInnerDocument());
			var ximElement = elems[elems.length-1].ximElement;
			if (ximElement) {
				var newElement = new XimElement(ximElement.schemaNode, false);
				var htmlNodeAdded = this.getSelection().selection.anchorNode;
				if(htmlNodeAdded) {
					var htmlNodeSplited = htmlNodeAdded.previousSibling;
					if(htmlNodeSplited) {
						newElement.value = (htmlNodeAdded.textContent && htmlNodeSplited.textContent
											&& htmlNodeAdded.textContent != '')
											? htmlNodeAdded.textContent
											: newElement.value;
						ximElement.value = (htmlNodeSplited.textContent
											&& htmlNodeSplited.textContent != '')
											? htmlNodeSplited.textContent
											: ximElement.value;
						this.getXimDocument().insertAfter(newElement, ximElement.parentNode, ximElement);
						this.setActionDescription(_('Splited element'));
						this.selNode = newElement;
						this.updateEditor({caller: this, updateContent: false, selNode: newElement});
					}
				}
				return;
			}
		}

		var target = (event && !['keyup', 'keydown'].contains(event.type))
			? (event.target || event.srcElement)
			: (this.selNode || null);

        this._setSelectionData(target);
		if (!this.selNode) return;

        for (var id in this.tools) {
            try {
                if (this.tools[id]['updateState']) this.tools[id].updateState({caller: this, selNode: this.selNode, event: event});
            } catch (e) {
                if (e == UpdateStateCancelBubble) {
                    this.updateState(event);
                    break;
                } else {
                 /*   this.logMessage(
                        _('Exception while processing updateState on ' +
                            '${id}: ${msg}', {'id': id, 'msg': e.message}), 2); */
                }
            }
        }
    };

    this.fireMouseupEvent = function(event) {

    	var target = (event.target || event.srcElement);
    	this._setSelectionData(target);
		if (!this.selNode) return;

    	for (var id in this.tools) {
            try {
                if (this.tools[id]['mouseUp']) this.tools[id].mouseUp({caller: this, selNode: this.selNode, event: event});
            } catch (e) {
                if (e == UpdateStateCancelBubble) {
                    this.fireMouseupEvent(event);
                    break;
                } else {
                    this.logMessage(
                        _('Exception while processing mouseUp on ' +
                            '${id}: ${msg}', {'id': id, 'msg': e.message}), 2);
                }
            }
        }
    };

    this.getSelectedNode = function() {
        return this.selNode ? this.selNode : this.lastSelNode;
    };

    this._setSelectionData = function(target) {

    	this.lastSelNode = this.selNode;

    	var ximdoc = this.getXimDocument();
		this.selNode = target || this.getSelection().parentElement();

		if(this.selNode.uid)
			this.selNode = $('[uid="' + this.selNode.uid + '"]', this.getInnerDocument())[0];

		// If function getAttribute does not exist, nothing to set.
		if(this.selNode && !this.selNode['getAttribute']) {
			this.selNode = null;
			return;
		}

		// being sure to get the correct element (UID)
		while (this.selNode && !this.selNode.getAttribute('uid')) {
			this.selNode = this.getParentWithUID(this.selNode);
		}

		if (!this.selNode) return;

		// All the editable elements with the same UID
		var elements = this.selNode.ximElement.getHtmlElements(true);

		// If selected element is a "no editable" element try to obtain the editable part of the element.
		if (!this.selNode.ximElement.isRoot && this.selNode.getAttribute('editable') == 'no') {
			if (elements[0]) this.selNode = elements[0];
		}

		this.selectedText = this.getSelection();
		this.selectedTextLength = this.selectedText.getContentLength();

		// Checking if node is selectable
		if(this.selNode.ximElement && !this.selNode.ximElement.isSelectable(this.nodeId)) {
			var firstSelectableParent = this.selNode.ximElement.getFirstSelectableParent(this.nodeId);
			if(!firstSelectableParent) {
				this.selNode = null;
				return;
			}
			var selectableParentElements = firstSelectableParent.getHtmlElements(true);
			this._setSelectionData(selectableParentElements[0]);
		}

		this.ximElement = this.selNode.ximElement;
		this.ximParent = this.ximElement.parentNode;

    };

	/**
	 * Function which returns a DOMElement object, parent of selNode
	 */
    this.getParentWithUID = function(selNode) {
    	var parent = selNode;
    	var uid = null;

		while (uid == null && parent != null) {
			parent = parent.parentNode;
			if (parent && parent.nodeType == 1) {
				// TODO: Check the existence of UID attribute
				uid = parent.getAttribute('uid');
			}
		}

		// If we can't find a parent with UID we assumed that is the body element
		if (!parent) parent = this.getBody();
		if (!parent.getAttribute('uid')) parent = null;

    	return parent;
    };

    this.beforeUpdateContent = function(options) {

    	// Called before the document is updated with the new content

        var selNode = this.getSelectedNode();
		options.selNode = selNode;

        for (var id in this.tools) {
            try {
                if (this.tools[id]['beforeUpdateContent']) this.tools[id].beforeUpdateContent(options);
            } catch (e) {
                this.logMessage(_('Exception while processing beforeUpdateContent on ${id}: ${msg}', {'id': id, 'msg': e.message}), 2);
                console.error(_('Exception while processing beforeUpdateContent on ${id}: ${msg}', {'id': id, 'msg': e}));
            }
        }
    	this.elements = $([]);

		// noscript
		/*$("noscript", $('body', xslResult)[0]).each(function(index, elem) {
			console.info(elem);
			elem.tagName = 'span';
			//$(elem).attr('src', path);
		});*/

    };

    this.afterUpdateContent = function(options) {

    	// Called after the document is updated with the new content

		this.setDinamicContentImages();
		this.setDefaultImages();

    	$('br[uid]', this.getBody()).each(
    		function(i, e) {
    			var div = this.getInnerDocument().createElement('div');
    			div.innerHTML = '&para;';
    			div.setAttribute('uid', e.getAttribute('uid'));
    			div.setAttribute('editable', 'no');
    			e.parentNode.insertBefore(div, e);
    			e.parentNode.removeChild(e);
    		}.bind(this)
    	);

    	this.extendElements();

		if (this.getView() == 'tree' || this.isRngEditionMode()) {
			$('.folding', this.getBody()).dblclick(
				function(event) {
					var target = event.currentTarget || event.target;
					var ctrl = $(target).siblings('div').toggle().siblings('.ctrl');
					if ($(ctrl).hasClass('minus')) {
						$(ctrl).attr('src', '../../xmd/images/tree/Lplus.png').toggleClass('minus');
						$('.folder', $(target).parent()).attr('src', '../../xmd/images/tree/folder.png');
					} else {
						$(ctrl).attr('src', '../../xmd/images/tree/Lminus.png').toggleClass('minus');
						$('.folder', $(target).parent()).attr('src', '../../xmd/images/tree/openfolder.png');
					}
				}
			);
		}

    	// Called after the XSL transformation and the iframe content update
        var selNode = this.getSelectedNode();
        options.selNode = selNode;

		options.editor = this;

        for (var id in this.tools) {
            try {
                if (this.tools[id]['afterUpdateContent']) this.tools[id].afterUpdateContent(options);
            } catch (e) {
                this.logMessage(_('Exception while processing afterUpdateContent on ${id}: ${msg}', {'id': id, 'msg': e.message}), 2);
                console.error(_('Exception while processing afterUpdateContent on ${id}: ${msg}', {'id': id, 'msg': e}));
            }
        }

        this.setEditableContent(this.getBody());
    };

    this.extendElements = function() {

		// ximdoc elements
    	this.elements = $('[uid]', this.getInnerDocument()).each(
    		function(index, elem) {
    			var ximElement = this.getXimDocument().getElement(elem.getAttribute('uid'));
    			elem.ximElement = ximElement;
    			elem.rngElement = ximElement.schemaNode;
    			ximElement._htmlElements.push(elem);
    			elem.isEditable = (
			    		elem['getAttribute'] &&
			    		elem.getAttribute('uid') &&
			    		elem.getAttribute('uid') != this.nodeId + '.1' &&
			   		elem.getAttribute('editable') != 'no'
				    	);
    		}.bind(this)
    	)
    	.addClass('xedit-rngelement');

    	// no ximdoc elements
    	$(':not([uid])', this.getInnerDocument()).each(
			function(index, elem) {
				elem.isEditable = false;
			}.bind(this)
		);
    };

    this.setEditableContent = function(node) {

		// TODO: Restore selected element before setting "no editable content"?
		// NOTE: Firefox3 will suport contentEditable attribute: http://starkravingfinkle.org/blog/2007/07/firefox-3-contenteditable/

		var mode = node.isEditable;

    	if (this.getBrowserName() == "IE") {
			this.this._setEDitableContent_IE(mode);
		} else {
		// FF sets design mode on documents.
		// mode values: on, off
		mode = mode ? 'Off' : 'On';
			if (this.getInnerDocument().designMode.toUpperCase() != mode.toUpperCase()) this.getInnerDocument().designMode = mode;
		}
    };

	 this._setEDitableContent_IE = function(mode) {
		 if (this.lastSelected) {
			 // This is an improvement for usability.
			 // It avoids multiple clicks for selecting an apply element.
			 this.lastSelected.setAttribute('contentEditable', 'false');
		 }

		 // MSIE can set design mode per elements.
		 // mode values: true, false, inherit, ?
		 mode = mode ? 'true' : 'false';
		 var editable = new String(node.getAttribute('contentEditable')).toUpperCase();

		 if (editable != mode.toUpperCase())
				node.setAttribute('contentEditable', mode);
		 this.lastSelected = node;

	 };

    this.enableButtonFF3 = function(elem) {
		// Workaround!
		// Firefox 3 disables the "Update Attribute" button, why?

		var buttons = null;
		if (elem['tagName'] && elem.tagName.toLowerCase() == 'button') {
			buttons = [elem];
		} else if (elem['length'] && elem['push']) {
			buttons = elem;
		} else {
			buttons = $('button', elem);
		}

		for (var i=0; i<buttons.length; i++) {
			var button = buttons[i];
			if (button.attributes.getNamedItem('disabled')) {
				button.attributes.removeNamedItem('disabled');
			}
		}
    };

	this.reloadXml = function() {
		var savebutton = getFromSelector('kupu-save-button');
		loadingImage.showLoadingImage();
	    var drawer = window.document.getElementById('kupu-librarydrawer');
	    if (drawer) {
	        drawer.parentNode.removeChild(drawer);
	    }
	    this.prepareForm(savebutton.form, 'kupu');

		var ximdoc = kupu.getXimDocument();
		var content = ximdoc.saveXML({
			asString: true,
			hideXimlets: true,
			resolveXimlinks: true
		});

		var encodedContent = "&content=" + encodeURIComponent(content);

		var xmlUrl = this._baseURL + '&ajax=json&method=getXmlFile';

		// XML request
		this.fileRequest(
			'_xmlDom', xmlUrl,
			this.updateEditor.bind(this),
			null, null, 'POST', '&content=' + content
		);
	};

};

XimdocEditor.prototype = new KupuEditor();
