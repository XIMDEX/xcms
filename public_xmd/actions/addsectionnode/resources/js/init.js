
X.actionLoaded(function (event, fn, params) {
        var scope = $(params.context).find("form").first().scope();
	scope.init(params);
    
});