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


var Alerts = Class.create({


	//Initialize alert objets
	initialize: function() {
	},


	clear: function(obj_notify, id_form )  {
		$(id_form).descendants().each(function(field) {
			if(field.hasClassName('field_in_error') ) {
				$(field.id).removeClassName('field_in_error');
			}
		}
		)
	},

	notify: function(obj_notify, id_form) {
		var msg = '';

		if(obj_notify.validateError()) {
			msg += " Atención. "+obj_notify.alerts[obj_notify.level_index][obj_notify.E_CRITICAL].text+":";
			for(var i= 1; i< obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING].length; i++) {
				try {
					msg += "\n "+ /* ( "+ obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING][i].id + " ) */" --> " ;
					sms = obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING][i].text;
					msg += this.html_entity_decode(sms); 
					if( obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING][i].id != id_form && obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING][i].id  ) {
						$(obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING][i].id).addClassName('field_in_error');
					}
				} catch(e) {}
			}

			//put focus in first field with error
			if( obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING].length && obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING][1].id != id_form && obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING][1].id  ) {				$(obj_notify.alerts[obj_notify.level_index][obj_notify.E_WARNING][1].id).activate();
			}
		}else {
			msg += " Atención. "+obj_notify.alerts[obj_notify.level_index][obj_notify.SUCCESSFULL].text+":";
		}
		alert(msg);
	},

	display: function(msg) {
		alert(msg);
	},

	question: function(question, obj, func_ok, param) {
		var result = confirm(question);
		if(result) {
			try{  obj[func_ok](obj, param);   }catch(err) { }
			return true;
		}
		return false;
	},

	resultActionOk: function(obj_notify, id_form, id_node) {
		msg = " <ul> ";
// 		msg = "<p>"+obj_notify.alerts[obj_notify.level_index][obj_notify.SUCCESSFULL].text+"</p>";
			for(var i= 1; i< obj_notify.alerts[obj_notify.level_index][obj_notify.INFORMATIVE].length; i++) {
				try {
					msg += "<li>"+obj_notify.alerts[obj_notify.level_index][obj_notify.INFORMATIVE][i].text+"</li>"; 

				} catch(e) { }
			}
		msg +=  " </ul> ";


		if(id_form != "undefined") {
	 		$(id_form).replace(msg);
		}

		//Reload node
		if(id_node != "undefined") {
			parent.parent.frames['toolbar'].reloadNode(id_node);
		}
	},
	
	html_entity_decode: function ( string ) {
	    // Convert all HTML entities to their applicable characters
	    // 
	    // +    discuss at: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_html_entity_decode/
	    // +       version: 804.1015
	    // +   original by: john (http://www.jd-tech.net)
	    // +      input by: ger
	    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // *     example 1: html_entity_decode('Kevin &amp; van Zonneveld');
	    // *     returns 1: 'Kevin & van Zonneveld'
	
	    var ret, tarea = document.createElement('textarea');
	    tarea.innerHTML = string;
	    ret = tarea.value;
	    return ret;
	}
	
});


View = new Alerts();
View.initialize();