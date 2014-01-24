X.actionLoaded(function (event, fn, params){

	fn("li.theme div.actions a.select").click(function(event){
		      
		var themeName = $(event.currentTarget).attr("data-theme");		
		fn("input[name='theme']").val("");
		var wasSelected = $(this).closest("li.theme").hasClass("selected")?true:false;
		fn("li.theme").removeClass("selected");		
		if (!wasSelected){
			$(this).closest("li.theme").addClass("selected");
			fn("input[name='theme']").val(themeName);
		}
		return false;
  	});
});