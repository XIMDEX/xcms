/**
 * jQuery UI versions between 1.7 and above changes the getters management.
 */
var fixJQueryGetters = function($) {

	if ($.ui.version != '1.7') return;
		
	$.each(['listview', 'treeview', 'searchpanel'], function(index, widget) {
		var w = $.ui[widget];
		if (typeof w === undefined) return;
		w.getter = w.prototype.getter;
	});
};