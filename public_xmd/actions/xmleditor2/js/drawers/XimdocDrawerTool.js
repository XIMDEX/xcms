
/**
 * The ximdex drawer tool
 * Implements DrawerTool, extends XimdocTool
 */
function XimdocDrawerTool() {
    /* a tool to open and fill drawers

        this tool has to (and should!) only be instantiated once
    */
    	this.drawers           = null;
    	this.current_drawer    = null;
    	this.current_id        = null;

    	this.initialize = function(editor) {

        	this.editor    = editor;
        	this.drawers   = {};
        	this.isIE      = this.editor.getBrowserName() == 'IE';
        	// this essentially makes the drawertool a singleton
        	window.ximdocdrawertool = this;
    	};

    	this.registerDrawer = function(id, drawer, editor) {
    		if (!this.drawers) this.drawers = {};
        	this.drawers[id] = drawer;
        	drawer.initialize(editor || this.editor, this);
    	};

    	this.openDrawer = function(id) {
        	/* open a drawer */
        	if (this.current_drawer) {
            		this.closeDrawer();
        	};
        	var drawer = this.drawers[id];
        	if (this.isIE) {
            		drawer.editor._saveSelection();
        	}
        	drawer.createContent();
        	//drawer.editor.suspendEditing();
        	this.current_drawer = drawer;
        	this.current_id = id;
    	};

    	this.isOpen = function(id) {
    		return this.current_id == id;
    	};

    	this.closeDrawer = function(button) {

        	if (!this.current_drawer) return;

        	this.current_drawer.hide();
        	//this.current_drawer.editor.resumeEditing();
        	this.current_drawer = null;
        	this.current_id = null;
    	};

    	this.updateState = function(options) {
    	};

    	this.beforeUpdateContent = function(options) {
    	};

    	this.afterUpdateContent = function(options) {
    	};
};

XimdocDrawerTool.prototype = new XimdocTool();

