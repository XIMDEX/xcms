X.actionLoaded(function (event, fn, params){
	
	fn("div.theme div.actions a.select").click(function(event){
		
		var themeName = $(event.currentTarget).attr("data-theme");
		fn("input[name='theme']").val("");
		var wasSelected = $(this).closest("div.theme").hasClass("selected")?true:false;
		fn("div.theme").removeClass("selected");
		if (!wasSelected){
			$(this).closest("div.theme").addClass("selected");
			fn("input[name='theme']").val(themeName);
		}
		return false;
  	});
});