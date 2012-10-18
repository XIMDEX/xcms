X.actionLoaded(function(event, fn, params) {

	try {

		var tagslist = fn('.xim-tagsinput-container');
		fn(tagslist).tagsinput();

	 //  var iksdata = '{"status":"ok","organisations":{"Article":[],"U . S . State Department":[],"Al - Jazeera":[],"BBC":["http:\/\/dbpedia.org\/resource\/BBC"],"Army":[]},"places":{"Tahrir Square":[],"Cairo":["http:\/\/dbpedia.org\/resource\/Cairo"],"Egypt":["http:\/\/dbpedia.org\/resource\/Egypt"],"U . S":[]}, "people":{"Hosni Mubarak":[]}}';
	 //  var iksdata  = $.parseJSON(iksdata);
	//  fn(tagslist).tagsinput('addTagslist', iksdata );

		fn('.nube_tags').children("li").children("span").click(function (event) {

			var element = event.target;
			var text = $(event.target).text();

			fn(tagslist).tagsinput('createTag', {text: text, typeTag: 'generics', url: '#', description:''});

		});
	}catch(e) {
		alert(_("Module ximTAGS needed"));
	}
});
