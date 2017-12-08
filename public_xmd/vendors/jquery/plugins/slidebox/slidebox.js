/**
 * Slide Box : a jQuery Plug-in
 * Samuel Garneau <samgarneau@gmail.com>
 * http://samgarneau.com
 * 
 * Released under no license, just use it where you want and when you want.
 */

(function($){
	
	$.fn.slideBox = function(params){
	
		var content = $(this).html();
		var defaults = {
			width: "100%",
			height: "200px",
			position: "bottom",			// Possible values : "top", "bottom"
			labelOpen: "Abrir",
			labelClose: "Cerrar"
		}
		
		// extending the fuction
		if(params){
			$.extend(defaults, params);
		}
		
		var divPanel = $("<div class='slide-panel-"+defaults.position+"'>");
		var divContent = $("<div class='content-"+defaults.position+"'>");
		//for ie
		$(divContent).css("height", "1px");
	
		
		var parent = this[0].parentNode;
		$(this).appendTo(divContent);
		
		$(divPanel).addClass(defaults.position);
		$(divPanel).css("width", defaults.width);
		
		// centering the slide panel
		$(divPanel).css("left", (100 - parseInt(defaults.width))/2 + "%");
	
		// if position is top we're adding 
		if(defaults.position == "top"){
			$(divPanel).append($(divContent));
		}
		
		// adding buttons
		$(divPanel).append("<div class='slide-button-"+defaults.position+"'>"+defaults.labelOpen+"</div>");
		$(divPanel).append("<div style='display: none' id='close-button' class='slide-button-"+defaults.position+"'>"+defaults.labelClose+"</div>");
		
		if(defaults.position == "bottom"){
			$(divPanel).append($(divContent));
		}
		
		$(parent).append(divPanel);
		
		// Buttons action
		$(".slide-button-"+defaults.position).click(function(){
			if($(this).attr("id") == "close-button"){
				$(divContent).animate({height: "1px"}, 1000);
				$(divPanel).css("z-index", 0);
				$(divContent).css("z-index", 0);
			}
			else{
				$(divContent).animate({height: defaults.height}, 1000);
				$(divPanel).css("z-index", 25);
				$(divContent).css("z-index", 25);
			}
				
			$(".slide-button-"+defaults.position).toggle();
		});
		
		//By default the box is open
//		$(divContent).animate({height: defaults.height}, 1000);
//		$(".slide-button").toggle();
	};
	
	
})(jQuery);