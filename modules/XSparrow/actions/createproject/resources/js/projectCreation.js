var projectCreation = (function(){

	var arraySteps = [];
	var project={};
	var name="";
	var formInputs = {};

	//Load project
	arraySteps.push({
		method:"loadProject",
		label: "Loading Project",
		getData: function(){
			return {
				"name": name
			};
		},
		callback: function(data){
			project.idProject = data.project.idproject;
			project.language = data.project.lang;
			project.channel = data.project.channel;
		}

	});

	//Load XimPVD
	arraySteps.push({
		method:"loadProjectXimPvd",
		label: "Loading schemas",
		getData: function(){
			return {
				"idProject":project.idProject
			}
		},
		callback: function(data){
			project.templates = data.project.templates;
		}
	});

	arraySteps.push({
		method:"loadProjectXimPtd",
		label: "Loading templates",
		getData: function(){
			return {
				"idProject":project.idProject,
				"name": name
			}
		}
	});



	arraySteps.push({
		method:"loadServer",
		label: "Loading Server",
		getData: function(){
			return {
				"idProject":project.idProject,
				"lang":project.language,
				"channel":project.channel,
				"templates":project.templates,
				"title":formInputs.title,
				"principal_color":formInputs.principalColor,
				"secundary_color":formInputs.secundaryColor,
				"font_color":formInputs.fontColor,

			}
		}
	});

	var fn = false;
	var actionUrl = "?action=createproject&mod=XSparrow&method=";
	var indexCalling = 0;



	var _initialize = function(fun){

		fn = fun;
		indexCalling=0;
		name = $("#name").val();
		formInputs.title = $("#title").val();
		formInputs.principalColor = $("#principal_color").val();
		formInputs.secundaryColor = $("#secundary_color").val();
		formInputs.fontColor = $("#font_color").val();


	}

	//Call the current step defined by ArraySteps and indexCalling
	var callNextStep = function(){

		var currentData = {};
		if(arraySteps[indexCalling].getData)
			 currentData = arraySteps[indexCalling].getData();
		$.ajax({
			url: X.restUrl+actionUrl+arraySteps[indexCalling].method,
			type: 'POST',
			async:false,
			dataType: 'json',
			data: currentData,
			success: function(data) {

				if (arraySteps[indexCalling].callback)
					arraySteps[indexCalling].callback(data);

				$("#"+arraySteps[indexCalling].method).
					removeClass("pending").
					addClass("success");

				//TODO: Erase before Elena see this.
				$("#"+arraySteps[indexCalling].method).css("opacity","1");

				indexCalling++;
				if (indexCalling < arraySteps.length){
					callNextStep();
				}
			}
		})
	}

	//Remove DOM Element related with the steps Message
	var cleanStepsMessages = function(){

		fn(".stepsMessageList").remove();
	}

	//Call DOM Element related with the steps Message
	var createStepsMessages = function(){

		var $divMessageList = $("<div/>",{class:"stepsMessageList"});
		for(var i = 0; i<arraySteps.length; i++){

			$divLabel = $("<div/>",{class:"pending"}).
						text(arraySteps[i].label).
						attr("id",arraySteps[i].method);
			//TODO: Erase before Elena see this.
			$divLabel.css("opacity","0.2");
			$divMessageList.append($divLabel);
		}
		fn(".second_color").append($divMessageList);

	}

	return {

		createProject: function(fn){

			_initialize(fn);
			cleanStepsMessages();
			createStepsMessages();
			callNextStep();
		},

		getArraySteps: function(){

			return arraySteps;
		},

		getIndexCalling: function(){
			return indexCalling;
		},

		increaseIndexCalling: function(){

			indexCalling++;
		}

	}
}());

