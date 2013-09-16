X.actionLoaded(function(event, fn, params) {

	try {

		var tagslist = fn('.xim-tagsinput-container');
		fn(tagslist).tagsinput();
		var url = X.baseUrl+"?mod=ximTAGS&action=setmetadata&method=getRelatedTagsFromContent";
		var limitResult = 5;
		$.ajax({
			url:url,
			data:{
					nodeid: params.nodes[0]
   				 },
			dataType:"json",
			success:function(data){

				var parsedData  = $.parseJSON(data);
				if (parsedData){
					var isSemantic = 0;
					tags = [];
					for (var i in parsedData){
					
						switch(i){
							case "content":
								isSemantic = 0;
								break;
							case "semantic":
								isSemantic = 1;
								break;
						}
						if (i != "status"){
						var typeElement = parsedData[i];
							for (var j in typeElement){
								var tag = typeElement[j];
								var count = 0;
								for (var nameTag in tag){
									tags.push({isSemantic:0,text:nameTag,type:tag.type});
									count++;
									if (count >= limitResult)
										break;
								}
							}
						}
					}

					fn(tagslist).tagsinput('addTagslist', tags );
				}
			}
		});

		fn('.nube_tags').children("li").children("span").click(function (event) {

			var element = event.target;
			var text = $(event.target).text();

			fn(tagslist).tagsinput('createTag', {text: text, typeTag: 'generics', url: '#', description:''});

		});
	}catch(e) {
		alert(_("Module ximTAGS needed"));
	}
});
