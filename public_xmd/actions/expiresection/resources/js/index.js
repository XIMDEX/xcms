X.actionLoaded(function(event, fn, params) {
	
	$('form#publication_form').submit(function () {
		$('#publishing_message').toggle();
		$('#form_layer').toggle();
	});

	function start_reading() {
		var t = XimTimer.getInstance();
		t.addObserver(read_log, 2000);
		t.start();
	}

	function read_log() {
		var url = window.url_root + '?actionid=' + window.actionId + '&nodeid=' + window.nodeId + '&method=publication_progress';
		$.get(
			url,
			function(data, textStatus) {
				$('#div_log').html(data);
			}
		);
	}
	
	// Create calendars
	var cals = fn('.xim-calendar-layer-container');
	
	var $groupList = fn('fieldset.notifications select[name=groups]');
	var $userList = fn('fieldset.notifications ol.user-list');
	var $textarea = fn('fieldset.notifications textarea[name=texttosend]');
	var $defaultMsg = fn('input[name=default_message]');
	var $gapList = fn('fieldset.publish_date select.gap_info');
	var $notifications = fn('fieldset.notifications input.send-notifications');
	
	// Levels selector
	var $levelsRadio = fn('[name="levels"]');
	if ($levelsRadio) {
		$levelsRadio.change(function() {
			var value = $('input[name=levels]:checked').val();
			if (value != 'deep') {
				
				// All levels or zero level
				fn('[id="deeplevel"]').addClass("disabled").attr("disabled", true);
			}
			else {
				
				// N levels of deep
				fn('[id="deeplevel"]').removeClass("disabled").attr("disabled", false);
			}
		});
	}
	
	// Node types checbox and selector
	var $publishType = fn('[name="publishType"]');
	if ($publishType) {
		$publishType.change(function() {
			var nodeTypes = $('select[name=types]');
			if ($publishType.attr('checked')) {
				nodeTypes.attr('disabled', false);
			}
			else {
				nodeTypes.val(0);
				nodeTypes.attr('disabled', true);
			}
		});
	}
	
	function request(options) {
		options.data = $.extend({
			action: 'publicatesection',
			nodeid: fn('input[name=nodeid]').val()
		}, options.data);
		$.ajax({
			url: X.restUrl,
			type: 'GET',
			dataType: 'json',
			data: options.data,
			success: options.cb,
			error: options.cb
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
		
		// Cleanup
		$userList.unbind().empty();
		if (textStatus != 'success') return;
		if (data.messages) {
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