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
	/*
	the json config obj.
	name: the class given to the element where you want the tooltip to appear
	bgcolor: the background color of the tooltip
	color: the color of the tooltip text
	text: the text inside the tooltip
	time: if automatic tour, then this is the time in ms for this step
	position: the position of the tip. Possible values are
		TL	top left
		TR  top right
		BL  bottom left
		BR  bottom right
		LT  left top
		LB  left bottom
		RT  right top
		RB  right bottom
		T   top
		R   right
		B   bottom
		L   left
	 */

	var config_action = [
		{
			"name" 		: ".hbox-panel-container-0 > div.hbox-panel",
			"bgcolor"	: "black",
			"color"		: "white",
			"position"	: "L",
			"text"		: "This is the navigation area. Here, different views can be chosen",
			"time" 		: 5000
		},
		{
			"name" 		: ".hbox-panel-container-0 .xim-first-tab",
			"bgcolor"	: "black",
			"color"		: "white",
			"text"		: "Clicking this tab, Project view is activated",
			"position"	: "L",
			"time" 		: 5000
		},
		{
			"name" 		: ".hbox-panel-container-0 .xim-first-tab + li",
			"bgcolor"	: "black",
			"color"		: "white",
			"text"		: "Clicking this tab, Control center view is activated",
			"position"	: "L",
			"time" 		: 5000
		},
		{
			"name" 		: ".hbox-panel-container-0 .xim-first-tab + li + li",
			"bgcolor"	: "black",
			"color"		: "white",
			"text"		: "Clicking this tab, Module view is activated",
			"position"	: "L",
			"time" 		: 5000
		},
		{
			"name" 		: ".browser-projects-view-menubar .button-container:first-child",
			"bgcolor"	: "black",
			"color"		: "white",
			"text"		: "In Project view, it can be chosen between Tree view...",
			"position"	: "L",
			"time" 		: 5000
		},
		{
			"name" 		: "button.Grid:parent",
			"bgcolor"	: "black",
			"color"		: "white",
			"text"		: "... Table view...",
			"position"	: "L",
			"time" 		: 5000
		},
		{
			"name" 		: "button.List",
			"bgcolor"	: "black",
			"color"		: "white",
			"text"		: "... or List view...",
			"position"	: "L",
			"time" 		: 5000
		}

	];

	config_action[0]["text"]=_('This is the navigation area. Here, different views can be chosen');
	config_action[1]["text"]=_('Clicking this tab, Project view is activated');
	config_action[2]["text"]=_('Clicking this tab, Control center view is activated');
	config_action[3]["text"]=_('Clicking this tab, Module view is activated');
	config_action[4]["text"]=_('In Project view, it can be chosen between Tree view...');
	config_action[5]["text"]=_('... Table view...');
	config_action[6]["text"]=_('... or List view...');
		
	X.getTourInstance().start(config_action);
	
});
