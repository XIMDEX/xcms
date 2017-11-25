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

//	console.info(params);
	
	//Create calendars
	var cals = fn ('.xim-calendar-layer-container');	

	var $groupList = fn('fieldset.notifications select[name=groups]');
	var $userList = fn('fieldset.notifications ol.user-list');
	var $textarea = fn('fieldset.notifications textarea[name=texttosend]');
	var $defaultMsg = fn('input[name=default_message]');
	var $gapList = fn('fieldset.publish_date select.gap_info');
	var $notifications = fn('fieldset.notifications input.send-notifications');
	var $allLevelCheck = fn('[id*="all_levels"]');

	if ($allLevelCheck){
		$allLevelCheck.change(function(){
			if ($(this).is(":checked")){
				fn('[id*="deeplevel"]').addClass("disabled").attr("disabled",true);
			}else{
				fn('[id*="deeplevel"]').removeClass("disabled").attr("disabled",false);
			}
		});
	}

	
	function request(options) {
	
		options.data = $.extend({
			action: 'workflow_forward',
			nodeid: fn('input[name=nodeid]').val()
		}, options.data);
		
		$.ajax({
			url: X.restUrl,
			type: 'GET',
			dataType: 'json',
			data: options.data,
			success: options.cb,
			error: options.cb
//			error(XMLHttpRequest, textStatus, errorThrown)
//			successdata, textStatus, XMLHttpRequest)
		});
	}
	
	function getNotificableUsers() {	
		var val = $groupList.val().split('|');
		var groupid = val[0];
		var stateid = val[1];
		fn('input[name=groupid]').val(groupid);		
		request({
			cb: notificableUsers,
			data: {method: 'notificableUsers', groupid: groupid, stateid: stateid}
		});
	}	
	
	function notificableUsers(data, textStatus) {
		
		// cleanup
		$userList.unbind().empty();
		
		if (textStatus != 'success') return;
		
		if (data.messages) {
			// TODO: show messages
			console.log(data.messages);
			return;
		}
		
		var checked = true;
		
		data.notificableUsers.each(function(index, user) {
		
			var li = $('<li></li>')
				.addClass('user_info')
				.appendTo($userList);

			var input = $('<input type="checkbox" />')
				.attr({
					id: 'user_' + user.idUser,
					name: 'users[]'
				})
				.addClass('validable notificable check_group_notificable')
				.val(user.idUser)
				.appendTo(li);
			
			var label = $('<label></label>')
				.attr({'for': 'user_' + user.idUser})
				.html(user.userName)
				.appendTo(li);
			
			if (checked) {
				input.attr({checked: 'checked'});
				checked = !checked;
			}
			
		});
	}
	
	
	$groupList.change(getNotificableUsers).change();
	
	$textarea.blur(function() {
		if (Object.isEmpty($textarea.val())) {
			$textarea.val($defaultMsg.val());
		}
	}).blur();
	
	$notifications.change(function() {
		if (fn(this).attr('checked')) {
			fn('fieldset.notifications li.conditioned').removeClass('hidden');
		} else {
			fn('fieldset.notifications li.conditioned').addClass('hidden');
		}
	}).change();
	
});
