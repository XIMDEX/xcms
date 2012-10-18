X.actionLoaded(function(event, fn, params) {

	fn('.button-select-all').click(function(event) {
		fn('input:checkbox').attr('checked', true);
	});

	fn('.button-deselect-all').click(function(event) {
		fn('input:checkbox').attr('checked', false);
	});




});
