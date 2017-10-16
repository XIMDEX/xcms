X.actionLoaded(function (event, fn, params){
  fn("input.input_colorpicker").ColorPicker(
  {
    onSubmit: function(hsb, hex, rgb, el) {
                $(el).val(hex);
                $(this).ColorPicker("hide");
              },

    onChange : function(hsb, hex, rgb){
                var el = $(this).data("colorpicker").el;
                var actionContainer = $(el).parentsUntil(".action_container");

                $(el).val("#"+hex);
                switch ($(el).attr("name")){
                  case "principal_color":
                          $("div.bsPreviewContainer", actionContainer).css("background-color","#"+hex);
                          break;
                  case "secundary_color":
                          $("div.bsPreviewTitle", actionContainer).css("background-color","#"+hex);
                          break;
                  case "font_color":
                          $("div.bsPreviewContent", actionContainer).css("color","#"+hex);
                          break;

                }
              }

  }).bind('keyup', function(){
        $(this).ColorPickerSetColor(this.value);
  });

  fn("input#title").bind("keyup", function(){

    var texto = $(this).val();
    var actionContainer = $(this).parentsUntil(".action_container");
    $("div.bsPreviewTitle h4", actionContainer).text(texto);


  });

   
  fn('.advanced-btn').click(
    function(){
      $(this).next("div").toggleClass("advanced-settings");
  });

  var btn = fn('.submit-button').get(0);
  $(btn).click(function(event, button){
      projectCreation.createProject(fn);
  });

  fn("li.theme div.actions a.select").click(function(){
	
	return false;
  });

  
  fn("li.theme div.actions a.custom").click(function(){
	
	var actionWidth = fn("div.action_container").width()*-1;

	fn("div.action_content form, div.action_content div.customize-template-form").animate({"margin-left":actionWidth+"px"}, "slow");
	return false;
  });

});
