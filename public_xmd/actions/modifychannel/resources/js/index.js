X.actionLoaded(function(event, fn, params) {
	var nodeID = params.nodes[0];
	if (!nodeID) {
		window.alert('Node ID parameter has not given');
		return false;
	}
	var outputType = $('[name="OutputType_' + nodeID + '"]');
	outputType.change(function() {
		if ($('input[name=OutputType_' + nodeID + ']:checked').val() == 'web')
		{
			$('#render_type_mode_' + nodeID).removeClass('hidden');
		}
		else
		{
			$('#render_type_mode_' + nodeID).addClass('hidden');
			$('input[name=RenderType_' + nodeID + ']').prop('checked', false);
		}
	});
	outputType.change();
	return true;
});