X.actionLoaded(function(event, fn, params) {
	$("form#up_form").submit(function(e) {
		e.preventDefault();
		var formData = new FormData();
		var input = $(this).find('#upload');
		if (input.length > 0) {
			formData.append("upload", input[0].files[0]);
			var request = new XMLHttpRequest();
			request.open("POST", $(this).attr("action"));
			request.onreadystatechange = function () {
				if (request.readyState == 4 && request.status == 200) {
					var data = $.parseJSON(request.responseText);
					var form = fn('form');
				    var fm = form.get(0).getFormMgr();
					fm.actionNotify(data.messages, $("form#up_form"));
				}
			};
			request.send(formData);
		}
	});
});