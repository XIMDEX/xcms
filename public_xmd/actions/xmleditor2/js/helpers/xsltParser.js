function xsltParser() {


	this.xsltTransform = function(xmldoc, xsldoc, editor) {

		var xsltProcessor = new XSLTProcessor();
		var resultDocument = null;

		try {
			xsltProcessor.importStylesheet(xsldoc);
		} catch (e) {
			editor.alert(e.name + " - "  + e.message + "\n\n\n" + _('XSL stylesheet cannot be imported. Changing to tree view.'));
			editor.tools.editorviewtool.setView(editor.tools.editorviewtool.VIEW_TREE);
			return false;
		}

		this.beforeTransform(xmldoc, xsldoc, editor);

		try {
			resultDocument = xsltProcessor.transformToDocument(xmldoc);
		} catch (e) {
			editor.alert(e.name + " - "  + e.message + "\n" + _('No valid XSLT templates or no docxap template detected. Changing back to tree view.'));
			editor.tools.editorviewtool.setView(editor.tools.editorviewtool.VIEW_TREE);

			return false;
		}

		this.afterTransform(xmldoc, xsldoc, resultDocument, editor);

		if(editor.getView()=="normal"){
			$('#kupu-designview-button').addClass("kupu-designview-pressed").removeClass("kupu-designview");
		}
		// IE doesn't allow to extend XML DOM documents.
		/*resultDocument.toString = function(node) {
		 n od*e = node || this;
		 var str = new XMLSerializer().serializeToString(node);
		 return str;
	}*/

		return resultDocument;
	};

	this.beforeTransform = function(xmldoc, xsldoc, editor) {

			// Called before the XSL transformation
			var tools = editor.getTools();

			for (var id in tools) {
				try {
					if (tools[id]['beforeTransform']) tools[id].beforeTransform(xmldoc, xsldoc);
				} catch (e) {
					editor.logMessage(_('Exception while processing beforeTransform on ${id}: ${msg}', {'id': id, 'msg': e.message}), 2);
					console.error(_('Exception while processing beforeTransform on ${id}: ${msg}', {'id': id, 'msg': e}));
			}
		}
	};

	this.afterTransform = function(xmldoc, xsldoc, resultDocument, editor) {
		// Called after the XSL transformation
		var tools = editor.getTools();

		for (var id in tools) {
			try {
				if (tools[id]['afterTransform']) tools[id].afterTransform(xmldoc, xsldoc, resultDocument);
			} catch (e) {
				editor.logMessage(_('Exception while processing afterTransform on ${id}: ${msg}', {'id': id, 'msg': e.message}), 2);
				console.error(_('Exception while processing afterTransform on ${id}: ${msg}', {'id': id, 'msg': e}));
			}
		}
	}
};

xslt = new xsltParser();
