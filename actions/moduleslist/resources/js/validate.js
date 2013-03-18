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

	var btn = fn('.submit-button').get(0);
	var active = fn('input[name="module_active"]');
	var last_active_state = $(active).is(':checked'); 
	var install = fn('input[name="module_install"]');
	var last_install_state = $(install).is(':checked'); 
	var states = fn('p.states');

	//Change install state if user check it
 	install.change(function() {
		//Save last state ( if there are errors, back last state )
		last_install_state = ! $(install).is(':checked'); 

		//if installed state is unchecked, we uncheck actived
		if(!$(install).is(':checked') ) {
			last_active_state = true;
			$(active).attr("checked", false);
		}

		sendChanges();
	});

	active.change(function(e) {
		//Save last state ( if there are errors, back last state )
		last_active_state = ! $(active).is(':checked'); 
		//Save active state ( if there are errors, back last state )
		if(!$(install).is(':checked') && $(active).is(':checked')  ) {
			last_active_state = false;
			$(install).attr("checked", true);
		}
		sendChanges();
	});

	//Send changes to servers
	function sendChanges() {
		//alternative to parseInt
		var module_active = ( true == $(active).is(':checked') )? 1 : 0;
		var module_install = ( true == $(install).is(':checked') )? 1 : 0;


		//Disable all ( not changes while saving )
		$(install).attr("disabled", true);
		$(active).attr("disabled", true);
		$(states).addClass("loading");


		var data_out  = {
			action: 'moduleslist',
			method: 'changeState',
			modsel: fn('input[name=modsel]').val(),
			module_active: module_active,
			module_install: module_install
		};

		
		//Do changes in server
		$.ajax({
			url: X.restUrl,
			type: 'GET',
			dataType: 'json',
            timeout: 25000,
			data: data_out,
			success: function(data) {
				//Error in proccess, show alerts and revert 
				if(0 == data.messages.type) {
					alert(data.messages.message);
					$(install).attr("checked",  last_install_state );
					$(active).attr("checked", last_active_state);
				}else {
					//all ok, save states
					last_active_state =  $(active).is(':checked');
					last_install_state =  $(install).is(':checked');
				}

				//Reload modules list
				X.widgetsVars.widgets.modulestab[0].loadModulesList();
				//Enable again
				$(install).attr("disabled", false);
				$(active).attr("disabled", false);
				$(states).removeClass("loading");
			},

			error: function(jqXHR, textStatus, errorThrown) {
				if("timeout" == textStatus ) {
					last_active_state =  $(active).is(':checked');
					last_install_state =  $(install).is(':checked');
				}else {
					alert(textStatus+": Error,please, revise your connection");
					//Revert states
					$(install).attr("checked",  last_install_state );
					$(active).attr("checked", last_active_state);
				}
				
				$(install).attr("disabled", false);
				$(active).attr("disabled", false);
				$(states).removeClass("loading");
			}
//			error(XMLHttpRequest, textStatus, errorThrown)
//			successdata, textStatus, XMLHttpRequest)
		});

	}

});
