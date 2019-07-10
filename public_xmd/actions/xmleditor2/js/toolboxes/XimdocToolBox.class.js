/**
 * Base prototype for ximdex tool boxes
 * Extends KupuToolBox
 */
function XimdocToolBox() {
    	this.getActionDescription = function() {
    		return this._actionDescription || '';
    	};
    	this.setActionDescription = function(description) {
    		this._actionDescription = description || '';
    	};
};

XimdocToolBox.prototype = new KupuToolBox();

