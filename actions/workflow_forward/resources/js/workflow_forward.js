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
	f(cals.length>0){ 
		var cal_from = $(cals[0]).calendar({format: 'dd-mm-yy'});
		var cal_to = $(cals[1]).calendar({format: 'dd-mm-yy'});
		$(params.context).data('cal_from', cal_from);
		$(params.context).data('cal_to', cal_to);
		var validitycalendar = new X.ValidityCalendar({
			calfrom: cal_from,
			calto: cal_to
		});
	}
	 
	var $groupList = fn('fieldset.notifications select[name=groups]');
	var $userList = fn('fieldset.notifications ol.user-list');
	var $textarea = fn('fieldset.notifications textarea[name=texttosend]');
	var $defaultMsg = fn('input[name=default_message]');
	var $gapList = fn('fieldset.publish_date select.gap_info');
	var $notifications = fn('fieldset.notifications input.send-notifications');
	
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
		fn('input[name=stateid]').val(stateid);
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
	
	$gapList.change(function() {
	   var cal_from = 	$(params.context).data('cal_from');
	   var cal_to =  $(params.context).data('cal_to');
		var val = $(this).val();
		if (Object.isEmpty(val)) return;
		val = val.split('-');
		var from = val[0] || null;
		var to = val[1] || null;

		cal_from.calendar('setNewDate',from);
		cal_to.calendar('setNewDate',to);
	});
	
	$notifications.change(function() {
		if (fn(this).attr('checked')) {
			fn('fieldset.notifications li.conditioned').removeClass('hidden');
		} else {
			fn('fieldset.notifications li.conditioned').addClass('hidden');
		}
	}).change();
	
});
