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


var Notify = Class.create({

	//indice de la cola
 	level_index : 0,

	INFORMATIVE : 0,
	SUCCESSFULL : 1,
	E_WARNING : 2,
	E_CRITICAL: 3,
	E_SYSTEM: 4,
	
	//type of alerts
	type_alerts :  [ "INFORMATIVE", "SUCCESSFULL", "E_WARNING", "E_CRITICAL", "E_SYSTEM" ],

	//Lists of alerts
	alerts : Array(),

	//Initialize alert objets
	initialize: function()
	{
		this.alerts = new Array();
		this.alerts[this.level_index] = [this.INFORMATIVE, this.SUCCESSFULL, this.E_WARNING, this.E_CRITICAL, this.E_SYSTEM];
		this.alerts[this.level_index][this.INFORMATIVE] = Array();
		this.alerts[this.level_index][this.E_WARNING] = Array();
	},

	//Create a new deep level
	startCapture: function() {
 		this.level_index++;
		this.initialize();
 	},

	//Close a deep level
	endCapture: function() {
		this.alerts[this.level_index] = Array();

	 	this.level_index = ( this.level_index >= 0)? --this.level_index : 0;
	},

	//it Throws a new alert in actual level
	send: function( alert_text, alert_id, alert_type) {
		if(alert_type != this.E_SYSTEM) {
			if(alert_type == this.E_WARNING || alert_type == this.INFORMATIVE ) {
				var alert_indice = this.alerts[this.level_index][alert_type].length+1;
				this.alerts[this.level_index][alert_type][alert_indice] =  { "text" : alert_text, "id" : alert_id };
			}else {
				this.alerts[this.level_index][alert_type] = { "text" : alert_text, "id" : alert_id };
			}
		}else {
			this.alerts[level_index_up][alert_type] = { "text" : alert_text, "id" : alert_id };
			this.errorSystem(alert_text, alert_id);
		}
	},

	//it Throws a new alert in before level
	sendUp: function(alert_text, alert_id, alert_type) {
		//Index for the up level
		var level_index_up = this.level_index-1;
		//Lowerflow?
		if(level_index_up < 0 ) level_index_up = 0;


		if(alert_type != this.E_SYSTEM) {
			if(alert_type == this.E_WARNING || alert_type == this.INFORMATIVE ) {
				var alert_indice = this.alerts[level_index_up][alert_type].length+1;
				this.alerts[level_index_up][alert_type][alert_indice] =  { "text" : alert_text, "id" : alert_id };
			}else {
				this.alerts[level_index_up][alert_type] = { "text" : alert_text, "id" : alert_id };
			}
		}else {
			this.alerts[level_index_up][alert_type] = { "text" : alert_text, "id" : alert_id };
			this.errorSystem(alert_text, alert_id);
		}
	},

	//All alerts to before level
	sendUpAll: function() {
		var all_alerts = this.alerts[this.level_index];
		var max = this.alerts[this.level_index].length;

		//Index for the up level
		var level_index_up = this.level_index-1;
		//Lowerflow?
		if(level_index_up < 0 ) level_index_up = 0;
		
		//Close of nivel
		this.alerts.pop();
		this.level_index--;

		for(var i= 0; i<=max; i++) {
			var alert_indice = this.alerts[level_index_up][alert_type].length+1;
			this.alerts[level_index_up][alert_type][alert_indice] =  { "text" : alert_text, "id" : alert_id };
		}
	},

	//it retrieves alerts by type 
	get: function(get_type, get_index) {
		if(get_index == "undefined" ) get_index = this.level_index;
	
		if (get_type != "undefined" )
			return this.alerts[get_level][get_type];
		else
			return this.alerts[get_level];
	},

	//it retrieves all alerts
	getAll: function() {
		return this.alerts;
	},


	validate: function(validate_type) {
		return this.alerts[this.level_index][validate_type][1];
	},

	validateError: function() { return this.validate(this.E_WARNING); },
	
	validateSucessfull: function() {  return this.validate(this.SUCCESSFULL); },

	validateInformative: function() { return this.validate(this.INFORMATIVE); },

	validateCritical: function() { return this.validate(this.E_CRITICAL); },

	validateSystem: function() { return this.validate(this.E_SYSTEM); },


	errorSystem: function(alert_text, alert_id) {
		View.display(alert_id + ": " + alert_text);
	},

	/** ************** Displaying notifys ***************************** */
	clear: function(id_form) {
		View.clear(this, id_form);
	},


	showAll: function(id_form) {
		View.notify(this, id_form);
	},

	showResultAction: function(id_form, id_node) {
		View.resultActionOk(this, id_form, id_node);
	}
});


Notifier = new Notify();
Notifier.initialize();