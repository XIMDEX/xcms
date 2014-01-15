X.actionLoaded(function (event, fn, params){

	fn("li.theme div.actions a.select").click(function(event){
		      
		var themeName = $(event.currentTarget).attr("data-theme");
		fn("input[name='theme']").val(themeName);
		fn("li.theme").removeClass("selected");
		$(this).closest("li.theme").addClass("selected");
		return false;
  	});
});