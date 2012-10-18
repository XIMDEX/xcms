/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
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

X.actionLoaded(function(event, fn, params) {

	$('h3.action_tour',params.context).click(function() {start_tour(params.action.command);}.bind(this));
	
});

function start_tour(command) {
	var config_action = [
 		{
			"name" 		: "li.ui-state-active a.browser-action-view:parent:parent",
			"bgcolor"	: "white",
			"color"		: "black",
			"position"	: "T",
			"text"		: "This action allows to edit in a text window the document code.<br/>",
			"time" 		: 5000
		},
		{
			"name" 		: ".browser-action-view-content:visible fieldset.editor",
			"bgcolor"	: "black",
			"color"		: "white",
			"position"	: "R",
			"text"		: "Edit the document code in the text box",
			"time" 		: 5000
		},
		{
			"name" 		: ".browser-action-view-content:visible fieldset.buttons-form > a",
			"bgcolor"	: "black",
			"color"		: "white",
			"text"		: "Press Save button",
			"position"	: "R",
			"time" 		: 5000
		}

	];
		
	config_action[0]["text"]=_('This action allows to edit in a text window the document code.<br/>');
	config_action[1]["text"]=_('Edit the document code in the text box');
	config_action[2]["text"]=_('Press Save button');

	X.getTourInstance().start(config_action, command);
}