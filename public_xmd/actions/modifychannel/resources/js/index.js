X.actionLoaded(function(event, fn, params)
{
	var nodeID = params.nodes[0];
	if (!nodeID) {
		window.alert('Node ID parameter has not given');
		return false;
	}
	var outputTypeVal = null;
	var languages = {
		web: {
			'static': ['html5'], 
			'include': ['php', 'jsp', 'ror', 'asp'], 
			'dynamic': ['php', 'jsp', 'ror', 'asp'], 
			'index': ['xif']
		},
		xml: {
			'static': ['xml']
		},
		other: {
			'static': ['json', 'rdf', 'sql']
		}
	};
	
	// Operations in output type selection
	var outputType = $('[name="output_type_' + nodeID + '"]');
	outputType.change(function() {
		
		// Enable the render type options panel and disable the code language
		$('#render_type_mode_' + nodeID).removeClass('disabled');
		$('#code_language_' + nodeID).addClass('disabled');
		if (removeChecks) {
			$('[name=render_type_' + nodeID + ']').attr('checked', false);
		}
		
		// Show enabled only the render types for the selected output type
		$('[name=render_type_' + nodeID + ']').each(function(){
			$(this).attr('disabled', true);
		});
		outputTypeVal = $('[name=output_type_' + nodeID + ']:checked').val();
		if (languages[outputTypeVal]) {
			for (var renderType in languages[outputTypeVal]) {
				$('input[id=render_type_' + renderType + '_' + nodeID + ']').attr('disabled', false);
			}
		}
	});
	
	// Operations in render type selection
	var renderType = $('[name="render_type_' + nodeID + '"]');
	renderType.change(function() {
		
		// Enable the code language selector and select the blank value
		$('#code_language_' + nodeID).removeClass('disabled');
		if (removeChecks) {
			$('#language_' + nodeID).val('');
		}
		
		// Show the code languages for the selected render type
		$('#language_' + nodeID + ' > option').each(function() {
			$(this).attr('disabled', true);
		});
		var renderTypeVal = $('[name=render_type_' + nodeID + ']:checked').val();
		if (languages[outputTypeVal][renderTypeVal]) {
			for (var i = 0; i < languages[outputTypeVal][renderTypeVal].length; i++) {
				$('#language_' + languages[outputTypeVal][renderTypeVal][i] + '_' + nodeID).attr('disabled', false);
			}
		}
	});
	
	// Apply the disabled options to the initial selection
	var removeChecks = false;
	outputType.change();
	renderType.change();
	var removeChecks = true;
});