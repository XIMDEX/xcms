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


function validate_dates(event, button,  arrValidity) {

	var form = button.getForm();
	var fm = form.getFormMgr();

	var div_dialog = $("<div/>").attr('id', 'dialog').appendTo(form);
	var system = 0;

	$.ajax({
		url: X.restUrl + '?action=unlinknewscolector&mod=ximNEWS&method=date_system',
		type: 'GET',
		async: false,
		success: function(data){
			system = data;
		}
	});

	var msg = false;
	var rel = null;
	for ( var i in arrValidity) {

		var cal =arrValidity[i].getCalTo();
		var downdate = $(cal).calendar('getStampValue');

		if(downdate != '' && system >= downdate) {
			msg = true;
			rel = $(cal).calendar('getRel');
			break;
		 }

	}
	if (msg) {

		div_dialog.html(_('The \"End date\" of the validity period is previous to system date. This will mark the end of validitiy for this document. Do you want to continue?'));



		var dialogCallback = function(send) {
			$(div_dialog).dialog("destroy");
			if (send) {
				fm.sendForm({
					button: this,
					confirm: false
				});
			}
		}.bind(this);

		div_dialog.dialog({
			buttons: {
				_('Accept'): function() {
					dialogCallback(true);
				},
				_('Cancel'): function() {
					dialogCallback(false);
				}
			}
		});

		return true;
	}

	//button.getForm().submit();
	return false;

}
