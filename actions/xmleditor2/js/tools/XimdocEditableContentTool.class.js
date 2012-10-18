
function XimdocEditableContentTool() {
	this.updateState = function(options) {
		if (!options.selNode || !options.event || !['click', 'keyup'].contains(options.event.type)) return;
		options.selNode = this.editor.selNode;
		this.editor.setEditableContent(options.selNode);
    	};
};

XimdocEditableContentTool.prototype = new XimdocTool();
