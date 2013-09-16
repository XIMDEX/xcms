X.actionLoaded(function (event, fn, params){

	var configidNode;
	var indexIdNode;
	var actionUrl = "?action=createproject&mod=XSparrow&method=getIdNodeByName&nodeName=";
	var projectName=fn(".projectName").val();

	$('li#treeview-nodeid-10000')
	.closest('div.xim-treeview-container')
	.treeview('refresh', 10000); 

	var getIdNode = function(methodName, callback){		
		$.ajax({
			url: X.restUrl+actionUrl+methodName,
			type: 'POST',			
			async:false,
			dataType: 'json',
			data: {
				"projectName":projectName
			},
			success: function(data) {
				callback(data.idnode);
			}
		});
	}

	var getConfigIdNode = function(callback){
		getIdNode("config-ides", callback);
	}

	var getIndexIdNode = function(callback){

		getIdNode("index-ides", callback);
	}

	var launchAction = function(idNode){
		 $("div.xim-treeview-container").treeview("navigate_to_idnode",idNode);
		 $("#bw1").browserwindow("openAction",{
                                                       label: "Edit xml",
                                                       name: "Edit xml",
                                                       command:'xmleditor2',
                                                       params:'',
                                                       bulk:'0'
                                               }, [idNode]);
	}
	fn(".config_ximlet").click(function(){

		getConfigIdNode(launchAction);
		return false;
	});
	fn(".edit_idnode").click(function(){

		getIndexIdNode(launchAction);
		return false;
	});
	
	fn(".edit_syncdata").click(function(){

		getIdNode("Local_server", function(idNode){
			$("div.xim-treeview-container").treeview("navigate_to_idnode",idNode);
	                 $("#bw1").browserwindow("openAction",{
                                                       label: "Edit data sync",
                                                       name: "Edit data sync",
                                                       command:'modifyserver',
                                                       params:'',
                                                       bulk:'0'
                                               }, [idNode]);

		});
		return false;	
	});
	
});
