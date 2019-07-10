/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

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