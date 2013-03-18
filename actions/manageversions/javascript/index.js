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

	var $nodeid = fn('input[name=nodeid]');
	var $nodetypename = fn('input[name=nodetypename]');
	var $version = fn('input[name=version]');
	var $subversion = fn('input[name=subversion]');


	function getRowVersion(rowItem) {
		return {
			version: fn(rowItem).closest('tr.version-info').find('input[name=row-version]').val(),
			subversion: fn(rowItem).closest('tr.version-info').find('input[name=row-subversion]').val()
		};
	}

	function getChannelList(rowItem) {
		return fn(rowItem).closest('tr.version-info').find('select')
	}	


	fn('a.prevdoc-button').click(function(e) {

		var v = getRowVersion(this);

		var nodetypename = $nodetypename.val();

		if (nodetypename != 'TextFile' && nodetypename != 'ImageFile' && nodetypename != 'BinaryFile') {

		    // If it is NOT a text file, a image or a binary, we take the channel
			var channellist = getChannelList(this);
			var channel = $("option:selected",channellist).val();
		} else {
			channel = 1;
		}

		var command = (nodetypename=='ImageFile' || nodetypename=='BinaryFile')
			? 'filepreview'
			: 'prevdoc';


		var action = $.extend({}, params.action, {
			command: command,
			name: _('Preview'),
			params: 'version=%s&sub_version=%s&channel=%s'.printf(v.version, v.subversion, channel)

		});

		// Opens an action in a new tab
		$(params.browser).browserwindow('openAction', action, params.nodes);

		return false;
	});


	var form = params.actionView.getForm('mv_form');

	// Recover button management
	var cbRecover = function(event, button) {
		var v = getRowVersion(button);
		$version.val(v.version);
		$subversion.val(v.subversion);
		form.action += '&method=recover';
	};

	fn('.recover-button').each(function(index, button) {
		button.beforeSubmit.add(cbRecover);
	});

	// Recover button management
	var cbDelete = function(event, button) {
		var v = getRowVersion(button);
		$version.val(v.version);
		$subversion.val(v.subversion);
		form.action += '&method=delete';
	};

	fn('.delete-button').each(function(index, button) {
		button.beforeSubmit.add(cbDelete);
	});

});
