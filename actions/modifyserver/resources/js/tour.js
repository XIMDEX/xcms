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
			"bgcolor"	: "#464646",
			"color"		: "#eee",
			"position"	: "TL",
			"text"		: "This action allows to define in which servers your documents are going to be published and which protocol will be used.",
			"time" 		: 5000
		},
		{
			"name" 		: ".browser-action-view-content:visible select#serverid",
			"bgcolor"	: "#464646",
			"color"		: "#eee",
			"position"	: "L",
			"text"		: "Create a new server where publishing your documents or choose an already configured one to edit its properties",
			"time" 		: 5000
		},
		{
			"name" 		: ".browser-action-view-content:visible select#protocol",
			"bgcolor"	: "#464646",
			"color"		: "#eee",
			"text"		: "Specify the way in which your documents are going to be copied to the server chosen in the previous step",
			"position"	: "L",
			"time" 		: 5000
		}

	];
		
	config_action[0]["text"]=_('This action allows to define in which servers your documents are going to be published and which protocol will be used.');
	config_action[1]["text"]=_('Create a new server where publishing your documents or choose an already configured one to edit its properties');
	config_action[2]["text"]=_('Specify the way in which your documents are going to be copied to the server chosen in the previous step');
	
	X.getTourInstance().start(config_action, command);
}