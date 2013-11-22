X.actionLoaded(function(event, fn, params) {

	try {

		var tagslist = fn('.xim-tagsinput-container');
		fn(tagslist).tagsinput();
		if (fn(".xim-tagsinput-list-related").length){


		var url = X.baseUrl+"/?mod=ximTAGS&action=setmetadata&method=getRelatedTagsFromContent";
		var limitResult = 50;
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
								var Xtags = typeElement[j];
                                                        	for (var tag in Xtags){
                                                                	var count = 0;
                                                                	tags.push({isSemantic:Xtags[tag].isSemantic,text:tag,type:Xtags[tag].type,conf:Xtags[tag].confidence});
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
		}

		fn('.tagcloud').find("li.xim-tagsinput-taglist").click(function (event) {

			var element = event.target;
			var text = $(event.target).text();
			$(this).slideUp(1000);
			fn(tagslist).tagsinput('createTag', {text: text, typeTag: 'generics', url: '#', description:''});


		});
		fn(".ontology-browser-container").ontologywidget(
			{
			onSelect: function(el) {
				this.attachedElement.tagsinput("createTag",
					{text: el.name, typeTag: "custom", url: '#', description:''}
				);
			},
			offSelect: function(name) {
				this.attachedElement.tagsinput("onRemovingTag", name);
			},
			attachedElement: fn(".xim-tagsinput-container")
			}
		);
	}catch(e) {
		alert(_("Module ximTAGS needed"));
	}
});
