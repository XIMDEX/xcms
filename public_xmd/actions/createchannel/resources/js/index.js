X.actionLoaded(function(event, fn, params)
{
	var outputTypeVal = null;
	var languages = {
		web: {
			'static': ['html5'], 
			'include': ['php'], 
			'dynamic': ['jsp', 'ror', 'asp', 'php'], 
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
	var outputType = $('[name="output_type"]');
	outputType.change(function() {
		
		// Enable the render type options panel and disable the code language
		$('#render_type_mode').removeClass('disabled');
		$('#code_language').addClass('disabled');
		$('[name=render_type]').attr('checked', false);
		
		// Show enabled only the render types for the selected output type
		$('[name=render_type]').each(function(){
			$(this).attr('disabled', true);
		});
		outputTypeVal = $('[name=output_type]:checked').val();
		if (languages[outputTypeVal]) {
			for (var renderType in languages[outputTypeVal]) {
				$('input[id=render_type_' + renderType + ']').attr('disabled', false);
			}
		}
	});
	
	// Operations in render type selection
	var renderType = $('[name="render_type"]');
	renderType.change(function() {
		
		// Enable the code language selector and select the blank value
		$('#code_language').removeClass('disabled');
		$('#language').val('');
		
		// Show the code languages for the selected render type
		$('#language > option').each(function() {
			$(this).attr('disabled', true);
		});
		var renderTypeVal = $('[name=render_type]:checked').val();
		if (languages[outputTypeVal][renderTypeVal]) {
			for (var i = 0; i < languages[outputTypeVal][renderTypeVal].length; i++) {
				$('#' + languages[outputTypeVal][renderTypeVal][i]).attr('disabled', false);
			}
		}
	});
});