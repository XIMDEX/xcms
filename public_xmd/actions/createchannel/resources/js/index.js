X.actionLoaded(function(event, fn, params) {
	var outputType = $('[name="output_type"]');
	if (outputType)
	{
		outputType.change(function() {
			if ($('input[name=output_type]:checked').val() == 'web')
			{
				$('#render_type_mode').removeClass('hidden');
			}
			else
			{
				$('#render_type_mode').addClass('hidden');
				$('input[name=web_render_type]').prop('checked', false);
			}
		});
	}
});